<nav id="nav-desktop" class="navbar navbar-expand fixed-top d-none d-lg-flex">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ Vite::asset('resources/images/logo.svg') }}" class="logo-shadow" width="32" height="32" alt="">
            {{ getSiteConfig('name') }}
        </a>

        <div id="header-navbar" class="collapse navbar-collapse">
            @include('partials.navlinks-desktop')
        </div>
    </div>
</nav>

<nav id="nav-mobile" class="navbar navbar-expand fixed-bottom d-lg-none">
    <div class="container">
        @include('partials.navlinks-mobile')
    </div>
</nav>
