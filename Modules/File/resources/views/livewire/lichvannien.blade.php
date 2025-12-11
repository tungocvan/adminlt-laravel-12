
@extends('adminlte::page')

@section('title', 'Lịch Vạn Niên - Vietnamese Perpetual Calendar')
@section('plugins.Toastr', true)
@section('content_header')
    <a href="{{ route('file.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left"></i> Back
    </a>
@stop

@section('content')
     <!-- Hero Section -->
     <header class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-4 font-weight-bold mb-3">Lịch Vạn Niên</h1>
                    <p class="lead mb-4">Tra cứu ngày tốt xấu, giờ hoàng đạo, tiết khí và tử vi hàng ngày</p>
                    <div class="d-flex align-items-center">
                        <div class="mr-4">
                            <h3 id="current-solar" class="font-weight-bold">Đang tải...</h3>
                            <p id="current-lunar" class="mb-0">Đang tải...</p>
                        </div>
                        <button id="today-btn" class="btn btn-light btn-lg rounded-pill px-4">
                            <i class="fas fa-calendar-day mr-2"></i>Hôm nay
                        </button>
                    </div>
                </div>
                <div class="col-md-4 text-right">
                    <div class="card bg-white text-dark" style="border-radius: 16px;">
                        <div class="card-body text-center">
                            <h5 class="card-title mb-3">Tiết khí</h5>
                            <h2 id="current-tietkhi" class="text-primary font-weight-bold mb-2">Đại Hàn</h2>
                            <p class="card-text">Tiết khí hiện tại</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="container">
        <!-- Date Selection & Calendar -->
        <section class="mb-5">
            <div class="month-nav">
                <button id="prev-month" class="btn btn-outline-primary">
                    <i class="fas fa-chevron-left mr-2"></i>Tháng trước
                </button>
                <h3 id="current-month" class="mb-0 font-weight-bold">Tháng 12, 2025</h3>
                <button id="next-month" class="btn btn-outline-primary">
                    Tháng sau<i class="fas fa-chevron-right ml-2"></i>
                </button>
            </div>
            
            <div class="date-picker-container mb-4">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="datepicker" class="font-weight-bold">Chọn ngày</label>
                            <input type="date" id="datepicker" class="form-control form-control-lg" value="2025-12-11">
                        </div>
                    </div>
                    <div class="col-md-8 d-flex align-items-end">
                        <div class="w-100">
                            <label class="font-weight-bold">Hoặc chọn nhanh</label>
                            <div class="d-flex">
                                <button class="btn btn-outline-primary mr-2 quick-date" data-days="0">Hôm nay</button>
                                <button class="btn btn-outline-primary mr-2 quick-date" data-days="1">Ngày mai</button>
                                <button class="btn btn-outline-primary mr-2 quick-date" data-days="7">7 ngày tới</button>
                                <button class="btn btn-outline-primary quick-date" data-days="30">30 ngày tới</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Calendar Grid -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Lịch tháng</h5>
                </div>
                <div class="card-body">
                    <!-- Day names -->
                    <div class="calendar-grid mb-3">
                        <div class="day-name text-center">CN</div>
                        <div class="day-name text-center">T2</div>
                        <div class="day-name text-center">T3</div>
                        <div class="day-name text-center">T4</div>
                        <div class="day-name text-center">T5</div>
                        <div class="day-name text-center">T6</div>
                        <div class="day-name text-center">T7</div>
                    </div>
                    
                    <!-- Calendar days will be populated by JavaScript -->
                    <div id="calendar-days" class="calendar-grid">
                        <!-- Dynamic content -->
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Detailed Information Cards -->
        <section class="mb-5">
            <h2 class="section-title">Thông tin chi tiết ngày đã chọn</h2>
            <div class="row">
                <!-- Lunar & Solar Info -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-calendar-alt mr-2 text-primary"></i>Ngày Âm - Dương</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-4">
                                <div class="info-icon">
                                    <i class="fas fa-sun"></i>
                                </div>
                                <div>
                                    <h4 id="detail-solar" class="font-weight-bold mb-0">Thứ 4, 11/12/2025</h4>
                                    <p class="text-muted mb-0">Ngày Dương lịch</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-4">
                                <div class="info-icon">
                                    <i class="fas fa-moon"></i>
                                </div>
                                <div>
                                    <h4 id="detail-lunar" class="font-weight-bold mb-0">21/11/Ất Tỵ</h4>
                                    <p class="text-muted mb-0">Ngày Âm lịch</p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-4 text-center">
                                    <h6 class="text-muted">Năm</h6>
                                    <h5 id="canchi-year" class="font-weight-bold">Ất Tỵ</h5>
                                </div>
                                <div class="col-4 text-center">
                                    <h6 class="text-muted">Tháng</h6>
                                    <h5 id="canchi-month" class="font-weight-bold">Bính Tý</h5>
                                </div>
                                <div class="col-4 text-center">
                                    <h6 class="text-muted">Ngày</h6>
                                    <h5 id="canchi-day" class="font-weight-bold">Giáp Ngọ</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Good/Bad Hours -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-clock mr-2 text-primary"></i>Giờ Hoàng Đạo - Hắc Đạo</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <h6 class="font-weight-bold mb-3"><span class="hour-good">Giờ Hoàng Đạo</span> (tốt)</h6>
                                <div id="good-hours" class="d-flex flex-wrap">
                                    <span class="badge badge-custom badge-good mr-2 mb-2">Tý (23h-1h)</span>
                                    <span class="badge badge-custom badge-good mr-2 mb-2">Sửu (1h-3h)</span>
                                    <span class="badge badge-custom badge-good mr-2 mb-2">Mão (5h-7h)</span>
                                    <span class="badge badge-custom badge-good mr-2 mb-2">Ngọ (11h-13h)</span>
                                </div>
                            </div>
                            <div>
                                <h6 class="font-weight-bold mb-3"><span class="hour-bad">Giờ Hắc Đạo</span> (xấu)</h6>
                                <div id="bad-hours" class="d-flex flex-wrap">
                                    <span class="badge badge-custom badge-bad mr-2 mb-2">Dần (3h-5h)</span>
                                    <span class="badge badge-custom badge-bad mr-2 mb-2">Thìn (7h-9h)</span>
                                    <span class="badge badge-custom badge-bad mr-2 mb-2">Tỵ (9h-11h)</span>
                                    <span class="badge badge-custom badge-bad mr-2 mb-2">Mùi (13h-15h)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Zodiac & Direction -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-user-check mr-2 text-primary"></i>Tuổi Xung & Hướng Xuất Hành</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <h6 class="font-weight-bold mb-3">Tuổi xung (khắc)</h6>
                                <div id="tuoi-xung" class="d-flex flex-wrap">
                                    <span class="badge badge-custom badge-bad mr-2 mb-2">Tuất</span>
                                    <span class="badge badge-custom badge-bad mr-2 mb-2">Dần</span>
                                </div>
                                <p class="text-muted small mt-2">Những tuổi nên tránh làm việc đại sự</p>
                            </div>
                            <div>
                                <h6 class="font-weight-bold mb-3">Hướng xuất hành tốt</h6>
                                <div class="d-flex align-items-center">
                                    <div class="info-icon bg-warning text-dark">
                                        <i class="fas fa-compass"></i>
                                    </div>
                                    <div>
                                        <h4 id="huong-xuat-hanh" class="font-weight-bold mb-0">Chính Nam</h4>
                                        <p class="text-muted mb-0">Hướng tốt cho xuất hành, khởi sự</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Good/Bad Stars -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-star mr-2 text-primary"></i>Sao Tốt - Sao Xấu</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <h6 class="font-weight-bold mb-3"><span class="text-success">Sao Tốt</span></h6>
                                <div id="sao-tot" class="d-flex flex-wrap">
                                    <span class="badge badge-custom badge-good mr-2 mb-2">Thiên Đức</span>
                                    <span class="badge badge-custom badge-good mr-2 mb-2">Nguyệt Tài</span>
                                    <span class="badge badge-custom badge-good mr-2 mb-2">Thiên Phúc</span>
                                </div>
                            </div>
                            <div>
                                <h6 class="font-weight-bold mb-3"><span class="text-danger">Sao Xấu</span></h6>
                                <div id="sao-xau" class="d-flex flex-wrap">
                                    <span class="badge badge-custom badge-bad mr-2 mb-2">Không Vong</span>
                                    <span class="badge badge-custom badge-bad mr-2 mb-2">Địa Tặc</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Events -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-bullhorn mr-2 text-primary"></i>Sự kiện trong ngày</h5>
                        </div>
                        <div class="card-body">
                            <div id="events-list">
                                <div class="event-item">
                                    <div class="d-flex">
                                        <div class="mr-3">
                                            <i class="fas fa-calendar-check text-primary"></i>
                                        </div>
                                        <div>
                                            <h6 class="font-weight-bold mb-1">Ngày vía Phật A Di Đà</h6>
                                            <p class="text-muted small mb-0">Ngày vía quan trọng trong Phật giáo</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="event-item">
                                    <div class="d-flex">
                                        <div class="mr-3">
                                            <i class="fas fa-calendar-check text-primary"></i>
                                        </div>
                                        <div>
                                            <h6 class="font-weight-bold mb-1">Ngày Quốc tế Núi</h6>
                                            <p class="text-muted small mb-0">International Mountain Day</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center mt-3">
                                <button class="btn btn-outline-primary btn-sm">Xem thêm sự kiện</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Horoscope -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-scroll mr-2 text-primary"></i>Trích đoạn tử vi</h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <div class="info-icon mx-auto mb-3" style="background-color: #f0f9ff;">
                                    <i class="fas fa-quote-left text-primary"></i>
                                </div>
                                <p id="horoscope-text" class="font-italic mb-0">"Hôm nay là ngày tốt để khởi sự công việc mới, đặc biệt liên quan đến tài chính. Có quý nhân phù trợ, mọi việc diễn ra thuận lợi. Tuy nhiên cần thận trọng trong giao tiếp, tránh xung đột không đáng có."</p>
                            </div>
                            <div class="text-center">
                                <span class="badge badge-primary p-2">Tử vi hàng ngày</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p class="mb-2">Lịch Vạn Niên - Vietnamese Perpetual Calendar</p>
            <p class="small mb-0">Dữ liệu được tính toán dựa trên thuật toán âm lịch truyền thống</p>
            <p class="small mb-0">© 2025 - Phiên bản demo</p>
        </div>
    </footer>

@endsection

@section('css')
    <!-- Bootstrap 4.6.1 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css" integrity="sha384-zCbKRCUGaJDkqS1kPbPd7TveP5iyJE0EjAuZQTgFLD2ylzuqKfdKlfG/eSrtxUkn" crossorigin="anonymous">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0ea5e9;
            --secondary: #fde047;
            --light: #f8fafc;
            --dark: #1e293b;
            --success: #10b981;
            --danger: #ef4444;
            --border-radius: 16px;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #334155;
            line-height: 1.6;
        }
        
        .hero-section {
            background: linear-gradient(135deg, var(--primary) 0%, #38bdf8 100%);
            color: white;
            border-radius: 0 0 var(--border-radius) var(--border-radius);
            padding: 2.5rem 0;
            box-shadow: 0 4px 20px rgba(14, 165, 233, 0.2);
            margin-bottom: 2rem;
        }
        
        .card {
            border-radius: var(--border-radius);
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            border-radius: var(--border-radius) var(--border-radius) 0 0 !important;
            background-color: white;
            border-bottom: 2px solid #f1f5f9;
            font-weight: 600;
            padding: 1rem 1.5rem;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 8px;
        }
        
        .calendar-day {
            background: white;
            border-radius: 12px;
            padding: 12px 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            border: 2px solid transparent;
            min-height: 90px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .calendar-day:hover {
            border-color: var(--primary);
            background-color: #f0f9ff;
        }
        
        .calendar-day.today {
            border-color: var(--secondary);
            background-color: #fefce8;
        }
        
        .calendar-day.selected {
            border-color: var(--primary);
            background-color: #e0f2fe;
        }
        
        .solar-date {
            font-weight: 600;
            font-size: 1.2rem;
            color: var(--dark);
        }
        
        .lunar-date {
            font-size: 0.85rem;
            color: #64748b;
            margin-top: 4px;
        }
        
        .day-name {
            font-weight: 500;
            color: #475569;
            margin-bottom: 10px;
        }
        
        .badge-custom {
            border-radius: 20px;
            padding: 4px 12px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .badge-good {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .badge-bad {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        .info-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e0f2fe;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: var(--primary);
            font-size: 1.2rem;
        }
        
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 500;
        }
        
        .btn-primary:hover {
            background-color: #0284c7;
            border-color: #0284c7;
        }
        
        .btn-outline-primary {
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 500;
        }
        
        .section-title {
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 10px;
        }
        
        .section-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 4px;
            background-color: var(--primary);
            border-radius: 2px;
        }
        
        .month-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            background: white;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        
        .date-picker-container {
            background: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        
        .hour-good {
            color: var(--success);
            font-weight: 500;
        }
        
        .hour-bad {
            color: var(--danger);
            font-weight: 500;
        }
        
        .event-item {
            padding: 8px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        
        .event-item:last-child {
            border-bottom: none;
        }
        
        footer {
            margin-top: 3rem;
            padding: 2rem 0;
            background-color: var(--dark);
            color: #cbd5e1;
            text-align: center;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
        }
        
        @media (max-width: 768px) {
            .calendar-day {
                min-height: 70px;
                padding: 8px 4px;
            }
            
            .solar-date {
                font-size: 1rem;
            }
            
            .lunar-date {
                font-size: 0.75rem;
            }
            
            .hero-section {
                padding: 1.5rem 0;
            }
            
            .card-body {
                padding: 1rem;
            }
        }
    </style>

@stop

@section('js')
    {{-- https://www.daterangepicker.com/#examples  --}}
    <!-- Bootstrap & jQuery -->
    {{-- <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-fQybjgWLrvvRgtW6bFlB7jaZrFsaBXjsOMm/tB9LTS58ONXgqbR9W8oWht/amnpF" crossorigin="anonymous"></script> --}}
    
    <!-- Vietnamese Lunar Calendar Library (simplified) -->
    <script>
        // Mock data generator for Vietnamese lunar calendar
        // In a real application, you would use a proper lunar calendar library
        // Recommended libraries: 
        // - vietnamese-lunar-calendar (npm)
        // - âm lịch Việt Nam (PHP)
        // Replace this mock implementation with actual lunar calculations
        const LunarCalendar = {
            // Simplified conversion - for demo purposes only
            // Real implementation would require complex calculations
            solarToLunar: function(solarDate) {
                // Mock conversion - just for UI demonstration
                // In reality, this would be complex astronomical calculations
                const date = new Date(solarDate);
                const year = date.getFullYear();
                const month = date.getMonth() + 1;
                const day = date.getDate();
                
                // Mock lunar date (always 10 days behind solar date for demo)
                let lunarDay = day - 10;
                let lunarMonth = month;
                let lunarYear = year;
                
                if (lunarDay <= 0) {
                    lunarMonth--;
                    if (lunarMonth <= 0) {
                        lunarMonth = 12;
                        lunarYear--;
                    }
                    // Simple day adjustment
                    lunarDay = 30 + lunarDay;
                }
                
                // Mock Can Chi data
                const canChiYears = ['Giáp Tý', 'Ất Sửu', 'Bính Dần', 'Đinh Mão', 'Mậu Thìn', 'Kỷ Tỵ', 'Canh Ngọ', 'Tân Mùi', 'Nhâm Thân', 'Quý Dậu'];
                const canChiMonths = ['Bính Dần', 'Đinh Mão', 'Mậu Thìn', 'Kỷ Tỵ', 'Canh Ngọ', 'Tân Mùi', 'Nhâm Thân', 'Quý Dậu', 'Giáp Tuất', 'Ất Hợi', 'Bính Tý', 'Đinh Sửu'];
                const canChiDays = ['Giáp Tý', 'Ất Sửu', 'Bính Dần', 'Đinh Mão', 'Mậu Thìn', 'Kỷ Tỵ', 'Canh Ngọ', 'Tân Mùi', 'Nhâm Thân', 'Quý Dậu', 'Giáp Tuất', 'Ất Hợi', 'Bính Tý', 'Đinh Sửu', 'Mậu Dần', 'Kỷ Mão', 'Canh Thìn', 'Tân Tỵ', 'Nhâm Ngọ', 'Quý Mùi', 'Giáp Thân', 'Ất Dậu', 'Bính Tuất', 'Đinh Hợi', 'Mậu Tý', 'Kỷ Sửu', 'Canh Dần', 'Tân Mão', 'Nhâm Thìn', 'Quý Tỵ'];
                
                const tietKhiList = ['Tiểu Hàn', 'Đại Hàn', 'Lập Xuân', 'Vũ Thủy', 'Kinh Trập', 'Xuân Phân', 'Thanh Minh', 'Cốc Vũ', 'Lập Hạ', 'Tiểu Mãn', 'Mang Chủng', 'Hạ Chí', 'Tiểu Thử', 'Đại Thử', 'Lập Thu', 'Xử Thử', 'Bạch Lộ', 'Thu Phân', 'Hàn Lộ', 'Sương Giáng', 'Lập Đông', 'Tiểu Tuyết', 'Đại Tuyết', 'Đông Chí'];
                
                // Special case for 2025-12-11 to match example JSON
                if (solarDate === '2025-12-11') {
                    return {
                        solar: solarDate,
                        lunar: '2025-11-21',
                        lunarDisplay: 'Ngày 21 tháng 11 năm 2025',
                        canChi: {
                            year: 'Ất Tỵ',
                            month: 'Bính Tý',
                            day: 'Giáp Ngọ'
                        },
                        tietKhi: 'Đại Hàn',
                        gioHoangDao: ['Tý', 'Sửu', 'Mão', 'Ngọ'],
                        gioHacDao: ['Dần', 'Thìn', 'Tỵ', 'Mùi'],
                        tuoiXung: ['Tuất', 'Dần'],
                        huongXuatHanh: 'Chính Nam',
                        saoTot: ['Thiên Đức', 'Nguyệt Tài'],
                        saoXau: ['Không Vong'],
                        events: ['Ngày vía Phật A Di Đà'],
                        horoscope: 'Hôm nay là ngày tốt để khởi sự công việc mới, đặc biệt liên quan đến tài chính. Có quý nhân phù trợ, mọi việc diễn ra thuận lợi. Tuy nhiên cần thận trọng trong giao tiếp, tránh xung đột không đáng có.'
                    };
                }
                
                // Random but consistent based on date
                const seed = year * 10000 + month * 100 + day;
                const canChiYear = canChiYears[seed % 10];
                const canChiMonth = canChiMonths[month - 1];
                const canChiDay = canChiDays[day % 30];
                const tietKhi = tietKhiList[month - 1];
                
                return {
                    solar: `${year}-${month.toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`,
                    lunar: `${lunarYear}-${lunarMonth.toString().padStart(2, '0')}-${lunarDay.toString().padStart(2, '0')}`,
                    lunarDisplay: `Ngày ${lunarDay} tháng ${lunarMonth} năm ${lunarYear}`,
                    canChi: {
                        year: canChiYear,
                        month: canChiMonth,
                        day: canChiDay
                    },
                    tietKhi: tietKhi,
                    gioHoangDao: ['Tý', 'Sửu', 'Mão', 'Ngọ'],
                    gioHacDao: ['Dần', 'Thìn', 'Tỵ', 'Mùi'],
                    tuoiXung: ['Tuất', 'Dần'],
                    huongXuatHanh: 'Chính Nam',
                    saoTot: ['Thiên Đức', 'Nguyệt Tài', 'Thiên Phúc'],
                    saoXau: ['Không Vong', 'Địa Tặc'],
                    events: ['Ngày vía Phật A Di Đà', 'Ngày Quốc tế Núi'],
                    horoscope: 'Hôm nay là ngày tốt để khởi sự công việc mới, đặc biệt liên quan đến tài chính. Có quý nhân phù trợ, mọi việc diễn ra thuận lợi. Tuy nhiên cần thận trọng trong giao tiếp, tránh xung đột không đáng có.'
                };
            },
            
            // Generate calendar for a specific month
            generateMonthCalendar: function(year, month) {
                const firstDay = new Date(year, month - 1, 1);
                const lastDay = new Date(year, month, 0);
                const daysInMonth = lastDay.getDate();
                const firstDayOfWeek = firstDay.getDay(); // 0 = Sunday, 1 = Monday, etc.
                
                const calendar = [];
                const today = new Date();
                const currentYear = today.getFullYear();
                const currentMonth = today.getMonth() + 1;
                const currentDay = today.getDate();
                
                // Add empty cells for days before the first day of month
                for (let i = 0; i < firstDayOfWeek; i++) {
                    calendar.push(null);
                }
                
                // Add days of the month
                for (let day = 1; day <= daysInMonth; day++) {
                    const solarDate = `${year}-${month.toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
                    const lunarData = this.solarToLunar(solarDate);
                    const isToday = (year === currentYear && month === currentMonth && day === currentDay);
                    
                    calendar.push({
                        solarDay: day,
                        lunarDay: parseInt(lunarData.lunar.split('-')[2]),
                        solarDate: solarDate,
                        lunarData: lunarData,
                        isToday: isToday,
                        isSelected: false
                    });
                }
                
                return calendar;
            }
        };
    </script>
    
    <!-- Main Application JavaScript -->
    <script>
        $(document).ready(function() {
            let currentDate = new Date();
            let selectedDate = new Date(currentDate);
            let currentMonth = currentDate.getMonth() + 1;
            let currentYear = currentDate.getFullYear();
            
            // Initialize the application
            function init() {
                updateSelectedDate(selectedDate);
                renderCalendar(currentYear, currentMonth);
                setupEventListeners();
            }
            
            // Update all UI elements for the selected date
            function updateSelectedDate(date) {
                const solarDateStr = date.toISOString().split('T')[0];
                const lunarData = LunarCalendar.solarToLunar(solarDateStr);
                
                // Update hero section
                const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                const solarDisplay = date.toLocaleDateString('vi-VN', options);
                $('#current-solar').text(solarDisplay.charAt(0).toUpperCase() + solarDisplay.slice(1));
                $('#current-lunar').text(`Ngày ${lunarData.lunar.split('-')[2]} tháng ${lunarData.lunar.split('-')[1]} năm ${lunarData.canChi.year}`);
                $('#current-tietkhi').text(lunarData.tietKhi);
                
                // Update detail cards
                $('#detail-solar').text(date.toLocaleDateString('vi-VN', { weekday: 'long', day: 'numeric', month: 'numeric', year: 'numeric' }));
                $('#detail-lunar').text(`${lunarData.lunar.split('-')[2]}/${lunarData.lunar.split('-')[1]}/${lunarData.canChi.year}`);
                $('#canchi-year').text(lunarData.canChi.year);
                $('#canchi-month').text(lunarData.canChi.month);
                $('#canchi-day').text(lunarData.canChi.day);
                
                // Update good/bad hours
                $('#good-hours').html('');
                lunarData.gioHoangDao.forEach(hour => {
                    const timeRange = getHourRange(hour);
                    $('#good-hours').append(`<span class="badge badge-custom badge-good mr-2 mb-2">${hour} (${timeRange})</span>`);
                });
                
                $('#bad-hours').html('');
                lunarData.gioHacDao.forEach(hour => {
                    const timeRange = getHourRange(hour);
                    $('#bad-hours').append(`<span class="badge badge-custom badge-bad mr-2 mb-2">${hour} (${timeRange})</span>`);
                });
                
                // Update zodiac
                $('#tuoi-xung').html('');
                lunarData.tuoiXung.forEach(tuoi => {
                    $('#tuoi-xung').append(`<span class="badge badge-custom badge-bad mr-2 mb-2">${tuoi}</span>`);
                });
                
                $('#huong-xuat-hanh').text(lunarData.huongXuatHanh);
                
                // Update stars
                $('#sao-tot').html('');
                lunarData.saoTot.forEach(sao => {
                    $('#sao-tot').append(`<span class="badge badge-custom badge-good mr-2 mb-2">${sao}</span>`);
                });
                
                $('#sao-xau').html('');
                lunarData.saoXau.forEach(sao => {
                    $('#sao-xau').append(`<span class="badge badge-custom badge-bad mr-2 mb-2">${sao}</span>`);
                });
                
                // Update events
                $('#events-list').html('');
                lunarData.events.forEach(event => {
                    $('#events-list').append(`
                        <div class="event-item">
                            <div class="d-flex">
                                <div class="mr-3">
                                    <i class="fas fa-calendar-check text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="font-weight-bold mb-1">${event}</h6>
                                    <p class="text-muted small mb-0">Sự kiện quan trọng</p>
                                </div>
                            </div>
                        </div>
                    `);
                });
                
                // Update horoscope
                $('#horoscope-text').text(`"${lunarData.horoscope}"`);
                
                // Update date picker
                $('#datepicker').val(solarDateStr);
            }
            
            // Helper function to get time range for zodiac hours
            function getHourRange(hour) {
                const hourRanges = {
                    'Tý': '23h-1h',
                    'Sửu': '1h-3h',
                    'Dần': '3h-5h',
                    'Mão': '5h-7h',
                    'Thìn': '7h-9h',
                    'Tỵ': '9h-11h',
                    'Ngọ': '11h-13h',
                    'Mùi': '13h-15h',
                    'Thân': '15h-17h',
                    'Dậu': '17h-19h',
                    'Tuất': '19h-21h',
                    'Hợi': '21h-23h'
                };
                return hourRanges[hour] || hour;
            }
            
            // Render calendar for a specific month
            function renderCalendar(year, month) {
                const calendar = LunarCalendar.generateMonthCalendar(year, month);
                const monthNames = ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'];
                $('#current-month').text(`${monthNames[month-1]}, ${year}`);
                
                $('#calendar-days').html('');
                
                calendar.forEach(day => {
                    if (day === null) {
                        $('#calendar-days').append('<div class="calendar-day empty"></div>');
                        return;
                    }
                    
                    const dayElement = `
                        <div class="calendar-day ${day.isToday ? 'today' : ''} ${day.solarDate === selectedDate.toISOString().split('T')[0] ? 'selected' : ''}" 
                             data-date="${day.solarDate}">
                            <div class="solar-date">${day.solarDay}</div>
                            <div class="lunar-date">${day.lunarDay} âm</div>
                            ${day.isToday ? '<div class="badge badge-primary badge-sm mt-1">Hôm nay</div>' : ''}
                        </div>
                    `;
                    $('#calendar-days').append(dayElement);
                });
                
                // Add click event to each day
                $('.calendar-day:not(.empty)').on('click', function() {
                    const dateStr = $(this).data('date');
                    selectedDate = new Date(dateStr);
                    updateSelectedDate(selectedDate);
                    renderCalendar(currentYear, currentMonth); // Re-render to update selection
                });
            }
            
            // Setup event listeners
            function setupEventListeners() {
                // Previous month button
                $('#prev-month').on('click', function() {
                    currentMonth--;
                    if (currentMonth < 1) {
                        currentMonth = 12;
                        currentYear--;
                    }
                    renderCalendar(currentYear, currentMonth);
                });
                
                // Next month button
                $('#next-month').on('click', function() {
                    currentMonth++;
                    if (currentMonth > 12) {
                        currentMonth = 1;
                        currentYear++;
                    }
                    renderCalendar(currentYear, currentMonth);
                });
                
                // Today button
                $('#today-btn').on('click', function() {
                    selectedDate = new Date();
                    currentMonth = selectedDate.getMonth() + 1;
                    currentYear = selectedDate.getFullYear();
                    updateSelectedDate(selectedDate);
                    renderCalendar(currentYear, currentMonth);
                });
                
                // Date picker
                $('#datepicker').on('change', function() {
                    const dateStr = $(this).val();
                    selectedDate = new Date(dateStr);
                    currentMonth = selectedDate.getMonth() + 1;
                    currentYear = selectedDate.getFullYear();
                    updateSelectedDate(selectedDate);
                    renderCalendar(currentYear, currentMonth);
                });
                
                // Quick date buttons
                $('.quick-date').on('click', function() {
                    const days = parseInt($(this).data('days'));
                    const newDate = new Date();
                    newDate.setDate(newDate.getDate() + days);
                    selectedDate = newDate;
                    currentMonth = selectedDate.getMonth() + 1;
                    currentYear = selectedDate.getFullYear();
                    updateSelectedDate(selectedDate);
                    renderCalendar(currentYear, currentMonth);
                });
            }
            
            // Initialize the app
            init();
        });
    </script>
@stop
