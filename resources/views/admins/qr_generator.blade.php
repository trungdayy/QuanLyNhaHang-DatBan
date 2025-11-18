@extends('layouts.admins.layout-admin')

@section('title', 'Danh sách Order món')

@section('content')

<main class="app-content">

    <div class="app-title">
        <ul class="app-breadcrumb breadcrumb side">
            <li class="breadcrumb-item active"><a href="#"><b>Danh sách QR bàn ăn</b></a></li>
        </ul>
        <div id="clock"></div>
    </div>

    <div class="row">
        <div class="col-md-12">

            <div class="tile">
                <div class="tile-body">

                    <div class="container-fluid mt-3">
                        <div class="row">

                            @forelse ($banAns as $banAn)
                                @php
                                    $finalUrl = url('/oderqr/menu/' . $banAn->ma_qr);
                                @endphp

                                <div class="col-md-2 mb-4">
                                    <div class="card shadow-sm p-2 text-center">

                                        <h5 class="text-primary mb-2">
                                            Bàn {{ $banAn->so_ban }}
                                        </h5>

                                        <div class="qr-image mb-2">
                                            {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(220)->generate($finalUrl) !!}
                                        </div>

                                        <p class="text-muted mb-1">ID Bàn: {{ $banAn->id }}</p>

                                        <a href="{{ $finalUrl }}" 
                                           target="_blank" 
                                           class="small text-info" 
                                           style="word-break: break-all;">
                                            {{ $finalUrl }}
                                        </a>

                                    </div>
                                </div>

                            @empty

                                <div class="col-12">
                                    <p class="alert alert-info">
                                        Chưa có bàn ăn nào được tạo trong hệ thống.
                                    </p>
                                </div>

                            @endforelse

                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

</main>

@endsection
