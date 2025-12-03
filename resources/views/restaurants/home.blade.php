@extends('layouts.master')
@section('title', 'Home')

{{-- 1. HERO SECTION --}}
@section('hero')
    <div class="container-xxl py-5 bg-dark hero-header mb-5">
        <div class="container my-5 py-5">
            <div class="row align-items-center g-5">
                <div class="col-lg-6 text-center text-lg-start">
                    <h1 class="display-3 text-white animated slideInLeft">Thưởng Thức<br>Tiệc Buffet Đẳng Cấp</h1>
                    <p class="text-white animated slideInLeft mb-4 pb-2">Trải nghiệm ẩm thực tuyệt vời với các gói Combo đa dạng từ 99k đến 499k. Hải sản tươi sống, thịt nướng thượng hạng và không gian sang trọng.</p>
                    <a href="#booking-section" class="btn btn-primary py-sm-3 px-sm-5 me-3 animated slideInLeft">Đặt Bàn Ngay</a>
                </div>
                <div class="col-lg-6 text-center text-lg-end overflow-hidden">
                    {{-- Ảnh Hero minh họa --}}
                    <img class="img-fluid" src="{{ asset('assets/img/hero.png') }}" alt="">
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- 2. MAIN CONTENT --}}
@section('content')

    {{-- Service Start --}}
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="service-item rounded pt-3">
                        <div class="p-4">
                            <i class="fa fa-3x fa-user-tie text-primary mb-4"></i>
                            <h5>Đầu Bếp Tài Ba</h5>
                            <p>Đội ngũ đầu bếp chuyên nghiệp với nhiều năm kinh nghiệm chế biến.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="service-item rounded pt-3">
                        <div class="p-4">
                            <i class="fa fa-3x fa-utensils text-primary mb-4"></i>
                            <h5>Thực Phẩm Tươi Ngon</h5>
                            <p>Nguyên liệu được nhập mới mỗi ngày, đảm bảo vệ sinh ATTP.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="service-item rounded pt-3">
                        <div class="p-4">
                            <i class="fa fa-3x fa-cart-plus text-primary mb-4"></i>
                            <h5>Đặt Bàn Online</h5>
                            <p>Dễ dàng đặt bàn trước qua website, tiết kiệm thời gian chờ đợi.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.7s">
                    <div class="service-item rounded pt-3">
                        <div class="p-4">
                            <i class="fa fa-3x fa-headset text-primary mb-4"></i>
                            <h5>Phục Vụ 24/7</h5>
                            <p>Đội ngũ nhân viên nhiệt tình, chu đáo, sẵn sàng hỗ trợ quý khách.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Service End --}}

    {{-- About Start --}}
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-6">
                    <div class="row g-3">
                        <div class="col-6 text-start">
                            <img class="img-fluid rounded w-100 wow zoomIn" data-wow-delay="0.1s" src="{{ asset('assets/img/about-1.jpg') }}">
                        </div>
                        <div class="col-6 text-start">
                            <img class="img-fluid rounded w-75 wow zoomIn" data-wow-delay="0.3s" src="{{ asset('assets/img/about-2.jpg') }}" style="margin-top: 25%;">
                        </div>
                        <div class="col-6 text-end">
                            <img class="img-fluid rounded w-75 wow zoomIn" data-wow-delay="0.5s" src="{{ asset('assets/img/about-3.jpg') }}">
                        </div>
                        <div class="col-6 text-end">
                            <img class="img-fluid rounded w-100 wow zoomIn" data-wow-delay="0.7s" src="{{ asset('assets/img/about-4.jpg') }}">
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <h5 class="section-title ff-secondary text-start text-primary fw-normal">Về Chúng Tôi</h5>
                    <h1 class="mb-4">Chào mừng đến với <i class="fa fa-utensils text-primary me-2"></i>Buffet Ocean</h1>
                    <p class="mb-4">Nhà hàng Buffet Ocean tự hào mang đến trải nghiệm ẩm thực đẳng cấp với thực đơn phong phú từ hải sản tươi sống đến các món thịt nướng thượng hạng.</p>
                    <p class="mb-4">Với không gian sang trọng và đội ngũ phục vụ chuyên nghiệp, chúng tôi cam kết mang lại sự hài lòng tuyệt đối cho mọi thực khách.</p>
                    <div class="row g-4 mb-4">
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center border-start border-5 border-primary px-3">
                                <h1 class="flex-shrink-0 display-5 text-primary mb-0" data-toggle="counter-up">15</h1>
                                <div class="ps-4">
                                    <p class="mb-0">Năm</p>
                                    <h6 class="text-uppercase mb-0">Kinh Nghiệm</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center border-start border-5 border-primary px-3">
                                <h1 class="flex-shrink-0 display-5 text-primary mb-0" data-toggle="counter-up">50</h1>
                                <div class="ps-4">
                                    <p class="mb-0">Đầu Bếp</p>
                                    <h6 class="text-uppercase mb-0">Hàng Đầu</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a class="btn btn-primary py-3 px-5 mt-2" href="">Xem Thêm</a>
                </div>
            </div>
        </div>
    </div>
    {{-- About End --}}

    {{-- MENU COMBO (Thay thế phần Món ăn cũ) --}}
    @if (isset($combos) && $combos->count() > 0)
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h5 class="section-title ff-secondary text-center text-primary fw-normal">Thực Đơn Combo</h5>
                <h1 class="mb-5">Các Gói Buffet Đặc Biệt</h1>
            </div>
            
            <div class="tab-class text-center wow fadeInUp" data-wow-delay="0.1s">
                
                {{-- Xử lý nhóm Combo theo loai_combo (99k, 199k, 299k...) --}}
                @php
                    $groupedCombos = $combos->groupBy('loai_combo');
                @endphp

                {{-- TABS HEADER --}}
                <ul class="nav nav-pills d-inline-flex justify-content-center border-bottom mb-5">
                    @foreach ($groupedCombos as $type => $typeCombos)
                        <li class="nav-item">
                            <a class="d-flex align-items-center text-start mx-3 ms-0 pb-3 {{ $loop->first ? 'active' : '' }}"
                               data-bs-toggle="pill" href="#tab-combo-{{ $type }}">
                                <i class="fa fa-utensils fa-2x text-primary"></i>
                                <div class="ps-3">
                                    <small class="text-body">Gói</small>
                                    <h6 class="mt-n1 mb-0">{{ strtoupper($type) }}</h6>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>

                {{-- TABS CONTENT --}}
                <div class="tab-content">
                    @foreach ($groupedCombos as $type => $typeCombos)
                        <div id="tab-combo-{{ $type }}" class="tab-pane fade show p-0 {{ $loop->first ? 'active' : '' }}">
                            <div class="row g-4">
                                @foreach($typeCombos as $combo)
                                    <div class="col-lg-6">
                                        <div class="d-flex align-items-center">
                                            {{-- Ảnh Combo: DB lưu dạng combo_buffet/filename.jpg, cần thêm 'uploads/' nếu cần --}}
                                            @php
                                                // Kiểm tra xem trong DB có sẵn 'uploads/' chưa, nếu chưa thì thêm vào
                                                $imagePath = $combo->anh;
                                                if ($imagePath && !str_starts_with($imagePath, 'uploads/')) {
                                                    $imagePath = 'uploads/' . $imagePath;
                                                }
                                            @endphp

                                            @if ($combo->anh)
                                                <img class="flex-shrink-0 img-fluid rounded" 
                                                     src="{{ asset($imagePath) }}" 
                                                     alt="{{ $combo->ten_combo }}" 
                                                     style="width: 80px; height: 80px; object-fit: cover;">
                                            @else
                                                <div class="flex-shrink-0 bg-secondary rounded d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                                    <i class="fa fa-utensils text-white"></i>
                                                </div>
                                            @endif
                                            
                                            <div class="w-100 d-flex flex-column text-start ps-4">
                                                <h5 class="d-flex justify-content-between border-bottom pb-2">
                                                    <span>{{ $combo->ten_combo }}</span>
                                                    <span class="text-primary">{{ number_format($combo->gia_co_ban, 0, ',', '.') }} đ</span>
                                                </h5>
                                                <small class="fst-italic text-muted">
                                                    <i class="fa fa-clock me-1"></i>{{ $combo->thoi_luong_phut }} phút
                                                </small>
                                                <small class="fst-italic">{{ \Illuminate\Support\Str::limit($combo->mo_ta, 80) }}</small>
                                                <div class="mt-2">
                                                    <a href="{{ route('combos.show', $combo->id) }}" class="btn btn-sm btn-primary py-1 px-3">Chi tiết</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif
    {{-- Menu Combo End --}}


    {{-- Reservation Start --}}
    <div class="container-xxl py-5 px-0 wow fadeInUp" id="booking-section" data-wow-delay="0.1s">
        <div class="row g-0">
            <div class="col-md-6">
                <div class="video">
                    <button type="button" class="btn-play" data-bs-toggle="modal" data-src="https://www.youtube.com/embed/DWRcNpR6Kdc" data-bs-target="#videoModal">
                        <span></span>
                    </button>
                </div>
            </div>
            <div class="col-md-6 bg-dark d-flex align-items-center">
                <div class="p-5 wow fadeInUp" data-wow-delay="0.2s">
                    <h5 class="section-title ff-secondary text-start text-primary fw-normal">Đặt Bàn</h5>
                    <h1 class="text-white mb-4">Đặt Bàn Trực Tuyến</h1>

                    {{-- Hiển thị thông báo --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('booking.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="ten_khach" name="ten_khach" 
                                           placeholder="Họ và Tên" value="{{ old('ten_khach') }}" required>
                                    <label for="ten_khach">Họ và Tên</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="email" class="form-control" id="email_khach" name="email_khach" 
                                           placeholder="Email" value="{{ old('email_khach') }}">
                                    <label for="email_khach">Email (Tùy chọn)</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="sdt_khach" name="sdt_khach" 
                                           placeholder="Số Điện Thoại" value="{{ old('sdt_khach') }}" required>
                                    <label for="sdt_khach">Số Điện Thoại</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="datetime-local" class="form-control" id="gio_den" name="gio_den" 
                                           value="{{ old('gio_den') }}" required>
                                    <label for="gio_den">Ngày & Giờ Đến</label>
                                </div>
                            </div>
                            
                            {{-- Cập nhật input theo DB: Nguoi Lon & Tre Em --}}
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="number" class="form-control" id="nguoi_lon" name="nguoi_lon" 
                                           placeholder="Người lớn" value="{{ old('nguoi_lon', 1) }}" min="1" required>
                                    <label for="nguoi_lon">Số Người Lớn</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="number" class="form-control" id="tre_em" name="tre_em" 
                                           placeholder="Trẻ em" value="{{ old('tre_em', 0) }}" min="0">
                                    <label for="tre_em">Số Trẻ Em</label>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating">
                                    <select class="form-select" id="ban_id" name="ban_id">
                                        <option value="">-- Chọn Bàn (Nếu muốn) --</option>
                                        @if(isset($banAns))
                                            @foreach ($banAns as $ban)
                                                {{-- Chỉ hiển thị bàn Trống --}}
                                                @if ($ban->trang_thai === 'trong')
                                                    <option value="{{ $ban->id }}" {{ old('ban_id') == $ban->id ? 'selected' : '' }}>
                                                        {{ $ban->so_ban }} - {{ $ban->khuVuc->ten_khu_vuc ?? 'Khu vực chung' }} ({{ $ban->so_ghe }} ghế)
                                                    </option>
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                    <label for="ban_id">Chọn Bàn Ăn</label>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control" placeholder="Ghi chú" id="ghi_chu" name="ghi_chu" style="height: 100px">{{ old('ghi_chu') }}</textarea>
                                    <label for="ghi_chu">Ghi chú / Yêu cầu đặc biệt</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary w-100 py-3" type="submit">Xác Nhận Đặt Bàn</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Video Modal --}}
    <div class="modal fade" id="videoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content rounded-0">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Video Giới Thiệu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="ratio ratio-16x9">
                        <iframe class="embed-responsive-item" src="" id="video" allowfullscreen allowscriptaccess="always"
                            allow="autoplay"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Reservation End --}}

    {{-- Team Start --}}
    <div class="container-xxl pt-5 pb-3">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h5 class="section-title ff-secondary text-center text-primary fw-normal">Đội Ngũ</h5>
                <h1 class="mb-5">Đầu Bếp Của Chúng Tôi</h1>
            </div>
            <div class="row g-4">
                {{-- Dữ liệu mẫu tĩnh cho phần Team, có thể thay bằng dynamic nếu có bảng nhan_vien vai_tro='bep' --}}
                <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="team-item text-center rounded overflow-hidden">
                        <div class="rounded-circle overflow-hidden m-4">
                            <img class="img-fluid" src="{{ asset('assets/img/team-1.jpg') }}" alt="">
                        </div>
                        <h5 class="mb-0">Full Name</h5>
                        <small>Bếp Trưởng</small>
                        <div class="d-flex justify-content-center mt-3">
                            <a class="btn btn-square btn-primary mx-1" href=""><i class="fab fa-facebook-f"></i></a>
                            <a class="btn btn-square btn-primary mx-1" href=""><i class="fab fa-twitter"></i></a>
                            <a class="btn btn-square btn-primary mx-1" href=""><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="team-item text-center rounded overflow-hidden">
                        <div class="rounded-circle overflow-hidden m-4">
                            <img class="img-fluid" src="{{ asset('assets/img/team-2.jpg') }}" alt="">
                        </div>
                        <h5 class="mb-0">Full Name</h5>
                        <small>Bếp Phó</small>
                        <div class="d-flex justify-content-center mt-3">
                            <a class="btn btn-square btn-primary mx-1" href=""><i class="fab fa-facebook-f"></i></a>
                            <a class="btn btn-square btn-primary mx-1" href=""><i class="fab fa-twitter"></i></a>
                            <a class="btn btn-square btn-primary mx-1" href=""><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="team-item text-center rounded overflow-hidden">
                        <div class="rounded-circle overflow-hidden m-4">
                            <img class="img-fluid" src="{{ asset('assets/img/team-3.jpg') }}" alt="">
                        </div>
                        <h5 class="mb-0">Full Name</h5>
                        <small>Quản Lý</small>
                        <div class="d-flex justify-content-center mt-3">
                            <a class="btn btn-square btn-primary mx-1" href=""><i class="fab fa-facebook-f"></i></a>
                            <a class="btn btn-square btn-primary mx-1" href=""><i class="fab fa-twitter"></i></a>
                            <a class="btn btn-square btn-primary mx-1" href=""><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.7s">
                    <div class="team-item text-center rounded overflow-hidden">
                        <div class="rounded-circle overflow-hidden m-4">
                            <img class="img-fluid" src="{{ asset('assets/img/team-4.jpg') }}" alt="">
                        </div>
                        <h5 class="mb-0">Full Name</h5>
                        <small>Đầu Bếp</small>
                        <div class="d-flex justify-content-center mt-3">
                            <a class="btn btn-square btn-primary mx-1" href=""><i class="fab fa-facebook-f"></i></a>
                            <a class="btn btn-square btn-primary mx-1" href=""><i class="fab fa-twitter"></i></a>
                            <a class="btn btn-square btn-primary mx-1" href=""><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Team End --}}

    {{-- Testimonial Start --}}
    <div class="container-xxl py-5 wow fadeInUp" data-wow-delay="0.1s">
        <div class="container">
            <div class="text-center">
                <h5 class="section-title ff-secondary text-center text-primary fw-normal">Đánh Giá</h5>
                <h1 class="mb-5">Khách Hàng Nói Gì!!!</h1>
            </div>
            <div class="owl-carousel testimonial-carousel">
                <div class="testimonial-item bg-transparent border rounded p-4">
                    <i class="fa fa-quote-left fa-2x text-primary mb-3"></i>
                    <p>Món ăn rất tươi ngon, đặc biệt là hải sản. Phục vụ chu đáo, không gian thoáng đãng.</p>
                    <div class="d-flex align-items-center">
                        <img class="img-fluid flex-shrink-0 rounded-circle" src="{{ asset('assets/img/testimonial-1.jpg') }}" style="width: 50px; height: 50px;">
                        <div class="ps-3">
                            <h5 class="mb-1">Nguyễn Văn A</h5>
                            <small>Khách hàng</small>
                        </div>
                    </div>
                </div>
                <div class="testimonial-item bg-transparent border rounded p-4">
                    <i class="fa fa-quote-left fa-2x text-primary mb-3"></i>
                    <p>Combo 199k quá hời, đồ ăn ra liên tục không phải chờ đợi. Sẽ quay lại ủng hộ.</p>
                    <div class="d-flex align-items-center">
                        <img class="img-fluid flex-shrink-0 rounded-circle" src="{{ asset('assets/img/testimonial-2.jpg') }}" style="width: 50px; height: 50px;">
                        <div class="ps-3">
                            <h5 class="mb-1">Trần Thị B</h5>
                            <small>Khách hàng</small>
                        </div>
                    </div>
                </div>
                <div class="testimonial-item bg-transparent border rounded p-4">
                    <i class="fa fa-quote-left fa-2x text-primary mb-3"></i>
                    <p>Không gian sạch sẽ, đặt bàn qua website rất tiện lợi. Đồ nướng ướp rất vừa miệng.</p>
                    <div class="d-flex align-items-center">
                        <img class="img-fluid flex-shrink-0 rounded-circle" src="{{ asset('assets/img/testimonial-3.jpg') }}" style="width: 50px; height: 50px;">
                        <div class="ps-3">
                            <h5 class="mb-1">Lê Văn C</h5>
                            <small>Khách hàng</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Testimonial End --}}

@endsection