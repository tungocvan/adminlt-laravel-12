<div>
    <h1>Thực hiện câu lệnh Artisan</h1>    
    <h5>Vui lòng bỏ qua từ khóa php artisan, chỉ thực hiện câu lệnh </h5>
    <h5>Ví dụ gõ câu lệnh: list => tương ứng: php artisan list</h5>
     <ul>
        <li>List</li>
        <li>key:generate</li>
        <li>optimize:clear</li>
        <li>make:migrate:fresh</li>
        <li>db:seed</li>
        <li>make:livewire [ten-component] <strong>make:livewire user.user-list</strong></li>
        <li>db:mysql [create|delete|show] [ten-database]</li>
        <li>create:module  {name}  {--delete}</li>
     </ul>
    <div class="my-3" style="width:200px">
        @if($errorMessage)
            <div class="alert alert-danger">{{ $errorMessage }}</div>
        @endif
        <input type="text" wire:model="artisanCommand" placeholder="Nhập câu lệnh php artisan" class="form-control my-1" />
        <button wire:click="executeArtisanCommand" style="width: 150px;" class="btn btn-outline-primary btn-sm my-2">
            Thực hiện
        </button>
    </div>

    @if($commandOutput)
        <div class="mt-3">
            <h5>Kết quả:</h5>
            <pre>{{ $commandOutput }}</pre>
        </div>
    @endif
</div>