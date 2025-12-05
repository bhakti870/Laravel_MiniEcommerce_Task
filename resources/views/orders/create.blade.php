@extends('layouts.app')

@section('content')
    <div class="row mb-3">
        <div class="col-lg-12 d-flex justify-content-between">
            <h2>Create Order</h2>
            <a class="btn btn-primary btn-sm" href="{{ route('orders.index') }}">Back</a>
        </div>
    </div>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="card p-3">

        {{-- CUSTOMER --}}
        <div class="mb-3">
            <label>Customer Name</label>
            <input type="text" id="customer_name" class="form-control">
        </div>

        <h4>Order Items</h4>

        <div id="items"></div>

        <button id="add_item" class="btn btn-secondary btn-sm my-3">+ Add Item</button><br>

        <button id="save_order" class="btn btn-success btn-sm">Save Order</button>
    </div>

    <script>
        const csrf = document.querySelector('meta[name="csrf-token"]').content;

        // ADD ITEM ROW
        document.getElementById("add_item").addEventListener("click", () => {
            let id = Date.now();
            let html = `
                        <div class="item-row border p-3 mb-2" id="row_${id}">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <input type="text" class="form-control product" placeholder="Product Name">
                                </div>

                                <div class="col-md-2">
                                    <input type="number" class="form-control qty" placeholder="Qty">
                                </div>

                                <div class="col-md-2">
                                    <input type="number" class="form-control price" placeholder="Price">
                                </div>

                                <div class="col-md-2">
                                    <input type="number" class="form-control total" placeholder="Total" disabled>
                                </div>

                                <div class="col-md-2">
                                    <button class="btn btn-danger btn-sm w-100"
                                            onclick="document.getElementById('row_${id}').remove()">
                                        X
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
            document.getElementById("items").insertAdjacentHTML("beforeend", html);
        });

        // SAVE ORDER
        document.getElementById("save_order").addEventListener("click", () => {

            let customer_name = document.getElementById("customer_name").value;
            let items = [];

            document.querySelectorAll(".item-row").forEach(row => {
                items.push({
                    product: row.querySelector(".product").value,
                    qty: row.querySelector(".qty").value,
                    price: row.querySelector(".price").value
                });
            });

            fetch("{{ route('orders.store') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrf
                },
                body: JSON.stringify({
                    customer_name,
                    items
                })
            })
                .then(res => res.json())
                .then(data => {
                    alert(data.message);
                    window.location.href = "{{ route('orders.index') }}";
                });

        });
    </script>

@endsection