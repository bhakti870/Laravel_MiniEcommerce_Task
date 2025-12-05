<nav>

    <i class='bx bx-menu bx-sm' id="menu-icon"></i>

    <a class="nav-link">Categories</a>

    <form>
        <div class="form-input">
            <input type="search" placeholder="Search...">
            <button type="submit">
                <i class='bx bx-search'></i>
            </button>
        </div>
    </form>

    <input type="checkbox" id="switch-mode" hidden>
    <label class="swith-lm" for="switch-mode">
        <i class="bx bxs-moon"></i>
        <i class="bx bx-sun"></i>
        <div class="ball"></div>
    </label>

    {{-- NOTIFICATION ICON --}}
    <a class="notification" id="notificationIcon">
        <i class='bx bxs-bell'></i>
        <span class="num" id="notifCount">0</span>
    </a>

    {{-- DROPDOWN --}}
    <div class="notification-menu" id="notificationMenu" style="display:none;">
        <ul id="notifList"></ul>
    </div>

    {{-- PROFILE --}}
    @guest
        @if (Route::has('login'))
            <a class="nav-link" href="{{ route('login') }}">Login</a>
        @endif
        @if (Route::has('register'))
            <a class="nav-link" href="{{ route('register') }}">Register</a>
        @endif
    @endguest

    @auth
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown"
           data-bs-toggle="dropdown">
            {{ Auth::user()->name }}
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="#">My Profile</a></li>
            <li><a class="dropdown-item" href="#">Settings</a></li>
            <li>
                <a href="{{ route('logout') }}" class="logout"
                   onclick="event.preventDefault(); document.getElementById('logout-form-nav').submit();">
                    <i class='bx bx-power-off'></i>
                    <span class="text">Logout</span>
                </a>

                <form id="logout-form-nav" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </li>
        </ul>
    </li>
    @endauth
</nav>

{{-- AJAX LOGIC --}}
<script>
document.getElementById("notificationIcon").addEventListener("click", function() {
    let menu = document.getElementById("notificationMenu");
    menu.style.display = (menu.style.display === "none") ? "block" : "none";
});

// AUTO FETCH EVERY 10 SECONDS
function loadNotifications() {
    fetch("{{ route('notifications.fetch') }}")
        .then(res => res.json())
        .then(data => {
            document.getElementById("notifCount").innerText = data.count;

            let list = document.getElementById("notifList");
            list.innerHTML = "";

            if (data.items.length === 0) {
                list.innerHTML = "<li>No new notifications</li>";
            } else {
                data.items.forEach(item => {
                    let li = document.createElement("li");
                    li.innerText = item.message;
                    list.appendChild(li);
                });
            }
        });
}

loadNotifications();
setInterval(loadNotifications, 10000);
</script>
