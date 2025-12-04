@extends('layouts.page')

@section('title', 'Về Chúng Tôi')

@section('content')
    {{-- CSS CHO POPUP (Có thể chuyển vào file CSS riêng nếu muốn) --}}
    <style>
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 1050;
            display: none;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .modal-overlay.show {
            display: flex;
            opacity: 1;
        }

        .about-popup {
            background: #fff;
            width: 90%;
            max-width: 700px;
            border-radius: 15px;
            padding: 30px;
            position: relative;
            transform: translateY(-20px);
            transition: transform 0.3s ease;
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-overlay.show .about-popup {
            transform: translateY(0);
        }

        .popup-close {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 24px;
            color: #666;
            cursor: pointer;
            transition: color 0.2s;
            background: none;
            border: none;
        }

        .popup-close:hover {
            color: #fea116; /* Màu primary của theme */
        }

        .popup-content h3 {
            color: #0f172b; /* Màu dark của theme */
            font-family: 'Heebo', sans-serif;
            font-weight: 800;
            margin-bottom: 20px;
            border-bottom: 3px solid #fea116;
            display: inline-block;
            padding-bottom: 5px;
        }

        .popup-content p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .popup-highlight {
            background: #fff7e6; /* Màu nền nhẹ nhàng */
            border-left: 4px solid #fea116;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        
        .popup-features {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 20px;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .feature-item i {
            color: #fea116;
        }

        /* Ẩn scrollbar cho đẹp */
        .about-popup::-webkit-scrollbar {
            width: 6px;
        }
        .about-popup::-webkit-scrollbar-thumb {
            background-color: #ccc;
            border-radius: 4px;
        }
    </style>

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
                    <h1 class="mb-4">Chào mừng đến với <i class="fa fa-utensils text-primary me-2"></i>Ocean Buffet</h1>
                    
                    <p class="mb-4">
                        Khám phá thiên đường ẩm thực tại Ocean Buffet, nơi hương vị biển cả hòa quyện cùng tinh hoa ẩm thực Á - Âu. Chúng tôi tự hào mang đến trải nghiệm buffet đẳng cấp với hơn 100 món ăn tươi ngon, được chế biến công phu bởi đội ngũ đầu bếp hàng đầu.
                    </p>
                    <p class="mb-4">
                        Từ hải sản tươi sống được đánh bắt trong ngày đến các món nướng BBQ đậm đà, lẩu thượng hạng và quầy tráng miệng phong phú. Tại Ocean Buffet, mỗi bữa ăn không chỉ là thưởng thức món ngon mà còn là những khoảnh khắc sum vầy đáng nhớ bên gia đình và bạn bè.
                    </p>
                    
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
                                    <h6 class="text-uppercase mb-0">Tài Năng</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Nút mở popup --}}
                    <button class="btn btn-primary py-3 px-5 mt-2" onclick="openAboutPopup()">Xem Thêm</button>
                </div>
            </div>
        </div>
    </div>

    {{-- POPUP MODAL --}}
    <div id="aboutModal" class="modal-overlay">
        <div class="about-popup">
            <button class="popup-close" onclick="closeAboutPopup()"><i class="fa fa-times"></i></button>
            <div class="popup-content">
                <h3>Câu Chuyện Ocean Buffet</h3>
                <p>
                    Được thành lập từ năm 2010, Ocean Buffet khởi nguồn từ niềm đam mê mãnh liệt với ẩm thực biển. Chúng tôi mong muốn mang "hương vị đại dương" tươi ngon nhất đến ngay giữa lòng thành phố, nơi thực khách có thể tận hưởng hải sản cao cấp với mức giá hợp lý nhất.
                </p>
                
                <div class="popup-highlight">
                    <strong><i class="fa fa-quote-left text-primary me-2"></i>Sứ mệnh của chúng tôi:</strong>
                    "Không chỉ phục vụ món ăn ngon, chúng tôi phục vụ niềm vui và sự hài lòng tuyệt đối cho từng thực khách."
                </div>

                <h5 class="mt-4 mb-3" style="font-weight: 700; color: #0f172b;">Tại sao chọn Ocean Buffet?</h5>
                <p>
                    Chúng tôi cam kết nguồn nguyên liệu sạch 100%, nhập khẩu trực tiếp và được kiểm định nghiêm ngặt mỗi ngày. Không gian nhà hàng được thiết kế sang trọng, ấm cúng với sức chứa lên đến 500 khách, phù hợp cho mọi bữa tiệc từ sinh nhật, liên hoan công ty đến hẹn hò lãng mạn.
                </p>

                <div class="popup-features">
                    <div class="feature-item"><i class="fa fa-check-circle"></i> Hải sản tươi sống tại bể</div>
                    <div class="feature-item"><i class="fa fa-check-circle"></i> Quầy line Á - Âu đa dạng</div>
                    <div class="feature-item"><i class="fa fa-check-circle"></i> Không gian view đẹp, thoáng</div>
                    <div class="feature-item"><i class="fa fa-check-circle"></i> Phục vụ chuyên nghiệp</div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-xxl pt-5 pb-3">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h5 class="section-title ff-secondary text-center text-primary fw-normal">Đội Ngũ</h5>
                <h1 class="mb-5">Đầu Bếp Hàng Đầu</h1>
            </div>
            <div class="row g-4">
                {{-- Bạn có thể foreach mảng chefs từ controller tại đây --}}
                <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="team-item text-center rounded overflow-hidden">
                        <div class="rounded-circle overflow-hidden m-4">
                            <img class="img-fluid" src="{{ asset('assets/img/team-1.jpg') }}" alt="">
                        </div>
                        <h5 class="mb-0">Nguyễn Văn A</h5>
                        <small>Bếp Trưởng</small>
                        <div class="d-flex justify-content-center mt-3">
                            <a class="btn btn-square btn-primary mx-1" href=""><i class="fab fa-facebook-f"></i></a>
                            <a class="btn btn-square btn-primary mx-1" href=""><i class="fab fa-twitter"></i></a>
                            <a class="btn btn-square btn-primary mx-1" href=""><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                </div>
                {{-- Copy thêm 3 item nữa để demo layout --}}
                <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="team-item text-center rounded overflow-hidden">
                        <div class="rounded-circle overflow-hidden m-4">
                            <img class="img-fluid" src="{{ asset('assets/img/team-2.jpg') }}" alt="">
                        </div>
                        <h5 class="mb-0">Trần Thị B</h5>
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
                        <h5 class="mb-0">Lê Văn C</h5>
                        <small>Đầu Bếp Nướng</small>
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
                        <h5 class="mb-0">Phạm Thị D</h5>
                        <small>Đầu Bếp Salad</small>
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

    {{-- SCRIPT XỬ LÝ POPUP --}}
    <script>
        function openAboutPopup() {
            const modal = document.getElementById('aboutModal');
            modal.style.display = 'flex';
            // setTimeout để transition opacity hoạt động mượt mà
            setTimeout(() => {
                modal.classList.add('show');
            }, 10);
            document.body.style.overflow = 'hidden'; // Khóa cuộn trang web nền
        }

        function closeAboutPopup() {
            const modal = document.getElementById('aboutModal');
            modal.classList.remove('show');
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300); // Đợi transition hoàn tất mới ẩn display
            document.body.style.overflow = 'auto'; // Mở lại cuộn trang
        }

        // Đóng popup khi click ra ngoài vùng nội dung
        document.getElementById('aboutModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeAboutPopup();
            }
        });
    </script>
@endsection