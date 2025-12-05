@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Carts</h2>
    <button class="btn btn-success mb-3" id="addCart">Add Cart</button>
    <table class="table table-bordered table-striped" id="cartTable">
        <thead>
            <tr>
                <th>#</th>
                <th>User</th>
                <th>Subtotal</th>
                <th>Discount</th>
                <th>Tax</th>
                <th>Total</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="cartModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="cartForm">
                @csrf
                <input type="hidden" id="id" name="id">
                <div class="modal-header">
                    <h5 class="modal-title">Cart Form</h5>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>User</label>
                        <select id="user_id" name="user_id" class="form-select">
                            <option value="">-- Guest --</option>
                            @foreach(App\Models\User::all() as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Subtotal</label>
                        <input type="number" step="0.01" id="subtotal" name="subtotal" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Discount</label>
                        <input type="number" step="0.01" id="discount" name="discount" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Tax</label>
                        <input type="number" step="0.01" id="tax" name="tax" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Total</label>
                        <input type="number" step="0.01" id="total" name="total" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Session ID (optional)</label>
                        <input type="text" id="session_id" name="session_id" class="form-control">
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
    var table = $('#cartTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('carts.index') }}",
        columns: [
            { data: "DT_RowIndex", name: "DT_RowIndex" },
            { data: "user", name: "user" },
            { data: "subtotal", name: "subtotal" },
            { data: "discount", name: "discount" },
            { data: "tax", name: "tax" },
            { data: "total", name: "total" },
            { data: "action", name: "action", orderable: false, searchable: false },
        ]
    });

    $('#addCart').click(function () {
        $('#cartForm')[0].reset();
        $('#id').val('');
        $('#cartModal').modal('show');
    });

    $('body').on('click', '.editBtn', function () {
        var id = $(this).data('id');
        $.get("{{ url('/carts') }}/" + id + "/edit", function (data) {
            $('#id').val(data.id);
            $('#user_id').val(data.user_id);
            $('#subtotal').val(data.subtotal);
            $('#discount').val(data.discount);
            $('#tax').val(data.tax);
            $('#total').val(data.total);
            $('#session_id').val(data.session_id);
            $('#cartModal').modal('show');
        });
    });

    $('#cartForm').submit(function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            url: "{{ route('carts.store') }}",
            type: "POST",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function (res) {
                $('#cartModal').modal('hide');
                table.ajax.reload();
                Swal.fire('Success', res.success, 'success');
            },
            error: function (xhr) {
                Swal.fire('Error', "Error saving cart!", 'error');
            }
        });
    });

    $('body').on('click', '.deleteBtn', function () {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                var id = $(this).data('id');
                $.ajax({
                    url: "{{ url('/carts') }}/" + id,
                    type: "DELETE",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function (res) {
                        table.ajax.reload();
                        Swal.fire('Deleted!', res.success, 'success');
                    }
                });
            }
        })
    });
});
</script>
@endpush
