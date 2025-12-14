@extends('layouts.admins.layout-admin')

@section('title', 'Tạo Order món')

@section('content')
<div class="app-content">
    <div class="app-title">
        <h4>Tạo Order món mới</h4>
    </div>

    <form action="{{ route('admin.order-mon.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-6">
<label for="dat_ban_id">Đặt bàn:</label>
<select name="dat_ban_id" id="dat_ban_id" class="form-control" required>
    <option value="">-- Chọn đặt bàn --</option>
    @foreach ($datBans as $db)
    @php
        $tongTienCombo = $db->combos->sum(function($combo) {
            return ($combo->pivot->so_luong ?? 1) * ($combo->gia_co_ban ?? 0);
        });

        $tongMonCombo = $db->combos->sum(function($combo) {
            return ($combo->monTrongCombo->sum('gioi_han_so_luong') ?? 0) * ($combo->pivot->so_luong ?? 1);
        });
    @endphp
    <option value="{{ $db->id }}"
        data-ban="{{ $db->banAn->so_ban ?? 'Chưa gán' }}"
        data-banid="{{ $db->banAn->id ?? '' }}"
        data-sokhach="{{ $db->so_khach }}"
        data-tongmon="{{ $tongMonCombo }}"
        data-tongtiencombo="{{ $tongTienCombo }}">
        {{ $db->ma_dat_ban }} - {{ $db->ten_khach }}
    </option>
@endforeach
</select>

<label for="ban_display" class="mt-3">Bàn:</label>
<input type="text" id="ban_display" class="form-control" readonly placeholder="Tự động hiển thị sau khi chọn Đặt bàn">
<input type="hidden" name="ban_id" id="ban_id">

<label for="tong_mon" class="mt-3">Tổng món:</label>
<input type="number" name="tong_mon" id="tong_mon" class="form-control" readonly>

<label for="tong_tien_display" class="mt-3">Tổng tiền:</label>
<input type="text" id="tong_tien_display" class="form-control" readonly>
<input type="hidden" name="tong_tien" id="tong_tien">
            </div>

            <div class="col-md-6">
                <label for="trang_thai">Trạng thái:</label>
                <select name="trang_thai" id="trang_thai" class="form-control">
                    <option value="dang_xu_li">Đang xử lí</option>
                </select>
            </div>
        </div>

        <div class="mt-4">
            <button class="btn btn-success"><i class="fas fa-save"></i> Lưu</button>
            <a href="{{ route('admin.order-mon.index') }}" class="btn btn-secondary">Hủy</a>
        </div>
    </form>
</div>

{{-- JS tự động điền bàn + tính tổng món, tổng tiền --}}
<script>
   document.getElementById('dat_ban_id').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    const banName = selected.getAttribute('data-ban') || '';
    const banId = selected.getAttribute('data-banid') || '';
    const tongMonCombo = parseInt(selected.getAttribute('data-tongmon') || 0);
    const tongTienCombo = parseFloat(selected.getAttribute('data-tongtiencombo') || 0);

    document.getElementById('ban_display').value = banName;
    document.getElementById('ban_id').value = banId;

    // Lấy tiền món gọi thêm nếu có (giả sử input + JS dynamic)
    let tongTienMonThem = 0;
    // example: tính tiền món gọi thêm nếu bạn có array món gọi thêm
    if (window.monGoiThem) {
        tongTienMonThem = window.monGoiThem.reduce((acc, item) => {
            return acc + (item.so_luong * item.gia);
        }, 0);
    }

    const tongMon = tongMonCombo + (window.monGoiThem ? window.monGoiThem.length : 0);
    const tongTien = tongTienCombo + tongTienMonThem;

    document.getElementById('tong_mon').value = tongMon;
    document.getElementById('tong_tien').value = tongTien;
    document.getElementById('tong_tien_display').value = tongTien.toLocaleString('vi-VN') + ' đ';
});
    function capNhatThongTinDatBan() {
    const select = document.getElementById('dat_ban_id');
    const selected = select.options[select.selectedIndex];

    if (!selected || !selected.value) return;

    const banName = selected.getAttribute('data-ban') || '';
    const banId = selected.getAttribute('data-banid') || '';
    const tongMonCombo = parseInt(selected.getAttribute('data-tongmon') || 0);
    const tongTienCombo = parseFloat(selected.getAttribute('data-tongtiencombo') || 0);

    document.getElementById('ban_display').value = banName;
    document.getElementById('ban_id').value = banId;

    // Tổng tiền món gọi thêm (nếu có)
    let tongTienMonThem = 0;
    let tongMonThem = 0;
    if (window.monGoiThem) {
        window.monGoiThem.forEach(item => {
            tongTienMonThem += item.so_luong * item.gia;
            tongMonThem += item.so_luong;
        });
    }

    const tongMon = tongMonCombo + tongMonThem;
    const tongTien = tongTienCombo + tongTienMonThem;

    document.getElementById('tong_mon').value = tongMon;
    document.getElementById('tong_tien').value = tongTien;
    document.getElementById('tong_tien_display').value = tongTien.toLocaleString('vi-VN') + ' đ';
}

// Khi thay đổi select
document.getElementById('dat_ban_id').addEventListener('change', capNhatThongTinDatBan);

// Khi trang load
window.addEventListener('DOMContentLoaded', capNhatThongTinDatBan);
</script>
@endsection