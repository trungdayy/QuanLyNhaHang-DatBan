@extends('layouts.page')

{{-- Tiêu đề trang --}}
@section('title', 'Liên Hệ & Đánh Giá')

{{-- Nội dung chính của trang --}}

@section('content')

    {{-- 1. CSS CHO ĐÁNH GIÁ SAO (Nhúng trực tiếp tại đây) --}}
    <style>
        .star-rating {
            direction: rtl;
            /* Đảo ngược chiều để xử lý hover từ phải qua trái */
            display: inline-flex;
            font-size: 30px;
            /* Kích thước sao */
        }

        .star-rating input[type=radio] {
            display: none;
            /* Ẩn nút radio tròn */
        }

        .star-rating label {
            color: #ccc;
            /* Màu xám khi chưa chọn */
            cursor: pointer;
            transition: all 0.2s;
            padding: 0 5px;
        }

        /* Hiệu ứng khi hover hoặc đã chọn */
        .star-rating label:hover,
        .star-rating label:hover~label,
        .star-rating input[type=radio]:checked~label {
            color: #ffc107;
            /* Màu vàng (Primary) */
        }
    </style>

    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h5 class="section-title ff-secondary text-center text-primary fw-normal">Contact Us</h5>
                <h1 class="mb-5">Liên hệ với bất kỳ thắc mắc nào</h1>
            </div>

            {{-- Thông tin liên hệ --}}
            <div class="row g-4">
                <div class="col-12">
                    <div class="row gy-4">
                        <div class="col-md-4">
                            <h5 class="section-title ff-secondary fw-normal text-start text-primary">Email</h5>
                            <p><i class="fa fa-envelope-open text-primary me-2"></i>buffetocean@gmail.com</p>
                        </div>
                        <div class="col-md-4">
                            <h5 class="section-title ff-secondary fw-normal text-start text-primary">Facebook</h5>
                            <p>
                                <i class="fab fa-facebook-f text-primary me-2"></i>
                                <a href="https://www.facebook.com/oceanbuffet.vn" target="_blank"
                                    class="text-dark">facebook.com/oceanbuffet.vn</a>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <h5 class="section-title ff-secondary fw-normal text-start text-primary">Instagram</h5>
                            <p>
                                <i class="fab fa-instagram text-primary me-2"></i>
                                <a href="https://www.instagram.com/ocean.buffet.vn" target="_blank"
                                    class="text-dark">instagram.com/ocean.buffet.vn</a>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Bản đồ Google Map --}}
                <div class="col-md-6 wow fadeIn" data-wow-delay="0.1s">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d28782.364959963812!2d105.74726181805156!3d21.038129822695367!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x313455e940879933%3A0xcf10b34e9f1a03df!2zVHLGsOG7nW5nIENhbyDEkeG6s25nIEZQVCBQb2x5dGVjaG5pYw!5e1!3m2!1svi!2s!4v1764844821135!5m2!1svi!2s"
                        width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>

                {{-- Form liên hệ & Đánh giá --}}
                <div class="col-md-6">
                    <div class="wow fadeInUp" data-wow-delay="0.2s">
                        <form action="{{ route('contact.send') }}" method="POST">
                            @csrf

                            {{-- Thông báo thành công --}}
                            @if (session('success'))
                                <div class="alert alert-success mb-3">{{ session('success') }}</div>
                            @endif

                            {{-- Thông báo lỗi (nếu có) --}}
                            @if (session('error'))
                                <div class="alert alert-danger mb-3">{{ session('error') }}</div>
                            @endif

                            <div class="row g-3">
                                {{-- Họ tên --}}
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="ten_khach" id="ten_khach"
                                            placeholder="Họ tên" required value="{{ old('ten_khach') }}">
                                        <label for="ten_khach">Họ tên *</label>
                                    </div>
                                    @error('ten_khach')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Số điện thoại --}}
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="sdt" id="sdt"
                                            placeholder="Số điện thoại" required value="{{ old('sdt') }}">
                                        <label for="sdt">Số điện thoại *</label>
                                    </div>
                                    @error('sdt')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Email --}}
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="email" class="form-control" name="email" id="email"
                                            placeholder="Email" value="{{ old('email') }}">
                                        <label for="email">Email</label>
                                    </div>
                                    @error('email')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Nghề nghiệp --}}
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="nghe_nghiep" id="nghe_nghiep"
                                            placeholder="Nghề nghiệp" value="{{ old('nghe_nghiep') }}">
                                        <label for="nghe_nghiep">Nghề nghiệp</label>
                                    </div>
                                    @error('nghe_nghiep')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- ĐÁNH GIÁ SAO (Có hiện chữ mô tả bên cạnh) --}}
                                <div class="col-12">
                                    <div class="form-group border rounded p-3 bg-light">
                                        <label class="fw-bold mb-0">Đánh giá trải nghiệm *</label>
                                        <div class="d-flex align-items-center mt-2">

                                            {{-- Khu vực các ngôi sao --}}
                                            <div class="star-rating">
                                                {{-- Sao 5 --}}
                                                <input type="radio" id="star5" name="so_sao" value="5"
                                                    checked />
                                                <label for="star5" title="Tuyệt vời">
                                                    <i class="fas fa-star"></i>
                                                </label>

                                                {{-- Sao 4 --}}
                                                <input type="radio" id="star4" name="so_sao" value="4" />
                                                <label for="star4" title="Tốt">
                                                    <i class="fas fa-star"></i>
                                                </label>

                                                {{-- Sao 3 --}}
                                                <input type="radio" id="star3" name="so_sao" value="3" />
                                                <label for="star3" title="Bình thường">
                                                    <i class="fas fa-star"></i>
                                                </label>

                                                {{-- Sao 2 --}}
                                                <input type="radio" id="star2" name="so_sao" value="2" />
                                                <label for="star2" title="Tệ">
                                                    <i class="fas fa-star"></i>
                                                </label>

                                                {{-- Sao 1 --}}
                                                <input type="radio" id="star1" name="so_sao" value="1" />
                                                <label for="star1" title="Rất tệ">
                                                    <i class="fas fa-star"></i>
                                                </label>
                                            </div>

                                            {{-- Chỗ này để hiện chữ mô tả (Thay đổi bằng JS) --}}
                                            <span id="rating-text" class="ms-3 fw-bold text-primary">Tuyệt vời</span>
                                        </div>
                                    </div>
                                    @error('so_sao')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- JAVASCRIPT XỬ LÝ HIỆN CHỮ THEO SAO (Đặt ngay dưới form hoặc cuối file đều được) --}}
                                <script>
                                    document.addEventListener("DOMContentLoaded", function() {
                                        const ratingInputs = document.querySelectorAll('.star-rating input');
                                        const ratingLabels = document.querySelectorAll('.star-rating label');
                                        const ratingText = document.getElementById('rating-text');

                                        // Định nghĩa nội dung text ứng với từng sao
                                        const messages = {
                                            5: 'Tuyệt vời',
                                            4: 'Tốt',
                                            3: 'Bình thường',
                                            2: 'Tệ',
                                            1: 'Rất tệ'
                                        };

                                        // 1. Xử lý khi người dùng click chọn sao
                                        ratingInputs.forEach(input => {
                                            input.addEventListener('change', function() {
                                                ratingText.textContent = messages[this.value];
                                                ratingText.className = "ms-3 fw-bold text-primary"; // Reset màu về primary
                                            });
                                        });

                                        // 2. Xử lý hiệu ứng Hover (Di chuột vào hiện chữ tạm thời)
                                        ratingLabels.forEach(label => {
                                            label.addEventListener('mouseenter', function() {
                                                const hoverValue = this.getAttribute('for').replace('star',
                                                ''); // Lấy số 1,2,3,4,5 từ ID
                                                ratingText.textContent = messages[hoverValue];
                                                ratingText.classList.remove('text-primary');
                                                ratingText.classList.add(
                                                'text-muted'); // Đổi màu xám khi đang hover để phân biệt
                                            });

                                            // Khi di chuột ra ngoài thì trả về text của ô đang được chọn (checked)
                                            label.addEventListener('mouseleave', function() {
                                                const checkedInput = document.querySelector('.star-rating input:checked');
                                                if (checkedInput) {
                                                    ratingText.textContent = messages[checkedInput.value];
                                                    ratingText.classList.remove('text-muted');
                                                    ratingText.classList.add('text-primary');
                                                }
                                            });
                                        });
                                    });
                                </script>

                                {{-- Nội dung --}}
                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea class="form-control" placeholder="Nội dung" name="noi_dung" id="noi_dung" style="height: 150px"
                                            required>{{ old('noi_dung') }}</textarea>
                                        <label for="noi_dung">Nội dung đánh giá *</label>
                                    </div>
                                    @error('noi_dung')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Nút gửi --}}
                                <div class="col-12">
                                    <button class="btn btn-primary w-100 py-3" type="submit">Gửi Đánh Giá</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
