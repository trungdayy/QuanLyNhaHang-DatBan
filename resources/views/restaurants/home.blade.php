
{{-- header --}}
@extends('layouts.restaurants.layout-shop')
@section('title', 'Home')
@section('content')
        <!-- Service Start -->
        <div class="container-xxl py-5">
            <div class="container">
                <div class="row g-4">
                    <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="service-item rounded pt-3">
                            <div class="p-4">
                                <i class="fa fa-3x fa-user-tie text-primary mb-4"></i>
                                <h5>Master Chefs</h5>
                                <p>Diam elitr kasd sed at elitr sed ipsum justo dolor sed clita amet diam</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.3s">
                        <div class="service-item rounded pt-3">
                            <div class="p-4">
                                <i class="fa fa-3x fa-utensils text-primary mb-4"></i>
                                <h5>Quality Food</h5>
                                <p>Diam elitr kasd sed at elitr sed ipsum justo dolor sed clita amet diam</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.5s">
                        <div class="service-item rounded pt-3">
                            <div class="p-4">
                                <i class="fa fa-3x fa-cart-plus text-primary mb-4"></i>
                                <h5>Online Order</h5>
                                <p>Diam elitr kasd sed at elitr sed ipsum justo dolor sed clita amet diam</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.7s">
                        <div class="service-item rounded pt-3">
                            <div class="p-4">
                                <i class="fa fa-3x fa-headset text-primary mb-4"></i>
                                <h5>24/7 Service</h5>
                                <p>Diam elitr kasd sed at elitr sed ipsum justo dolor sed clita amet diam</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Service End -->


        <!-- About Start -->
        <div class="container-xxl py-5">
            <div class="container">
                <div class="row g-5 align-items-center">
                    <div class="col-lg-6">
                        <div class="row g-3">
                            <div class="col-6 text-start">
                                <img class="img-fluid rounded w-100 wow zoomIn" data-wow-delay="0.1s" src="img/about-1.jpg">
                            </div>
                            <div class="col-6 text-start">
                                <img class="img-fluid rounded w-75 wow zoomIn" data-wow-delay="0.3s" src="img/about-2.jpg" style="margin-top: 25%;">
                            </div>
                            <div class="col-6 text-end">
                                <img class="img-fluid rounded w-75 wow zoomIn" data-wow-delay="0.5s" src="img/about-3.jpg">
                            </div>
                            <div class="col-6 text-end">
                                <img class="img-fluid rounded w-100 wow zoomIn" data-wow-delay="0.7s" src="img/about-4.jpg">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <h5 class="section-title ff-secondary text-start text-primary fw-normal">About Us</h5>
                        <h1 class="mb-4">Welcome to <i class="fa fa-utensils text-primary me-2"></i>Restoran</h1>
                        <p class="mb-4">Tempor erat elitr rebum at clita. Diam dolor diam ipsum sit. Aliqu diam amet diam et eos erat ipsum et lorem et sit, sed stet lorem sit.</p>
                        <p class="mb-4">Tempor erat elitr rebum at clita. Diam dolor diam ipsum sit. Aliqu diam amet diam et eos. Clita erat ipsum et lorem et sit, sed stet lorem sit clita duo justo magna dolore erat amet</p>
                        <div class="row g-4 mb-4">
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center border-start border-5 border-primary px-3">
                                    <h1 class="flex-shrink-0 display-5 text-primary mb-0" data-toggle="counter-up">15</h1>
                                    <div class="ps-4">
                                        <p class="mb-0">Years of</p>
                                        <h6 class="text-uppercase mb-0">Experience</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center border-start border-5 border-primary px-3">
                                    <h1 class="flex-shrink-0 display-5 text-primary mb-0" data-toggle="counter-up">50</h1>
                                    <div class="ps-4">
                                        <p class="mb-0">Popular</p>
                                        <h6 class="text-uppercase mb-0">Master Chefs</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a class="btn btn-primary py-3 px-5 mt-2" href="">Read More</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- About End -->


        <!-- Combo Buffet Start -->
        @if($combos->count() > 0)
        <div class="container-xxl py-5">
            <div class="container">
                <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                    <h5 class="section-title ff-secondary text-center text-primary fw-normal">Combo Buffet</h5>
                    <h1 class="mb-5">Các Combo Đặc Biệt</h1>
                </div>
                <div class="row g-4">
                    @foreach($combos as $combo)
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="combo-item rounded overflow-hidden">
                            <div class="position-relative">
                                @if($combo->anh)
                                <img class="img-fluid w-100" src="{{ asset('uploads/' . $combo->anh) }}" alt="{{ $combo->ten_combo }}" style="height: 250px; object-fit: cover;">
                                @else
                                <div class="bg-secondary d-flex align-items-center justify-content-center" style="height: 250px;">
                                    <i class="fa fa-utensils fa-3x text-white"></i>
                                </div>
                                @endif
                                <div class="position-absolute top-0 start-0 p-3">
                                    @php
                                    $loaiColors = [
                                        'nguoi_lon' => 'bg-warning',
                                        'tre_em' => 'bg-info',
                                        'vip' => 'bg-danger',
                                        'khuyen_mai' => 'bg-success',
                                    ];
                                    $badgeClass = $loaiColors[$combo->loai_combo] ?? 'bg-secondary';
                                    @endphp
                                    <span class="badge {{ $badgeClass }} text-white px-3 py-2">{{ $combo->loai_combo_display }}</span>
                                </div>
                            </div>
                            <div class="p-4">
                                <h5 class="mb-3">{{ $combo->ten_combo }}</h5>
                                <p class="text-primary mb-3">
                                    <strong>{{ number_format($combo->gia_co_ban, 0, ',', '.') }} VNĐ</strong>
                                </p>
                                @if($combo->thoi_luong_phut)
                                <p class="mb-2">
                                    <i class="fa fa-clock text-primary me-2"></i>
                                    <small>{{ $combo->thoi_luong_phut }} phút</small>
                                </p>
                                @endif
                                <a class="btn btn-primary py-2 px-4 mt-2" href="{{ route('combos.show', $combo->id) }}">Xem Chi Tiết</a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="text-center mt-4">
                    <a class="btn btn-primary py-3 px-5" href="{{ route('combos.index') }}">Xem Tất Cả Combo</a>
                </div>
            </div>
        </div>
        @endif
        <!-- Combo Buffet End -->

        <!-- Menu Start -->
        @if($danhMucs->count() > 0)
        <div class="container-xxl py-5">
            <div class="container">
                <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                    <h5 class="section-title ff-secondary text-center text-primary fw-normal">Food Menu</h5>
                    <h1 class="mb-5">Thực Đơn Nhà Hàng</h1>
                </div>
                <div class="tab-class text-center wow fadeInUp" data-wow-delay="0.1s">
                    <ul class="nav nav-pills d-inline-flex justify-content-center border-bottom mb-5">
                        @foreach($danhMucs as $index => $danhMuc)
                        <li class="nav-item">
                            <a class="d-flex align-items-center text-start mx-3 {{ $index === 0 ? 'ms-0' : '' }} {{ $index === $danhMucs->count() - 1 ? 'me-0' : '' }} pb-3 {{ $index === 0 ? 'active' : '' }}" 
                               data-bs-toggle="pill" href="#tab-{{ $danhMuc->id }}">
                                <i class="fa fa-utensils fa-2x text-primary"></i>
                                <div class="ps-3">
                                    <small class="text-body">{{ $danhMuc->monAn->count() }} món</small>
                                    <h6 class="mt-n1 mb-0">{{ $danhMuc->ten_danh_muc }}</h6>
                                </div>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                    <div class="tab-content">
                        @foreach($danhMucs as $index => $danhMuc)
                        <div id="tab-{{ $danhMuc->id }}" class="tab-pane fade show p-0 {{ $index === 0 ? 'active' : '' }}">
                            <div class="row g-4">
                                @forelse($danhMuc->monAn as $mon)
                                <div class="col-lg-6">
                                    <div class="d-flex align-items-center">
                                        @if($mon->hinh_anh)
                                        <img class="flex-shrink-0 img-fluid rounded" src="{{ asset($mon->hinh_anh) }}" alt="{{ $mon->ten_mon }}" style="width: 80px; height: 80px; object-fit: cover;" onerror="this.style.display='none';">
                                        @else
                                        <div class="flex-shrink-0 bg-secondary rounded d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                            <i class="fa fa-utensils text-white fa-2x"></i>
                                        </div>
                                        @endif
                                        <div class="w-100 d-flex flex-column text-start ps-4">
                                            <h5 class="d-flex justify-content-between border-bottom pb-2">
                                                <span>{{ $mon->ten_mon }}</span>
                                                <span class="text-primary">{{ number_format($mon->gia, 0, ',', '.') }} VNĐ</span>
                                            </h5>
                                            @if($mon->mo_ta)
                                            <small class="fst-italic">{{ mb_substr($mon->mo_ta, 0, 80) }}{{ mb_strlen($mon->mo_ta) > 80 ? '...' : '' }}</small>
                                            @else
                                            <small class="fst-italic text-muted">Món ăn ngon miệng</small>
                                            @endif
                                            @if($mon->loai_mon)
                                            <small class="text-muted mt-1">
                                                <i class="fa fa-tag text-primary me-1"></i>{{ $mon->loai_mon }}
                                            </small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="col-12">
                                    <p class="text-center text-muted">Danh mục này chưa có món ăn.</p>
                                </div>
                                @endforelse
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif
        <!-- Menu End -->


        <!-- Reservation Start -->
        <div class="container-xxl py-5 px-0 wow fadeInUp" data-wow-delay="0.1s">
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
                        <h5 class="section-title ff-secondary text-start text-primary fw-normal">Reservation</h5>
                        <h1 class="text-white mb-4">Book A Table Online</h1>
                        
                        {{-- Thông báo --}}
                        @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        @endif
                        @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        @endif
                        @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
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
                                               placeholder="Họ và tên" value="{{ old('ten_khach') }}" required>
                                        <label for="ten_khach">Họ và tên</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="email" class="form-control" id="email_khach" name="email_khach" 
                                               placeholder="Email" value="{{ old('email_khach') }}">
                                        <label for="email_khach">Email</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="sdt_khach" name="sdt_khach" 
                                               placeholder="Số điện thoại" value="{{ old('sdt_khach') }}" required>
                                        <label for="sdt_khach">Số điện thoại</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" id="ban_id" name="ban_id">
                                            <option value="">-- Chọn Bàn (Tùy chọn) --</option>
                                            @foreach($banAns as $ban)
                                            @if($ban->trang_thai === 'trong')
                                            <option value="{{ $ban->id }}" {{ old('ban_id') == $ban->id ? 'selected' : '' }}>
                                                Bàn {{ $ban->so_ban }} - {{ $ban->khuVuc->ten_khu_vuc ?? '' }}
                                            </option>
                                            @endif
                                            @endforeach
                                        </select>
                                        <label for="ban_id">Bàn Ăn</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="datetime-local" class="form-control" id="gio_den" name="gio_den" 
                                               value="{{ old('gio_den') }}" required>
                                        <label for="gio_den">Ngày & Giờ Đến</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" id="so_khach" name="so_khach" 
                                               placeholder="Số khách" value="{{ old('so_khach', 1) }}" min="1" required>
                                        <label for="so_khach">Số khách</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea class="form-control" placeholder="Ghi chú" id="ghi_chu" name="ghi_chu" style="height: 100px">{{ old('ghi_chu') }}</textarea>
                                        <label for="ghi_chu">Ghi chú / Yêu cầu đặc biệt</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-primary w-100 py-3" type="submit">Đặt Bàn Ngay</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="videoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content rounded-0">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Youtube Video</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- 16:9 aspect ratio -->
                        <div class="ratio ratio-16x9">
                            <iframe class="embed-responsive-item" src="" id="video" allowfullscreen allowscriptaccess="always"
                                allow="autoplay"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Reservation Start -->


        <!-- Team Start -->
        <div class="container-xxl pt-5 pb-3">
            <div class="container">
                <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                    <h5 class="section-title ff-secondary text-center text-primary fw-normal">Team Members</h5>
                    <h1 class="mb-5">Our Master Chefs</h1>
                </div>
                <div class="row g-4">
                    <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="team-item text-center rounded overflow-hidden">
                            <div class="rounded-circle overflow-hidden m-4">
                                <img class="img-fluid" src="img/team-1.jpg" alt="">
                            </div>
                            <h5 class="mb-0">Full Name</h5>
                            <small>Designation</small>
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
                                <img class="img-fluid" src="img/team-2.jpg" alt="">
                            </div>
                            <h5 class="mb-0">Full Name</h5>
                            <small>Designation</small>
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
                                <img class="img-fluid" src="img/team-3.jpg" alt="">
                            </div>
                            <h5 class="mb-0">Full Name</h5>
                            <small>Designation</small>
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
                                <img class="img-fluid" src="img/team-4.jpg" alt="">
                            </div>
                            <h5 class="mb-0">Full Name</h5>
                            <small>Designation</small>
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
        <!-- Team End -->


        <!-- Testimonial Start -->
        <div class="container-xxl py-5 wow fadeInUp" data-wow-delay="0.1s">
            <div class="container">
                <div class="text-center">
                    <h5 class="section-title ff-secondary text-center text-primary fw-normal">Testimonial</h5>
                    <h1 class="mb-5">Our Clients Say!!!</h1>
                </div>
                <div class="owl-carousel testimonial-carousel">
                    <div class="testimonial-item bg-transparent border rounded p-4">
                        <i class="fa fa-quote-left fa-2x text-primary mb-3"></i>
                        <p>Dolor et eos labore, stet justo sed est sed. Diam sed sed dolor stet amet eirmod eos labore diam</p>
                        <div class="d-flex align-items-center">
                            <img class="img-fluid flex-shrink-0 rounded-circle" src="img/testimonial-1.jpg" style="width: 50px; height: 50px;">
                            <div class="ps-3">
                                <h5 class="mb-1">Client Name</h5>
                                <small>Profession</small>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial-item bg-transparent border rounded p-4">
                        <i class="fa fa-quote-left fa-2x text-primary mb-3"></i>
                        <p>Dolor et eos labore, stet justo sed est sed. Diam sed sed dolor stet amet eirmod eos labore diam</p>
                        <div class="d-flex align-items-center">
                            <img class="img-fluid flex-shrink-0 rounded-circle" src="img/testimonial-2.jpg" style="width: 50px; height: 50px;">
                            <div class="ps-3">
                                <h5 class="mb-1">Client Name</h5>
                                <small>Profession</small>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial-item bg-transparent border rounded p-4">
                        <i class="fa fa-quote-left fa-2x text-primary mb-3"></i>
                        <p>Dolor et eos labore, stet justo sed est sed. Diam sed sed dolor stet amet eirmod eos labore diam</p>
                        <div class="d-flex align-items-center">
                            <img class="img-fluid flex-shrink-0 rounded-circle" src="img/testimonial-3.jpg" style="width: 50px; height: 50px;">
                            <div class="ps-3">
                                <h5 class="mb-1">Client Name</h5>
                                <small>Profession</small>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial-item bg-transparent border rounded p-4">
                        <i class="fa fa-quote-left fa-2x text-primary mb-3"></i>
                        <p>Dolor et eos labore, stet justo sed est sed. Diam sed sed dolor stet amet eirmod eos labore diam</p>
                        <div class="d-flex align-items-center">
                            <img class="img-fluid flex-shrink-0 rounded-circle" src="img/testimonial-4.jpg" style="width: 50px; height: 50px;">
                            <div class="ps-3">
                                <h5 class="mb-1">Client Name</h5>
                                <small>Profession</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Testimonial End -->
@endsection

{{-- footer --}}
       
       