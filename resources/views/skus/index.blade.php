@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Product SKUs</h2>
    <button class="btn btn-success mb-3" id="addSku">Add SKU</button>

    <table class="table table-bordered" id="skuTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th>SKU</th>
                <th>Size</th>
                <th>Color</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="skuModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="skuForm">
                @csrf
                <input type="hidden" id="id" name="id">

                <div class="modal-header">
                    <h5 class="modal-title">SKU Form</h5>
                </div>

                <div class="modal-body">
                    <label>Product</label>
                    <select name="product_id" id="product_id" class="form-select">
                        @foreach(App\Models\Product::all() as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>

                    <label class="mt-2">SKU</label>
                    <input type="text" class="form-control" name="sku" id="sku">

                    <label class="mt-2">Size</label>
                    <input type="text" class="form-control" name="size" id="size">

                    <label class="mt-2">Color</label>
                    <input type="text" class="form-control" name="color" id="color">

                    <label class="mt-2">Price</label>
                    <input type="number" step="0.01" class="form-control" name="price" id="price">

                    <label class="mt-2">Stock</label>
                    <input type="number" class="form-control" name="stock" id="stock">
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" id="saveBtn">Save</button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function(){

    let table = $('#skuTable').DataTable({
        processing:true,
        serverSide:true,
        ajax:"{{ route('product-skus.index') }}",
        columns:[
            { data:"DT_RowIndex" },
            { data:"product" },
            { data:"sku" },
            { data:"size" },
            { data:"color" },
            { data:"price" },
            { data:"stock" },
            { data:"action", orderable:false, searchable:false }
        ]
    });

    $('#addSku').click(()=>{
        $('#skuForm')[0].reset();
        $('#id').val('');
        $('#skuModal').modal('show');
    });

    $('body').on('click','.editBtn',function(){
        let id = $(this).data('id');
        $.get("product-skus/"+id+"/edit", data=>{
            $('#id').val(data.id);
            $('#product_id').val(data.product_id);
            $('#sku').val(data.sku);
            $('#size').val(data.size);
            $('#color').val(data.color);
            $('#price').val(data.price);
            $('#stock').val(data.stock);
            $('#skuModal').modal('show');
        });
    });

    $('#skuForm').submit(function(e){
        e.preventDefault();
        $.ajax({
            url:"{{ route('product-skus.store') }}",
            type:"POST",
            data:new FormData(this),
            contentType:false,
            processData:false,
            success:function(res){
                $('#skuModal').modal('hide');
                table.ajax.reload();
                Swal.fire("Saved!", res.success, "success");
            }
        });
    });

    $('body').on('click','.deleteBtn',function(){
        let id = $(this).data('id');
        Swal.fire({title:"Delete?", icon:"warning", showCancelButton:true})
        .then(r=>{
            if(r.isConfirmed){
                $.ajax({
                    url:"product-skus/"+id,
                    type:"DELETE",
                    data:{ _token:"{{ csrf_token() }}" },
                    success:res=>{
                        table.ajax.reload();
                        Swal.fire("Deleted!",res.success,"success");
                    }
                });
            }
        })
    });

});
</script>
@endpush
