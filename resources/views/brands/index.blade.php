@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Brands</h2>
    <button class="btn btn-success mb-3" id="addBrand">Add Brand</button>
    <table class="table table-bordered table-striped" id="brandTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Logo</th>
                <th>Name</th>
                <th>Slug</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="brandModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="brandForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Brand Form</h5>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    <div class="mb-3">
                        <label>Brand Name</label>
                        <input type="text" id="name" name="name" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Logo</label>
                        <input type="file" id="logo" name="logo" class="form-control">
                        <div id="logoPreview" class="mt-2"></div>
                    </div>
                    <div class="mb-3">
                        <label>Status</label>
                        <select id="status" name="status" class="form-select">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
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
    var table = $('#brandTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('brands.index') }}",
        columns: [
            { data: "DT_RowIndex", name: "DT_RowIndex" },
            { data: "logo", name: "logo", orderable: false, searchable: false },
            { data: "name", name: "name" },
            { data: "slug", name: "slug" },
            { data: "status", name: "status" },
            { data: "action", name: "action", orderable: false, searchable: false },
        ]
    });

    $('#addBrand').click(function () {
        $('#brandForm')[0].reset();
        $('#id').val('');
        $('#logoPreview').html('');
        $('#brandModal').modal('show');
    });

    $('body').on('click', '.editBtn', function () {
        var id = $(this).data('id');
        $.get("{{ url('/brands') }}/" + id + "/edit", function (data) {
            $('#id').val(data.id);
            $('#name').val(data.name);
            $('#status').val(data.status);
            if(data.logo){
                $('#logoPreview').html('<img src="{{ asset('storage') }}/'+data.logo+'" width="50">');
            } else {
                $('#logoPreview').html('');
            }
            $('#brandModal').modal('show');
        });
    });

    $('#brandForm').submit(function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            url: "{{ route('brands.store') }}",
            type: "POST",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function (res) {
                $('#brandModal').modal('hide');
                table.ajax.reload();
                Swal.fire('Success', res.success, 'success');
            },
            error: function (xhr) {
                Swal.fire('Error', "Error saving brand!", 'error');
            }
        });
    });

    $('body').on('click', '.deleteBtn', function () {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                var id = $(this).data('id');
                $.ajax({
                    url: "{{ url('/brands') }}/" + id,
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
