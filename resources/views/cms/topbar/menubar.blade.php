<ul class="navbar-nav">
    <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
        <a href="/cms" class="nav-link">Home</a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
        <form action="/logout" method="post">
            @csrf
            <button class="btn btn-warning">Logout</button>
        </form>
    </li>
</ul>
