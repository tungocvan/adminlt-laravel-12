@extends('adminlte::page')
@section('title', 'Tùy chọn Hệ Thống')



@section('content')
<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Options</h2>
        <a href="{{ route('options.create') }}" class="btn btn-primary">Add Option</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <!-- Form chung -->
    <form method="GET" action="{{ route('options.index') }}" id="searchForm" class="form-inline mb-3">
        <div class="input-group mr-2" style="width: 300px;">
            <input type="text" name="search" id="searchInput"
                   value="{{ request('search') }}"
                   class="form-control"
                   placeholder="Tìm kiếm option...">
            <div class="input-group-append">
                <button type="button" id="clearBtn" class="btn btn-outline-secondary">&times;</button>
            </div>
        </div>

        <a href="{{ route('options.index', ['search' => request('search'), 'sort' => $sort === 'asc' ? 'desc' : 'asc']) }}"
           class="btn btn-outline-secondary mr-2">
            Sắp xếp {{ $sort === 'asc' ? '↓' : '↑' }}
        </a>
    </form>

    <!-- Bulk Action Form -->
    <form action="{{ route('options.bulk') }}" method="POST" id="bulkForm">
        @csrf

        <div class="mb-3 d-flex">
            <select name="action" class="form-control w-auto mr-2" required>
                <option value="">-- Chọn hành động --</option>
                <option value="delete">Xóa</option>
                <option value="autoload_yes">Autoload = yes</option>
                <option value="autoload_no">Autoload = no</option>
            </select>
            <button type="submit" class="btn btn-danger">Thực hiện</button>
        </div>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th><input type="checkbox" id="checkAll"></th>
                    <th>ID</th>
                    <th>Option Name</th>
                    <th>Option Value</th>
                    <th>Autoload</th>
                    <th style="width:20%">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($options as $option)
                    <tr>
                        <td>
                            <input type="checkbox" name="ids[]" value="{{ $option->option_id }}" class="checkItem">
                        </td>
                        <td>{{ $option->option_id }}</td>
                        <td>{{ $option->option_name }}</td>
                        <td>{{ Str::limit($option->option_value, 50) }}</td>
                        <td>{{ $option->autoload }}</td>
                        <td>
                            <a href="{{ route('options.edit', $option->option_id) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('options.destroy', $option->option_id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Xóa option này?')" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">Không có dữ liệu</td></tr>
                @endforelse
            </tbody>
        </table>
    </form>

    <div class="d-flex justify-content-center">
        {{ $options->links('pagination::bootstrap-4') }}
    </div>
</div>

<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script>
    $(document).ready(function () {
        let timer = null;
    
        // debounce 300ms
        $('#searchInput').on('keyup', function () {
            clearTimeout(timer);
            timer = setTimeout(function () {
                $('#searchForm').submit();
            }, 500);
        });
    
        // clear input
        $('#clearBtn').on('click', function () {
            $('#searchInput').val('');
            $('#searchForm').submit();
        });
        // check/uncheck all
        $('#checkAll').on('click', function(){
            $('.checkItem').prop('checked', $(this).prop('checked'));
        });

        $('.checkItem').on('change', function(){
            $('#checkAll').prop('checked', $('.checkItem:checked').length === $('.checkItem').length);
        });

        // confirm khi bulk action
        $('#bulkForm').on('submit', function(){
            return confirm('Bạn chắc chắn muốn thực hiện hành động này?');
        });
    });
    </script>
@endsection



