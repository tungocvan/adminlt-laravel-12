@extends('adminlte::page')

@section('title', 'Settings')

@section('content_header')
<div class="container">
    <div class="card mt-5">
        <h3 class="card-header p-3">Laravel 12 Add More Fields Example - ItSolutionStuff.com</h3>
        <div class="card-body">

            @session('success')
                <div class="alert alert-success" role="alert"> 
                    {{ $value }}
                </div>
            @endsession

            <form method="post" action="{{ route('products.add-more.store') }}" enctype="multipart/form-data">
                @csrf
                <h5>Create Product:</h5>
                <div class="form-group">
                    <label>Name:</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter Name" value="{{ request()->old('name') }}" />
                    @error('name')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <table class="table table-bordered mt-2 table-add-more">
                    <thead>
                        <tr>
                            <th colspan="2">Add Stocks</th>
                            <th><button class="btn btn-primary btn-sm btn-add-more"><i class="fa fa-plus"></i> Add More</button></th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $key = 0;
                        @endphp
                        @if(request()->old('stocks'))
                            @foreach(request()->old('stocks') as $key => $stock)
                            <tr>
                                <td>
                                    <input type="number" name="stocks[{{$key}}][quantity]" class="form-control" placeholder="Quantity" value="{{ $stock['quantity'] ?? '' }}" />
                                    @error("stocks.{$key}.quantity")
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </td>
                                <td>
                                    <input type="number" name="stocks[{{$key}}][price]" class="form-control" placeholder="Price" value="{{ $stock['price'] ?? '' }}" />
                                    @error("stocks.{$key}.price")
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </td>
                                <td><button class="btn btn-danger btn-sm btn-add-more-rm"><i class="fa fa-trash"></i></button></td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td><input type="number" name="stocks[0][quantity]" class="form-control" placeholder="Quantity" /></td>
                                <td><input type="number" name="stocks[0][price]" class="form-control" placeholder="Price" /></td>
                                <td><button class="btn btn-danger btn-sm btn-add-more-rm"><i class="fa fa-trash"></i></button></td>
                            </tr>
                        @endif
                    </tbody>
                </table>

                <div class="form-group mt-2">
                    <button type="submit" class="btn btn-success btn-block"><i class="fa fa-save"></i> Submit</button>
                </div>
            </form>

            <h5 class="mt-5">Product List:</h5>
            <table class="table table-bordered data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Total Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>{{ $product->id }}</td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->stocks->sum('quantity') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">There are no products.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
          
            {!! $products->links('pagination::bootstrap-5') !!}
        </div>
    </div>
</div>
@stop

@section('content')
      {{-- @livewire('env.env-list') --}}
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}

@stop

@section('js')
     {{-- https://www.daterangepicker.com/#examples  --}}
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>
    <script type="text/javascript">
        $(document).ready(function(){  
    
            i = "{{$key}}";
            
            $(".btn-add-more").click(function(e){
                e.preventDefault();
                i++;
                $(".table-add-more tbody").append('<tr><td><input type="number" name="stocks['+i+'][quantity]" class="form-control" placeholder="Quantity" /></td><td><input type="number" name="stocks['+i+'][price]" class="form-control" placeholder="Price" /></td><td><button class="btn btn-danger btn-sm btn-add-more-rm"><i class="fa fa-trash"></i></button></td></tr>');
            });
    
            $(document).on('click', '.btn-add-more-rm', function(){  
                $(this).parents("tr").remove();
            }); 
    
        });
    </script> 

@stop
