@extends('layouts.frontend')

@section('content')
<div class="row">
  <div class="col-md-3">
    <h5>Categories</h5>
    <ul class="list-group">
      @foreach($categories as $cat)
        <li class="list-group-item">
          <a href="{{ route('shop', ['category' => $cat->id]) }}">{{ $cat->name }}</a>
        </li>
      @endforeach
    </ul>
  </div>

  <div class="col-md-9">
    <h4 class="mb-3">Featured Products</h4>
    <div class="row">
      @foreach($featured as $p)
      <div class="col-6 col-md-3 mb-3">
        <div class="card h-100">
          <img src="{{ $p->image ?? '/images/no-image.png' }}" class="card-img-top" style="height:160px;object-fit:cover">
          <div class="card-body d-flex flex-column">
            <h6 class="card-title">{{ \Illuminate\Support\Str::limit($p->name, 40) }}</h6>
            <p class="mb-1">₹{{ number_format($p->price, 2) }}</p>
            <div class="mt-auto d-flex gap-2">
              <a href="{{ route('product.detail', $p->slug) }}" class="btn btn-sm btn-outline-primary">View</a>
              <button class="btn btn-sm btn-primary add-to-cart" data-id="{{ $p->id }}">Add</button>
            </div>
          </div>
        </div>
      </div>
      @endforeach
    </div>

    <hr>

    <h4 class="mb-3">New Arrivals</h4>
    <div class="row">
      @foreach($newArrivals as $p)
      <div class="col-6 col-md-3 mb-3">
        <div class="card h-100">
          <img src="{{ $p->image ?? '/images/no-image.png' }}" class="card-img-top" style="height:140px;object-fit:cover">
          <div class="card-body d-flex flex-column">
            <h6 class="card-title">{{ \Illuminate\Support\Str::limit($p->name, 40) }}</h6>
            <p class="mb-1">₹{{ number_format($p->price, 2) }}</p>
            <div class="mt-auto d-flex gap-2">
              <a href="{{ route('product.detail', $p->slug) }}" class="btn btn-sm btn-outline-primary">View</a>
              <button class="btn btn-sm btn-primary add-to-cart" data-id="{{ $p->id }}">Add</button>
            </div>
          </div>
        </div>
      </div>
      @endforeach
    </div>

  </div>
</div>
@endsection
