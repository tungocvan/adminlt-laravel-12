<footer class="bg-white text-dark pt-5 pb-4">
  <div class="container">
    <div class="row"> 
      <!-- Logo + mô tả -->
      <div class="col-md-3 mb-4">
        <img src="/images/logo-tnv.png" 
               alt="Logo" height="128">
        <p class="small mt-2">
          <a href="https://tungocvan.com" class="text-success">tungocvan.com</a> là website thuộc sở hữu của tungocvan.com
        </p>
      </div>

      <!-- Thông tin công ty -->
      <div class="col-md-3 mb-4">
        <h6 class="text-uppercase font-weight-bold">tungocvan.com</h6>
        <p class="mb-1"><strong>Địa chỉ:</strong></p>
        <p class="small">Tầng 8, Tòa Nhà Vincom Center Đồng Khởi, 72 Lê Thánh Tôn, P.Bến Nghé, Q.1, TP.HCM</p>
        <p class="small mb-1"><strong>Số chứng nhận ĐKKD:</strong><br>0314758651 (29/11/2017)</p>
        <p class="small mb-1"><strong>Số giấy phép TMĐT:</strong><br>0314758651/KD-1016</p>
      </div>

      <!-- Thông tin chung -->
      <div class="col-md-2 mb-4">
        <h6 class="text-uppercase font-weight-bold">Thông Tin Chung</h6>
        <ul class="list-unstyled small">
          <li><a href="#" class="text-dark">Thông tin về Buymed</a></li>
          <li><a href="#" class="text-dark">Quy chế hoạt động</a></li>
          <li><a href="#" class="text-dark">Điều khoản sử dụng</a></li>
          <li><a href="#" class="text-dark">Chính sách bảo mật</a></li>
          <li><a href="#" class="text-dark">Chính sách đổi trả</a></li>
          <li><a href="#" class="text-dark">Chính sách vận chuyển</a></li>
        </ul>
      </div>

      <!-- Hỗ trợ người dùng -->
      <div class="col-md-2 mb-4">
        <h6 class="text-uppercase font-weight-bold">Hỗ Trợ Người Dùng</h6>
        <ul class="list-unstyled small">
          <li><a href="#" class="text-dark">Câu hỏi thường gặp</a></li>
          <li><a href="#" class="text-dark">Hướng dẫn đăng sản phẩm</a></li>
          <li><a href="#" class="text-dark">Hướng dẫn đặt hàng & thanh toán</a></li>
        </ul>
        <h6 class="text-uppercase font-weight-bold mt-3">Dịch Vụ Giao Hàng</h6>
        <img src="/images/Viettel_Post_logo.jpg" alt="Giao Hàng" class="img-fluid">
      </div>

      <!-- Ứng dụng + kết nối -->
      <div class="col-md-2 mb-4 text-center">
        <h6 class="text-uppercase font-weight-bold">Tải Ứng Dụng</h6>
        <a class="openAppButton" href="exp://u.expo.dev/65def5d4-5f12-40d5-91a5-df9c677e2406/group/6d1fe5e4-e424-4038-a271-e7e276e239ad"><img src="https://cdn-web-next.thuocsi.vn/images/footer-v2/Download/appstore_v2.png" width="120" class="mb-2"></a>
        <a class="openAppButton" href="exp://u.expo.dev/65def5d4-5f12-40d5-91a5-df9c677e2406/group/6d1fe5e4-e424-4038-a271-e7e276e239ad"><img src="https://cdn-web-next.thuocsi.vn/images/footer-v2/Download/googleplay_v2.png" width="120"></a>
        <div class="mt-3">
          <img src="https://cdn-web-next.thuocsi.vn/images/home/footer-phone-new.png" alt="App" class="img-fluid" style="max-height:200px">
        </div>
      </div>
    </div>

    <hr>

    <!-- Liên hệ + mạng xã hội -->
    <div class="row">
      <div class="col-md-6 small">
        <p>Email: <a href="mailto:hotro@buymed.com">hotro@buymed.com</a></p>
        <p>Tổng đài miễn phí: <a href="tel:18002038">1800 2038</a> (T2 - CN: 8h-20h)</p>
      </div>
      <div class="col-md-6 text-md-right">
        <a href="#" class="mr-2"><img src="https://cdn-web-next.thuocsi.vn/images/footer-v2/Connect/facebook_icon.svg" width="24"></a>
        <a href="#" class="mr-2"><img src="https://cdn-web-next.thuocsi.vn/images/footer-v2/Connect/zalo_icon.svg" width="24"></a>
        <a href="#" class="mr-2"><img src="https://cdn-web-next.thuocsi.vn/images/footer-v2/Connect/linked_icon.svg" width="24"></a>
        <a href="#"><img src="https://cdn-web-next.thuocsi.vn/images/footer-v2/Connect/tiktok_icon.svg" width="24"></a>
      </div>
    </div>
  </div>
</footer>
<script>
  const appLink = "exp://u.expo.dev/65def5d4-5f12-40d5-91a5-df9c677e2406/group/6d1fe5e4-e424-4038-a271-e7e276e239ad";
  const fallbackLink = "https://expo.dev/go"; // nếu chưa có app Expo Go

  document.querySelectorAll('.openAppButton').forEach(button => {
    button.addEventListener('click', (e) => {
      e.preventDefault();

      const start = Date.now();
      window.location.href = appLink;

      // Nếu sau 1s không mở được app => chuyển hướng sang cài Expo Go
      setTimeout(() => {
        if (Date.now() - start < 1500) {
          window.location.href = fallbackLink;
        }
      }, 1000);
    });
  });
</script>

