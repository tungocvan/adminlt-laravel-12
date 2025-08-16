<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tìm kiếm học sinh</title>
</head>
<body>
    <h1>Tìm kiếm học sinh theo mã định danh</h1>

    <form method="POST" action="{{ route('students.search') }}">
        @csrf
        <input type="text" name="ma_dinh_danh" placeholder="Nhập mã định danh" value="{{ old('ma_dinh_danh', $keyword ?? '') }}">
        <button type="submit">Tìm kiếm</button>
    </form>

    @isset($student)
        @if ($student)
            <h2>Kết quả:</h2>
            <ul>
                <li><b>Mã định danh:</b> {{ $student['ma_dinh_danh'] }}</li>
                <li><b>Họ tên:</b> {{ $student['ho_ten'] }}</li>
                <li><b>Ngày sinh:</b> {{ $student['ngay_sinh'] }}</li>
            </ul>
        @else
            <p>Không tìm thấy học sinh</p>
        @endif
    @endisset
</body>
</html>
