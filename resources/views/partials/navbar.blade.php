<!-- Header -->
<header class="vogue-header"> 
    <div class="container-fluid px-4">
    <div class="row align-items-center py-1">
            <!-- Logo -->
            <div class="col-auto">
                <a href="{{ route('home') }}" class="logo-container">
                    <img src="{{ asset('images/logo1.png') }}" alt="VogueVault logo" class="logo-image">
                    <span class="logo-text">VogueVault</span>
                </a>
            </div>
            
            <!-- Right Navigation -->
            <div class="col">
                <div class="nav-icons-container">
                    @auth
                        @if(!Auth::user()->isAdmin())
                            <!-- Orders -->
                            <a href="{{ route('orders.index') }}" class="nav-icon" title="Orders">
                                <i class="bi bi-receipt"></i>
                                <span class="nav-text">Orders</span>
                            </a>
                        @endif
                    @endauth

                    <!-- Cart -->
                    <a href="{{ route('cart.overview') }}" class="nav-icon" title="Cart">
                        <i class="bi bi-cart3"></i>
                        <span class="nav-text">Cart</span>
                    </a>

                    <!-- Profile Dropdown -->
                    <div class="dropdown d-inline-block">
                        <a class="nav-icon profile-icon dropdown-toggle text-decoration-none"
                           href="#" id="profileDropdown" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle"></i>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                            @auth
                                <li class="dropdown-item text-center">
                                    <strong>{{ Auth::user()->name }}</strong>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right me-1"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            @endauth

                            @guest
                                <li>
                                    <a class="dropdown-item" href="{{ route('login') }}">
                                        <i class="bi bi-box-arrow-in-right me-1"></i> Login
                                    </a>
                                </li>
                            @endguest
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>