@extends('layouts.shop.layout-nhanvien')

@section('title', 'Hàng chờ - ' . Auth::user()->ho_ten)

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Heebo:wght@700;800&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

{{-- 1. FILE ÂM THANH NỘI BỘ (ting_ting.mp3) --}}
<audio id="notif-sound" preload="auto">
    <source src="{{ asset('assets/audio/ting_ting.mp3') }}" type="audio/mpeg">
</audio>

<style>
    /* CSS CŨ GIỮ NGUYÊN */
    :root { --primary: #fea116; --dark: #0f172b; --white: #ffffff; --radius: 4px; --shadow-card: 0 0 45px rgba(0, 0, 0, 0.08); }
    .page-header-title { font-family: 'Heebo', sans-serif; font-weight: 800; color: var(--dark); text-transform: uppercase; position: relative; padding-left: 15px; font-size: 1.5rem; }
    .page-header-title::before { content: ''; position: absolute; left: 0; top: 50%; transform: translateY(-50%); height: 80%; width: 5px; background-color: var(--primary); border-radius: 2px; }
    .card-zone { border: none; border-radius: var(--radius); box-shadow: var(--shadow-card); background: var(--white); margin-bottom: 24px; overflow: hidden; }
    .card-zone-header { background: var(--dark); color: var(--primary); padding: 15px 20px; font-weight: 700; text-transform: uppercase; border-bottom: 3px solid var(--primary); }
    .food-item-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-bottom: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.05); transition: 0.2s; }
    .food-item-card:hover { border-color: #10b981; background: #f0fff0; }
    .food-item-card strong { font-size: 1.15rem; }
    .badge-suat { background: #ef4444; color: #fff; font-weight: 700; padding: 6px 10px; border-radius: 4px; font-size: 0.95rem; }
    .btn-ocean-served { background: #10b981; color: #fff; border: none; font-weight: 700; text-transform: uppercase; padding: 10px 20px; border-radius: 4px; transition: 0.3s; }
    .btn-ocean-served:hover { background: #059669; color: #fff; }
    
    #sound-toggle-btn { transition: all 0.3s; border: 1px solid #dee2e6; color: #6c757d; background: #fff; min-width: 130px; }
    #sound-toggle-btn.active { background-color: #10b981 !important; border-color: #10b981 !important; color: white !important; box-shadow: 0 4px 6px rgba(16, 185, 129, 0.25); }

    /* TOAST THÔNG BÁO */
    #custom-toast {
        visibility: hidden; min-width: 300px; background-color: #333; color: #fff; text-align: left; border-radius: 12px; padding: 16px;
        position: fixed; z-index: 9999; left: 50%; bottom: 30px; transform: translateX(-50%); box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        font-size: 16px; display: flex; align-items: center; justify-content: center; gap: 15px; border-left: 5px solid var(--primary);
    }
    #custom-toast.show { visibility: visible; animation: fadein 0.5s, fadeout 0.5s 6s; }
    @keyframes fadein { from {bottom: 0; opacity: 0;} to {bottom: 30px; opacity: 1;} }
    @keyframes fadeout { from {bottom: 30px; opacity: 1;} to {bottom: 0; opacity: 0;} }
    .bell-shake { animation: bellshake .5s cubic-bezier(.36,.07,.19,.97) both; color: var(--primary); }
    @keyframes bellshake { 0% { transform: rotate(0); } 15% { transform: rotate(5deg); } 30% { transform: rotate(-5deg); } 45% { transform: rotate(4deg); } 60% { transform: rotate(-4deg); } 75% { transform: rotate(2deg); } 85% { transform: rotate(-2deg); } 100% { transform: rotate(0); } }

    /* --- CSS CHO BONG BÓNG CHAT & POPUP (ĐÃ SỬA LỖI CLICK) --- */
    .floating-chat-btn {
        position: fixed;
        bottom: 120px; /* Vị trí nổi */
        right: 30px;
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, var(--dark), #1a243a); 
        color: var(--primary);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
        cursor: pointer;
        transition: transform 0.2s;
        z-index: 10000; /* Z-index RẤT CAO để không bị lớp nào che */
    }
    .floating-chat-btn:hover {
        transform: scale(1.05);
    }

    .notif-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background-color: #ff4d4f; 
        color: white;
        font-size: 0.75rem;
        font-weight: 800;
        padding: 3px 8px;
        border-radius: 12px;
        border: 2px solid var(--white);
        animation: pulse 1s infinite;
        z-index: 10001;
    }

    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(255, 77, 79, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(255, 77, 79, 0); }
        100% { box-shadow: 0 0 0 0 rgba(255, 77, 79, 0); }
    }

    /* POPUP MODAL */
    #notif-modal {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0, 0, 0, 0.7); 
        z-index: 15000; /* Z-index cao hơn cả nút bấm */
        display: none;
        align-items: center;
        justify-content: center;
    }
    #notif-modal.active { display: flex; }

    .notif-modal-box {
        background: #f8fafc;
        width: 95%;
        max-width: 450px;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4);
        animation: zoomIn 0.2s ease-out;
    }
    .notif-modal-header {
        background: var(--dark);
        color: var(--primary);
        padding: 18px 25px; 
        font-weight: 800;
        font-size: 1.1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 3px solid var(--primary);
    }
    .notif-item {
        padding: 15px 20px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #fff;
        transition: background-color 0.2s;
    }
    .notif-item:hover {
        background: #f8fafc;
    }
    .notif-item-content {
        flex-grow: 1;
        padding-right: 10px;
    }
    .notif-item-content strong {
        color: #ff4d4f; 
        font-weight: 700;
        display: block;
    }
    .notif-item-content small {
        color: #64748b;
        font-size: 0.85rem;
    }

    .btn-mark-read {
        background: #10b981; 
        color: #fff;
        padding: 8px 15px;
        border: none;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 700;
        cursor: pointer;
        transition: 0.2s;
        white-space: nowrap; 
    }
    .btn-mark-read:hover {
        background: #059669;
    }
    
    .notif-modal-footer .btn-ocean-served {
        padding: 12px 20px;
        border-radius: 10px;
        font-size: 1rem;
    }

    /* MOBILE RESPONSIVE */
    @media (max-width: 767px) {
        .food-item-card { flex-direction: column; align-items: flex-start !important; padding: 15px; }
        .food-item-card > div { width: 100%; margin-top: 10px; }
        .btn-ocean-served { width: 100%; }
        .page-header-title { font-size: 1.4rem; padding-left: 10px; }
        .status-text { display: none; }

        .floating-chat-btn {
            bottom: 80px; 
            right: 15px;
            width: 50px;
            height: 50px;
            font-size: 1.2rem;
        }
    }
</style>

<div class="container-fluid py-4 px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="page-header-title m-0">Hàng chờ phục vụ</h4>
            <p class="text-muted small ms-3 mb-0 mt-1 d-none d-sm-block">Hệ thống tự động cập nhật món ăn & yêu cầu</p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            
            {{-- NÚT BẬT/TẮT TIẾNG --}}
            <button id="sound-toggle-btn" class="btn btn-white shadow-sm fw-bold">
                <i class="fas fa-volume-mute me-1"></i> <span>Bật tiếng</span>
            </button>
            
            <span id="status-indicator" class="btn btn-white shadow-sm border fw-bold text-dark disabled" style="cursor: default;">
                <i class="fa-solid fa-rotate text-primary fa-spin-slow me-2"></i> <span class="status-text">Đang tải...</span>
            </span>
        </div>
    </div>

    <div class="card-zone">
        <div class="card-zone-header">
            <span><i class="fas fa-utensils me-2"></i> Danh sách chờ bưng</span>
        </div>
        <div class="card-body p-4 bg-light-subtle">
            <div id="food-queue-container"></div>
            <div id="loading-indicator" class="text-center py-4" style="display: none;">
                <i class="fas fa-spinner fa-spin text-primary fa-2x"></i>
            </div>
        </div>
    </div>
</div>

<div id="custom-toast">
    <i class="fas fa-bell fa-2x bell-shake"></i>
    <div>
        <strong id="toast-title" style="display: block; font-size: 1.1em;">BẾP RA MÓN!</strong>
        <span id="toast-message">Có món mới hoàn thành!</span>
    </div>
</div>

{{-- NÚT BONG BÓNG CHAT NỔI --}}
<div class="floating-chat-btn" id="floating-chat-btn" onclick="openNotifModal()">
    <i class="fa-solid fa-bell"></i>
    <span class="notif-badge" id="notif-counter" style="display:none;">0</span>
</div>

{{-- POPUP DANH SÁCH HÀNG CHỜ HỖ TRỢ --}}
<div id="notif-modal" class="modal-overlay">
    <div class="notif-modal-box">
        <div class="notif-modal-header">
            <span><i class="fa-solid fa-triangle-exclamation me-2"></i> YÊU CẦU HỖ TRỢ KHẨN</span>
            <button class="btn-close" onclick="closeNotifModal()"><i class="fa-solid fa-xmark" style="color:var(--primary);"></i></button>
        </div>
        <div class="modal-body p-0" id="notif-list-body" style="max-height: 400px; overflow-y: auto;">
            {{-- Nội dung thông báo sẽ được render bằng JS --}}
        </div>
        <div class="modal-footer p-3 bg-white" id="notif-modal-footer" style="display:none;">
            <button class="btn-ocean-served w-100" onclick="markAllVisibleRead()">
                <i class="fa-solid fa-check"></i> Đánh dấu TẤT CẢ đã xem
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        const nhanVienId = {{ Auth::id() }}; 
        const queueContainer = $('#food-queue-container');
        const statusIndicator = $('#status-indicator');
        const loadingIndicator = $('#loading-indicator');
        const soundBtn = $('#sound-toggle-btn');
        const audio = document.getElementById("notif-sound");
        const notifCounter = $('#notif-counter');

        // Biến lưu trữ
        let previousIds = [];      
        let processedNotifIds = []; 
        let isFirstLoad = true;    
        let audioEnabled = false;
        let activeAlertNotifs = []; 

        // 1. XỬ LÝ NÚT BẬT TIẾNG
        soundBtn.on('click', function() {
            if (!audioEnabled) {
                audio.play().then(() => {
                    audio.pause(); audio.currentTime = 0;
                    audioEnabled = true;
                    soundBtn.addClass('active');
                    soundBtn.find('i').attr('class', 'fas fa-volume-up me-1');
                    soundBtn.find('span').text('Đã bật tiếng');
                    setTimeout(() => audio.play(), 200);
                }).catch(err => alert("Lỗi: Trình duyệt chặn tiếng. Hãy thử click lại!"));
            } else {
                audioEnabled = false;
                soundBtn.removeClass('active');
                soundBtn.find('i').attr('class', 'fas fa-volume-mute me-1');
                soundBtn.find('span').text('Bật tiếng');
            }
        });

        // 2. PHÁT THÔNG BÁO CHUNG
        function showNotification(title, message, type = 'food') {
            if (audioEnabled) {
                audio.currentTime = 0;
                audio.play().catch(e => console.log("Lỗi loa:", e));
            }
            
            const toast = document.getElementById("custom-toast");
            const titleEl = document.getElementById("toast-title");
            const msgEl = document.getElementById("toast-message");
            const iconEl = toast.querySelector('i');

            toast.className = ""; 
            void toast.offsetWidth; 

            // Cấu hình giao diện Toast dựa theo loại
            if (type === 'alert') {
                toast.style.borderLeft = "5px solid #ff4d4f"; 
                toast.style.backgroundColor = "#ff4d4f"; 
                titleEl.style.color = "#fff";
                msgEl.style.color = "#fff";
                iconEl.style.color = "#fff";
                iconEl.classList.remove('fa-bell');
                iconEl.classList.add('fa-triangle-exclamation'); 
            } else {
                toast.style.borderLeft = "5px solid #fea116"; 
                toast.style.backgroundColor = "#333"; 
                titleEl.style.color = "#fea116";
                msgEl.style.color = "#fff";
                iconEl.style.color = "#fea116";
                iconEl.classList.remove('fa-triangle-exclamation');
                iconEl.classList.add('fa-bell'); 
            }

            titleEl.innerText = title;
            msgEl.innerText = message;
            toast.className = "show";
            
            setTimeout(() => { toast.className = ""; }, 6000);
        }

        // 3. RENDER MÓN ĂN
        function renderFoodItem(mon) {
            const updateTime = mon.updated_at ? new Date(mon.updated_at).toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' }) : 'Vừa xong';
            const banSo = mon.order_mon && mon.order_mon.ban_an ? mon.order_mon.ban_an.so_ban : 'N/A';
            
            return `
                <div class="food-item-card d-flex justify-content-between align-items-center animate__animated animate__fadeIn" id="item-${mon.id}">
                    <div>
                        <p class="mb-1">
                            <strong>${mon.mon_an.ten_mon}</strong>
                            <span class="badge-suat ms-2">${mon.so_luong} suất</span>
                        </p>
                        <small class="text-muted">
                            <i class="fas fa-table"></i> Bàn: <strong>${banSo}</strong>
                            <span class="text-success fw-bold ms-2"><i class="fas fa-check-circle"></i> Xong lúc: ${updateTime}</span>
                        </small>
                    </div>
                    <div>
                        <button class="btn btn-ocean-served btn-sm" onclick="xacNhanDaBung(${mon.id})">
                            <i class="fas fa-check"></i> Đã lên
                        </button>
                    </div>
                </div>
            `;
        }

        // 4. MARK AS READ API
        // Đã sửa lại route trong file routes/web.php ở bước trước
        window.markNotifRead = function(ids) {
            const idsArray = Array.isArray(ids) ? ids : [ids];
            axios.post('{{ route('nhanVien.phuc-vu.mark_read') }}', { ids: idsArray })
                .then(() => {
                    fetchFoodQueue(); 
                    closeNotifModal();
                })
                .catch(e => {
                    console.error("Lỗi đánh dấu đã xem:", e);
                    alert("Lỗi: Không thể đánh dấu đã xem. Vui lòng thử lại!");
                });
        }

        // 5. RENDER POPUP YÊU CẦU HỖ TRỢ
        function renderNotifModal() {
            const body = $('#notif-list-body');
            const footer = $('#notif-modal-footer');
            
            if (activeAlertNotifs.length === 0) {
                body.html('<p class="text-center text-secondary py-4 m-0">🎉 Không có yêu cầu hỗ trợ mới.</p>');
                notifCounter.hide().text(0);
                footer.hide();
                $('#floating-chat-btn').find('.fa-bell').css('color', 'var(--primary)');
                return;
            }

            // Cập nhật số lượng thông báo
            notifCounter.show().text(activeAlertNotifs.length);
            $('#floating-chat-btn').find('.fa-bell').css('color', '#ff4d4f'); 
            footer.show();

            let html = '';
            activeAlertNotifs.forEach(n => {
                const timeStr = new Date(n.created_at).toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
                const banSo = n.dat_ban?.ban_an?.so_ban || 'N/A';
                
                html += `
                    <div class="notif-item" id="notif-item-${n.id}">
                        <div class="notif-item-content">
                            <strong>${n.noi_dung}</strong>
                            <small>Bàn ${banSo} • ${timeStr}</small>
                        </div>
                        <button class="btn-mark-read" onclick="markNotifRead(${n.id})">Đã xử lý</button>
                    </div>
                `;
            });
            body.html(html);
        }

        // 6. XỬ LÝ SỰ KIỆN POPUP
        window.openNotifModal = function() {
            $('#notif-modal').addClass('active');
            renderNotifModal();
        }

        window.closeNotifModal = function() {
            $('#notif-modal').removeClass('active');
        }

        window.markAllVisibleRead = function() {
            if (activeAlertNotifs.length === 0) return;
            const ids = activeAlertNotifs.map(n => n.id);
            markNotifRead(ids);
        }
        
        // 7. FETCH DATA (POLLING)
        function fetchFoodQueue() {
            axios.get("{{ route('nhanVien.phuc-vu.dashboard_api') }}")
                .then(response => {
                    const data = response.data;
                    
                    // --- A. XỬ LÝ DANH SÁCH MÓN ĂN ---
                    const newItems = data.monChoPhucVu || [];
                    let newHtml = '';
                    
                    if (newItems.length > 0) {
                        const currentIds = newItems.map(item => item.id);
                        if (!isFirstLoad) {
                            const newDishes = currentIds.filter(id => !previousIds.includes(id));
                            if (newDishes.length > 0) {
                                showNotification('BẾP RA MÓN!', `Có ${newDishes.length} món mới vừa hoàn thành.`, 'food');
                            }
                        }
                        previousIds = currentIds;
                        newItems.forEach(mon => { newHtml += renderFoodItem(mon); });
                    } else {
                        previousIds = [];
                        newHtml = `<div class="text-center text-secondary py-4">✅ Không có món nào chờ phục vụ.</div>`;
                    }
                    
                    if (queueContainer.html() !== newHtml) queueContainer.html(newHtml);

                    // --- B. XỬ LÝ THÔNG BÁO KHÁCH GỌI ---
                    const notifs = data.thongBao || [];
                    
                    // Lọc ra các thông báo loại 'goi_phuc_vu' và lưu vào biến global
                    activeAlertNotifs = notifs.filter(n => n.loai === 'goi_phuc_vu');
                    renderNotifModal(); // Cập nhật số lượng trên icon nổi ngay

                    if (notifs.length > 0) {
                        const newAlertNotifs = notifs.filter(n => 
                            n.loai === 'goi_phuc_vu' && !processedNotifIds.includes(n.id)
                        );
                        
                        if (newAlertNotifs.length > 0) {
                            const lastNotif = newAlertNotifs[0]; 
                            showNotification('KHẨN CẤP: GỌI HỖ TRỢ!', lastNotif.noi_dung, 'alert');
                            
                            newAlertNotifs.forEach(n => processedNotifIds.push(n.id));
                        }
                    }

                    isFirstLoad = false;
                    const now = new Date().toLocaleTimeString('vi-VN');
                    statusIndicator.html(`<i class="fa-solid fa-check text-success me-2"></i> <span class="status-text">Cập nhật lúc ${now}</span>`);
                })
                .catch(err => {
                    console.error("LỖI POLLING API:", err);
                    statusIndicator.html('<i class="fa-solid fa-triangle-exclamation text-danger me-2"></i> Lỗi');
                })
                .finally(() => loadingIndicator.hide());
        }

        // 8. GLOBAL FUNCTION FOR ONCLICK (GIỮ NGUYÊN)
        window.xacNhanDaBung = function(chiTietOrderId) {
            const url = "{{ route('nhanVien.phuc-vu.confirm_served', ['id' => 'TEMP_ID']) }}".replace('TEMP_ID', chiTietOrderId);
            $(`#item-${chiTietOrderId} button`).html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
            axios.post(url).then(() => fetchFoodQueue()).catch(err => { 
                alert('Lỗi: ' + (err.response?.data?.message || err.message)); 
                fetchFoodQueue(); 
            });
        }

        fetchFoodQueue();
        setInterval(fetchFoodQueue, 5000);
    });
</script>
@endpush
@endsection