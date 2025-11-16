<div class="card mb-3">
    <div class="card-body d-flex align-items-center">

        <button class="btn btn-success mr-2" wire:click="exportJson">
            <i class="fas fa-download"></i> Xuất JSON
        </button>

        <div x-data="{open:false}" class="position-relative">
            <button class="btn btn-info" @click="open = !open">
                <i class="fas fa-upload"></i> Nhập JSON
            </button>

            <div x-show="open"
                 class="border p-3 bg-light position-absolute mt-2"
                 style="z-index: 999;background:white;width:250px">

                <input type="file" wire:model="jsonFile" class="form-control mb-2">

                <button class="btn btn-primary btn-sm" wire:click="importJson">
                    Xác nhận nhập
                </button>
            </div>
        </div>

    </div>
</div>
