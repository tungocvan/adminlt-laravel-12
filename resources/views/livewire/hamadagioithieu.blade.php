<?php

use Livewire\Volt\Component;

new class extends Component
{
    public string $tab = 'mangluoi';

    public function setTab(string $tab)
    {
        $this->tab = $tab;
    }
}; ?>

<div class="container my-5">
    <!-- Tiêu đề -->
    <div class="mb-4 text-center">
        <h4 class="font-weight-bold d-inline-block border-bottom border-success pb-2">
            Giới thiệu về công ty
        </h4>
    </div>

    <div class="row align-items-center">
        <!-- Cột trái -->
        <div class="col-md-6 mb-4">
            <p>
                Công ty TNHH Buymed được thành lập vào năm 2017, là một start-up hướng đến mục tiêu 
                cách mạng hóa ngành chăm sóc sức khỏe trên toàn Châu Á.
            </p>
            <p>
                Buymed bắt đầu sứ mệnh của mình từ một văn phòng nhỏ tại Singapore và chỉ sau một thời gian ngắn, 
                chúng tôi đã mở rộng thành một tổ chức đa quốc gia trong khu vực Đông Nam Á với quy mô hơn 500 nhân viên.
            </p>
            <p>
                Chúng tôi phát triển mạng lưới phân phối dược phẩm, sản phẩm chăm sóc sức khỏe và trang thiết bị 
                vật tư y tế với nguồn cung 100% đến từ các nhà sản xuất uy tín trên toàn bộ Việt Nam và Đông Nam Á.
            </p>
        </div>

        <!-- Cột phải -->
        <div class="col-md-6 mb-4 text-center">
            <img src="https://gcs.buymed.com/thuocsi-live/web/static/img/new/GT1_NEW.jpg?quality=100" 
                 alt="Giới thiệu công ty"
                 class="img-fluid rounded shadow">
        </div>
    </div>

    <!-- Tabs -->
    <div class="mt-4 text-center">
        <ul class="nav nav-pills justify-content-center">
            <li class="nav-item">
                <a href="#" wire:click.prevent="setTab('mangluoi')" 
                   class="nav-link {{ $tab === 'mangluoi' ? 'active' : '' }}">
                    Mạng lưới
                </a>
            </li>
            <li class="nav-item">
                <a href="#" wire:click.prevent="setTab('tamnhin')" 
                   class="nav-link {{ $tab === 'tamnhin' ? 'active' : '' }}">
                    Tầm nhìn
                </a>
            </li>
            <li class="nav-item">
                <a href="#" wire:click.prevent="setTab('sumenh')" 
                   class="nav-link {{ $tab === 'sumenh' ? 'active' : '' }}">
                    Sứ mệnh
                </a>
            </li>
        </ul>
    </div>

    <!-- Nội dung tab với hiệu ứng slide -->
    <div id="tabCarousel" class="carousel slide mt-3" data-ride="carousel" data-interval="false">
        <div class="carousel-inner">
            <div class="carousel-item {{ $tab === 'mangluoi' ? 'active' : '' }}">
                <div class="card card-body">
                    <p>Mạng lưới phân phối dược phẩm, sản phẩm chăm sóc sức khỏe và thiết bị y tế phủ rộng khắp Việt Nam và Đông Nam Á.</p>
                </div>
            </div>
            <div class="carousel-item {{ $tab === 'tamnhin' ? 'active' : '' }}">
                <div class="card card-body">
                    <p>Tầm nhìn: Trở thành nền tảng thương mại điện tử dược phẩm số 1 khu vực Đông Nam Á.</p>
                </div>
            </div>
            <div class="carousel-item {{ $tab === 'sumenh' ? 'active' : '' }}">
                <div class="card card-body">
                    <p>Sứ mệnh: Kết nối các nhà sản xuất, nhà thuốc và bệnh viện, mang đến dịch vụ chăm sóc sức khỏe tốt hơn cho cộng đồng.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("livewire:navigated", function () {
    // Reset lại carousel về slide đang active
    $('#tabCarousel').carousel('dispose').carousel({ interval: false });
});
</script>

