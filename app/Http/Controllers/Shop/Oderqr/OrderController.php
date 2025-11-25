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

use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

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

        return view('shop.oderqr.select-combo', [
            'maQr' => $ban->ma_qr, // dùng trong form
            'tenBan' => $ban->so_ban,
            'combos' => $combos,
            'qrKey' => $qrKey,
            'datBan' => $datBan,   // truyền xuống view
        ]);
    }

public function startOrder(Request $request)
{
    // 1. Validate dữ liệu - THÊM dat_ban_id
    $validator = Validator::make($request->all(), [
        'ma_qr' => 'required|exists:ban_an,ma_qr',
        'dat_ban_id' => 'nullable|exists:dat_ban,id', // 🔥 THÊM DÒNG NÀY
        'combo_id' => 'nullable|exists:combo_buffet,id',
        'so_khach' => 'required|integer|min:1',
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
    $comboId = $request->input('combo_id');
    $soKhach = $request->input('so_khach');
    
    $tenKhachInput = $request->input('ten_khach') ?: 'Khách Vãng Lai';
    $sdtKhachInput = $request->input('sdt_khach') ?: '0';

    // --- KHẮC PHỤC LỖI GIỜ: Lấy giờ VN và chuyển thành chuỗi ---
    $nowObj = Carbon::now('Asia/Ho_Chi_Minh');
    $nowString = $nowObj->toDateTimeString(); // Chuỗi giờ VN chính xác

    // 3. 🔥 TÌM ĐƠN ĐẶT BÀN - ƯU TIÊN THEO dat_ban_id
    $datBanId = $request->input('dat_ban_id');
    $datBan = null;
    
    if ($datBanId) {
        // Nếu có dat_ban_id từ form, tìm đúng đơn đó
        $datBan = DatBan::where('id', $datBanId)
            ->where('ban_id', $banId)
            ->first();
            
        // Kiểm tra nếu đã có combo rồi
        if ($datBan && !is_null($datBan->combo_id)) {
            return redirect()->route('oderqr.menu', ['qrKey' => $ban->ma_qr])
                ->with('info', 'Bàn này đã chọn combo. Vui lòng tiếp tục gọi món.');
        }
    }
    
    // Nếu không có dat_ban_id hoặc không tìm thấy, tìm theo bàn + ngày
    if (!$datBan) {
        $datBan = DatBan::where('ban_id', $banId)
            ->whereIn('trang_thai', ['khach_da_den', 'da_xac_nhan'])
            ->whereNull('combo_id') // Chỉ tìm đơn chưa có combo
            ->whereDate('gio_den', $nowObj->toDateString())
            ->orderBy('gio_den', 'desc') // Lấy đơn mới nhất
            ->first();
    }

    // 💡 BỔ SUNG: Nếu không tìm thấy đơn chưa có combo, kiểm tra xem đã có đơn có combo đang phục vụ chưa
    if (!$datBan) {
        $existingOrderWithCombo = DatBan::where('ban_id', $banId)
            ->whereIn('trang_thai', ['khach_da_den', 'da_xac_nhan'])
            ->whereNotNull('combo_id')
            ->whereDate('gio_den', $nowObj->toDateString()) 
            ->first();

        if ($existingOrderWithCombo) {
            // Đã có đơn đang phục vụ VÀ đã chọn combo, không tạo đơn mới, chuyển hướng
            return redirect()->route('oderqr.menu', ['qrKey' => $ban->ma_qr])
                ->with('info', 'Bàn này đã được phục vụ và chọn combo. Vui lòng gọi món.');
        }
    }
    
    if ($datBan) {
        // --- TRƯỜNG HỢP A: 🔥 CẬP NHẬT ĐƠN ĐÃ CÓ ---
        $updateData = [
            'combo_id' => $comboId,
            'so_khach' => $soKhach,
            'trang_thai' => 'khach_da_den',
            'updated_at' => $nowString,
        ];

        if ($request->filled('ten_khach')) {
            $updateData['ten_khach'] = $tenKhachInput;
        }
        if ($request->filled('sdt_khach')) {
            $updateData['sdt_khach'] = $sdtKhachInput;
        }

        // 🔥 CẬP NHẬT THỜI LƯỢNG NẾU CÓ COMBO
        if ($comboId) {
            $combo = ComboBuffet::find($comboId);
            if ($combo) {
                $updateData['thoi_luong_phut'] = $combo->thoi_luong_phut;
            }
        }

        $datBan->update($updateData);
        $ban->update(['trang_thai' => 'dang_phuc_vu']);



    } else {
        // --- TRƯỜNG HỢP B: TẠO MỚI ---
        $combo = $comboId ? ComboBuffet::find($comboId) : null;
        $thoiLuongPhut = $combo ? $combo->thoi_luong_phut : 120;

        $datBan = DatBan::create([
            'ma_dat_ban' => 'QR' . $nowObj->format('Ymd') . '-' . strtoupper(bin2hex(random_bytes(2))),
            'ten_khach' => $tenKhachInput, 
            'sdt_khach' => $sdtKhachInput,
            'so_khach' => $soKhach,
            'ban_id' => $banId,
            'combo_id' => $comboId,
            'gio_den' => $nowString, // Lưu chuỗi giờ VN
            'thoi_luong_phut' => $thoiLuongPhut,
            'trang_thai' => 'khach_da_den',
            'created_at' => $nowString,
            'updated_at' => $nowString,
        ]);
        
        $ban->update(['trang_thai' => 'dang_phuc_vu']);


    }

    // 4. Thêm món Combo
    if ($comboId) {
        $comboItems = MonTrongCombo::where('combo_id', $comboId)->get();
        
        // Tạo hoặc tìm OrderMon và ép buộc giờ tạo là giờ VN
        $orderMon = OrderMon::firstOrCreate(
            ['dat_ban_id' => $datBan->id, 'trang_thai' => 'dang_xu_li'],
            [
                'ban_id' => $datBan->ban_id, 
                'tong_mon' => 0, 
                'tong_tien' => 0,
                'created_at' => $nowString, 
                'updated_at' => $nowString 
            ]
        );

        // 💡 SỬA LỖI: Kiểm tra $orderMon trước khi truy cập thuộc tính 'id'
        if (is_null($orderMon)) {
            // Rất có thể do lỗi $fillable hoặc Khóa ngoại, nên redirect và thông báo lỗi
            return back()->with('error', 'Lỗi hệ thống: Không thể tạo phiếu gọi món (OrderMon).')->withInput();
        }

        // 🔥 KIỂM TRA: Chỉ thêm món combo nếu chưa có
        $hasComboItems = ChiTietOrder::where('order_id', $orderMon->id)
            ->where('loai_mon', 'combo')
            ->exists();
        
        if (!$hasComboItems) {
            $itemsToInsert = [];
            foreach ($comboItems as $comboItem) {
                $monAn = MonAn::find($comboItem->mon_an_id);
                if ($monAn && $monAn->trang_thai === 'con') {
                    $itemsToInsert[] = [
                        'order_id' => $orderMon->id,
                        'mon_an_id' => $comboItem->mon_an_id,
                        'so_luong' => $datBan->so_khach,
                        'loai_mon' => 'combo',
                        'trang_thai' => 'cho_bep',
                        'ghi_chu' => null,
                        'created_at' => $nowString, 
                        'updated_at' => $nowString,
                    ];
                }
            }
            if (!empty($itemsToInsert)) ChiTietOrder::insert($itemsToInsert);
        }
    }

    // 5. Redirect
    return redirect()->route('oderqr.menu', ['qrKey' => $ban->ma_qr]);
}

    // Hiển thị trang gọi món
    public function showGoiMonPage($qrKey)
    {
        $ban = $this->findBanAnByQrKey($qrKey);
        if (!$ban) abort(404, 'Mã QR không hợp lệ hoặc bàn không tồn tại.');

        $datBan = DatBan::where('ban_id', $ban->id)
            ->where('trang_thai', 'khach_da_den')
            ->first();

        if (!$datBan || is_null($datBan->combo_id)) {
            return redirect()->route('oderqr.select_combo', ['qrKey' => $qrKey]);
        }

        return view('shop.oderqr.menu', [
            'banId' => $ban->id,
            'tenBan' => $ban->so_ban,
            'qrKey' => $qrKey
        ]);
    }

    // API lấy thông tin session
    public function getSessionInfo($qrKey)
    {
        $ban = $this->findBanAnByQrKey($qrKey);
        if (!$ban) return response()->json(['message' => 'Mã QR không hợp lệ.'], 404);

        $datBan = DatBan::where('ban_id', $ban->id)
            ->where('trang_thai', 'khach_da_den')
            ->with('banAn', 'comboBuffet')
            ->first();

        if (!$datBan) return response()->json(['message' => 'Bàn này chưa sẵn sàng phục vụ.'], 404);

        // Tính thời gian còn lại
        $thoiGianConLaiPhut = null;
        if ($datBan->gio_den && $datBan->thoi_luong_phut) {
            $gioKetThuc = Carbon::parse($datBan->gio_den)->addMinutes($datBan->thoi_luong_phut);
            $thoiGianConLaiPhut = $gioKetThuc->isFuture() ? $gioKetThuc->diffInMinutes(now()) : 0;
        }

        $monTrongComboIds = $datBan->combo_id
            ? MonTrongCombo::where('combo_id', $datBan->combo_id)->pluck('mon_an_id')->toArray()
            : [];

        $menu = DanhMuc::where('hien_thi', 1)
            ->with(['monAn' => function ($query) {
                $query->where('trang_thai', 'con');
            }])
            ->get();

        $menu->each(function ($danhMuc) use ($monTrongComboIds) {
            if ($danhMuc->monAn) {
                $danhMuc->monAn->each(function ($monAn) use ($monTrongComboIds) {
                    $monAn->is_in_combo = in_array($monAn->id, $monTrongComboIds);
                });
            }
        });

        return response()->json([
            'dat_ban_info' => $datBan,
            'menu' => $menu,
        ]);
    }

    // API gửi order
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

        // Lấy giờ VN hiện tại
        $nowString = Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString();

        // Tạo OrderMon với ngày tạo là giờ VN
        $orderMon = OrderMon::firstOrCreate(
            ['dat_ban_id' => $datBanId, 'trang_thai' => 'dang_xu_li'],
            [
                'ban_id' => $datBan->ban_id, 
                'tong_mon' => 0, 
                'tong_tien' => 0,
                'created_at' => $nowString, // 💡 FIX: Ngày tạo OrderMon
                'updated_at' => $nowString  // 💡 FIX: Ngày update OrderMon
            ]
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
                'created_at' => $nowString, // 💡 FIX: Ngày tạo Chi tiết
                'updated_at' => $nowString  // 💡 FIX: Ngày update Chi tiết
            ]);
        }

        return response()->json([
            'message' => 'Gửi order thành công!',
            'order_id' => $orderMon->id
        ], 201);
    }

    // API xem trạng thái tất cả món đã gọi
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
        // Lấy tất cả bàn ăn
        $banAns = BanAn::all();
        $selectedBanId = $request->query('ban') ?? ($banAns->first()?->id ?? null);
        $selectedBan = $banAns->find($selectedBanId);

        return view('shop.oderqr.list', compact('banAns', 'selectedBan'));
    }
}