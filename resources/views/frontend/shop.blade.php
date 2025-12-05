@extends('layouts.front')

@section('title', 'Shop')

@section('content')
<div class="row">
    <div class="col-md-3">
        <h4 class="mb-3">Categories</h4>
        <ul class="list-group">
            <li class="list-group-item {{ !request('category') ? 'active' : '' }}">
                <a href="{{ route('shop') }}" class="text-decoration-none {{ !request('category') ? 'text-white' : 'text-dark' }}">All Categories</a>
            </li>
            @foreach($categories as $category)
            <li class="list-group-item {{ request('category') == $category->id ? 'active' : '' }}">
                <a href="{{ route('shop', ['category' => $category->id]) }}" class="text-decoration-none {{ request('category') == $category->id ? 'text-white' : 'text-dark' }}">
                    {{ $category->name }}
                </a>
            </li>
            @endforeach
        </ul>
    </div>
    
    <div class="col-md-9">
        <h2 class="mb-4">Shop</h2>
        
        <div class="row">
            @forelse($products as $product)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="{{ $product->image ?? '/images/no-image.png' }}" class="card-img-top" alt="{{ $product->name }}" style="height: 200px; object-fit: cover;">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <p class="card-text text-muted">₹{{ number_format($product->price, 2) }}</p>
                        <div class="mt-auto">
                            <a href="{{ route('product.detail', $product->slug ?? \Illuminate\Support\Str::slug($product->name)) }}" class="btn btn-outline-primary w-100 mb-2">View Details</a>
                            <button class="btn btn-primary w-100 add-to-cart" data-id="{{ $product->id }}">Add to Cart</button>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="alert alert-warning">No products found.</div>
            </div>
            @endforelse
        </div>
        
        <div class="d-flex justify-content-center mt-4">
            {{ $products->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
