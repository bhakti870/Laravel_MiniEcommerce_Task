@extends('layouts.front')

@section('content')
<div class="container">
    <h2>{{ $product->name }}</h2>
    <img src="{{ $product->image ?? '/images/no-image.png' }}" style="max-width:300px;">
    <p>Price: ₹{{ number_format($product->price,2) }}</p>
    <p>{{ $product->description ?? 'No description available.' }}</p>
</div>
@endsection
