<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    @include('cms.topbar.menubar')

    <ul class="navbar-nav ml-auto">
        @include('cms.topbar.message')

        @include('cms.topbar.notification')
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-widget="control-sidebar" data-controlsidebar-slide="true" href="#"
                role="button">
                <i class="fas fa-th-large"></i>
            </a>
        </li>
    </ul>
</nav>
