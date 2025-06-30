<div class="row">
    <div class="col-6">
        <h1>Quản lý Script .sh</h1>

        <!-- Chọn file .sh -->
        <div class="my-3">
            <label for="scriptSelect">Chọn file .sh:</label>
            <select id="scriptSelect" wire:model="selectedScript" class="form-control" wire:change="selectScript($event.target.value)">
                <option value="">-- Chọn file --</option>
                @foreach($scripts as $script)
                    <option value="{{ $script }}">{{ basename($script) }}</option>
                @endforeach
            </select>
        </div>
    
        @if($selectedScript)
            <div class="my-3">
                <h5>Nội dung script:</h5>
                <textarea class="form-control" rows="10" wire:model="scriptContent"></textarea>
            </div>
    
            <button wire:click="saveScript" class="btn btn-outline-primary">
                Cập nhật Script
            </button>
    
            <button wire:click="executeScript" class="btn btn-outline-success">
                Thực hiện Script
            </button>
    
            <button wire:click="deleteScript" class="btn btn-outline-danger">
                Xóa Script
            </button>
        @endif
    
        <div class="my-3">
            <h5>Tạo mới script:</h5>
            <input type="text" wire:model="newScriptName" placeholder="Tên file (bao gồm đuôi .sh)" class="form-control my-1" />
            <button wire:click="saveScript" class="btn btn-outline-primary">
                Tạo Script
            </button>
        </div>
    
        @if($errorMessage)
            <div class="alert alert-danger mt-3">{{ $errorMessage }}</div>
        @endif
    
        <!-- Hiển thị kết quả thực thi script -->
        @if($executionOutput)
            <div class="my-3">
                <h5>Kết quả thực thi:</h5>
                <pre>{{ $executionOutput }}</pre>
            </div>
        @endif
    </div>
</div>