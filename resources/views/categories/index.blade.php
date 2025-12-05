@extends('layouts.app')

@section('content')
<div class="container">

    <h2>Categories</h2>

    <button class="btn btn-success mb-3" id="addCategory">Add Category</button>

    <table class="table table-bordered table-striped" id="categoryTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Parent Category</th>
                <th>Slug</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>

</div>

<!-- Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="categoryForm">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Category Form</h5>
                </div>

                <div class="modal-body">

                    <input type="hidden" id="id" name="id">

                    <div class="mb-3">
                        <label>Category Name</label>
                        <input type="text" id="name" name="name" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label>Parent Category</label>
                        <select id="parent_id" name="parent_id" class="form-select select2" style="width: 100%;">
                            <option value="">None</option>
                            @foreach($parentCategories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
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

    // Init Select2
    $('.select2').select2({
        theme: 'bootstrap-5',
        dropdownParent: $('#categoryModal')
    });

    var table = $('#categoryTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('categories.index') }}",
        columns: [
            { data: "DT_RowIndex", name: "DT_RowIndex" },
            { data: "name", name: "name" },
            { data: "parent_name", name: "parent_name" },
            { data: "slug", name: "slug" },
            { data: "status", name: "status" },
            { data: "action", name: "action", orderable: false, searchable: false },
        ]
    });

    // Add category
    $('#addCategory').click(function () {
        $('#categoryForm')[0].reset();
        $('#id').val('');
        $('#parent_id').val('').trigger('change');
        $('#categoryModal').modal('show');
    });

    // Edit category
    $('body').on('click', '.editBtn', function () {
        var id = $(this).data('id');

        $.get("{{ url('/categories/edit') }}/" + id, function (data) {

            $('#id').val(data.id);
            $('#name').val(data.name);
            $('#parent_id').val(data.parent_id).trigger('change');
            $('#status').val(data.status);

            $('#categoryModal').modal('show');
        });
    });

    // Save category
    $('#categoryForm').submit(function (e) {
        e.preventDefault();

        $.ajax({
            url: "{{ route('categories.store') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function (res) {
                $('#categoryModal').modal('hide');
                table.ajax.reload();
                // Reload page to update parent categories list or use AJAX to update it
                // For now, simple alert
                Swal.fire('Success', res.success, 'success');
            },
            error: function (xhr) {
                Swal.fire('Error', "Name already exists or other error!", 'error');
            }
        });
    });

    // Delete category
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
                    url: "{{ url('/categories/delete') }}/" + id,
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
