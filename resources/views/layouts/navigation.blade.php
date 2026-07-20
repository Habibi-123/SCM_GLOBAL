<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">
            Supply Chain Risk Platform
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMain">

            <!-- Menu Utama -->
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active fw-semibold' : '' }}"
                       href="{{ route('dashboard') }}">
                        Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('countries.*') ? 'active fw-semibold' : '' }}"
                       href="{{ route('countries.index') }}">
                        Countries
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('compare.*') ? 'active fw-semibold' : '' }}"
                       href="{{ route('compare.index') }}">
                        Compare
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('ports.*') ? 'active fw-semibold' : '' }}"
                       href="{{ route('ports.index') }}">
                        Ports
                    </a>
                </li>


                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('weather.*') ? 'active fw-semibold' : '' }}"
                       href="{{ route('weather.index') }}">
                        Weather
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('currency.*') ? 'active fw-semibold' : '' }}"
                       href="{{ route('currency.index') }}">
                        Currency
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('news.*') ? 'active fw-semibold' : '' }}"
                       href="{{ route('news.index') }}">
                        News
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('visualization.*') ? 'active fw-semibold' : '' }}"
                       href="{{ route('visualization.index') }}">
                        Visualization
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('watchlist.*') ? 'active fw-semibold' : '' }}"
                       href="{{ route('watchlist.index') }}">
                        Watchlist
                    </a>
                </li>
            </ul>

            <!-- Menu User -->
            <ul class="navbar-nav ms-auto align-items-center">

                @role('Admin')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.*') ? 'active fw-semibold' : '' }}"
                           href="{{ route('admin.dashboard') }}">
                            Admin Panel
                        </a>
                    </li>
                @endrole

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle"
                       href="#"
                       role="button"
                       data-bs-toggle="dropdown"
                       aria-expanded="false">
                        {{ Auth::user()->name }}
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                Profil
                            </a>
                        </li>

                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>

        </div>
    </div>
</nav>