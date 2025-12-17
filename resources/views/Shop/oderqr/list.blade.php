@extends('layouts.Shop.layout-oderqr')

@section('title', 'QR bàn ăn')

@section('content')

<style>
    /* Style cho ô nhập PIN */
    .pin-display {
        letter-spacing: 8px;
        font-size: 30px;
        text-align: center;
        font-weight: bold;
        border: 2px solid #ddd;
        border-radius: 8px;
        padding: 10px;
        margin-bottom: 20px;
        background: #f8f9fa;
    }
</style>

<main class="app-content">

    <div class="row justify-content-center position-relative">
        <div class="col-md-6 text-center">
            <div class="card p-4 position-relative">

                {{-- FORM CHỌN BÀN --}}
                <form id="tableForm" method="GET" action="{{ route('oderqr.list') }}" class="position-absolute top-0 end-0 m-3" style="z-index: 10;">
                    <select id="banSelect" name="ban" class="form-select form-select-sm"
                        style="min-width: 100px; cursor: pointer;"
                        onfocus="this.previousValue = this.value"
                        onchange="verifyPin(this)">
                        @foreach($banAns as $ban)
                        <option value="{{ $ban->id }}" {{ $selectedBan && $ban->id == $selectedBan->id ? 'selected' : '' }}>
                            {{ $ban->so_ban }}
                        </option>
                        @endforeach
                    </select>
                </form>

                @if($selectedBan)
                @php
                $finalUrl = url('/oderqr/menu/' . $selectedBan->ma_qr);
                @endphp

                <h4 class="text-primary mt-4 mb-3"> {{ $selectedBan->so_ban }}</h4>

                {{-- === BẮT ĐẦU VÙNG CẬP NHẬT TỰ ĐỘNG (Thêm ID tại đây) === --}}
                <div id="qr-dynamic-area">
                    {{-- QR code --}}
                    <div class="qr-image mb-3">
                        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(350)->generate($finalUrl) !!}
                    </div>

                    <a href="{{ $finalUrl }}" target="_blank" class="small text-info text-break">
                        {{ $finalUrl }}
                    </a>
                </div>
                {{-- === KẾT THÚC VÙNG CẬP NHẬT === --}}
                
                @endif

            </div>
        </div>
    </div>

</main>

{{-- Các Modal giữ nguyên --}}
<div class="modal fade" id="confirmPinModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h6 class="modal-title">NHẬP MÃ PIN ĐỂ CHUYỂN BÀN</h6>
                <button type="button" class="btn-close btn-close-white" onclick="cancelChangeTable()"></button>
            </div>
            <div class="modal-body text-center">
                <p class="small text-muted mb-2">Quên mật khẩu thì hỏi dev</p>
                <input type="password" id="inputPinConfirm" class="form-control pin-display text-center" maxlength="4" inputmode="numeric" placeholder="****">
                <div id="errorMsg" class="text-danger small fw-bold" style="display:none;">Mã PIN không đúng!</div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary btn-sm" onclick="cancelChangeTable()">Hủy</button>
                <button type="button" class="btn btn-primary btn-sm px-4" onclick="submitTableChange()">Xác nhận</button>
                <button type="button" class="btn btn-sm btn-light text-muted" data-bs-toggle="modal" data-bs-target="#changePinModal">
                    <i class="fa fa-cog"></i> Đổi mã pin
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="changePinModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Đổi mã PIN bảo mật</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold">PIN hiện tại</label>
                    <input type="password" id="currentPinInput" class="form-control text-center" maxlength="4">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">PIN mới (4 số)</label>
                    <input type="password" id="newPinInput" class="form-control text-center" maxlength="4">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary w-100" onclick="saveNewPin()">Lưu thay đổi</button>
            </div>
        </div>
    </div>
</div>

<script>
    // --- CẤU HÌNH & BIẾN TOÀN CỤC ---
    const DEFAULT_PIN = '1234';
    let confirmModal;
    let changeModal;

    document.addEventListener('DOMContentLoaded', function() {
        confirmModal = new bootstrap.Modal(document.getElementById('confirmPinModal'));
        changeModal = new bootstrap.Modal(document.getElementById('changePinModal'));

        document.getElementById('inputPinConfirm').addEventListener('keyup', function(e) {
            if (e.key === 'Enter') submitTableChange();
        });

        // --- CODE MỚI: AUTO RELOAD QR SAU 5 GIÂY ---
        setInterval(refreshQR, 5000);
    });

    // Hàm cập nhật QR không cần load lại cả trang
    function refreshQR() {
        // 1. Nếu đang mở Modal nhập PIN thì KHÔNG reload (để tránh mất focus khi đang nhập)
        if (document.querySelector('.modal.show')) {
            console.log('Đang mở modal, tạm dừng update QR');
            return;
        }

        // 2. Fetch lại chính URL hiện tại (bao gồm cả query param ?ban=...)
        fetch(window.location.href)
            .then(response => response.text())
            .then(html => {
                // 3. Parse HTML string thành DOM
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                // 4. Tìm vùng chứa QR mới
                const newContent = doc.querySelector('#qr-dynamic-area');
                const oldContent = document.querySelector('#qr-dynamic-area');

                if (newContent && oldContent) {
                    // 5. Thay thế nội dung cũ bằng nội dung mới
                    oldContent.innerHTML = newContent.innerHTML;
                    // console.log('Đã cập nhật QR mới lúc: ' + new Date().toLocaleTimeString());
                }
            })
            .catch(err => console.error('Lỗi cập nhật QR:', err));
    }

    // --- CÁC HÀM CŨ GIỮ NGUYÊN ---
    function getSystemPin() {
        return localStorage.getItem('app_table_pin') || DEFAULT_PIN;
    }

    function verifyPin(selectElement) {
        confirmModal.show();
        document.getElementById('inputPinConfirm').value = '';
        document.getElementById('errorMsg').style.display = 'none';
        setTimeout(() => document.getElementById('inputPinConfirm').focus(), 500);
    }

    function submitTableChange() {
        const userPin = document.getElementById('inputPinConfirm').value;
        const correctPin = getSystemPin();

        if (userPin === correctPin) {
            document.getElementById('tableForm').submit();
        } else {
            document.getElementById('errorMsg').style.display = 'block';
            document.getElementById('inputPinConfirm').value = '';
            document.getElementById('inputPinConfirm').focus();
        }
    }

    function cancelChangeTable() {
        confirmModal.hide();
        const select = document.getElementById('banSelect');
        if (select.previousValue) {
            select.value = select.previousValue;
        }
    }

    function saveNewPin() {
        const oldPin = document.getElementById('currentPinInput').value;
        const newPin = document.getElementById('newPinInput').value;
        const sysPin = getSystemPin();

        if (oldPin !== sysPin) {
            alert('Mã PIN hiện tại không đúng!');
            return;
        }

        if (newPin.length !== 4 || isNaN(newPin)) {
            alert('Mã PIN mới phải gồm 4 chữ số!');
            return;
        }

        localStorage.setItem('app_table_pin', newPin);
        alert('Đổi mã PIN thành công! Hãy ghi nhớ mã mới.');

        document.getElementById('currentPinInput').value = '';
        document.getElementById('newPinInput').value = '';
        changeModal.hide();
    }
</script>

@endsection