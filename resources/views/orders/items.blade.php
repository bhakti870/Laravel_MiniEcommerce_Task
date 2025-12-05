@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Order #{{ $order->id }} Items</h3>

    <button class="btn btn-success mb-3" id="addItem">Add Item</button>

    <table class="table table-bordered" id="itemTable">
        <thead>
            <tr>
                <th>#</th>
                <th>SKU</th>
                <th>Product</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Discount</th>
                <th>Total</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="itemModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="itemForm">
                @csrf
                <input type="hidden" name="id" id="id">
                <input type="hidden" name="order_id" value="{{ $order->id }}">

                <div class="modal-header">
                    <h5 class="modal-title">Order Item</h5>
                </div>

                <div class="modal-body">
                    <label>Product SKU</label>
                    <select name="product_sku_id" id="product_sku_id" class="form-select">
                        @foreach(App\Models\ProductSku::all() as $sku)
                            <option value="{{ $sku->id }}">{{ $sku->sku }} — {{ optional($sku->product)->name }}</option>
                        @endforeach
                    </select>

                    <label class="mt-2">Quantity</label>
                    <input type="number" class="form-control" id="quantity" name="quantity">

                    <label class="mt-2">Price</label>
                    <input type="number" class="form-control" id="price" name="price">

                    <label class="mt-2">Discount</label>
                    <input type="number" class="form-control" id="discount" name="discount">
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" id="saveItemBtn">Save</button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection


@push('scripts')
<script>
$(function(){

    var table = $('#itemTable').DataTable({
        processing:true,
        serverSide:true,
        ajax:"{{ route('order.items', $order->id) }}",
        columns:[
            { data:"DT_RowIndex" },
            { data:"sku" },
            { data:"product" },
            { data:"quantity" },
            { data:"price" },
            { data:"discount" },
            { data:"total" },
            { data:"action", orderable:false, searchable:false }
        ]
    });

    $('#addItem').click(()=>{
        $('#itemForm')[0].reset();
        $('#id').val('');
        $('#itemModal').modal('show');
    });

    $('body').on('click','.editItemBtn',function(){
        $.get("{{ url('order-items') }}/"+$(this).data('id')+"/edit", data=>{
            $('#id').val(data.id);
            $('#product_sku_id').val(data.product_sku_id);
            $('#quantity').val(data.quantity);
            $('#price').val(data.price);
            $('#discount').val(data.discount);
            $('#itemModal').modal('show');
        });
    });

    $('#itemForm').submit(function(e){
        e.preventDefault();

        $.ajax({
            url:"{{ route('orderitems.store') }}",
            type:"POST",
            data:new FormData(this),
            contentType:false,
            processData:false,
            success:function(res){
                $('#itemModal').modal('hide');
                table.ajax.reload();
                Swal.fire("Success",res.success,"success");
            },
            error:function(xhr){
                Swal.fire("Error","Failed to save item","error");
            }
        });
    });

    $('body').on('click','.deleteItemBtn',function(){
        Swal.fire({title:"Are you sure?",icon:"warning",showCancelButton:true})
        .then(res=>{
            if(res.isConfirmed){
                $.ajax({
                    url:"{{ url('order-items') }}/"+$(this).data('id'),
                    type:"DELETE",
                    data:{_token:"{{ csrf_token() }}"},
                    success:function(r){
                        table.ajax.reload();
                        Swal.fire("Deleted!",r.success,"success");
                    }
                });
            }
        });
    });

});
</script>
@endpush
