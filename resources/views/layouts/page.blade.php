{{-- Kế thừa từ layout chính (chứa Head, Footer, Script) --}}
@extends('layouts.master')

{{-- Định nghĩa phần Hero riêng cho trang con --}}
@section('hero')
    <div class="container-xxl py-5 bg-dark hero-header mb-5">
        <div class="container text-center my-5 pt-5 pb-4">
            {{-- Hiển thị tiêu đề được truyền từ trang con --}}
            <h1 class="display-3 text-white mb-3 animated slideInDown">
                 @yield('title') 
            </h1>
            
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center text-uppercase">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="#">@yield('title')</a></li>

                </ol>
            </nav>
        </div>
    </div>
@endsection

{{-- Nội dung chính sẽ được các trang con (About, Menu) đẩy vào đây --}}
{{-- Không cần làm gì thêm vì nó sẽ tự "xuyên qua" layout này xuống master --}}