<div wire:ignore class="d-flex flex-column align-items-center gap-3 select-none">

    <!-- Wheel container -->
    <div class="wheel-container position-relative" style="width:380px; height:380px;">
        @foreach($rings as $i => $ring)
            <div class="ring position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
                <img src="{{ $ring['image'] }}" 
                     class="rotable" 
                     data-ring="{{ $i }}"
                     style="max-width:100%; max-height:100%; object-fit:contain; transform: rotate(0deg);">
            </div>
        @endforeach

        <!-- Labels -->
        @foreach($labels as $index => $label)
            <div class="position-absolute" style="
                width:32px; height:32px;
                left:50%; top:50%;
                transform: rotate({{ $index * (360/count($labels)) }}deg) translate(-50%, -38%) rotate(-{{ $index * (360/count($labels)) }}deg);
            ">
                <img src="{{ $label['icon'] }}" class="w-100 h-100 object-fit-contain" alt="{{ $label['name'] }}">
            </div>
        @endforeach

        <!-- Arrow -->
        <div class="wheel-arrow">
            <svg width="36" height="44" viewBox="0 0 24 34" fill="none">
                <path d="M1.01625 33L12 1.99725L22.9837 33H1.01625Z" fill="#FF2C2C" stroke="white"/>
            </svg>
        </div>
    </div>

    <!-- Spin Button -->
    <button id="spinBtn" class="btn btn-warning text-danger fw-bold shadow">Bắt đầu</button>

    <!-- Result -->
    <div id="resultText" class="text-dark fw-semibold mt-2"></div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const rings = @json($rings);
    const labels = @json($labels);

    let currentAngle = 0;
    const spinBtn = document.getElementById('spinBtn');
    const resultText = document.getElementById('resultText');

    spinBtn.addEventListener('click', function() {
        spinBtn.disabled = true;
        resultText.textContent = 'Đang quay...';

        const labelsCount = labels.length;          // 60 phần
        const anglePerLabel = 360 / labelsCount;    
        const arrowOffset = 180;                    // arrow ở dưới

        // Chọn ngẫu nhiên phần trúng
        const index = Math.floor(Math.random() * labelsCount);
        const randomOffset = Math.random() * anglePerLabel;
        const targetAngle = 360*5 + (index*anglePerLabel) + randomOffset; // 5 vòng + target

        // Quay tất cả vòng
        rings.forEach((r, i) => {
            const img = document.querySelector(`.rotable[data-ring="${i}"]`);
            if(!img) return;

            const spins = r.spins || 5;
            const duration = r.duration || 4;
            const easing = r.easing || 'cubic-bezier(.25,.9,.25,.98)';
            const angle = currentAngle + targetAngle;

            img.style.transition = `transform ${duration}s ${easing}`;
            img.style.transform = `rotate(${angle}deg)`;
        });

        currentAngle += targetAngle;

        // Sau animation, tính kết quả chính xác
        const maxDuration = Math.max(...rings.map(r => r.duration)) * 1000 + 100; // buffer 0.1s
        setTimeout(() => {
            const angleAtArrow = (currentAngle + arrowOffset) % 360;
            const idx = Math.floor(angleAtArrow / anglePerLabel) % labelsCount;
            resultText.innerText = `Kết quả: ${idx+1} - ${labels[idx].name}`;
            spinBtn.disabled = false;

            // Gọi Livewire nếu muốn lưu kết quả
            @this.call('calculateResult', currentAngle);
        }, maxDuration);
    });
});
</script>

<style>
.wheel-container img.rotable {
    display: block !important;
    margin: 0 !important;
    padding: 0 !important;
    transform-origin: center center;
}

.wheel-arrow {
    position: absolute;
    left: 50%;
    bottom: -12px;
    transform: translateX(-50%);
    z-index: 20;
}
</style>
