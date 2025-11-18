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


    // Xử lý POST chọn combo và bắt đầu order
    public function startOrder(Request $request)
    {
        // 1. Validate dữ liệu theo ma_qr
        $validator = Validator::make($request->all(), [
            'ma_qr' => 'required|exists:ban_an,ma_qr',
            'combo_id' => 'nullable|exists:combo_buffet,id',
            'so_khach' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // 2. Lấy bàn từ ma_qr
        $maQr = $request->input('ma_qr');
        $ban = BanAn::where('ma_qr', $maQr)->first();
        if (!$ban) return back()->withErrors(['ma_qr' => 'Mã QR không hợp lệ'])->withInput();

        $banId = $ban->id;
        $comboId = $request->input('combo_id');
        $soKhach = $request->input('so_khach');

        // 3. Tìm hoặc tạo DatBan
        $datBan = DatBan::where('ban_id', $banId)
            ->whereIn('trang_thai', ['khach_da_den'])
            ->first();

        if (!$datBan) {
            $combo = $comboId ? ComboBuffet::find($comboId) : null;
            $thoiLuongPhut = $combo ? $combo->thoi_luong_phut : 120;

            $datBan = DatBan::create([
                'ma_dat_ban' => 'QR' . now()->format('Ymd') . '-' . strtoupper(bin2hex(random_bytes(2))),
                'ten_khach' => 'Khách Vãng Lai',
                'sdt_khach' => '0',
                'so_khach' => $soKhach,
                'ban_id' => $banId,
                'combo_id' => $comboId,
                'gio_den' => now(),
                'thoi_luong_phut' => $thoiLuongPhut,
                'trang_thai' => 'khach_da_den',
            ]);
        } else {
            $datBan->update([
                'combo_id' => $comboId,
                'so_khach' => $soKhach,
            ]);
        }

        // 4. Thêm món trong combo tự động
        if ($comboId) {
            $comboItems = MonTrongCombo::where('combo_id', $comboId)->get();
            $itemsToInsert = [];

            $orderMon = OrderMon::firstOrCreate(
                ['dat_ban_id' => $datBan->id, 'trang_thai' => 'dang_xu_li'],
                ['ban_id' => $datBan->ban_id, 'tong_mon' => 0, 'tong_tien' => 0]
            );

            foreach ($comboItems as $comboItem) {
                $monAn = MonAn::find($comboItem->mon_an_id);
                if ($monAn && $monAn->trang_thai === 'con') {
                    $itemsToInsert[] = [
                        'order_id' => $orderMon->id,
                        'mon_an_id' => $comboItem->mon_an_id,
                        'so_luong' => $datBan->so_khach,
                        'loai_mon' => 'combo',
                        'trang_thai' => 'cho_bep',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if (!empty($itemsToInsert)) ChiTietOrder::insert($itemsToInsert);
        }

        // 5. Redirect đến trang Menu
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

        $orderMon = OrderMon::firstOrCreate(
            ['dat_ban_id' => $datBanId, 'trang_thai' => 'dang_xu_li'],
            ['ban_id' => $datBan->ban_id, 'tong_mon' => 0, 'tong_tien' => 0]
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

