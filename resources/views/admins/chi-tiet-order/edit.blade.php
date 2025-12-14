@extends('layouts.admins.layout-admin')

@section('title', 'Chỉnh sửa món ăn trong đơn')

@section('content')
<main class="app-content">
    <div class="app-title">
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.chi-tiet-order.index', ['order_id' => $ct->orderMon->id]) }}">
                    Chi tiết đơn #{{ $ct->orderMon->id }}
                </a>
            </li>
            <li class="breadcrumb-item active">Chỉnh sửa món</li>
        </ul>
    </div>

    <div class="tile">
        <h3 class="tile-title">Món: {{ $ct->monAn->ten_mon ?? 'N/A' }}</h3>
        <p>Đơn hàng: <strong>#{{ $ct->orderMon->id }}</strong></p>
        <p><strong>Trạng thái hiện tại:</strong> {{ ucfirst(str_replace('_', ' ', $ct->trang_thai)) }}</p>

        @php
        $trangThai = $ct->trang_thai;
        $dsTrangThai = [
        'cho_bep' => 'Chờ bếp',
        'dang_che_bien' => 'Đang chế biến',
        'da_len_mon' => 'Đã lên món',
        'huy_mon' => 'Hủy món',
        ];

        @endphp

        <div class="tile-body">
            <form method="POST" action="{{ route('admin.chi-tiet-order.update', $ct->id) }}" class="row">
                @csrf
                @method('PUT')

                <div class="col-md-12">
                    {{-- Nếu đã hủy hoặc hoàn thành thì không cho chỉnh --}}
                    @if (in_array($trangThai, ['huy_mon', 'hoan_thanh']))
                    <div class="form-group">
                        <label class="control-label">Trạng thái</label>
                        <input type="text" class="form-control"
                            value="{{ $dsTrangThai[$trangThai] }}" readonly>
                    </div>
                    @else
                    <div class="form-group">
                        <label class="control-label">Thay đổi trạng thái</label>
                        <select name="trang_thai" class="form-control" required>
                            @php
                            $allowedTransitions = [
                            'cho_bep' => ['cho_bep', 'dang_che_bien', 'huy_mon'],
                            'dang_che_bien' => ['dang_che_bien', 'da_len_mon', 'huy_mon'],
                            'da_len_mon' => ['da_len_mon'], // đã lên món thì không quay lại
                            'huy_mon' => ['huy_mon'], // hủy món thì không quay lại
                            ];
                            @endphp

                            @foreach ($dsTrangThai as $key => $label)
                            @if (in_array($key, $allowedTransitions[$trangThai] ?? []))
                            <option value="{{ $key }}" {{ $ct->trang_thai == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                            @endif
                            @endforeach
                        </select>
                        @error('trang_thai')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    @endif

                    <div class="form-group mt-3">
                        <label class="control-label">Ghi chú (Tùy chọn)</label>
                        <textarea class="form-control" name="ghi_chu" rows="3" style="resize: none;">{{ trim(old('ghi_chu', $ct->ghi_chu)) }}</textarea>
                        @error('ghi_chu')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="col-md-12 text-right mt-4">
                    @if (!in_array($trangThai, ['huy_mon', 'hoan_thanh']))
                    <button class="btn btn-primary" type="submit">
                        <i class="fa fa-fw fa-lg fa-check-circle"></i> Lưu thay đổi
                    </button>
                    @endif
                    <a href="{{ route('admin.chi-tiet-order.index', ['order_id' => $ct->orderMon->id]) }}" class="btn btn-secondary">
                        <i class="fa fa-fw fa-lg fa-times-circle"></i> Quay lại
                    </a>
                </div>
            </form>
        </div>
    </div>
</main>
@endsection