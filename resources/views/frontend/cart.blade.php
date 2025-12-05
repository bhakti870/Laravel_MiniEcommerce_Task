@extends('layouts.front')

@section('title', 'Shopping Cart')

@section('content')
<div class="row">
    <div class="col-12">
        <h2 class="mb-4">Shopping Cart</h2>
        
        @if(session('cart') && count(session('cart')) > 0)
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $total = 0; @endphp
                        @foreach(session('cart') as $id => $details)
                            @php $total += $details['price'] * $details['qty']; @endphp
                            <tr data-id="{{ $id }}">
                                <td style="width:100px">
                                    <img src="{{ $details['image'] ?? '/images/no-image.png' }}" width="80" height="80" class="img-fluid rounded" style="object-fit:cover">
                                </td>
                                <td>
                                    <h5 class="mb-0">{{ $details['name'] }}</h5>
                                </td>
                                <td>₹{{ number_format($details['price'], 2) }}</td>
                                <td style="width:150px">
                                    <input type="number" value="{{ $details['qty'] }}" class="form-control quantity update-cart" min="1">
                                </td>
                                <td>₹{{ number_format($details['price'] * $details['qty'], 2) }}</td>
                                <td>
                                    <button class="btn btn-danger btn-sm remove-from-cart"><i class="fa fa-trash"></i> Remove</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end"><strong>Total:</strong></td>
                            <td><strong>₹{{ number_format($total, 2) }}</strong></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="6" class="text-end">
                                <a href="{{ route('shop') }}" class="btn btn-warning"><i class="fa fa-angle-left"></i> Continue Shopping</a>
                                <a href="{{ route('checkout') }}" class="btn btn-success">Checkout <i class="fa fa-angle-right"></i></a>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="alert alert-info">
                Your cart is empty. <a href="{{ route('shop') }}">Go to Shop</a>
            </div>
        @endif
    </div>
</div>

@section('scripts')
<script type="text/javascript">
    $(".update-cart").change(function (e) {
        e.preventDefault();
        var ele = $(this);
        $.ajax({
            url: '{{ route("cart.update") }}',
            method: "patch",
            data: {
                _token: '{{ csrf_token() }}', 
                id: ele.parents("tr").attr("data-id"), 
                quantity: ele.parents("tr").find(".quantity").val()
            },
            success: function (response) {
               window.location.reload();
            }
        });
    });
  
    $(".remove-from-cart").click(function (e) {
        e.preventDefault();
        var ele = $(this);
        if(confirm("Are you sure want to remove?")) {
            $.ajax({
                url: '{{ route("cart.remove") }}',
                method: "DELETE",
                data: {
                    _token: '{{ csrf_token() }}', 
                    id: ele.parents("tr").attr("data-id")
                },
                success: function (response) {
                    window.location.reload();
                }
            });
        }
    });
</script>
@endsection
@endsection
