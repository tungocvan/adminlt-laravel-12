<div class="card">
    <div class="card-body p-0">
        <table class="table table-bordered table-hover mb-0">
            <thead class="thead-light">
                <tr>
                    <th width="50">ID</th>
                    <th>Tên</th>
                    <th>Slug</th>
                    <th>Loại</th>
                    <th>Cha</th>
                    <th width="160">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $item)
                    @include('livewire.category.parts.tree-row', [
                        'item' => $item,
                        'level' => 0  {{-- Khởi đầu level 0 --}}
                    ])
                @endforeach
            </tbody>
        </table>
    </div>
</div>
