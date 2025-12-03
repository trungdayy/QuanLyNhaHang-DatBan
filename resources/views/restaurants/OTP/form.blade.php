@extends('layouts.page')

@section('title', 'Xác thực OTP')

@section('content')
<main class="container py-5">

    <div class="row justify-content-center">
        <div class="col-md-6">

            {{-- Thông báo success --}}
            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            {{-- Thông báo lỗi chung --}}
            @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Xác thực OTP</h5>
                </div>

                <div class="card-body">

                    <p>Chúng tôi đã gửi mã OTP 6 chữ số tới email: <strong>{{ $email }}</strong></p>
                    <p>Vui lòng nhập mã OTP để tiếp tục đặt bàn.</p>

                    {{-- Form xác thực OTP --}}
                    <form action="{{ route('otp.verify') }}" method="POST">
                        @csrf

                        <input type="hidden" name="email" value="{{ $email }}">

                        <div class="mb-3">
                            <label for="otp" class="form-label">Mã OTP</label>
                            <input type="text" name="otp" id="otp"
                                class="form-control @error('otp') is-invalid @enderror" placeholder="Nhập 6 chữ số"
                                maxlength="6" required>

                            @error('otp')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <button type="submit" class="btn btn-success">Xác thực OTP</button>

                            {{-- Nút gửi lại OTP --}}
                            <button type="button" class="btn btn-outline-primary btn-sm"
                                onclick="event.preventDefault(); document.getElementById('resend-otp-form').submit();">
                                Gửi lại OTP
                            </button>
                        </div>
                    </form>

                    {{-- Form ẩn để gửi lại OTP --}}
                    <form id="resend-otp-form" action="{{ route('otp.send') }}" method="POST" style="display:none;">
                        @csrf
                        <input type="hidden" name="email" value="{{ $email }}">
                        <input type="hidden" name="booking_id" value="{{ session('booking_id') }}">
                    </form>

                </div>
            </div>

        </div>
    </div>

</main>
@endsection