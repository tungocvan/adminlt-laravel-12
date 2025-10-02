<div class="container my-5">
    <div class="swiper mySwiper">
      <div class="swiper-wrapper">
  
        <div class="swiper-slide">
          <div class="card h-100 shadow-sm border-0">
            <img src="https://gcs.buymed.com/thuocsi-live/web/static/img/new/TT1_New.jpg" class="card-img-top" alt="Sản phẩm đa dạng" />
            <div class="card-body">
              <h5 class="card-title font-weight-bold">Sản phẩm đa dạng</h5>
              <p class="card-text small">
                Hơn 35.000 sản phẩm thuốc chính hãng, thực phẩm chức năng, vật tư và thiết bị y tế...
              </p>
            </div>
          </div>
        </div>
  
        <div class="swiper-slide">
          <div class="card h-100 shadow-sm border-0">
            <img src="https://gcs.buymed.com/thuocsi-live/web/static/img/new/TT2_New.jpg" class="card-img-top" alt="Mua hàng tiện lợi" />
            <div class="card-body">
              <h5 class="card-title font-weight-bold">Mua hàng tiện lợi</h5>
              <p class="card-text small">
                Đặt hàng nhanh chóng trong vòng 5 giây...
              </p>
            </div>
          </div>
        </div>
  
        <div class="swiper-slide">
          <div class="card h-100 shadow-sm border-0">
            <img src="https://gcs.buymed.com/thuocsi-live/web/static/img/new/TT3_New.jpg" class="card-img-top" alt="Giao hàng toàn quốc" />
            <div class="card-body">
              <h5 class="card-title font-weight-bold">Giao hàng toàn quốc</h5>
              <p class="card-text small">
                Mạng lưới giao hàng rộng rãi trên khắp 63 tỉnh thành...
              </p>
            </div>
          </div>
        </div>
  
        <div class="swiper-slide">
          <div class="card h-100 shadow-sm border-0">
            <img src="https://gcs.buymed.com/thuocsi-live/web/static/img/new/TT4_New.jpg" class="card-img-top" alt="Đội ngũ chuyên nghiệp" />
            <div class="card-body">
              <h5 class="card-title font-weight-bold">Đội ngũ chuyên nghiệp</h5>
              <p class="card-text small">
                Chăm sóc khách hàng 1:1, tư vấn miễn phí...
              </p>
            </div>
          </div>
        </div>
  
      </div>
    </div>
  </div>
  
  <script>
      
        document.addEventListener("livewire:navigated", () => {
            new Swiper(".mySwiper", {
            direction: "horizontal",    
            slidesPerView: 3,
            spaceBetween: 20,
            loop: true,
            speed: 3000,
            autoplay: { delay: 0, disableOnInteraction: false,reverseDirection: true, pauseOnMouseEnter: true, },
            freeMode: true,
            });
        });
  </script>
  