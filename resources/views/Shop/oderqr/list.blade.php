@extends('layouts.Shop.layout-oderqr')

@section('title', 'QR bàn ăn')

@section('content')

<main class="app-content">

    <div class="row justify-content-center position-relative">

        <div class="col-md-6 text-center">

            <div class="card p-4 position-relative">

                {{-- Select góc phải trên --}}
                <form method="GET" action="{{ route('oderqr.list') }}" class="position-absolute top-0 end-0 m-3">
                    <select name="ban" class="form-select form-select-sm" onchange="this.form.submit()">
                        @foreach($banAns as $ban)
                            <option value="{{ $ban->id }}" {{ $selectedBan && $ban->id == $selectedBan->id ? 'selected' : '' }}>
                                Bàn {{ $ban->so_ban }}
                            </option>
                        @endforeach
                    </select>
                </form>

                @if($selectedBan)
                    @php
                        $finalUrl = url('/oderqr/menu/' . $selectedBan->ma_qr);
                    @endphp

                    <h4 class="text-primary mt-4 mb-3">Bàn {{ $selectedBan->so_ban }}</h4>

                    {{-- QR code lớn --}}
                    <div class="qr-image mb-3">
                        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(350)->generate($finalUrl) !!}
                    </div>

                    <p class="text-muted mb-1">ID Bàn: {{ $selectedBan->id }}</p>

                    <a href="{{ $finalUrl }}" target="_blank" class="small text-info" style="word-break: break-all;">
                        {{ $finalUrl }}
                    </a>
                @endif

            </div>

        </div>

    </div>

</main>

@endsection
