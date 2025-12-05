@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Products</h2>
    <button class="btn btn-success mb-3" id="addProduct">Add Product</button>
    <table class="table table-bordered table-striped" id="productTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Image</th>
                <th>Name</th>
                <th>Category</th>
                <th>Brand</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form id="productForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="method_field" name="_method" value="POST">
                <input type="hidden" id="product_id" name="id">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card mb-3">
                                <div class="card-header">Basic Info</div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label>Name</label>
                                        <input type="text" id="name" name="name" class="form-control" required>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label>Category</label>
                                            <select id="category_id" name="category_id" class="form-select select2" required style="width: 100%;">
                                                <option value="">Select Category</option>
                                                @foreach($categories as $cat)
                                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Brand</label>
                                            <select id="brand_id" name="brand_id" class="form-select select2" required style="width: 100%;">
                                                <option value="">Select Brand</option>
                                                @foreach($brands as $brand)
                                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label>SKU</label>
                                            <input type="text" id="sku" name="sku" class="form-control">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label>Price</label>
                                            <input type="number" step="0.01" id="price" name="price" class="form-control" required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label>Stock</label>
                                            <input type="number" id="stock" name="stock" class="form-control" value="0">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label>Detail</label>
                                        <textarea id="detail" name="detail" class="form-control" rows="3"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label>Status</label>
                                        <select id="status" name="status" class="form-select">
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-3">
                                <div class="card-header">Variants</div>
                                <div class="card-body">
                                    <table class="table table-bordered" id="variantTable">
                                        <thead>
                                            <tr>
                                                <th>SKU</th>
                                                <th>Size</th>
                                                <th>Color</th>
                                                <th>Price</th>
                                                <th>Stock</th>
                                                <th><button type="button" class="btn btn-sm btn-success" id="addVariant">+</button></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Variants will be added here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card mb-3">
                                <div class="card-header">Main Image</div>
                                <div class="card-body">
                                    <input type="file" id="image" name="image" class="form-control">
                                    <div id="imagePreview" class="mt-2"></div>
                                </div>
                            </div>
                            <div class="card mb-3">
                                <div class="card-header">Gallery Images</div>
                                <div class="card-body">
                                    <input type="file" id="images" name="images[]" class="form-control" multiple>
                                    <div id="galleryPreview" class="mt-2 row"></div>
                                    <div id="newGalleryPreview" class="mt-2 row"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="saveBtn" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

<!-- Show Modal -->
<div class="modal fade" id="showProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Product Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div id="showImage" class="mb-3 text-center"></div>
                        <div id="showGallery" class="row"></div>
                    </div>
                    <div class="col-md-6">
                        <h4 id="showName"></h4>
                        <p><strong>Category:</strong> <span id="showCategory"></span></p>
                        <p><strong>Brand:</strong> <span id="showBrand"></span></p>
                        <p><strong>SKU:</strong> <span id="showSku"></span></p>
                        <p><strong>Price:</strong> $<span id="showPrice"></span></p>
                        <p><strong>Stock:</strong> <span id="showStock"></span></p>
                        <p><strong>Status:</strong> <span id="showStatus"></span></p>
                        <p><strong>Detail:</strong></p>
                        <p id="showDetail"></p>
                    </div>
                </div>
                <div class="mt-3">
                    <h5>Variants</h5>
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>SKU</th>
                                <th>Size</th>
                                <th>Color</th>
                                <th>Price</th>
                                <th>Stock</th>
                            </tr>
                        </thead>
                        <tbody id="showVariants"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function() {
    // Initialize Select2 inside Modal
    $('.select2').select2({
        theme: 'bootstrap-5',
        dropdownParent: $('#productModal')
    });

    var table = $('#productTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('products.index') }}",
        columns: [
            { data: 'id', name: 'id' },
            { data: 'image', name: 'image', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'category', name: 'category', name: 'category.name' },
            { data: 'brand', name: 'brand', name: 'brand.name' },
            { data: 'status', name: 'status' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    // Add Product
    $('#addProduct').click(function() {
        $('#productForm')[0].reset();
        $('#product_id').val('');
        $('#method_field').val('POST');
        $('#modalTitle').text('Add Product');
        $('#imagePreview').html('');
        $('#galleryPreview').html('');
        $('#variantTable tbody').html('');
        $('.select2').val('').trigger('change');
        $('#productModal').modal('show');
    });

    // Edit Product
    $('body').on('click', '.edit', function() {
        var url = $(this).attr('href');
        
        $.get(url, function(data) {
            $('#product_id').val(data.id);
            $('#name').val(data.name);
            $('#category_id').val(data.category_id).trigger('change');
            $('#brand_id').val(data.brand_id).trigger('change');
            $('#sku').val(data.sku);
            $('#price').val(data.price);
            $('#stock').val(data.stock);
            $('#detail').val(data.detail);
            $('#status').val(data.status);
            
            $('#method_field').val('PUT');
            $('#modalTitle').text('Edit Product');

            // Image Preview
            if(data.image) {
                $('#imagePreview').html('<img src="{{ asset('storage') }}/'+data.image+'" class="img-fluid" width="100">');
            } else {
                $('#imagePreview').html('');
            }

            // Gallery Preview
            let galleryHtml = '';
            if(data.images && data.images.length > 0) {
                data.images.forEach(function(img) {
                    galleryHtml += `
                        <div class="col-4 position-relative mb-2">
                            <img src="{{ asset('storage') }}/`+img.image_path+`" class="img-fluid">
                            <div class="form-check mt-1">
                                <input class="form-check-input" type="checkbox" name="delete_images[]" value="`+img.id+`">
                                <label class="form-check-label text-danger" style="font-size: 0.8rem;">Delete</label>
                            </div>
                        </div>
                    `;
                });
            }
            $('#galleryPreview').html(galleryHtml);

            // Variants
            let variantHtml = '';
            variantIndex = 0; 
            if(data.variants && data.variants.length > 0) {
                data.variants.forEach(function(v) {
                    variantHtml += `
                        <tr>
                            <input type="hidden" name="variants[${variantIndex}][id]" value="${v.id}">
                            <td><input type="text" name="variants[${variantIndex}][sku]" class="form-control form-control-sm" value="${v.sku}" required></td>
                            <td><input type="text" name="variants[${variantIndex}][size]" class="form-control form-control-sm" value="${v.size || ''}"></td>
                            <td><input type="text" name="variants[${variantIndex}][color]" class="form-control form-control-sm" value="${v.color || ''}"></td>
                            <td><input type="number" step="0.01" name="variants[${variantIndex}][price]" class="form-control form-control-sm" value="${v.price}" required></td>
                            <td><input type="number" name="variants[${variantIndex}][stock]" class="form-control form-control-sm" value="${v.stock}"></td>
                            <td><button type="button" class="btn btn-sm btn-danger removeVariant">x</button></td>
                        </tr>
                    `;
                    variantIndex++;
                });
            }
            $('#variantTable tbody').html(variantHtml);

            $('#productModal').modal('show');
        });
        return false; 
    });

    // Dynamic Variants
    let variantIndex = 1000; 
    
    $('#addVariant').click(function() {
        let i = Date.now(); 
        let html = `
            <tr>
                <td><input type="text" name="variants[${i}][sku]" class="form-control form-control-sm" required></td>
                <td><input type="text" name="variants[${i}][size]" class="form-control form-control-sm"></td>
                <td><input type="text" name="variants[${i}][color]" class="form-control form-control-sm"></td>
                <td><input type="number" step="0.01" name="variants[${i}][price]" class="form-control form-control-sm" required></td>
                <td><input type="number" name="variants[${i}][stock]" class="form-control form-control-sm" value="0"></td>
                <td><button type="button" class="btn btn-sm btn-danger removeVariant">x</button></td>
            </tr>
        `;
        $('#variantTable tbody').append(html);
    });

    $(document).on('click', '.removeVariant', function() {
        $(this).closest('tr').remove();
    });

    // Submit Form
    $('#productForm').submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        var id = $('#product_id').val();
        var url = "{{ route('products.store') }}";
        
        if(id) {
            url = "{{ url('/products') }}/" + id; 
        }

        $.ajax({
            url: url,
            type: "POST", 
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(res) {
                $('#productModal').modal('hide');
                table.ajax.reload();
                Swal.fire('Success', res.success, 'success');
            },
            error: function(xhr) {
                var err = JSON.parse(xhr.responseText);
                Swal.fire('Error', err.message || 'Error saving product!', 'error');
            }
        });
    });

    // Delete
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
                    url: "{{ url('/products') }}/" + id,
                    type: "DELETE",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function (res) {
                        table.ajax.reload();
                        Swal.fire('Deleted!', res.success, 'success');
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'Error deleting product!', 'error');
                    }
                });
            }
        })
    });
    // Show Product
    $('body').on('click', '.showBtn', function() {
        var id = $(this).data('id');
        $.get("{{ url('/products') }}" + '/' + id, function(data) {
            $('#showName').text(data.name);
            $('#showCategory').text(data.category ? data.category.name : '-');
            $('#showBrand').text(data.brand ? data.brand.name : '-');
            $('#showSku').text(data.sku);
            $('#showPrice').text(data.price);
            $('#showStock').text(data.stock);
            $('#showStatus').html(data.status ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>');
            $('#showDetail').text(data.detail);

            // Image
            if(data.image) {
                $('#showImage').html('<img src="{{ asset('storage') }}/'+data.image+'" class="img-fluid" style="max-height: 300px;">');
            } else {
                $('#showImage').html('');
            }

            // Gallery
            let galleryHtml = '';
            if(data.images && data.images.length > 0) {
                data.images.forEach(function(img) {
                    galleryHtml += '<div class="col-4 mb-2"><img src="{{ asset('storage') }}/'+img.image_path+'" class="img-fluid"></div>';
                });
            }
            $('#showGallery').html(galleryHtml);

            // Variants
            let variantHtml = '';
            if(data.variants && data.variants.length > 0) {
                data.variants.forEach(function(v) {
                    variantHtml += `
                        <tr>
                            <td>${v.sku}</td>
                            <td>${v.size || '-'}</td>
                            <td>${v.color || '-'}</td>
                            <td>${v.price}</td>
                            <td>${v.stock}</td>
                        </tr>
                    `;
                });
            } else {
                variantHtml = '<tr><td colspan="5" class="text-center">No variants</td></tr>';
            }
            $('#showVariants').html(variantHtml);

            $('#showProductModal').modal('show');
        });
    });

    // Image Preview for Selected Files
    $('#image').change(function(){
        let reader = new FileReader();
        reader.onload = (e) => { 
            $('#imagePreview').html('<img src="'+e.target.result+'" class="img-fluid" width="100">'); 
        }
        if(this.files[0]) {
            reader.readAsDataURL(this.files[0]); 
        } else {
             $('#imagePreview').html('');
        }
    });

    $('#images').change(function(){
        $('#newGalleryPreview').html('');
        if(this.files) {
            Array.from(this.files).forEach(file => {
                let reader = new FileReader();
                reader.onload = (e) => {
                    $('#newGalleryPreview').append('<div class="col-4 mb-2"><img src="'+e.target.result+'" class="img-fluid"></div>');
                }
                reader.readAsDataURL(file);
            });
        }
    });

    // Clear new gallery preview when modal is closed or reset
    $('#productModal').on('hidden.bs.modal', function () {
        $('#newGalleryPreview').html('');
    });

});
</script>
@endpush