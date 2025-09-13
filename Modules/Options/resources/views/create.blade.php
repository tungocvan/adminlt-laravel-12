@extends('adminlte::page')
@section('title', 'Tùy chọn Hệ Thống')

@section('content')
<div class="container">
    <h2>Add Option</h2>
    <form action="{{ route('options.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label>Option Name</label>
            <input type="text" name="option_name" class="form-control" value="{{ old('option_name') }}" required>
            @error('option_name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="form-group">
            <label>Option Value</label>
            <textarea name="option_value" class="form-control" rows="4" required>{{ old('option_value') }}</textarea>
            @error('option_value') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="form-group">
            <label>Autoload</label>
            <select name="autoload" class="form-control">
                <option value="yes" {{ old('autoload') == 'yes' ? 'selected' : '' }}>yes</option>
                <option value="no" {{ old('autoload') == 'no' ? 'selected' : '' }}>no</option>
            </select>
            @error('autoload') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <button class="btn btn-success">Save</button>
        <a href="{{ route('options.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
