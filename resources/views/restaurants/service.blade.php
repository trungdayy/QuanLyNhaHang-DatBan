@extends('layouts.page')

{{-- Tiêu đề trang sẽ tự động chui vào Banner và Breadcrumb --}}
@section('title', 'Dịch Vụ')

@section('content')
<!-- Service Start -->
<div class="container-xxl py-5">
    <div class="container">

        <div class="text-center mb-5">
            <h5 class="section-title ff-secondary text-primary fw-normal wow fadeInUp" data-wow-delay="0.1s">
                Dịch Vụ Của Chúng Tôi
            </h5>
            <h1 class="wow fadeInUp" data-wow-delay="0.2s">Trải Nghiệm Ẩm Thực Đẳng Cấp</h1>
        </div>

        <div class="row g-4">

            <!-- ITEM -->
            <div class="col-md-6 col-lg-3 wow fadeInUp" data-wow-delay="0.1s">
                <div class="service-card">
                    <div class="icon-wrap">
                        <i class="fa fa-user-tie"></i>
                    </div>
                    <h5>Đầu Bếp Đẳng Cấp</h5>
                    <p>Đội ngũ đầu bếp chuyên nghiệp với nhiều năm kinh nghiệm tại các nhà hàng cao cấp, mang đến
                        hương vị tinh tế và sáng tạo trong từng món ăn.</p>
                </div>
            </div>

            <!-- ITEM -->
            <div class="col-md-6 col-lg-3 wow fadeInUp" data-wow-delay="0.2s">
                <div class="service-card">
                    <div class="icon-wrap">
                        <i class="fa fa-utensils"></i>
                    </div>
                    <h5>Nguyên Liệu Tươi Sạch</h5>
                    <p>Chúng tôi chọn lọc nguyên liệu tươi ngon nhất mỗi ngày, đảm bảo độ an toàn, sạch và giữ trọn
                        hương vị tự nhiên.</p>
                </div>
            </div>

            <!-- ITEM -->
            <div class="col-md-6 col-lg-3 wow fadeInUp" data-wow-delay="0.3s">
                <div class="service-card">
                    <div class="icon-wrap">
                        <i class="fa fa-cart-plus"></i>
                    </div>
                    <h5>Đặt Món Online</h5>
                    <p>Hệ thống đặt món nhanh chóng, giao diện thân thiện, hỗ trợ khách hàng chọn món dễ dàng mọi
                        lúc – mọi nơi.</p>
                </div>
            </div>

            <!-- ITEM -->
            <div class="col-md-6 col-lg-3 wow fadeInUp" data-wow-delay="0.4s">
                <div class="service-card">
                    <div class="icon-wrap">
                        <i class="fa fa-headset"></i>
                    </div>
                    <h5>Hỗ Trợ 24/7</h5>
                    <p>Đội ngũ CSKH luôn sẵn sàng hỗ trợ, giải đáp và tư vấn để khách hàng luôn có trải nghiệm tốt
                        nhất khi sử dụng dịch vụ.</p>
                </div>
            </div>

            <!-- ITEM -->
            <div class="col-md-6 col-lg-3 wow fadeInUp" data-wow-delay="0.5s">
                <div class="service-card">
                    <div class="icon-wrap">
                        <i class="fa fa-wine-glass-alt"></i>
                    </div>
                    <h5>Không Gian Sang Trọng</h5>
                    <p>Thiết kế tinh tế, ánh sáng ấm áp, phù hợp cho buổi hẹn hò, gặp đối tác hoặc tổ chức bữa tiệc
                        thân mật.</p>
                </div>
            </div>

            <!-- ITEM -->
            <div class="col-md-6 col-lg-3 wow fadeInUp" data-wow-delay="0.6s">
                <div class="service-card">
                    <div class="icon-wrap">
                        <i class="fa fa-concierge-bell"></i>
                    </div>
                    <h5>Phục Vụ Chuyên Nghiệp</h5>
                    <p>Nhân viên được đào tạo bài bản, thân thiện, chu đáo, đảm bảo khách hàng luôn cảm thấy thoải
                        mái và hài lòng.</p>
                </div>
            </div>

            <!-- ITEM -->
            <div class="col-md-6 col-lg-3 wow fadeInUp" data-wow-delay="0.7s">
                <div class="service-card">
                    <div class="icon-wrap">
                        <i class="fa fa-cocktail"></i>
                    </div>
                    <h5>Menu Đồ Uống Cao Cấp</h5>
                    <p>Đa dạng từ cocktail, rượu vang nhập khẩu đến nước ép tươi mát, phù hợp mọi khẩu vị.</p>
                </div>
            </div>

            <!-- ITEM -->
            <div class="col-md-6 col-lg-3 wow fadeInUp" data-wow-delay="0.8s">
                <div class="service-card">
                    <div class="icon-wrap">
                        <i class="fa fa-calendar-check"></i>
                    </div>
                    <h5>Tổ Chức Tiệc & Sự Kiện</h5>
                    <p>Nhận đặt tiệc sinh nhật, họp mặt, liên hoan công ty với thực đơn đa dạng và trang trí theo
                        yêu cầu.</p>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- Service End -->
 <style>
    .service-card {
        background: #fff;
        border-radius: 18px;
        padding: 30px 25px;
        text-align: center;
        transition: 0.35s ease;
        height: 100%;
        border: 1px solid #eee;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    .service-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 25px rgba(0,0,0,0.10);
        border-color: transparent;
    }

    .icon-wrap {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        margin: 0 auto 20px auto;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 99, 33, 0.12);
        color: #ff6a00;
        font-size: 28px;
        transition: 0.3s ease;
    }

    .service-card:hover .icon-wrap {
        background: #ff6a00;
        color: #fff;
        transform: scale(1.08);
    }

    .service-card h5 {
        font-weight: 600;
        margin-bottom: 12px;
    }

    .service-card p {
        color: #555;
        font-size: 15px;
        line-height: 1.6;
    }
</style>

    @endsection