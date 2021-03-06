<!DOCTYPE html>
<!--[if IE 9]>
<html class="no-js lt-ie10" lang="pt-BR"> <![endif]-->
<!--[if gt IE 9]><!-->
<html class="no-js" lang="pt-BR"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <title>{{ config('app.name', 'Bets') }}</title>

    <link rel="apple-touch-icon" sizes="57x57" href="/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if(file_exists(public_path('css/theme.css')))
        <link rel="stylesheet" href="/css/theme.css">
    @else
        <link rel="stylesheet" href="/theme.css">
    @endif

    @if(config('app.env') !== 'production')
        <link href="/css/app.css" rel="stylesheet">
        <link href="/css/plugins.css" rel="stylesheet">
    @else
        <link href="{{ elixir('css/app.css') }}" rel="stylesheet">
        <link href="{{ elixir('css/plugins.css') }}" rel="stylesheet">
    @endif

    @yield('styles')

    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>

    <style type="text/css">
        .nav .btn-primary {
            background-color: var(--btn-primary-color);
        }
    </style>
</head>
<body>
<div id="app">
    <div id="page-container" class="header-fixed-top sidebar-visible-lg-full">
        <!-- Main Sidebar -->
        <div id="sidebar">
            <!-- Sidebar Brand -->
            <div id="sidebar-brand" class="themed-background-primary">
                @if(file_exists(public_path('storage/logo.jpeg')))
                    <a href="{{ route('web.index') }}">
                        <img width="220" src="/storage/logo.jpeg">
                    </a>
                @else
                    <a href="{{ route('web.index') }}" class="sidebar-title">
                        <i class="fa fa-futbol-o"></i>
                        <span class="sidebar-nav-mini-hide">{{ config('app.name') }}</span>
                    </a>
                @endif
            </div>
            <!-- END Sidebar Brand -->

            <!-- Wrapper for scrolling functionality -->
            <div id="sidebar-scroll">
                <!-- Sidebar Content -->
                <div class="sidebar-content">
                    <ul class="sidebar-nav">
                        <li>
                            <a href="{{ route('regras') }}">
                                <i class="fa fa-file-text-o sidebar-nav-icon"></i>
                                <span class="sidebar-nav-mini-hide">Regras</span>
                            </a>
                        </li>
                    </ul>
                    <!-- Sidebar Navigation -->
                    <web-simulator-leagues-menu></web-simulator-leagues-menu>
                    <!-- END Sidebar Navigation -->
                </div>
                <!-- END Sidebar Content -->
            </div>
            <!-- END Wrapper for scrolling functionality -->
        </div>
        <!-- END Main Sidebar -->

        <!-- Main Container -->
        <div id="main-container">
            <header class="navbar navbar-inverse navbar-fixed-top">
                <!-- Left Header Navigation -->
                <ul class="nav navbar-nav-custom">
                    <!-- Main Sidebar Toggle Button -->
                    <li>
                        <a href="javascript:void(0)" onclick="App.sidebar('toggle-sidebar');this.blur();">
                            <i class="fa fa-ellipsis-v fa-fw animation-fadeInRight" id="sidebar-toggle-mini"></i>
                            <i class="fa fa-bars fa-fw animation-fadeInRight" id="sidebar-toggle-full"></i>
                        </a>
                    </li>
                    <!-- END Main Sidebar Toggle Button -->

                    <!-- Header Link -->
                    <li class="hidden-xs animation-fadeInQuick">
                        <a><strong>Bem-vindo visitante</strong></a>
                    </li>
                    <!-- END Header Link -->
                </ul>
                <!-- END Left Header Navigation -->

                <!-- Right Header Navigation -->
                <ul class="nav navbar-nav-custom pull-right">
                    <!-- Search Form -->
                    <li>
                        <bet-search></bet-search>
                    </li>
                    <!-- END Search Form -->
                    <li>
                        <a class="btn btn-primary" href="{{ route('download') }}">
                            <i class="fa fa-android"></i>
                            &nbsp;
                            APK
                        </a>
                    </li>

                    <!-- User Dropdown -->
                    <li>
                        <a class="btn btn-primary" href="{{ url('/login') }}">
                            <i class="fa fa-user"></i>
                            &nbsp;
                            Entrar
                        </a>
                    </li>
                    <!-- END User Dropdown -->
                </ul>
                <!-- END Right Header Navigation -->
            </header>
            <!-- END Header -->

            <!-- Page content -->
            <div id="page-content">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade in" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                    aria-hidden="true">×</span>
                        </button>
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade in" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                    aria-hidden="true">×</span>
                        </button>
                        {{ session('error') }}
                    </div>
                @endif
                @yield('content')
            </div>
            <!-- END Page Content -->
        </div>
        <!-- END Main Container -->
    </div>
    <!-- END Page Container -->
</div>
<!-- END Page Wrapper -->

<!-- jQuery, Bootstrap, jQuery plugins and Custom JS code -->
@if(config('app.env') !== 'production')
    <script src="/js/app.js"></script>
    <script src="/js/plugins.js"></script>
    <script src="/js/theme.js"></script>
@else
    <script src="{{ elixir('js/app.js') }}"></script>
    <script src="{{ elixir('js/plugins.js') }}"></script>
    <script src="{{ elixir('js/theme.js') }}"></script>
@endif
@yield("scripts")
</body>
</html>
