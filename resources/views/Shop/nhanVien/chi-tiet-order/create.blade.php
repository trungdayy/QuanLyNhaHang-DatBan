@extends('layouts.Shop.layout-nhanvien')

@section('title', 'Thêm món vào Order')

@section('content')
<main class="app-content">

    <h3 class="fw-bold mb-3">Thêm món vào Order #{{ $order->id }}</h3>
    <p class="mb-3"><b>Bàn:</b> {{ $order->banAn->so_ban ?? 'Không xác định' }}</p>
    <hr>

    <div class="container d-flex flex-wrap gap-4">
        {{-- Danh sách món ăn --}}
        <div id="menu-container" class="flex-grow-1" style="min-width: 600px;">
            @foreach($monAns as $mon)
            <div class="card mb-3 d-flex flex-row align-items-center p-3 mon-card shadow-sm rounded-4"
                data-id="{{ $mon->id }}"
                data-ten="{{ $mon->ten_mon }}"
                data-gia="{{ $mon->gia }}"
                data-loai="{{ $mon->loai_mon }}">
                <img src="{{ asset($mon->hinh_anh ?? 'https://placehold.co/60x60') }}"
                    alt="{{ $mon->ten_mon }}" class="me-3" style="width:70px;height:70px;object-fit:cover;border-radius:12px;">
                <div class="flex-grow-1">
                    <h6 class="mb-1 fw-semibold">{{ $mon->ten_mon }}</h6>
                    <small class="text-muted">Giá: {{ number_format($mon->gia,0,',','.') }}đ | Loại: {{ $mon->loai_mon }}</small>
                </div>
                <button class="btn btn-success btn-lg add-to-cart rounded-circle d-flex align-items-center justify-content-center ms-3 shadow-sm"
                    style="width:50px; height:50px;">
                    <i class="bi bi-plus fs-5"></i>
                </button>
            </div>
            @endforeach
        </div>

        {{-- Giỏ hàng --}}
        <aside style="width: 320px;">
            <div class="card p-3 shadow-sm rounded-4 border-0" style="background: #ffffff;">
                <h5 class="fw-semibold mb-3"><i class="bi bi-cart3 me-2"></i>Giỏ hàng</h5>
                <ul id="cart-items" class="list-unstyled"></ul>
                <button id="submit-order-btn" class="btn btn-primary w-100 mt-3 rounded-pill shadow-sm py-2 fs-6">
                    <i class="bi bi-send me-1"></i> Thêm món ăn
                </button>
            </div>
        </aside>
    </div>

</main>

<script>
    const orderId = {{$order -> id}};
    // const orderId = {{$order -> id}};
    let cart = [];

    function renderCart() {
        const ul = document.getElementById('cart-items');
        ul.innerHTML = '';
        cart.forEach((item, index) => {
            const li = document.createElement('li');
            li.className = "d-flex justify-content-between align-items-center mb-2";
            li.innerHTML = `
                <div>
                    ${item.ten_mon} (x${item.so_luong}) <small>(${item.loai_mon})</small>
                    ${item.ghi_chu ? `<br><small class="text-muted">Ghi chú: ${item.ghi_chu}</small>` : ''}
                </div>
                <div>
                    <button class="btn btn-sm btn-secondary me-1" onclick="editNote(${index})"><i class="bi bi-pencil-square"></i></button>
                    <button class="btn btn-sm btn-danger" onclick="deleteItem(${index})"><i class="bi bi-trash"></i></button>
                </div>
            `;
            ul.appendChild(li);
        });
    }

    function addToCart(monId, tenMon, gia, loaiMon) {
        const existing = cart.find(i => i.mon_an_id == monId);
        if (existing) {
            existing.so_luong++;
        } else {
            cart.push({
                mon_an_id: monId,
                ten_mon: tenMon,
                so_luong: 1,
                ghi_chu: null,
                loai_mon: loaiMon
            });
        }
        renderCart();
    }

    function deleteItem(index) {
        if (confirm(`Bạn có chắc muốn xóa món ${cart[index].ten_mon}?`)) {
            cart.splice(index, 1);
            renderCart();
        }
    }

    function editNote(index) {
        const note = prompt(`Nhập ghi chú cho ${cart[index].ten_mon}:`, cart[index].ghi_chu || '');
        if (note !== null) {
            cart[index].ghi_chu = note.trim();
            renderCart();
        }
    }

    document.querySelectorAll('.add-to-cart').forEach(btn => {
        btn.addEventListener('click', e => {
            const card = e.target.closest('.mon-card');
            addToCart(
                card.dataset.id,
                card.dataset.ten,
                card.dataset.gia,
                card.dataset.loai
            );
        });
    });

    document.getElementById('submit-order-btn').addEventListener('click', async () => {
        if (cart.length === 0) return alert('Chọn món trước khi gửi!');
        try {
            const res = await fetch("{{ route('nhanVien.chi-tiet-order.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    order_id: {{$order -> id}},
                    // order_id: {{$order -> id}},
                    items: cart
                })
            });

            const data = await res.json();
            if (!data.success) throw new Error(data.message || 'Lỗi khi gửi order');

            alert(data.message);
            const referrer = document.referrer || "{{ route('nhanVien.order.index') }}";
            window.location.href = referrer;
        } catch (err) {
            alert(err.message);
        }
    });
</script>

<style>
    .mon-card {
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .mon-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    }

    .add-to-cart i {
        pointer-events: none;
    }

    #cart-items li {
        border-bottom: 1px solid #f0f0f0;
        padding-bottom: 6px;
    }

    #cart-items li:last-child {
        border-bottom: none;
    }

    button:focus {
        outline: none;
        box-shadow: none;
    }
</style>
@endsection
