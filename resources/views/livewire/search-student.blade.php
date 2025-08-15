<div>
    @section('content')
<div class="p-4 max-w-2xl mx-auto">
    <input type="text"
           wire:model.live="search"
           placeholder="Nhập mã định danh hoặc họ tên..."
           class="w-full border rounded p-2 mb-4" />

    @if(!empty($results))
        <table class="w-full border-collapse border border-gray-300 text-sm">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border p-2">Mã định danh</th>
                    <th class="border p-2">Họ và tên</th>
                    <th class="border p-2">Giới tính</th>
                    <th class="border p-2">Ngày sinh</th>
                    <th class="border p-2">Khối</th>
                    <th class="border p-2">Lớp</th>
                </tr>
            </thead>
            <tbody>
                @foreach($results as $row)
                    <tr>
                        <td class="border p-2">{{ $row[0] }}</td>
                        <td class="border p-2">{{ $row[1] }}</td>
                        <td class="border p-2">{{ $row[2] }}</td>
                        <td class="border p-2">{{ $row[3] }}</td>
                        <td class="border p-2">{{ $row[4] }}</td>
                        <td class="border p-2">{{ $row[5] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        @if(strlen($search) >= 2)
            <p class="text-red-500">Không tìm thấy kết quả</p>
        @endif
    @endif
</div>
@endsection
</div>


