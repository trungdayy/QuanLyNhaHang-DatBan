<div class="dashboard-wrapper">
    <div class="dashboard-grid">
        <a href="{{ route('admin.dashboard') }}" target="_blank" class="dashboard-card card-admin">
            <i class="fa fa-cogs fa-3x"></i><span>Admin</span>
        </a>
        <a href="{{ route('home') }}" target="_blank" class="dashboard-card card-client">
            <i class="fa fa-home fa-3x"></i><span>Client</span>
        </a>
        <a href="{{ route('nhanVien.ban-an.index') }}" target="_blank" class="dashboard-card card-nv">
            <i class="fa fa-user fa-3x"></i><span>Nhân viên</span>
        </a>
        <a href="{{ route('bep.dashboard') }}" target="_blank" class="dashboard-card card-bep">
            <i class="fa fa-utensils fa-3x"></i><span>Bếp</span>
        </a>
        <a href="{{ route('oderqr.list') }}" target="_blank" class="dashboard-card card-qr">
            <i class="fa fa-qrcode fa-3x"></i><span>QR List</span>
        </a>
    </div>
</div>