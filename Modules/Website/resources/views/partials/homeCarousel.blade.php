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
                        <img class="d-block w-100" src="images/banner/HomeBanner1.webp" alt="Banner 1" />
                    </div>
                    <div class="carousel-item">
                        <img class="d-block w-100" src="images/banner/HomeBanner2.webp" alt="Banner 2" />
                    </div>
                    <div class="carousel-item">
                        <img class="d-block w-100" src="images/banner/HomeBanner3.webp" alt="Banner 3" />
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
                    <input type="text" class="form-control" placeholder="Bạn Đang Tìm Sản Phẩm Gì?" />
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Họ Và Tên*" />
                </div>
                <div class="form-group">
                    <input type="tel" class="form-control" placeholder="Số Điện Thoại*" />
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
