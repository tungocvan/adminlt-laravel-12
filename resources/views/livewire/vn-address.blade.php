@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />  
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@stop
@section('js')
    
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> 
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    window.onload = function () {
        const map = L.map('map').setView([21.0285, 105.8542], 13);
        let marker;
        let isSearching = false;

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        map.on('click', function (e) {
            const { lat, lng } = e.latlng;
            if (marker) map.removeLayer(marker);
            marker = L.marker([lat, lng]).addTo(map).bindPopup(`Lat: ${lat}, Lng: ${lng}`).openPopup();

            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
        });

        function buildFullQuery() {
            const street = document.getElementById('streetInput').value.trim();
            const ward = document.getElementById('wards-dropdown').selectedOptions[0]?.text ?? '';
            const province = document.getElementById('provinces-dropdown').selectedOptions[0]?.text ?? '';

            const fullQuery = `${street}, ${ward}, ${province}, Việt Nam`;
            document.getElementById('fullAddressPreview').value = fullQuery;

            return { fullQuery, street };
        }

        window.searchAddress = function () {
            const { fullQuery, street } = buildFullQuery();
            if (!street || isSearching) return;

            isSearching = true;
            const btn = document.getElementById('searchBtn');
            btn.disabled = true;
            btn.innerText = 'Đang tìm...';

            const firstWord = street.split(' ')[0];

            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(fullQuery)}&countrycodes=vn`)
                .then(res => res.json())
                .then(data => {
                    if (data.length > 0) {
                        const lat = data[0].lat;
                        const lon = data[0].lon;
                        let fullAddress = data[0].display_name;

                        if (!fullAddress.startsWith(firstWord)) {
                            fullAddress = `${firstWord} ${fullAddress}`;
                        }

                        document.getElementById('streetInput').value = fullAddress;
                        document.getElementById('latitude').value = lat;
                        document.getElementById('longitude').value = lon;

                        if (marker) map.removeLayer(marker);
                        marker = L.marker([lat, lon]).addTo(map)
                            .bindPopup(fullAddress)
                            .openPopup();

                        map.setView([lat, lon], 15);
                    } else {
                        alert("Không tìm thấy địa chỉ!");
                    }
                });
        }

        document.getElementById('streetInput').addEventListener('input', function () {
            const btn = document.getElementById('searchBtn');
            btn.disabled = false;
            btn.innerText = 'Tìm địa điểm';
            isSearching = false;
            buildFullQuery();
        });

        document.getElementById('provinces-dropdown').addEventListener('change', buildFullQuery);
        document.getElementById('wards-dropdown').addEventListener('change', buildFullQuery);
    }
</script>
<script>
         $('#provinces-dropdown').select2({ theme: "classic" }).off('change').on('change', function (e) {
               @this.set('selectedProvince', this.value);                                  
         });
         

        //   $('#wards-dropdown').select2({ theme: "classic" });
       
       Livewire.on('refreshSelect2', () => {
            // $('#wards-dropdown').select2({ theme: "classic" }).off('change').on('change', function (e) {
            //     Livewire.emit('setSelectedWard', this.value);                                 
            // });
           
            setTimeout(() => {
                $('#provinces-dropdown').select2({ theme: "classic" }).off('change').on('change', function () {    
                    @this.set('selectedProvince', this.value);                
                    //Livewire.emit('selectedProvince', this.value);                    
                });            
            }, 100);
            
            setTimeout(() => {
                $('#wards-dropdown').select2({ theme: "classic" }).off('change').on('change', function () {                            
                    @this.set('selectedWard', this.value);                 
                });            
            }, 100);

           
        });
        
       



</script>
@stop
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
                    <select wire:model="selectedProvince" id="provinces-dropdown" class="form-control select2">
                        <option value="">-- Chọn Tỉnh/Thành phố --</option>
                        @foreach ($provinces as $province)
                            <option value="{{ $province->code }}" {{ $province->code == $selectedProvince ? 'selected' : '' }}>
                                {{ $province->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                {{-- Dropdown Wards --}}
                <div class="form-group mb-3">
                    <label for="wards-dropdown" class="mb-1">Chọn Xã/Phường</label>                    
                    <select wire:model="selectedWard" id="wards-dropdown" class="form-control select2" @disabled(empty($wards))>
                        <option value="">-- Chọn Xã/Phường --</option>
                        @foreach ($wards as $ward)
                            <option value="{{ $ward->id }}" {{ $ward->id == $selectedWard ? 'selected' : '' }}>
                                {{ $ward->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
      
                {{-- Nhập số nhà / tên đường --}}
                <div class="form-group mb-3">
                    <label>Số nhà / Tên đường</label>
                    <input type="text" id="streetInput" class="form-control" placeholder="VD: 36 Nguyễn Minh Hoàng">
                </div>

                {{-- Hiển thị địa chỉ đầy đủ sẽ được tìm kiếm --}}
                <div class="form-group mb-3">
                    <label>Địa chỉ đầy đủ sẽ tìm:</label>
                    <input type="text" id="fullAddressPreview" class="form-control text-muted" readonly>
                </div>

                <div class="form-group mb-3">
                    <button type="button" id="searchBtn" onclick="searchAddress()" class="btn btn-primary">
                        Tìm địa điểm
                    </button>
                </div>

                <div class="form-group mb-3">
                    <label>Bản đồ</label>
                    <div wire:ignore>
                        <div id="map" style="height: 400px; width: 100%; border: 1px solid #ccc;"></div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label>Latitude</label>
                    <input type="text" id="latitude" name="latitude" class="form-control">
                </div>

                <div class="form-group mb-3">
                    <label>Longitude</label>
                    <input type="text" id="longitude" name="longitude" class="form-control">
                </div>
            </form>
        </div>
    </div>
    
{{-- Leaflet & Scripts --}}

</div>

