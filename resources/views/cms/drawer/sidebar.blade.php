<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-header">Dashboard</li>
        <li class="nav-item menu-open">
            <a href="#" class="nav-link active">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>
                    Dashboard
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="/cms" class="nav-link {{ Request::is('cms') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/cms/helper" class="nav-link {{ Request::is('cms/helper') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Helper</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/cms/bantuan" class="nav-link {{ Request::is('cms/bantuan') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Bantuan</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/cms/notif" class="nav-link {{ Request::is('cms/notif') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Notification</p>
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</nav>
