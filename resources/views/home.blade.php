@extends('layouts.app')
@section('title', 'Trường Tiểu Học Nguyễn Thị Định')
@section('titleHeader', 'Trường Tiểu Học Nguyễn Thị Định')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><strong>{{ __('TRA CỨU HỌC SINH LỚP 1') }}</strong></div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="small-box bg-warning">
                        <div class="inner">
                          <p><strong>{{ \App\Models\Hocsinh::count() }} Học sinh</strong></p>
                          <p>Số học sinh nam: {{ \App\Models\Hocsinh::where('gioi_tinh', 'Nam')->count() }}</p>
                          <p>Số học sinh nữ: {{ \App\Models\Hocsinh::where('gioi_tinh', 'Nữ')->count() }}</p>
                            
                          
                        </div>
                        <div class="icon">
                          <i class="ion ion-person-add"></i>
                        </div>
                        <a href="{{route('ntd.tracuu')}}" class="small-box-footer">Click vào tra cứu <i class="fas fa-arrow-circle-right"></i></a>
                      </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
