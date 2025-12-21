<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DatBan;
use App\Models\BanAn;
use App\Models\ComboBuffet;
use App\Models\NhanVien;
use App\Models\ChiTietDatBan; // Đảm bảo Model này đã map đúng bảng 'dat_ban_combo'
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DatBanController extends Controller
{
    /**
     * Danh sách đặt bàn
     */
    public function index(Request $request)
    {
        try {
            $query = DatBan::with(['chiTietDatBan.combo', 'banAn', 'nhanVien']);

            // Tìm kiếm
            if ($request->filled('q')) {
                $keyword = trim($request->q);
                $query->where(function ($q) use ($keyword) {
                    $q->where('ma_dat_ban', 'LIKE', "%$keyword%")
                        ->orWhere('ten_khach', 'LIKE', "%$keyword%")
                        ->orWhere('sdt_khach', 'LIKE', "%$keyword%");
                });
            }
            // Lọc trạng thái
            if ($request->filled('status')) {
                $query->where('trang_thai', $request->status);
            }
            // Lọc theo bàn
            if ($request->filled('ban_id')) {
                $query->where('ban_id', $request->ban_id);
            }
            // Lọc ngày
            if ($request->filled('date_from')) {
                $query->where('gio_den', '>=', Carbon::parse($request->date_from)->startOfDay());
            }
            if ($request->filled('date_to')) {
                $query->where('gio_den', '<=', Carbon::parse($request->date_to)->endOfDay());
            }

            $danhSachDatBan = $query->orderByDesc('created_at')->paginate(10);
            $banAnsAll = BanAn::with('khuVuc')->where('trang_thai', '!=', 'khong_su_dung')->get(['id', 'so_ban', 'khu_vuc_id']);

            return view('admins.dat-ban.index', compact('danhSachDatBan', 'banAnsAll'));
        } catch (\Exception $e) {
            Log::error("Lỗi index: " . $e->getMessage());
            return back()->with('error', 'Không thể tải danh sách.');
        }
    }

    /**
     * Form tạo mới
     */
    public function create()
    {
        try {
            // Lấy tất cả combo đang bán (giống nhân viên)
            $combos = ComboBuffet::where('trang_thai', 'dang_ban')->get();
            
            // Lấy danh sách nhân viên
            $nhanViens = NhanVien::where('trang_thai', 1)
                ->whereIn('vai_tro', ['le_tan', 'phuc_vu', 'quan_ly'])
                ->get();

            return view('admins.dat-ban.create', compact('combos', 'nhanViens'));
        } catch (\Exception $e) {
            Log::error("Lỗi create: " . $e->getMessage());
            return back()->with('error', 'Lỗi tải form.');
        }
    }

    /**
     * Xử lý lưu đặt bàn (Store)
     */
    public function store(Request $request)
    {
        $now = Carbon::now();

        // 1. Validate cơ bản (giống nhân viên)
        $request->validate([
            'ten_khach' => 'required|string|max:255',
            'sdt_khach' => 'required|string|max:20',
            'tong_khach' => 'required|integer|min:1',
            'ban_id' => [
                'required',
                'exists:ban_an,id',
                \Illuminate\Validation\Rule::exists('ban_an', 'id')->where('trang_thai', '!=', 'khong_su_dung'),
            ],
            'gio_den' => 'required|date',
        ], [
            'ban_id.required' => 'Vui lòng chọn bàn.',
            'ban_id.exists' => 'Bàn được chọn không tồn tại hoặc đang bị khóa.',
        ]);

        $tongKhach = $request->nguoi_lon + ($request->tre_em ?? 0);
        
        // Logic kiểm tra số lượng Combo (từ cart_data)
        $tongCombo = 0;
        if ($request->filled('cart_data')) {
            $cartItems = json_decode($request->cart_data, true);
            if (is_array($cartItems)) {
                $tongCombo = collect($cartItems)->where('key', 'like', 'combo_%')->sum('quantity');
            }
        }
        
        if ($tongCombo > 0 && $tongCombo < $tongKhach) {
            return back()->withInput()->with('error', "Số suất Combo phải >= số khách.");
        }

        $banAn = BanAn::find($request->ban_id);
        
        if (!$banAn) {
            return back()->withInput()->with('error', "Bàn được chọn không tồn tại.");
        }
        
        if ($banAn->trang_thai === 'khong_su_dung') {
            return back()->withInput()->with('error', "Bàn này đang bị khóa, không thể sử dụng.");
        }
        
        if ($banAn->so_ghe < $tongKhach) {
            return back()->withInput()->with('error', "Bàn này không đủ ghế. Bàn có {$banAn->so_ghe} ghế nhưng cần {$tongKhach} ghế.");
        }

        // Tính thời lượng mong muốn (Mặc định 120p)
        $thoiLuongMongMuon = 120;

        // Kiểm tra xung đột & Tự động cắt giờ (Gap Filling) - giống nhân viên
        $start = Carbon::parse($request->gio_den);
        $endMongMuon = $start->copy()->addMinutes($thoiLuongMongMuon);

        $conflict = DatBan::where('ban_id', $request->ban_id)
            ->whereNotIn('trang_thai', ['huy', 'hoan_tat'])
            ->where('gio_den', '>', $start)
            ->where('gio_den', '<', $endMongMuon)
            ->orderBy('gio_den', 'asc')
            ->first();

        $thoiLuongChot = $thoiLuongMongMuon;
        $messageWarning = null;

        if ($conflict) {
            $conflictStart = Carbon::parse($conflict->gio_den);
            $minutesGap = $start->diffInMinutes($conflictStart, false);
            $thoiLuongKhaDung = $minutesGap - 15;

            if ($thoiLuongKhaDung >= 60) {
                $thoiLuongChot = $thoiLuongKhaDung;
                $messageWarning = "Lưu ý: Bàn này có khách lúc " . $conflictStart->format('H:i') . 
                                  ". Thời gian phục vụ giới hạn còn {$thoiLuongChot} phút.";
            } else {
                return back()->withInput()->with('error', 
                    "Bàn bị kẹt lịch lúc {$conflictStart->format('H:i')}. Chỉ còn trống {$minutesGap} phút, không đủ phục vụ.");
            }
        } else {
            // Kiểm tra đơn trước đó
            $prevBooking = DatBan::where('ban_id', $request->ban_id)
                ->whereNotIn('trang_thai', ['huy', 'hoan_tat'])
                ->where('gio_den', '<=', $start)
                ->whereRaw("DATE_ADD(gio_den, INTERVAL thoi_luong_phut MINUTE) > ?", [$start])
                ->first();
                
            if ($prevBooking) {
                 return back()->withInput()->with('error', "Bàn chưa giải phóng xong (Khách cũ chưa hết giờ).");
            }
        }

        DB::beginTransaction();
        try {
            $maDatBan = 'DB-' . $now->format('YmdHis') . '-' . strtoupper(Str::random(4));
            
            $datBan = DatBan::create([
                'ma_dat_ban' => $maDatBan,
                'ten_khach' => $request->ten_khach,
                'email_khach' => $request->email_khach,
                'sdt_khach' => $request->sdt_khach,
                'nguoi_lon' => $request->nguoi_lon,
                'tre_em' => $request->tre_em ?? 0,
                'ban_id' => $request->ban_id,
                'gio_den' => $start,
                'thoi_luong_phut' => $thoiLuongChot,
                'ghi_chu' => ($request->ghi_chu ?? '') . ($messageWarning ? " | [System] $messageWarning" : ""),
                'trang_thai' => 'cho_xac_nhan',
                'nhan_vien_id' => $request->nhan_vien_id ?? null,
                'tien_coc' => $request->tien_coc ?? 0,
                'la_dat_online' => 0,
            ]);

            // Lưu combo từ cart_data
            $this->syncBookingDetails($datBan, $request->cart_data);

            DB::commit();

            if ($messageWarning) {
                return redirect()->route('admin.dat-ban.index')
                    ->with('warning', "Tạo đơn thành công nhưng bị giới hạn giờ! " . $messageWarning);
            }

            return redirect()->route('admin.dat-ban.index')
                ->with('success', 'Tạo đặt bàn thành công! Mã: ' . $maDatBan);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Lỗi lưu Đặt bàn Admin: " . $e->getMessage());
            return back()->withInput()->with('error', 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }

    /**
     * Helper: Lưu chi tiết món/combo
     */
    private function syncBookingDetails($datBan, $cartJson)
    {
        if (empty($cartJson) || trim($cartJson) === '' || trim($cartJson) === '[]') {
            return;
        }

        $cartItems = json_decode($cartJson, true);
        
        if (!is_array($cartItems) || empty($cartItems)) {
            return;
        }

        foreach ($cartItems as $item) {
            $key = $item['key'] ?? '';
            $qty = (int)($item['quantity'] ?? 0);
            
            if ($qty <= 0) {
                continue;
            }

            if (Str::startsWith($key, 'combo_')) {
                ChiTietDatBan::create([
                    'dat_ban_id' => $datBan->id,
                    'combo_id'   => str_replace('combo_', '', $key),
                    'so_luong'   => $qty,
                ]);
            } elseif (Str::startsWith($key, 'mon_')) {
                ChiTietDatBan::create([
                    'dat_ban_id' => $datBan->id,
                    'mon_an_id'  => str_replace('mon_', '', $key),
                    'so_luong'   => $qty,
                ]);
            }
        }
    }

    /**
     * Chi tiết đặt bàn
     */
    public function show($id) {
        try {
            $datBan = DatBan::with(['banAn', 'chiTietDatBan.combo', 'nhanVien'])->findOrFail($id);
            return view('admins.dat-ban.show', compact('datBan'));
        } catch (\Exception $e) {
            return redirect()->route('admin.dat-ban.index')->with('error', 'Không tìm thấy đơn đặt bàn.');
        }
    }

    /**
     * Form chỉnh sửa (Đây là hàm bạn bị thiếu trước đó)
     */
    public function edit($id)
    {
        try {
            $datBan = DatBan::with('chiTietDatBan.combo')->findOrFail($id);
            if (in_array($datBan->trang_thai, ['hoan_tat', 'huy'])) {
                return redirect()->route('admin.dat-ban.index')->with('error', 'Không thể sửa đơn đã đóng.');
            }

            $banAns = BanAn::where('trang_thai', '!=', 'khong_su_dung')->get();

            $loaiCombos = ComboBuffet::where('trang_thai', 'dang_ban')
                ->select('loai_combo', 'gia_co_ban')
                ->groupBy('loai_combo', 'gia_co_ban')
                ->orderBy('gia_co_ban', 'asc')
                ->get();

            // Logic lấy combo hiện tại để fill vào form
            $currentLoaiCombo = null;
            if ($datBan->chiTietDatBan->count() > 0) {
                $firstDetail = $datBan->chiTietDatBan->first();
                if ($firstDetail && $firstDetail->combo) {
                    $currentLoaiCombo = $firstDetail->combo->loai_combo;
                }
            }

            $combosOfCurrentType = [];
            if ($currentLoaiCombo) {
                $combosOfCurrentType = ComboBuffet::where('loai_combo', $currentLoaiCombo)
                    ->where('trang_thai', 'dang_ban')
                    ->get();
            }

            return view('admins.dat-ban.edit', compact('datBan', 'banAns', 'loaiCombos', 'currentLoaiCombo', 'combosOfCurrentType'));
        } catch (\Exception $e) {
            Log::error("Lỗi edit: " . $e->getMessage());
            return redirect()->route('admin.dat-ban.index')->with('error', 'Lỗi tải đơn.');
        }
    }

    /**
     * Cập nhật đặt bàn (Update)
     */
    public function update(Request $request, $id)
    {
        $datBan = DatBan::findOrFail($id);
        
        $request->validate([
            'ten_khach' => 'required|string|max:255',
            'sdt_khach' => 'required|string|max:20',
            'email_khach' => 'nullable|email|max:255',
            'nguoi_lon' => 'required|integer|min:1',
            'tre_em'    => 'nullable|integer|min:0',
            'gio_den'   => 'required|date',
            'combos'    => 'nullable|array',
            'combos.*.id' => 'nullable|exists:combo_buffet,id',
            'combos.*.so_luong' => 'nullable|integer|min:0',
        ]);

        $tongNguoi = $request->nguoi_lon + ($request->tre_em ?? 0);
        $hasCombo = false;
        $tongCombo = 0;

        if ($request->filled('combos')) {
            foreach ($request->combos as $c) {
                $qty = (int)($c['so_luong'] ?? 0);
                if ($qty > 0) {
                    $tongCombo += $qty;
                    $hasCombo = true;
                }
            }
        }

        if ($hasCombo && $tongCombo < $tongNguoi) {
            return back()->with('error', "Lỗi: Tổng số suất Combo ($tongCombo) không được ít hơn tổng số người ($tongNguoi).")->withInput();
        }

        // Tính lại thời lượng
        $duration = 120;
        if ($hasCombo) {
            $comboIds = array_column($request->combos, 'id');
            $dbMaxDuration = ComboBuffet::whereIn('id', $comboIds)->max('thoi_luong_phut');
            if ($dbMaxDuration) $duration = $dbMaxDuration;
        }

        $newStart = Carbon::parse($request->gio_den);

        // Check trùng lịch (Trừ chính đơn này ra)
        if ($request->ban_id) {
            $conflict = DatBan::where('ban_id', $request->ban_id)
                ->where('id', '!=', $id)
                ->whereNotIn('trang_thai', ['huy', 'hoan_tat'])
                ->whereBetween('gio_den', [
                    $newStart->copy()->subMinutes($duration - 1),
                    $newStart->copy()->addMinutes($duration - 1)
                ])->first();

            if ($conflict) {
                return back()->with('error', "Bàn bị trùng giờ với đơn khác.")->withInput();
            }
        }

        DB::beginTransaction();
        try {
            $datBan->update([
                'ten_khach' => $request->ten_khach,
                'sdt_khach' => $request->sdt_khach,
                'email_khach' => $request->email_khach,
                'nguoi_lon' => $request->nguoi_lon,
                'tre_em'    => $request->tre_em ?? 0,
                'ban_id' => $request->ban_id,
                'gio_den' => $request->gio_den,
                'thoi_luong_phut' => $duration,
                'tien_coc' => $request->tien_coc ?? 0,
                'ghi_chu' => $request->ghi_chu,
            ]);

            // Xóa hết combo cũ của đơn này và thêm mới (nếu có)
            ChiTietDatBan::where('dat_ban_id', $id)->delete();
            
            if ($hasCombo) {
                foreach ($request->combos as $item) {
                    if (($item['so_luong'] ?? 0) > 0) {
                        ChiTietDatBan::create([
                            'dat_ban_id' => $datBan->id,
                            'combo_id'   => $item['id'],
                            'so_luong'   => $item['so_luong'],
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.dat-ban.index')->with('success', 'Cập nhật thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Lỗi update: " . $e->getMessage());
            return back()->with('error', 'Lỗi cập nhật.');
        }
    }

    /**
     * Xóa đặt bàn
     */
    public function destroy($id) {
        try {
            $datBan = DatBan::findOrFail($id);
            if ($datBan->trang_thai == 'khach_da_den') {
                return back()->with('error', 'Không thể xóa khi khách đang ăn.');
            }
            $datBan->delete();
            return redirect()->route('admin.dat-ban.index')->with('success', 'Xóa thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Không thể xóa.');
        }
    }

    /**
     * AJAX: Cập nhật trạng thái nhanh
     */
    public function updateStatus(Request $request, $id) {
        $datBan = DatBan::findOrFail($id);
        $datBan->update(['trang_thai' => $request->trang_thai_moi]);
        return back()->with('success', 'Cập nhật trạng thái thành công!');
    }

    /**
     * AJAX: Kiểm tra bàn trống THÔNG MINH (Gap Filling) - giống nhân viên
     */
    public function ajaxCheckBanTrong(Request $request)
    {
        $checkTime = $request->input('time') ? Carbon::parse($request->input('time')) : Carbon::now();
        $soKhach = (int) $request->input('so_khach', 1);

        // 1. Lấy tất cả bàn khả dụng
        $tables = BanAn::where('trang_thai', '!=', 'khong_su_dung')
            ->where('so_ghe', '>=', $soKhach)
            ->orderBy('so_ghe', 'asc')
            ->orderBy('so_ban', 'asc')
            ->get();

        // 2. Lấy danh sách Đơn Online "Chưa xếp bàn" (Phantom Bookings)
        $unassignedBookings = DatBan::whereNull('ban_id')
            ->whereIn('trang_thai', ['cho_xac_nhan', 'da_xac_nhan', 'khach_da_den'])
            ->where(function ($q) use ($checkTime) {
                $q->where('gio_den', '<=', $checkTime)
                  ->whereRaw("DATE_ADD(gio_den, INTERVAL IFNULL(thoi_luong_phut, 120) MINUTE) > ?", [$checkTime]);
                $q->orWhereBetween('gio_den', [$checkTime, $checkTime->copy()->addMinutes(120)]);
            })
            ->orderBy('nguoi_lon', 'desc')
            ->get();

        $result = [];
        $hasFreeTables = false;
        $hasLimitedTables = false;

        foreach ($tables as $table) {
            $prefix = "";
            if ($table->khu_vuc_id == 9) $prefix = "[SOS] ";

            // BƯỚC 1: KIỂM TRA BÀN CÓ ĐANG BỊ GÁN CỨNG (KHÁCH NGỒI) KHÔNG?
            $physicallyOccupied = DatBan::where('ban_id', $table->id)
                ->whereIn('trang_thai', ['cho_xac_nhan', 'da_xac_nhan', 'khach_da_den'])
                ->where('gio_den', '<=', $checkTime)
                ->whereRaw("DATE_ADD(gio_den, INTERVAL IFNULL(thoi_luong_phut, 120) MINUTE) > ?", [$checkTime])
                ->exists();

            if ($physicallyOccupied) {
                continue;
            }

            // BƯỚC 2: KIỂM TRA GAP FILLING (KHÁCH TƯƠNG LAI)
            $nextHardBooking = DatBan::where('ban_id', $table->id)
                ->where('gio_den', '>', $checkTime)
                ->whereIn('trang_thai', ['cho_xac_nhan', 'da_xac_nhan', 'khach_da_den'])
                ->orderBy('gio_den', 'asc')
                ->first();

            $minutesAvailable = 9999;
            if ($nextHardBooking) {
                $minutesAvailable = $checkTime->diffInMinutes(Carbon::parse($nextHardBooking->gio_den), false) - 15;
            }

            // BƯỚC 3: MÔ PHỎNG GÁN ĐƠN ONLINE (GÁN ẢO)
            $isReservedForOnline = false;
            $onlineBookingInfo = null;

            if (!in_array($table->khu_vuc_id, [5, 9])) { 
                foreach ($unassignedBookings as $key => $booking) {
                    if ($table->so_ghe >= $booking->nguoi_lon) {
                        $isReservedForOnline = true;
                        $onlineBookingInfo = $booking;
                        unset($unassignedBookings[$key]);
                        break; 
                    }
                }
            }

            // BƯỚC 4: TỔNG HỢP KẾT QUẢ
            if ($isReservedForOnline) {
                $gioKhach = Carbon::parse($onlineBookingInfo->gio_den)->format('H:i');
                $result[] = [
                    'id' => $table->id,
                    'so_ban' => $prefix . $table->so_ban,
                    'so_ghe' => $table->so_ghe,
                    'trang_thai' => 'reserved',
                    'khu_vuc_id' => $table->khu_vuc_id,
                    'phut_con_lai' => 0,
                    'message' => "⚠️ Giữ cho đơn Online {$onlineBookingInfo->ma_dat_ban} lúc {$gioKhach}"
                ];
            } 
            elseif ($minutesAvailable < 9999) {
                if ($minutesAvailable >= 45) {
                    $hasLimitedTables = true;
                    $gioKhachSau = Carbon::parse($nextHardBooking->gio_den)->format('H:i');
                    $result[] = [
                        'id' => $table->id,
                        'so_ban' => $prefix . $table->so_ban,
                        'so_ghe' => $table->so_ghe,
                        'trang_thai' => 'limited',
                        'phut_con_lai' => $minutesAvailable,
                        'khu_vuc_id' => $table->khu_vuc_id,
                        'message' => "Trống {$minutesAvailable}p (Khách sau: {$gioKhachSau})"
                    ];
                }
            } 
            else {
                $hasFreeTables = true;
                $result[] = [
                    'id' => $table->id,
                    'so_ban' => $prefix . $table->so_ban,
                    'so_ghe' => $table->so_ghe,
                    'trang_thai' => 'free',
                    'phut_con_lai' => 9999,
                    'khu_vuc_id' => $table->khu_vuc_id,
                    'message' => "Trống"
                ];
            }
        }

        // LỌC KẾT QUẢ
        if ($hasFreeTables || $hasLimitedTables) {
            $result = array_filter($result, function($item) {
                return $item['trang_thai'] !== 'reserved';
            });
        }

        // SẮP XẾP
        usort($result, function ($a, $b) {
            $isA_Backup = in_array($a['khu_vuc_id'], [5, 9]);
            $isB_Backup = in_array($b['khu_vuc_id'], [5, 9]);
            if ($isA_Backup && !$isB_Backup) return -1;
            if (!$isA_Backup && $isB_Backup) return 1;

            if ($a['trang_thai'] === 'free' && $b['trang_thai'] !== 'free') return -1;
            if ($a['trang_thai'] !== 'free' && $b['trang_thai'] === 'free') return 1;

            if ($a['trang_thai'] === 'limited' && $b['trang_thai'] !== 'limited') return -1;
            if ($a['trang_thai'] !== 'limited' && $b['trang_thai'] === 'limited') return 1;

            return $a['so_ghe'] <=> $b['so_ghe'];
        });

        return response()->json(array_values($result));
    }

    /**
     * AJAX: Lấy danh sách bàn trống theo giờ (giữ lại để tương thích)
     */
    public function ajaxGetAvailableTables(Request $request) {
        // Chuyển sang dùng ajaxCheckBanTrong
        return $this->ajaxCheckBanTrong($request);
    }

    /**
     * AJAX: Lấy combo theo loại (99k, 199k...)
     */
    public function ajaxGetCombosByLoai(Request $request)
    {
        $loai = $request->input('loai_combo');
        if (!$loai) {
            return response()->json(['combos' => []]);
        }

        $combos = ComboBuffet::where('loai_combo', $loai)
            ->where('trang_thai', 'dang_ban')
            ->get(['id', 'ten_combo', 'gia_co_ban']);

        return response()->json([
            'combos' => $combos
        ]);
    }
}