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
                    <option value="{{ $db->id }}"
                        data-ban="{{ $db->banAn->so_ban ?? 'Chưa gán' }}"
                        data-banid="{{ $db->banAn->id ?? '' }}"
                        data-sokhach="{{ $db->so_khach }}"
                        data-giacombo="{{ $db->comboBuffet?->gia_co_ban ?? 0 }}"
                        data-somon="{{ $db->comboBuffet?->monTrongCombo?->sum('gioi_han_so_luong') ?? 0 }}">
                        {{ $db->ma_dat_ban }} - {{ $db->ten_khach }}
                        {{ $db->comboBuffet ? $db->comboBuffet->ten_combo : '❌ chưa có combo' }}
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
        const soKhach = parseInt(selected.getAttribute('data-sokhach') || 0);
        const giaCombo = parseFloat(selected.getAttribute('data-giacombo') || 0);
        const soMon = parseInt(selected.getAttribute('data-somon') || 0);

        document.getElementById('ban_display').value = banName;
        document.getElementById('ban_id').value = banId;

        // Tính tổng món và tổng tiền
        if (soKhach > 0 && giaCombo > 0) {
            const tongMon = soMon || soKhach;
            const tongTien = soKhach * giaCombo;

            document.getElementById('tong_mon').value = tongMon;
            document.getElementById('tong_tien').value = tongTien;
            document.getElementById('tong_tien_display').value = tongTien.toLocaleString('vi-VN') + ' đ';
        } else {
            document.getElementById('tong_mon').value = '';
            document.getElementById('tong_tien').value = '';
            document.getElementById('tong_tien_display').value = '';
        }
    });
</script>
@endsection