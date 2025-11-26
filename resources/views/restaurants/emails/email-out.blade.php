<p>Xin chào {{ $datBan->ten_khach }},</p>
<p>Cảm ơn bạn đã đặt bàn tại nhà hàng chúng tôi.</p>
<p><strong>Mã đặt bàn:</strong> {{ $datBan->ma_dat_ban }}</p>
<p><strong>Số khách:</strong> {{ $datBan->so_khach }}</p>
<p><strong>Bàn:</strong> {{ $ban->ten_ban ?? 'Chưa chọn' }}</p>
<p><strong>Combo:</strong> {{ $combo->ten_combo ?? 'Chưa chọn' }}</p>
<p><strong>Tiền cọc:</strong> {{ number_format($datBan->tien_coc) }} VND</p>
<p>Ngày đến: {{ $datBan->gio_den->format('H:i d/m/Y') }}</p>