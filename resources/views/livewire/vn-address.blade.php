<div class="container mt-4">
    <div class="card mt-5">
        <h3 class="card-header p-3">
            Laravel 12 Livewire Dependent Provinces & Wards Dropdown Example
        </h3>
        <div class="card-body">
            <form>
                {{-- Dropdown Provinces --}}
                <div class="form-group mb-3">
                    <label for="provinces-dropdown" class="mb-1">Chọn Tỉnh/Thành phố</label>
                    <select wire:model.live="selectedProvince" id="provinces-dropdown" class="form-control">
                        <option value="">-- Chọn Tỉnh/Thành phố --</option>
                        @foreach ($provinces as $province)
                            <option value="{{ $province->code }}">{{ $province->full_name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Dropdown Wards --}}
                <div class="form-group mb-3">
                    <label for="wards-dropdown" class="mb-1">Chọn Xã/Phường</label>
                    <select wire:model="selectedWard" id="wards-dropdown" class="form-control" @disabled(empty($wards))>
                        <option value="">-- Chọn Xã/Phường --</option>
                        @foreach ($wards as $ward)
                            <option value="{{ $ward->id }}">{{ $ward->full_name }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>
</div>
