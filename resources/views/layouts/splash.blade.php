<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('includes.meta')
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.6/clipboard.min.js"></script>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="https://kit.fontawesome.com/af0f139af6.js"></script>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

</head>
<body class="bg-gray-200">
    <div id="app">
        <nav class="bg-blue-200 shadow-md z-50">
            <div class="container mx-auto px-2">
                <div class="flex justify-between items-center py-2">
                    <div>
                        <div class="flex items-center">
                            <div>
                                <a class="navbar-brand"
                                @guest
                                href="{{ url('/') }}"
                                @else
                                    href="{{ url('/home') }}"
                                @endguest
                                >
                                <img class="h-10 w-10" src="/images/logo.svg" alt="CPD Portfolio Builder">
                            </div>
                            <div>
                                <h1 class="pl-2"><span class="hidden sm:block md:block lg:block">CPD Portfolio Builder</span>
                                <span class="block sm:hidden md:hidden lg:hidden">CPD PB</span></h1>
                            </div>

                        </div>

                        </a>
                    </div>

                    <div>
                        <ul class="flex">
                        @guest
                            <li class="nav-item mr-4">
                                <a  href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="mr-4">
                                    <a href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                        <li class="mr-4"><a href="/home">Dashboard</a></li>
                        <li class="mr-4"><a href="{{ route('portfolio.index') }}">Portfolio</a></li>
                        <li class="mr-4"><a href="{{ route('audit.index') }}">Audit</a></li>
                            <li class="mr-4">
                                <a href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                                document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </li>
                        @endguest
                        </ul>
                    </div>

                </div>

            </div>
        </nav>

        <main>
            @yield('content')
        </main>
        @include('includes.footer')
    </div>
</body>
</html>
