@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Orders</h2>
    <button class="btn btn-success mb-3" id="addOrder">Add Order</button>

    <table class="table table-bordered table-striped" id="orderTable">
        <thead>
            <tr>
                <th>#</th>
                <th>User</th>
                <th>Subtotal</th>
                <th>Discount</th>
                <th>Total</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="orderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="orderForm">
                @csrf
                <input type="hidden" id="id" name="id">

                <div class="modal-header">
                    <h5 class="modal-title">Order Form</h5>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label>User</label>
                        <select name="user_id" id="user_id" class="form-select">
                            @foreach(App\Models\User::all() as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Subtotal</label>
                        <input type="number" step="0.01" name="subtotal" id="subtotal" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label>Discount</label>
                        <input type="number" step="0.01" name="discount" id="discount" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label>Total</label>
                        <input type="number" step="0.01" name="total" id="total" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label>Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
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
$(function () {

   var table = $('#orderTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('orders.index') }}",
    columns: [
        { data: "DT_RowIndex", name: "DT_RowIndex" },
        { data: "user", name: "user" },
        { data: "subtotal", name: "subtotal" },
        { data: "discount", name: "discount" },
        { data: "total", name: "total" },
        { data: "status_dropdown", orderable: false, searchable: false },
        { data: "action", orderable: false, searchable: false },
    ]
});


    $('#addOrder').click(function () {
        $('#orderForm')[0].reset();
        $('#id').val('');
        $('#orderModal').modal('show');
    });

    $('body').on('click', '.editBtn', function () {
        var id = $(this).data('id');
        $.get("{{ url('/orders') }}/" + id + "/edit", function (data) {
            $('#id').val(data.id);
            $('#user_id').val(data.user_id);
            $('#subtotal').val(data.subtotal);
            $('#discount').val(data.discount);
            $('#total').val(data.total);
            $('#status').val(data.status);
            $('#orderModal').modal('show');
        });
    });

    $('#orderForm').submit(function (e) {
        e.preventDefault();

        var formData = new FormData(this);

        $.ajax({
            url: "{{ route('orders.store') }}",
            type: "POST",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function (res) {
                $('#orderModal').modal('hide');
                table.ajax.reload();
                Swal.fire("Success", res.success, "success");
            },
            error: function () {
                Swal.fire("Error", "Something went wrong!", "error");
            }
        });
    });

    $('body').on('click', '.deleteBtn', function () {
        var id = $(this).data('id');

        Swal.fire({
            title: "Are you sure?",
            text: "This action cannot be undone!",
            icon: "warning",
            showCancelButton: true
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    url: "{{ url('/orders') }}/" + id,
                    type: "DELETE",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function (res) {
                        table.ajax.reload();
                        Swal.fire("Deleted!", res.success, "success");
                    }
                });

            }
        });
    });


    /**
     * Order status change
     */
    $('body').on('change', '.changeStatus', function () {
        var id = $(this).data('id');
        var status = $(this).val();

        $.ajax({
            url: "{{ route('orders.change-status') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: id,
                status: status
            },
            success: function(res){
                Swal.fire('Updated!', res.message, 'success');
            }
        });
    });

});
</script>
@endpush
