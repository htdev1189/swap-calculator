<div class="left-side-bar">
    <div class="brand-logo">
        <a href="{{ route('admin.home') }}">
            <img
                src="{{ asset('vendors/images/deskapp-logo.svg') }}"
                alt="logo"
                class="dark-logo"
            />
            <img
                src="{{ asset('vendors/images/deskapp-logo-white.svg') }}"
                alt="logo"
                class="light-logo"
            />
        </a>
        <div class="close-sidebar" data-toggle="left-sidebar-close">
            <i class="ion-close-round"></i>
        </div>
    </div>

    <div class="menu-block customscroll">
        <div class="sidebar-menu">
            <ul id="accordion-menu">
                <li>
                    <a href="{{ route('admin.swap') }}"
                        class="dropdown-toggle no-arrow {{ request()->routeIs('admin.swap') ? 'active' : '' }}">
                        <span class="micon bi bi-house"></span>
                        <span class="mtext">Add Swap</span>
                    </a>
                    <a href="{{ route('admin.swap.history.datatable') }}"
                        class="dropdown-toggle no-arrow {{ request()->routeIs('admin.swap.history.datatable') ? 'active' : '' }}">
                        <span class="micon bi bi-house"></span>
                        <span class="mtext">History Swap</span>
                    </a>
                    <a href="{{ route('admin.swap.history.statistics') }}"
                        class="dropdown-toggle no-arrow {{ request()->routeIs('admin.swap.history.statistics') ? 'active' : '' }}">
                        <span class="micon bi bi-house"></span>
                        <span class="mtext">Statistics</span>
                    </a>
                    <a href="{{ route('admin.swap.import') }}"
                        class="dropdown-toggle no-arrow {{ request()->routeIs('admin.swap.import') ? 'active' : '' }}">
                        <span class="micon bi bi-house"></span>
                        <span class="mtext">Data Import</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
