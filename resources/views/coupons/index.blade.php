@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Coupons</h2>
    <button class="btn btn-success mb-3" id="addCoupon">Add Coupon</button>

    <table class="table table-bordered" id="couponTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Code</th>
                <th>Type</th>
                <th>Value</th>
                <th>Max Uses</th>
                <th>Min Cart</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="couponModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="couponForm">
                @csrf
                <input type="hidden" id="id" name="id">

                <div class="modal-header">
                    <h5 class="modal-title">Coupon Form</h5>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label>Code</label>
                        <input type="text" name="code" id="code" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Type</label>
                        <select name="type" id="type" class="form-select" required>
                            <option value="fixed">Fixed</option>
                            <option value="percent">Percent</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Value</label>
                        <input type="number" step="0.01" name="value" id="value" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Max Uses</label>
                        <input type="number" name="max_uses" id="max_uses" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Min Cart Value</label>
                        <input type="number" step="0.01" name="min_cart_value" id="min_cart_value" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Start Date</label>
                        <input type="date" name="start_date" id="start_date" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>End Date</label>
                        <input type="date" name="end_date" id="end_date" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Status</label>
                        <input type="checkbox" name="is_active" id="is_active" value="1" checked>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="saveBtn" class="btn btn-primary">Save</button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function(){

    var table = $('#couponTable').DataTable({
        processing:true,
        serverSide:true,
        ajax:"{{ route('coupons.index') }}",
        columns:[
            { data:"DT_RowIndex", name:"DT_RowIndex" },
            { data:"code", name:"code" },
            { data:"type", name:"type" },
            { data:"value", name:"value" },
            { data:"max_uses", name:"max_uses" },
            { data:"min_cart_value", name:"min_cart_value" },
            { data:"status", name:"status", orderable:false, searchable:false },
            { data:"action", name:"action", orderable:false, searchable:false }
        ]
    });

    $('#addCoupon').click(function(){
        $('#couponForm')[0].reset();
        $('#id').val('');
        $('#couponModal').modal('show');
    });

    $('body').on('click','.editBtn', function(){
        var id = $(this).data('id');
        $.get("{{ url('coupons') }}/"+id+"/edit", function(data){
            $('#id').val(data.id);
            $('#code').val(data.code);
            $('#type').val(data.type);
            $('#value').val(data.value);
            $('#max_uses').val(data.max_uses);
            $('#min_cart_value').val(data.min_cart_value);
            $('#start_date').val(data.start_date);
            $('#end_date').val(data.end_date);
            $('#is_active').prop('checked', data.is_active);
            $('#couponModal').modal('show');
        });
    });

    $('#couponForm').submit(function(e){
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            url:"{{ route('coupons.store') }}",
            type:"POST",
            data:formData,
            contentType:false,
            processData:false,
            success:function(res){
                $('#couponModal').modal('hide');
                table.ajax.reload();
                Swal.fire("Success", res.success,"success");
            },
            error:function(xhr){
                Swal.fire("Error","Something went wrong!","error");
            }
        });
    });

    $('body').on('click','.deleteBtn', function(){
        var id = $(this).data('id');
        Swal.fire({
            title:"Are you sure?",
            icon:"warning",
            showCancelButton:true
        }).then(res=>{
            if(res.isConfirmed){
                $.ajax({
                    url:"{{ url('coupons') }}/"+id,
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

    $('body').on('change','.changeStatus', function(){
        var id = $(this).data('id');
        var status = $(this).is(':checked') ? 1 : 0;
        $.post("{{ route('coupons.change-status') }}", {_token:"{{ csrf_token() }}", id:id, status:status}, function(res){
            Swal.fire("Updated!",res.message,"success");
        });
    });

});
</script>
@endpush
