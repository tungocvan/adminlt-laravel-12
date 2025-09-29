@extends('layouts.hamada')
@section('plugins.Toastr', true)
@section('plugins.Summernote', true)
{{-- @section('plugins.Select2', true) --}}
@section('title', 'HOME PAGE')

@section('content_header')
    {{-- <h1 id="page-header">HOME PAGE1</h1> --}}
@stop

@section('header')
    <div class="container-fluid p-0">
      <img src="images/banner.webp" class="img-fluid w-100" style="height:80px; object-fit:cover;" alt="Banner">
    </div>
    <!-- Header -->
<div class="container">
      <div class="row align-items-center" style="height:72px;">
        
        <!-- Logo -->
        <div class="col-md-2 d-flex align-items-center">
          <a href="/home">
            <img src="https://cdn-web-next.thuocsi.vn/images/logo/buymed-logo.svg" 
                 alt="Logo" height="40">
          </a>
        </div>
    
        <!-- Menu center -->
        <div class="col-md-7 d-flex justify-content-center">
          <nav class="nav">
            <a class="nav-link" href="/about-us">Về chúng tôi</a>
            <a class="nav-link" href="https://thuocsi.vn/huong-dan-dat-hang-va-thanh-toan">Hướng dẫn đặt hàng</a>
            <a class="nav-link" href="https://news.thuocsi.vn">Tin tức</a>
            <a class="nav-link" href="https://hoptacbanhang.thuocsi.vn">Đăng ký bán hàng</a>
          </nav>
        </div>
    
        <!-- Login / Register -->
        <div class="col-md-3 d-flex justify-content-end">
            @livewire('hamada.hamada-content')
          <button class="btn btn-primary">Đăng Nhập</button>
        </div>
    
      </div>
</div>

            <div class="container my-4">
                  <div class="row">
            
                  <!-- Slider (col-md-8) -->
                  <div class="col-md-8 mb-3 mb-md-0">
                        <div id="homeCarousel" class="carousel slide carousel-fade" data-ride="carousel" data-interval="3000">
                        <ol class="carousel-indicators">
                        <li data-target="#homeCarousel" data-slide-to="0" class="active"></li>
                        <li data-target="#homeCarousel" data-slide-to="1"></li>
                        <li data-target="#homeCarousel" data-slide-to="2"></li>
                        </ol>
                        <div class="carousel-inner">
                        <div class="carousel-item active">
                        <img class="d-block w-100" src="images/banner/HomeBanner1.webp" alt="Banner 1">
                        </div>
                        <div class="carousel-item">
                        <img class="d-block w-100" src="images/banner/HomeBanner2.webp" alt="Banner 2">
                        </div>
                        <div class="carousel-item">
                        <img class="d-block w-100" src="images/banner/HomeBanner3.webp" alt="Banner 3">
                        </div>
                        </div>
                        <a class="carousel-control-prev" href="#homeCarousel" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#homeCarousel" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                        </a>
                  </div>
                  </div>
            
                  <!-- Form (col-md-4) -->
                  <div class="col-md-4">
                  <h5 class="text-center mb-4">Liên Hệ Đặt Hàng</h5>
                  <form>
                        <div class="form-group">
                        <input type="text" class="form-control" placeholder="Bạn Đang Tìm Sản Phẩm Gì?">
                        </div>
                        <div class="form-group">
                        <input type="text" class="form-control" placeholder="Họ Và Tên*">
                        </div>
                        <div class="form-group">
                        <input type="tel" class="form-control" placeholder="Số Điện Thoại*">
                        </div>
                        <div class="form-group">
                        <select class="form-control">
                        <option selected disabled>Bạn Là...</option>
                        <option>Nhà Thuốc</option>
                        <option>Bác Sĩ</option>
                        <option>Khác</option>
                        </select>
                        </div>
                        <div class="form-group">
                        <select class="form-control">
                        <option selected disabled>Tỉnh/Thành Phố</option>
                        <option>Hà Nội</option>
                        <option>TP. Hồ Chí Minh</option>
                        </select>
                        </div>
                        <div class="form-group">
                        <textarea class="form-control" rows="2" placeholder="Địa Chỉ"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success btn-block">LIÊN HỆ NGAY</button>
                  </form>
                  </div>
            
                  </div>
            </div>
    
    
            <div class="container my-5">
                  <div class="row text-center">
                    <!-- Card 1 -->
                    <div class="col-sm-6 col-md-3 mb-4">
                      <div class="card h-100 shadow-sm border-0">
                        <img src="https://gcs.buymed.com/thuocsi-live/web/static/img/new/TT1_New.jpg" class="card-img-top" alt="Sản phẩm đa dạng">
                        <div class="card-body">
                          <h5 class="card-title font-weight-bold">Sản phẩm đa dạng</h5>
                          <p class="card-text small">
                            Hơn 35.000 sản phẩm thuốc chính hãng, thực phẩm chức năng, vật tư và thiết bị y tế đến từ nhà máy, công ty dược phẩm và hơn 2000 nhà phân phối uy tín.
                          </p>
                        </div>
                      </div>
                    </div>
                
                    <!-- Card 2 -->
                    <div class="col-sm-6 col-md-3 mb-4">
                      <div class="card h-100 shadow-sm border-0">
                        <img src="https://gcs.buymed.com/thuocsi-live/web/static/img/new/TT2_New.jpg" class="card-img-top" alt="Mua hàng tiện lợi">
                        <div class="card-body">
                          <h5 class="card-title font-weight-bold">Mua hàng tiện lợi</h5>
                          <p class="card-text small">
                            Đặt hàng nhanh chóng trong vòng 5 giây trên một nền tảng duy nhất nhưng vẫn đảm bảo chất lượng với mức giá cạnh tranh, khuyến mãi mỗi ngày.
                          </p>
                        </div>
                      </div>
                    </div>
                
                    <!-- Card 3 -->
                    <div class="col-sm-6 col-md-3 mb-4">
                      <div class="card h-100 shadow-sm border-0">
                        <img src="https://gcs.buymed.com/thuocsi-live/web/static/img/new/TT3_New.jpg" class="card-img-top" alt="Giao hàng toàn quốc">
                        <div class="card-body">
                          <h5 class="card-title font-weight-bold">Giao hàng toàn quốc</h5>
                          <p class="card-text small">
                            Mạng lưới giao hàng rộng rãi trên khắp 63 tỉnh thành với 3 kho trung tâm xử lý đơn hàng, 120 hubs giao hàng trên toàn quốc.
                          </p>
                        </div>
                      </div>
                    </div>
                
                    <!-- Card 4 -->
                    <div class="col-sm-6 col-md-3 mb-4">
                      <div class="card h-100 shadow-sm border-0">
                        <img src="https://gcs.buymed.com/thuocsi-live/web/static/img/new/TT4_New.jpg" class="card-img-top" alt="Đội ngũ chuyên nghiệp">
                        <div class="card-body">
                          <h5 class="card-title font-weight-bold">Đội ngũ chuyên nghiệp</h5>
                          <p class="card-text small">
                            Chăm sóc khách hàng 1:1, tư vấn miễn phí, tận tình và chu đáo, đem lại trải nghiệm mua hàng cho quý khách tốt nhất.
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
    


                <div class="container my-5">
                  <!-- Tabs -->
                  <div class="text-center mb-4">
                    <button class="btn btn-outline-primary mr-2 active">
                      Buymed hướng dẫn bạn 4 bước trở thành khách hàng thuocsi.vn
                    </button>
                    <button class="btn btn-outline-secondary">
                      4 Bước xử lý đơn hàng
                    </button>
                  </div>
                
                  <div class="row">
                    <!-- Stepper -->
                    <div class="col-md-3">
                      <ul class="list-group">
                        <li class="list-group-item active">
                          <span class="badge badge-light mr-2">1</span> Điền thông tin doanh nghiệp
                        </li>
                        <li class="list-group-item">
                          <span class="badge badge-primary mr-2">2</span> Kích hoạt tài khoản
                        </li>
                        <li class="list-group-item">
                          <span class="badge badge-primary mr-2">3</span> Tra cứu sản phẩm
                        </li>
                        <li class="list-group-item">
                          <span class="badge badge-primary mr-2">4</span> Nhận ưu đãi đơn đầu
                        </li>
                      </ul>
                    </div>
                
                    <!-- Content -->
                    <div class="col-md-9">
                      <div class="card border-0 shadow-sm">
                        <div class="card-body">
                          <h4 class="card-title">Điền đầy đủ thông tin doanh nghiệp và cung cấp các loại hồ sơ sau:</h4>
                          <p class="font-weight-bold">Giấy chứng nhận:</p>
                          <ul class="pl-3">
                            <li>Thực hành tốt phân phối thuốc (GDP).</li>
                            <li>Thực hành tốt cơ sở bán lẻ thuốc (GPP).</li>
                            <li>Thực hành tốt bảo quản thuốc (GSP).</li>
                            <li>Đủ điều kiện kinh doanh dược.</li>
                            <li>Đăng ký doanh nghiệp.</li>
                          </ul>
                          <button class="btn btn-primary mt-3">Đăng Ký Ngay</button>
                        </div>
                        <img class="card-img-bottom" 
                             src="https://gcs.buymed.com/thuocsi-live/web/static/img/new/1_New.jpg" 
                             alt="step image">
                      </div>
                    </div>
                  </div>
                </div>
                

@section('content')
      
     
@stop

@section('footer')
      <h1>FOOTER</h1>     
@endsection

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}


@stop

@section('js')
     {{-- https://www.daterangepicker.com/#examples  --}}
    {{-- <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script> --}}

    

@stop
 