<div>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">🔎 Tra cứu & Xuất hóa đơn GDT</h5>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <label>Từ ngày</label>
                    <input type="date" class="form-control" wire:model="start_date">
                </div>

                <div class="col-md-4">
                    <label>Đến ngày</label>
                    <input type="date" class="form-control" wire:model="end_date">
                </div>

                <div class="col-md-4">
                    <label>Loại hóa đơn</label>
                    <select class="form-control" wire:model.live="vatIn">
                        <option value="0">Hóa đơn bán ra</option>
                        <option value="1">Hóa đơn mua vào</option>
                    </select>
                </div>
            </div>

            <div class="form-check mt-3">
                <input class="form-check-input" type="checkbox" wire:model="useQueue" id="useQueueCheck">
                <label class="form-check-label" for="useQueueCheck">Xử lý qua Queue</label>
            </div>

            <button class="btn btn-success mt-3" wire:click="run" wire:loading.attr="disabled" wire:target="run">
                <span wire:loading.remove wire:target="run">🚀 Chạy xử lý</span>
                <span wire:loading wire:target="run">⏳ Đang xử lý…</span>
            </button>
            <button class="btn btn-primary mt-3" wire:click="importExcel">
                📥 Import Excel vào Database
            </button>
            
        </div>

        <!-- LOG -->
        <div class="card mt-3">
            <div class="card-header bg-light">
                <h6 class="mb-0">📄 Nhật ký xử lý</h6>
            </div>

            <div class="card-body" id="logBox" style="height: 300px; overflow-y: auto; background: #f8f9fa;">
                @foreach ($logs as $line)
                    <div>{{ $line }}</div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Modal Processing -->
    <div wire:loading.delay.longest>
        <div class="modal fade show d-block" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content p-4 text-center">
                    <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status">
                        <span class="sr-only">Đang xử lý...</span>
                    </div>
                    <h5 class="mb-2">Đang thực hiện lệnh lấy dữ liệu...</h5>
                    <p class="text-muted">Vui lòng chờ trong giây lát</p>
                </div>
            </div>
            <div class="modal-backdrop fade show"></div>
        </div>

        <script>
            // Auto scroll log xuống cuối
            document.addEventListener('livewire:load', () => {
                Livewire.hook('message.processed', () => {
                    const box = document.getElementById('logBox');
                    Livewire.on('scroll-bottom', () => {
                        box.scrollTop = box.scrollHeight;
                    });
                });
            });
        </script>

        {{-- Poll cache logs mỗi 3s --}}
        {{-- <div wire:poll.3s="pollLogs"></div> --}}
    </div>
