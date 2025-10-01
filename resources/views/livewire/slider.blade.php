<?php

use Livewire\Volt\Component;

new class extends Component {
    // Mảng ảnh mặc định (nằm trong public/images/sliders)
    public array $images = [];
    public function mount()
    {
        // Lấy tất cả ảnh từ public/images
        $files = File::files(public_path('images/sliders'));
        
        $this->images = collect($files)
            ->filter(fn($file) => in_array(strtolower($file->getExtension()), ['jpg','jpeg','png','webp','gif']))
            ->map(fn($file) => 'images/sliders/' . $file->getFilename())
            ->values()
            ->toArray();            
      }
     
}; ?>

<!-- HTML Swiper -->
<div class="container p-0">
    <div class="swiper-container" style="overflow: hidden;">
        <div class="swiper-wrapper">
            @foreach($images as $image)
                <div class="swiper-slide">
                    <img src="{{ asset($image) }}" class="img-fluid w-100" alt="Slide">
                </div>
            @endforeach
        </div>

        <!-- Nút điều hướng -->
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>

        <!-- Pagination -->
        <div class="swiper-pagination"></div>
    </div>
</div>


<script>
    document.addEventListener("livewire:navigated", () => {
        new Swiper('.swiper-container', {
            loop: true,
            slidesPerView: 1, // chỉ 1 ảnh trên 1 slide            
            autoplay: { delay: 3000 },
            effect: 'coverflow', // đổi hiệu ứng ở đây
            coverflowEffect: {
                rotate: 50,
                stretch: 0,
                depth: 100,
                modifier: 1,
                slideShadows: true,
            },
            pagination: { el: '.swiper-pagination', clickable: true },
            navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
        });
    });
    </script>
    

