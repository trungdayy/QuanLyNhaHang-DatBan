<?php

namespace App\Http\Controllers\Shop\Oderqr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Models
use App\Models\DatBan;
use App\Models\DanhMuc;
use App\Models\MonTrongCombo;
use App\Models\OrderMon;
use App\Models\ChiTietOrder;
use App\Models\MonAn;
use App\Models\ComboBuffet;
use App\Models\BanAn;
use App\Models\ChiTietDatBan; // [MỚI] Model bảng trung gian

use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB; // [MỚI] Dùng Transaction

class OrderController extends Controller
{
    // HÀM HELPER: Tìm bàn theo mã QR
    private function findBanAnByQrKey($qrKey)
    {
        return BanAn::where('ma_qr', $qrKey)->first();
    }

    // Hiển thị trang chọn combo
    public function showComboSelectionPage($qrKey)
    {
        $ban = $this->findBanAnByQrKey($qrKey);
        if (!$ban) abort(404, 'Mã QR không hợp lệ hoặc bàn không tồn tại.');

        $combos = ComboBuffet::where('trang_thai', 'dang_ban')
            ->orderBy('gia_co_ban')
            ->get();

        // Lấy thông tin DatBan nếu đã có khách đến
        $datBan = DatBan::where('ban_id', $ban->id)
            ->where('trang_thai', 'khach_da_den')
            ->first();

        // [SỬA] Kiểm tra nếu bàn này đã chọn combo rồi (check trong bảng chi tiết)
        if ($datBan && $datBan->chiTietDatBan()->exists()) {
            return redirect()->route('oderqr.menu', ['qrKey' => $ban->ma_qr])
                ->with('info', 'Bàn này đã chọn combo. Vui lòng tiếp tục gọi món.');
        }

        return view('shop.oderqr.select-combo', [
            'maQr' => $ban->ma_qr,
            'tenBan' => $ban->so_ban,
            'combos' => $combos,
            'qrKey' => $qrKey,
            'datBan' => $datBan,
        ]);
    }

    // [QUAN TRỌNG] Hàm xử lý bắt đầu gọi món với nhiều Combo
    public function startOrder(Request $request)
    {
        // 1. Validate dữ liệu
        // Input 'combos' là mảng: [{id: 1, so_luong: 2}, ...]
        $validator = Validator::make($request->all(), [
            'ma_qr' => 'required|exists:ban_an,ma_qr',
            'dat_ban_id' => 'nullable|exists:dat_ban,id',
            
            'combos' => 'required|array|min:1', 
            'combos.*.id' => 'required|exists:combo_buffet,id',
            'combos.*.so_luong' => 'required|integer|min:1',

            'ten_khach' => 'nullable|string|max:255',
            'sdt_khach' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // 2. Lấy thông tin bàn
        $maQr = $request->input('ma_qr');
        $ban = BanAn::where('ma_qr', $maQr)->first();
        if (!$ban) return back()->withErrors(['ma_qr' => 'Mã QR không hợp lệ'])->withInput();
        $banId = $ban->id;

        $inputCombos = $request->input('combos');
        $tenKhachInput = $request->input('ten_khach') ?: 'Khách Vãng Lai';
        $sdtKhachInput = $request->input('sdt_khach') ?: '0';

        $nowObj = Carbon::now('Asia/Ho_Chi_Minh');
        $nowString = $nowObj->toDateTimeString();

        // Tính tổng số khách và thời lượng lớn nhất
        $tongSoKhach = 0;
        $maxThoiLuong = 0;

        foreach ($inputCombos as $c) {
            $tongSoKhach += $c['so_luong'];
            $comboInfo = ComboBuffet::find($c['id']);
            if ($comboInfo && $comboInfo->thoi_luong_phut > $maxThoiLuong) {
                $maxThoiLuong = $comboInfo->thoi_luong_phut;
            }
        }
        if ($maxThoiLuong == 0) $maxThoiLuong = 120; // Mặc định

        // --- BẮT ĐẦU TRANSACTION ---
        DB::beginTransaction();
        try {
            // 3. TÌM HOẶC TẠO ĐƠN ĐẶT BÀN
            $datBan = null;
            if ($request->input('dat_ban_id')) {
                $datBan = DatBan::find($request->input('dat_ban_id'));
            }

            if (!$datBan) {
                $datBan = DatBan::where('ban_id', $banId)
                    ->where('trang_thai', 'khach_da_den')
                    ->whereDate('gio_den', $nowObj->toDateString())
                    ->orderBy('gio_den', 'desc')
                    ->first();
            }

            if ($datBan) {
                // [CASE A] CẬP NHẬT
                $datBan->update([
                    'nguoi_lon' => $tongSoKhach, // Cập nhật tổng khách
                    'thoi_luong_phut' => $maxThoiLuong,
                    'ten_khach' => $request->filled('ten_khach') ? $tenKhachInput : $datBan->ten_khach,
                    'sdt_khach' => $request->filled('sdt_khach') ? $sdtKhachInput : $datBan->sdt_khach,
                    'updated_at' => $nowString,
                ]);
            } else {
                // [CASE B] TẠO MỚI
                $datBan = DatBan::create([
                    'ma_dat_ban' => 'QR' . $nowObj->format('Ymd') . '-' . strtoupper(bin2hex(random_bytes(2))),
                    'ten_khach' => $tenKhachInput,
                    'sdt_khach' => $sdtKhachInput,
                    'nguoi_lon' => $tongSoKhach,
                    'tre_em' => 0,
                    'ban_id' => $banId,
                    // 'combo_id' => null, // Đã bỏ cột này
                    'gio_den' => $nowString,
                    'thoi_luong_phut' => $maxThoiLuong,
                    'trang_thai' => 'khach_da_den',
                    'created_at' => $nowString,
                    'updated_at' => $nowString,
                ]);
            }
            
            $ban->update(['trang_thai' => 'dang_phuc_vu']);

            // 4. LƯU CHI TIẾT COMBO (Xóa cũ tạo mới để tránh trùng)
            ChiTietDatBan::where('dat_ban_id', $datBan->id)->delete();

            foreach ($inputCombos as $item) {
                $comboModel = ComboBuffet::find($item['id']);
                ChiTietDatBan::create([
                    'dat_ban_id' => $datBan->id,
                    'combo_id' => $item['id'],
                    'so_luong' => $item['so_luong'],

                ]);
            }

            // 5. TẠO ORDER VÀ ĐẨY MÓN TỰ ĐỘNG
            $orderMon = OrderMon::firstOrCreate(
                ['dat_ban_id' => $datBan->id, 'trang_thai' => 'dang_xu_li'],
                ['ban_id' => $datBan->ban_id, 'tong_mon' => 0, 'tong_tien' => 0]
            );

            // Kiểm tra xem đã lên món lần nào chưa
            $hasItems = ChiTietOrder::where('order_id', $orderMon->id)
                ->where('loai_mon', 'combo')
                ->exists();

            if (!$hasItems) {
                $itemsToInsert = [];
                // Tổng số lượng suất ăn để nhân món (Logic cũ của bạn là nhân theo tổng người)
                // Hoặc Logic mới: Món của combo nào thì nhân theo số lượng combo đó?
                // Ở đây mình giữ Logic cũ: Tổng người lớn + trẻ em = Số lượng món được lên
                $tongNguoiAn = $datBan->nguoi_lon + $datBan->tre_em;

                // Lấy tất cả món từ các combo đã chọn
                foreach ($inputCombos as $c) {
                    $monTrongCombo = MonTrongCombo::where('combo_id', $c['id'])->get();
                    
                    foreach ($monTrongCombo as $mon) {
                        // Kiểm tra trùng: Nếu món này đã được thêm vào mảng itemsToInsert rồi thì thôi
                        // (Tránh trường hợp 2 combo có cùng 1 món, khách ăn bị double món quá nhiều)
                        $alreadyAdded = false;
                        foreach ($itemsToInsert as $inserted) {
                            if ($inserted['mon_an_id'] == $mon->mon_an_id) {
                                $alreadyAdded = true; break;
                            }
                        }
                        if ($alreadyAdded) continue;

                        $monAn = MonAn::find($mon->mon_an_id);
                        if ($monAn && $monAn->trang_thai === 'con') {
                            $itemsToInsert[] = [
                                'order_id' => $orderMon->id,
                                'mon_an_id' => $mon->mon_an_id,
                                'so_luong' => $tongNguoiAn, // Mỗi người 1 phần
                                'loai_mon' => 'combo',
                                'trang_thai' => 'cho_bep',
                                'ghi_chu' => null,
                                'created_at' => $nowString,
                                'updated_at' => $nowString,
                            ];
                        }
                    }
                }
                if (!empty($itemsToInsert)) ChiTietOrder::insert($itemsToInsert);
            }

            DB::commit(); // Lưu thành công
            return redirect()->route('oderqr.menu', ['qrKey' => $ban->ma_qr]);

        } catch (\Exception $e) {
            DB::rollBack(); // Lỗi thì hoàn tác
            return back()->with('error', 'Lỗi hệ thống: ' . $e->getMessage())->withInput();
        }
    }

    // Hiển thị Menu gọi món
    public function showGoiMonPage($qrKey)
    {
        $ban = $this->findBanAnByQrKey($qrKey);
        if (!$ban) abort(404, 'Mã QR không hợp lệ hoặc bàn không tồn tại.');

        $datBan = DatBan::where('ban_id', $ban->id)
            ->where('trang_thai', 'khach_da_den')
            ->first();

        // [SỬA] Check quan hệ chi tiết thay vì combo_id
        if (!$datBan || !$datBan->chiTietDatBan()->exists()) {
            return redirect()->route('oderqr.select_combo', ['qrKey' => $qrKey]);
        }

        return view('shop.oderqr.menu', [
            'banId' => $ban->id,
            'tenBan' => $ban->so_ban,
            'qrKey' => $qrKey
        ]);
    }

    // API lấy thông tin Session & Menu
    public function getSessionInfo($qrKey)
    {
        $ban = $this->findBanAnByQrKey($qrKey);
        if (!$ban) return response()->json(['message' => 'Mã QR không hợp lệ.'], 404);

        $datBan = DatBan::where('ban_id', $ban->id)
            ->where('trang_thai', 'khach_da_den')
            ->with(['banAn', 'chiTietDatBan.combo']) // [MỚI] Eager load
            ->first();

        if (!$datBan) return response()->json(['message' => 'Bàn này chưa sẵn sàng phục vụ.'], 404);

        $thoiGianConLaiPhut = 0;
        if ($datBan->gio_den && $datBan->thoi_luong_phut) {
            $gioKetThuc = Carbon::parse($datBan->gio_den)->addMinutes($datBan->thoi_luong_phut);
            $thoiGianConLaiPhut = $gioKetThuc->isFuture() ? $gioKetThuc->diffInMinutes(now()) : 0;
        }

        // [SỬA] Lấy danh sách ID món ăn từ TẤT CẢ combo đã chọn
        $ownedComboIds = $datBan->chiTietDatBan->pluck('combo_id')->toArray();
        $monTrongComboIds = [];
        
        if (!empty($ownedComboIds)) {
            $monTrongComboIds = MonTrongCombo::whereIn('combo_id', $ownedComboIds)
                ->pluck('mon_an_id')
                ->unique()
                ->toArray();
        }

        $menu = DanhMuc::where('hien_thi', 1)
            ->with(['monAn' => function ($query) {
                $query->where('trang_thai', 'con');
            }])
            ->get();

        $menu->each(function ($danhMuc) use ($monTrongComboIds) {
            if ($danhMuc->monAn) {
                $danhMuc->monAn->each(function ($monAn) use ($monTrongComboIds) {
                    // Check nếu món nằm trong danh sách được phép gọi miễn phí (combo)
                    $monAn->is_in_combo = in_array($monAn->id, $monTrongComboIds);
                });
            }
        });

        // Thông tin các combo đã chọn để hiển thị FE
        $selectedCombosInfo = $datBan->chiTietDatBan->map(function($item) {
            return [
                'ten' => $item->combo->ten_combo ?? 'Combo cũ',
                'sl' => $item->so_luong
            ];
        });

        return response()->json([
            'dat_ban_info' => $datBan,
            'selected_combos' => $selectedCombosInfo,
            'menu' => $menu,
        ]);
    }

    public function submitOrder(Request $request)
    {
        // ... (Giữ nguyên logic cũ của bạn) ...
        $validator = Validator::make($request->all(), [
            'dat_ban_id' => 'required|exists:dat_ban,id',
            'items' => 'required|array|min:1',
            'items.*.mon_an_id' => 'required|exists:mon_an,id',
            'items.*.so_luong' => 'required|integer|min:1',
            'items.*.ghi_chu' => 'nullable|string|max:255',
            'items.*.loai_mon' => 'required|in:combo,goi_them',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $datBanId = $request->input('dat_ban_id');
        $items = $request->input('items');

        $datBan = DatBan::find($datBanId);
        if (!$datBan || $datBan->trang_thai !== 'khach_da_den') {
            return response()->json(['message' => 'Phiếu đặt bàn không hợp lệ hoặc đã đóng.'], 403);
        }

        $nowString = Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString();

        $orderMon = OrderMon::firstOrCreate(
            ['dat_ban_id' => $datBanId, 'trang_thai' => 'dang_xu_li'],
            ['ban_id' => $datBan->ban_id, 'tong_mon' => 0, 'tong_tien' => 0, 'created_at' => $nowString, 'updated_at' => $nowString]
        );

        foreach ($items as $item) {
            $monAn = MonAn::find($item['mon_an_id']);
            if ($monAn->trang_thai !== 'con') continue;

            ChiTietOrder::create([
                'order_id' => $orderMon->id,
                'mon_an_id' => $item['mon_an_id'],
                'so_luong' => $item['so_luong'],
                'loai_mon' => $item['loai_mon'],
                'trang_thai' => 'cho_bep',
                'ghi_chu' => $item['ghi_chu'],
                'created_at' => $nowString,
                'updated_at' => $nowString
            ]);
        }

        return response()->json([
            'message' => 'Gửi order thành công!',
            'order_id' => $orderMon->id
        ], 201);
    }

    public function getOrderStatus(Request $request, $datBanId)
    {
        $chiTietMonAn = ChiTietOrder::whereHas('orderMon', function ($query) use ($datBanId) {
            $query->where('dat_ban_id', $datBanId);
        })
            ->with('monAn:id,ten_mon,hinh_anh')
            ->select('id', 'mon_an_id', 'so_luong', 'trang_thai', 'loai_mon', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        if ($chiTietMonAn->isEmpty()) {
            return response()->json(['message' => 'Chưa gọi món nào.'], 404);
        }

        return response()->json(['items' => $chiTietMonAn]);
    }

    public function showQrListPage(Request $request)
    {
        $banAns = BanAn::all();
        $selectedBanId = $request->query('ban') ?? ($banAns->first()?->id ?? null);
        $selectedBan = $banAns->find($selectedBanId);

        return view('shop.oderqr.list', compact('banAns', 'selectedBan'));
    }
}