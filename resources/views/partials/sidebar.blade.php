<section id="sidebar">

    <a href="#" class="brand">
        <i class='bx bxs-smile bx-lg'></i>
        <span class="text">AdminHub</span>
    </a>

    <ul class="side-menu top">

     <!-- <li class="active">
            <a href="{{ route('home') }}">
                <i class='bx bxs-home bx-sm'></i>
                <span class="text">Home</span>
            </a>
        </li> -->


        <!-- <li class="active">
            <a href="{{ route('dashboard') }}">
                <i class='bx bxs-dashboard bx-sm'></i>
                <span class="text">Dashboard</span>
            </a>
        </li> -->

        @can('product-list')
        <li>
            <a href="{{ route('products.index') }}">
                <i class='bx bxs-package bx-sm'></i>
                <span class="text">Products</span>
            </a>
        </li>
        @endcan

          @can('product-sku-list')
         <li>
            <a href="{{ route('product-skus.index') }}">
                <i class='bx bxs-cart bx-sm'></i>
                <span class="text">Product Skus</span>
            </a>
        </li>
        @endcan

        @can('category-list')
        <li>
            <a href="{{ route('categories.index') }}">
                <i class='bx bxs-category bx-sm'></i>
                <span class="text">Categories</span>
            </a>
        </li>
        @endcan

        @can('brand-list')
        <li>
            <a href="{{ route('brands.index') }}">
                <i class='bx bxs-purchase-tag bx-sm'></i>
                <span class="text">Brands</span>
            </a>
        </li>
        @endcan

         @can('cart-list')
        <li>
            <a href="{{ route('carts.index') }}">
                <i class='bx bxs-cart bx-sm'></i>
                <span class="text">Carts</span>
            </a>
        </li>
        @endcan

      


        @can('order-list')
        <li>
            <a href="{{ url('/orders') }}">
                <i class='bx bxs-cart bx-sm'></i>
                <span class="text">Orders</span>
            </a>
        </li>
        @endcan

        <!-- coupons -->
        @can('coupon-list')
        <li>
            <a href="{{ route('coupons.index') }}">
                <i class='bx bxs-tag bx-sm'></i>
                <span class="text">Coupons</span>
            </a>
        </li>
        @endcan

        
     

        @can('role-list')
        <li>
            <a href="{{ route('roles.index') }}">
                <i class='bx bxs-shield-alt-2 bx-sm'></i>
                <span class="text">Roles</span>
            </a>
        </li>
        @endcan

           @can('user-list')
        <li>
            <a href="{{ route('users.index') }}">
                <i class='bx bxs-user bx-sm'></i>
                <span class="text">Users</span>
            </a>
        </li>
        @endcan
    </ul>

    <ul class="side-menu bottom">
        <!-- <li>
            <a href="#">
                <i class='bx bxs-cog'></i>
                <span class="text">Settings</span>
            </a>
        </li> -->

        <li>
            <a href="{{ route('logout') }}" class="logout"
               onclick="event.preventDefault(); document.getElementById('logout-form-side').submit();">
                <i class='bx bx-power-off'></i>
                <span class="text">Logout</span>
            </a>

            <form id="logout-form-side" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </li>
    </ul>

</section>
