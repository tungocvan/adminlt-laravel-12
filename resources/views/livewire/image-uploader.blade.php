<div class="container mt-4" x-data="{
    previews: [],
    uploadedIndexes: [],
    progress: {}, // Trạng thái tiến trình của từng file

    resetFileInput() {
        this.previews.forEach(preview => URL.revokeObjectURL(preview.url));
        this.previews = [];
        this.uploadedIndexes = [];
        this.progress = {};
        @this.cancelAll();
    },

    uploadImage(index) {
        this.progress[index] = 0; // Bắt đầu từ 0%
        @this.uploadImage(index, (percentage) => {
            this.progress[index] = percentage; // Cập nhật tiến trình
        }).then(() => {
            this.uploadedIndexes.push(index);
            this.progress[index] = 100; // Hoàn thành
        });
    },

    uploadAll() {
        this.previews.forEach((_, index) => {
            if (!this.uploadedIndexes.includes(index)) {
                this.uploadImage(index);
            }
        });
    }
}">


    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Upload Ảnh -{{ $countFiles }}</h3>            
        </div>

        <div class="card-body">
            <div>
                Đường dẫn lưu ảnh: <strong> storage/app/public/{{ $pathDirectory }} <strong>

            </div>
            <input type="file" multiple wire:model="photos" accept="image/*"
            x-ref="fileInput"
            x-on:change="
                Array.from($event.target.files).forEach(file => {
                    // Kiểm tra định dạng file
                    if (!file.type.startsWith('image/')) {
                        alert('Chỉ được chọn file ảnh!');
                        return;
                    }
        
                    // Kiểm tra xem file đã tồn tại chưa (tránh trùng lặp)
                    if (!previews.some(p => p.name === file.name)) {
                        previews.push({
                            url: URL.createObjectURL(file),
                            name: file.name,
                            size: (file.size / 1024).toFixed(2) + ' KB'
                        });
                    }
                });
            "
            class="form-control mb-3">
        

            <!-- Hiển thị danh sách ảnh -->
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-center">Ảnh</th>
                        <th>Tên File</th>
                        <th>Kích thước (KB)</th>
                        <th>Tiến trình</th>
                        <th class="text-center">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(preview, index) in previews" :key="index">
                        <tr>
                            <td class="text-center">
                                <img :src="preview.url" class="rounded border" style="width: 80px; height: 80px;">
                            </td>
                            <td x-text="preview.name"></td>
                            <td x-text="preview.size"></td>
                            <td>
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar" role="progressbar"
                                        :style="'width: ' + (progress[index] || 0) + '%'"
                                        :aria-valuenow="progress[index] || 0" aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                                <span x-text="(progress[index] || 0) + '%'"></span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-primary btn-sm" @click="uploadImage(index)"
                                    :disabled="uploadedIndexes.includes(index)">
                                    Upload
                                </button>
                                <button class="btn btn-danger btn-sm" @click="
                                    URL.revokeObjectURL(preview.url); 
                                    previews.splice(index, 1);
                                    delete progress[index];
                                " :disabled="uploadedIndexes.includes(index)">
                                    Hủy
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
            

            <!-- Nút tổng -->
            <div class="mt-3">
                <button class="btn btn-success" @click="uploadAll()"
                    :disabled="uploadedIndexes.length === previews.length">
                    Upload Tất Cả
                </button>
                <button class="btn btn-danger" @click="resetFileInput()" :disabled="previews.length === 0">
                    Hủy Tất Cả
                </button>
            </div>

            <!-- Thông báo upload thành công -->
            @if ($messages)
                <div class="alert alert-success mt-3">
                    @foreach ($messages as $message)
                        <div>{{ $message }}</div>
                    @endforeach
                </div>
            @endif
          
        </div>
        </div>
    </div>

</div>
