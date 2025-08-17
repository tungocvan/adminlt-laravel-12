<div class="min-h-screen flex flex-col items-center bg-gradient-to-b from-sky-400 to-sky-600 p-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row items-center justify-center gap-6 mb-6 text-white">
        <!-- Logo -->
        <div class="flex-shrink-0">
            <img width="90" src="{{ asset('storage/logo.png') }}" alt="Logo" class="mx-auto md:mx-0">
        </div>
    
        <!-- Nội dung chữ -->
        <div class="text-center md:text-left">
            <h2 class="font-semibold">ỦY BAN NHÂN DÂN PHƯỜNG TÂN THUẬN</h2>
            <h3 class="font-semibold mb-4">TRƯỜNG TIỂU HỌC NGUYỄN THỊ ĐỊNH</h3>         
        </div>
    </div>
    <div class="flex flex-col md:flex-row items-center justify-center gap-6 mb-6 text-white">     
        <!-- Nội dung chữ -->
        <div class="text-center md:text-center">
            <h1 class="text-2xl sm:text-3xl font-bold mb-2">TRA CỨU HỌC SINH LỚP 1</h1>
        <h1 class="text-2xl sm:text-3xl font-bold mb-2">NĂM HỌC 2025 - 2026</h1>
        <p class="mt-2">PHỤ HUYNH VUI LÒNG NHẬP MÃ ĐỊNH DANH HỌC SINH</p>       
        </div>
    </div>
    
    

    {{-- Form Tra cứu --}}
    <form wire:submit.prevent="search" class="w-full max-w-md bg-white bg-opacity-90 rounded-xl shadow p-4 flex flex-col sm:flex-row gap-3">
        <input
            wire:model.defer="keyword"
            type="number"
            placeholder="Nhập mã định danh"
            class="flex-1 rounded-lg border-gray-300 focus:ring-0 focus:border-sky-500 text-center text-lg font-semibold">
        <button type="submit"
            class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded-lg">
            TRA CỨU
        </button>
    </form>
    @error('keyword')
        <p class="text-red-600 mt-2">{{ $message }}</p>
    @enderror

    {{-- Kết quả --}}
    <div class="w-full max-w-4xl mt-8">
        @if($searched &&  !$errors->has('keyword'))
            @if($student)
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-center border border-white">
                        <thead class="bg-blue-900 text-white">
                            <tr>
                                <th class="border px-4 py-2">LỚP</th>
                                <th class="border px-4 py-2">HỌ VÀ TÊN</th>
                                <th class="border px-4 py-2">NGÀY SINH</th>
                                <th class="border px-4 py-2">GIỚI TÍNH</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            <tr>
                                <td class="border px-4 py-2">{{ $student['lop'] ?? '' }}</td>
                                <td class="border px-4 py-2">{{ $student['ho_ten'] ?? '' }}</td>
                                <td class="border px-4 py-2">{{ \Carbon\Carbon::parse($student['ngay_sinh'])->format('d/m/Y') ?? '' }}</td>
                                <td class="border px-4 py-2">{{ $student['gioi_tinh'] ?? '' }}</td>
                            </tr>
                        </tbody>
                    </table>

                </div>
                <div class="mt-4 bg-white bg-opacity-90 rounded-lg p-4 text-center">
                    <p class="font-medium text-blue-600">GVCN: {{ $student['gvcn'] ?? '' }}</p>
                    <p class="font-medium text-blue-600">Bảo Mẫu: {{ $student['bao_mau'] ?? '' }}</p>
                </div>
            @else
                <div class="mt-4 bg-white bg-opacity-90 rounded-lg p-4 text-center">
                    <p class="font-medium text-red-600">Không tìm thấy học sinh với mã: {{ $keyword }}</p>
                </div>
            @endif
        @endif
    </div>
</div>