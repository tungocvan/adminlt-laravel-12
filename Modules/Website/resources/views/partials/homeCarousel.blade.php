<div class="container my-4">
    <div class="row">
        <!-- Slider (col-md-8) -->
        <div class="col-md-8 mb-3 mb-md-0">
            <livewire:slider />  
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
