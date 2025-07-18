@extends('Products::layout-products')
   
@section('content') 
<div class="container">
    <div class="row navbar-light bg-light pt-2 pb-2">
        <div class="col-lg-10">
            <h3 class="mt-2">Laravel Add to Cart Example - ItSolutionStuff.com</h3>
        </div>
        <div class="col-md-2 text-right main-section">
            <div class="dropdown">
                <button type="button" class="btn btn-info dropdown-toggle mt-1" data-bs-toggle="dropdown">
                    <i class="fa fa-shopping-cart" aria-hidden="true"></i> Cart <span class="badge badge-pill badge-danger">{{ count((array) session('cart')) }}</span>
                </button>
                <div class="dropdown-menu">
                    <div class="row total-header-section">
                        <div class="col-lg-6 col-sm-6 col-6">
                            <i class="fa fa-shopping-cart" aria-hidden="true"></i> <span class="badge badge-pill badge-danger">{{ count((array) session('cart')) }}</span>
                        </div>
                        @php $total = 0 @endphp
                        @foreach((array) session('cart') as $id => $details)
                            @php $total += $details['price'] * $details['quantity'] @endphp
                        @endforeach
                        <div class="col-md-6 text-end">
                            <p><strong>Total: <span class="text-info">${{ $total }}</span></strong></p>
                        </div>
                    </div>
                    @if(session('cart'))
                        @foreach(session('cart') as $id => $details)
                            <div class="row cart-detail pb-3 pt-2">
                                <div class="col-lg-4 col-sm-4 col-4">
                                    <img src="{{ $details['image'] }}" class="img-fluid" />
                                </div>
                                <div class="col-lg-8 col-sm-8 col-8 cart-detail-product">
                                    <p class="mb-0">{{ $details['name'] }}</p>
                                    <span class="fs-8 text-info"> Price: ${{ $details['price'] }}</span> <br/>
                                    <span class="fs-8 fw-lighter"> Quantity: {{ $details['quantity'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    @endif
                    <div class="text-center">
                        <a href="{{ route('products.cart') }}" class="btn btn-info">View all</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-10 offset-md-1"> 
            @if(session('success'))
                <div class="alert alert-success">
                  {{ session('success') }}
                </div> 
            @endif              
            <div class="row mt-4">
                @foreach($products as $product)
                    <div class="col-md-3">
                        <div class="card text-center">
                            <img src="{{ $product->image }}" alt="" class="card-img-top">
                            <div class="caption card-body">
                                <h4>{{ $product->name }}</h4>
                                <p>{{ $product->description }}</p>
                                <p><strong>Price: </strong> $ {{ $product->price }}</p>
                                <a href="{{ route('products.add.to.cart', $product->id) }}" class="btn btn-warning btn-block text-center" role="button">Add to cart</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
        </div>
    </div>
</div>

@endsection

