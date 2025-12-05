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
use App\Models\ChiTietDatBan;

use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


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

        // Kiểm tra nếu bàn này đã chọn combo rồi
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

    // Hàm xử lý bắt đầu gọi món với nhiều Combo
// ... trong class OrderController
public function startOrder(Request $request)
{
    // 1. Validate dữ liệu
    $validator = Validator::make($request->all(), [
        'ma_qr' => 'required|exists:ban_an,ma_qr',
        'dat_ban_id' => 'nullable|exists:dat_ban,id',

        'combos' => 'required|array|min:1',
        'combos.*.id' => 'required|exists:combo_buffet,id',
        'combos.*.so_luong' => 'required|integer|min:1',
        
        // [SỬA 1]: THÊM VALIDATION CHO HAI TRƯỜNG KHÁCH
        'nguoi_lon' => 'required|integer|min:1',
        'tre_em' => 'nullable|integer|min:0',

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

    // [SỬA 2]: LẤY DỮ LIỆU NGƯỜI LỚN & TRẺ EM TỪ REQUEST
    $nguoiLonInput = $request->input('nguoi_lon', 1);
    $treEmInput = $request->input('tre_em', 0);

    $nowObj = Carbon::now('Asia/Ho_Chi_Minh');
    $nowString = $nowObj->toDateTimeString();

    // Tính tổng số khách và thời lượng lớn nhất
    // LƯU Ý: Đang tính $tongSoKhach = TỔNG SỐ LƯỢNG VÉ COMBO đã chọn.
    // Nếu bạn muốn tính theo số lượng người nhập tay thì dùng:
    // $tongSoKhach = $nguoiLonInput + $treEmInput;
    
    // Nếu giữ logic tính theo số lượng vé Combo (current code):
    $tongSoKhach = 0; // Sẽ được tính lại trong vòng lặp dưới

    $maxThoiLuong = 0;
    $tongTienComboBanDau = 0;

    foreach ($inputCombos as $c) {
        $tongSoKhach += $c['so_luong']; // Vẫn giữ logic tính tổng số lượng combo đã chọn
        $comboInfo = ComboBuffet::find($c['id']);
        if ($comboInfo) {
            // ... (Phần tính maxThoiLuong và tongTienComboBanDau giữ nguyên)
            if ($comboInfo->thoi_luong_phut > $maxThoiLuong) {
                $maxThoiLuong = $comboInfo->thoi_luong_phut;
            }
            
            $giaVe = $comboInfo->gia_co_ban ?? $comboInfo->gia_ban ?? 0;
            $tongTienComboBanDau += ($giaVe * $c['so_luong']);
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
                'nguoi_lon' => $nguoiLonInput, // [SỬA 3]: CẬP NHẬT TRƯỜNG NGƯỜI LỚN
                'tre_em' => $treEmInput,     // [SỬA 3]: CẬP NHẬT TRƯỜNG TRẺ EM
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
                'nguoi_lon' => $nguoiLonInput, // [SỬA 4]: GÁN GIÁ TRỊ TẠO MỚI
                'tre_em' => $treEmInput,     // [SỬA 4]: GÁN GIÁ TRỊ TẠO MỚI
                'ban_id' => $banId,
                'gio_den' => $nowString,
                'thoi_luong_phut' => $maxThoiLuong,
                'trang_thai' => 'khach_da_den',
                'created_at' => $nowString,
                'updated_at' => $nowString,
            ]);
        }

        // ... (Phần còn lại của hàm giữ nguyên) ...
        $ban->update(['trang_thai' => 'dang_phuc_vu']);

        // 4. LƯU CHI TIẾT COMBO
        ChiTietDatBan::where('dat_ban_id', $datBan->id)->delete();

        foreach ($inputCombos as $item) {
            ChiTietDatBan::create([
                'dat_ban_id' => $datBan->id,
                'combo_id' => $item['id'],
                'so_luong' => $item['so_luong'],
            ]);
        }

        // 5. TẠO ORDER VÀ ĐẨY MÓN TỰ ĐỘNG
        $orderMon = OrderMon::firstOrCreate(
            ['dat_ban_id' => $datBan->id, 'trang_thai' => 'dang_xu_li'],
            [
                'ban_id' => $datBan->ban_id, 
                'tong_mon' => 0, 
                'tong_tien' => $tongTienComboBanDau, 
                'created_at' => $nowString,
                'updated_at' => $nowString
            ]
        );
        
        // Nếu order đã tồn tại (quét lại), cập nhật lại giá tiền cho đúng thực tế
        if (!$orderMon->wasRecentlyCreated) {
            $orderMon->tong_tien = $tongTienComboBanDau; 
            $orderMon->save();
        }

        // Kiểm tra xem đã lên món lần nào chưa
        $hasItems = ChiTietOrder::where('order_id', $orderMon->id)
            ->where('loai_mon', 'combo')
            ->exists();

        $soLuongMonMoi = 0; // Đếm số món được đẩy xuống bếp

        if (!$hasItems) {
            $itemsToInsert = [];

            foreach ($inputCombos as $c) {
                $monTrongCombo = MonTrongCombo::where('combo_id', $c['id'])->get();

                foreach ($monTrongCombo as $mon) {
                    // Kiểm tra trùng
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
                            'so_luong' => 1, // Món mở màn
                            'loai_mon' => 'combo',
                            'trang_thai' => 'cho_bep',
                            'ghi_chu' => null,
                            'created_at' => $nowString,
                            'updated_at' => $nowString,
                        ];
                    }
                }
            }
            
            if (!empty($itemsToInsert)) {
                ChiTietOrder::insert($itemsToInsert);
                $soLuongMonMoi = count($itemsToInsert);
            }
        }

        // [MỚI 4] Cập nhật số lượng món vào OrderMon
        if ($soLuongMonMoi > 0) {
            $orderMon->tong_mon += $soLuongMonMoi;
            $orderMon->save();
        }

        DB::commit();
        return redirect()->route('oderqr.menu', ['qrKey' => $ban->ma_qr]);
    } catch (\Exception $e) {
        DB::rollBack();
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
            ->with(['banAn', 'chiTietDatBan.combo'])
            ->first();

        if (!$datBan) return response()->json(['message' => 'Bàn này chưa sẵn sàng phục vụ.'], 404);

        // --- [MỚI] TÍNH TIỀN RIÊNG BIỆT ---
        $tienCombo = 0;
        $tienGoiThem = 0;

        // 1. Tính tiền Combo
        foreach ($datBan->chiTietDatBan as $ct) {
            // [SỬA LẠI TÊN CỘT Ở ĐÂY]
            // Kiểm tra kỹ trong database bảng combo_buffet xem cột giá tên là gì
            // Khả năng cao là 'gia_co_ban' hoặc 'gia_tien'
            $giaCombo = $ct->combo->gia_co_ban ?? 0; 
            
            $tienCombo += $giaCombo * $ct->so_luong;
        }

        // 2. Tính tiền Món gọi thêm
        $order = OrderMon::where('dat_ban_id', $datBan->id)->first();
        if ($order) {
            $monGoiThem = ChiTietOrder::where('order_id', $order->id)
                ->where('loai_mon', 'goi_them')
                ->where('trang_thai', '!=', 'huy_mon') // Không tính món hủy
                ->with('monAn') // Load món để lấy giá
                ->get();

            foreach ($monGoiThem as $item) {
                $giaMon = $item->monAn->gia ?? 0;
                $tienGoiThem += $giaMon * $item->so_luong;
            }
        }
        // ----------------------------------

        // Lấy danh sách ID món ăn từ TẤT CẢ combo đã chọn
        $ownedComboIds = $datBan->chiTietDatBan->pluck('combo_id')->toArray();
        $monTrongComboIds = [];

        if (!empty($ownedComboIds)) {
            $monTrongComboIds = MonTrongCombo::whereIn('combo_id', $ownedComboIds)
                ->pluck('mon_an_id')
                ->unique()
                ->toArray();
        }

        // Truy vấn menu (Đã xóa đoạn lặp thừa)
        $menu = DanhMuc::where('hien_thi', 1)
            ->with(['monAn' => function ($query) {
                $query->where('trang_thai', 'con')
                    ->with('thuVienAnh'); // Đã thêm lấy ảnh
            }])
            ->get();

        $menu->each(function ($danhMuc) use ($monTrongComboIds) {
            if ($danhMuc->monAn) {
                $danhMuc->monAn->each(function ($monAn) use ($monTrongComboIds) {
                    $monAn->is_in_combo = in_array($monAn->id, $monTrongComboIds);
                });
            }
        });

        // Thông tin các combo đã chọn để hiển thị FE
        $selectedCombosInfo = $datBan->chiTietDatBan->map(function ($item) {
            return [
                'ten' => $item->combo->ten_combo ?? 'Combo cũ',
                'sl' => $item->so_luong
            ];
        });

        return response()->json([
            'dat_ban_info' => $datBan,
            'selected_combos' => $selectedCombosInfo,
            'menu' => $menu,
            // [TRẢ VỀ 2 BIẾN MỚI]
            'tien_combo' => $tienCombo,
            'tien_goi_them' => $tienGoiThem
        ]);
    }

    // Xử lý gửi gọi món
public function submitOrder(Request $request)
    {
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

        // Tìm hoặc tạo OrderMon
        $orderMon = OrderMon::firstOrCreate(
            ['dat_ban_id' => $datBanId, 'trang_thai' => 'dang_xu_li'],
            ['ban_id' => $datBan->ban_id, 'tong_mon' => 0, 'tong_tien' => 0, 'created_at' => $nowString, 'updated_at' => $nowString]
        );

        // [MỚI] Biến tạm để tính tổng tiền và số lượng đợt gọi này
        $themTien = 0;
        $themSl = 0;

        foreach ($items as $item) {
            $monAn = MonAn::find($item['mon_an_id']);
            
            // Nếu món hết hoặc không tồn tại thì bỏ qua
            if (!$monAn || $monAn->trang_thai !== 'con') continue;

            ChiTietOrder::create([
                'order_id' => $orderMon->id,
                'mon_an_id' => $item['mon_an_id'],
                'so_luong' => $item['so_luong'],
                'loai_mon' => $item['loai_mon'],
                'trang_thai' => 'cho_bep',
                'ghi_chu' => $item['ghi_chu'] ?? null,
                'created_at' => $nowString,
                'updated_at' => $nowString
            ]);

            // [MỚI] Cộng dồn số lượng
            $themSl += $item['so_luong'];

            // [MỚI] Chỉ cộng tiền nếu là món gọi thêm (Món trong combo giá = 0 ở bước này)
            if ($item['loai_mon'] === 'goi_them') {
                $themTien += ($monAn->gia * $item['so_luong']);
            }
        }

        // [MỚI] Cập nhật lại tổng tiền và tổng món vào bảng OrderMon (cho Admin/Thu ngân xem)
        $orderMon->tong_mon += $themSl;
        $orderMon->tong_tien += $themTien;
        $orderMon->updated_at = $nowString;
        $orderMon->save();

        return response()->json([
            'message' => 'Gửi order thành công!',
            'order_id' => $orderMon->id
        ], 201);
    }

    // [CẬP NHẬT] Lấy trạng thái món (Thêm thời gian chế biến cho Timer)
    public function getOrderStatus(Request $request, $datBanId)
    {
        $chiTietMonAn = ChiTietOrder::whereHas('orderMon', function ($query) use ($datBanId) {
            $query->where('dat_ban_id', $datBanId);
        })
            // [QUAN TRỌNG] Thêm thoi_gian_che_bien và gia vào đây
            ->with('monAn:id,ten_mon,hinh_anh,thoi_gian_che_bien,gia')
            ->select('id', 'mon_an_id', 'so_luong', 'trang_thai', 'loai_mon', 'created_at', 'ghi_chu')
            ->orderBy('created_at', 'desc')
            ->get();

        if ($chiTietMonAn->isEmpty()) {
            return response()->json(['message' => 'Chưa gọi món nào.'], 404);
        }

        return response()->json(['items' => $chiTietMonAn]);
    }

    // [MỚI] Hàm xử lý Hủy món
    public function cancelItem(Request $request)
    {
        try {
            $id = $request->input('id');
            $item = ChiTietOrder::find($id);

            if (!$item) {
                return response()->json(['message' => 'Không tìm thấy món.'], 404);
            }

            // Chỉ cho phép hủy nếu trạng thái là 'cho_bep'
            if ($item->trang_thai === 'cho_bep') {
                $item->trang_thai = 'huy_mon'; // Hoặc $item->delete() nếu muốn xóa hẳn
                $item->save();
                return response()->json(['message' => 'Đã hủy món thành công.', 'status' => 'success']);
            } else {
                return response()->json(['message' => 'Món đang nấu hoặc đã lên, không thể hủy!'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Lỗi hệ thống: ' . $e->getMessage()], 500);
        }
    }

    public function showQrListPage(Request $request)
    {
        $banAns = BanAn::all();
        $selectedBanId = $request->query('ban') ?? ($banAns->first()?->id ?? null);
        $selectedBan = $banAns->find($selectedBanId);

        return view('shop.oderqr.list', compact('banAns', 'selectedBan'));
    }
}
