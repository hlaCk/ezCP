<!DOCTYPE html>
<html lang="{{ config('app.locale') }}" dir="{{ __('ezcp::generic.is_rtl') == 'true' ? 'rtl' : 'ltr' }}">
<head>
    <title>@yield('page_title', setting('admin.title') . " - " . setting('admin.description'))</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <meta name="assets-path" content="{{ route('ezcp.assets') }}"/>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ ezcp_asset('images/logo-icon.png') }}" type="image/x-icon">



    <!-- App CSS -->
    <link rel="stylesheet" href="{{ ezcp_asset('css/app.css') }}">

    @yield('css')
    @if(config('ezcp.multilingual.rtl'))
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-rtl/3.4.0/css/bootstrap-rtl.css">
        <link rel="stylesheet" href="{{ ezcp_asset('css/rtl.css') }}">
    @endif

    <!-- Few Dynamic Styles -->
    <style type="text/css">
        .ezcp .side-menu .navbar-header {
            background:{{ config('ezcp.primary_color','#22A7F0') }};
            border-color:{{ config('ezcp.primary_color','#22A7F0') }};
        }
        .widget .btn-primary{
            border-color:{{ config('ezcp.primary_color','#22A7F0') }};
        }
        .widget .btn-primary:focus, .widget .btn-primary:hover, .widget .btn-primary:active, .widget .btn-primary.active, .widget .btn-primary:active:focus{
            background:{{ config('ezcp.primary_color','#22A7F0') }};
        }
        .ezcp .breadcrumb a{
            color:{{ config('ezcp.primary_color','#22A7F0') }};
        }
    </style>

    @if(!empty(config('ezcp.additional_css')))<!-- Additional CSS -->
        @foreach(config('ezcp.additional_css') as $css)<link rel="stylesheet" type="text/css" href="{{ asset($css) }}">@endforeach
    @endif

    @yield('head')
</head>

<body class="ezcp @if(isset($dataType) && isset($dataType->slug)){{ $dataType->slug }}@endif">

<div id="ezcp-loader">
    <?php $admin_loader_img = ezCP::setting('admin.loader', ''); ?>
    @if($admin_loader_img == '')
        <img src="{{ ezcp_asset('images/logo-icon.png') }}" alt="ezCP Loader">
    @else
        <img src="{{ ezCP::image($admin_loader_img) }}" alt="ezCP Loader">
    @endif
</div>

<?php
if (starts_with(app('ezCPAuth')->user()->avatar, 'http://') || starts_with(app('ezCPAuth')->user()->avatar, 'https://')) {
    $user_avatar = app('ezCPAuth')->user()->avatar;
} else {
    $user_avatar = ezCP::image(app('ezCPAuth')->user()->avatar);
}
?>

<div class="app-container">
    <div class="fadetoblack visible-xs"></div>
    <div class="row content-container">
        @include('ezcp::dashboard.navbar')
        @include('ezcp::dashboard.sidebar')
        <script>
            (function(){
                    var appContainer = document.querySelector('.app-container'),
                        sidebar = appContainer.querySelector('.side-menu'),
                        navbar = appContainer.querySelector('nav.navbar.navbar-top'),
                        loader = document.getElementById('ezcp-loader'),
                        hamburgerMenu = document.querySelector('.hamburger'),
                        sidebarTransition = sidebar.style.transition,
                        navbarTransition = navbar.style.transition,
                        containerTransition = appContainer.style.transition;

                    sidebar.style.WebkitTransition = sidebar.style.MozTransition = sidebar.style.transition =
                    appContainer.style.WebkitTransition = appContainer.style.MozTransition = appContainer.style.transition =
                    navbar.style.WebkitTransition = navbar.style.MozTransition = navbar.style.transition = 'none';

                    if (window.localStorage && window.localStorage['ezcp.stickySidebar'] == 'true') {
                        appContainer.className += ' expanded no-animation';
                        loader.style.left = (sidebar.clientWidth/2)+'px';
                        hamburgerMenu.className += ' is-active no-animation';
                    }

                   navbar.style.WebkitTransition = navbar.style.MozTransition = navbar.style.transition = navbarTransition;
                   sidebar.style.WebkitTransition = sidebar.style.MozTransition = sidebar.style.transition = sidebarTransition;
                   appContainer.style.WebkitTransition = appContainer.style.MozTransition = appContainer.style.transition = containerTransition;
            })();
        </script>
        <!-- Main Content -->
        <div class="container-fluid">
            <div class="side-body padding-top">
                @yield('page_header')
                <div id="ezcp-notifications"></div>
                @yield('content')
            </div>
        </div>
    </div>
</div>
@include('ezcp::partials.app-footer')

<!-- Javascript Libs -->


<script type="text/javascript" src="{{ ezcp_asset('js/app.js') }}"></script>

<script>
    @if(Session::has('alerts'))
        let alerts = {!! json_encode(Session::get('alerts')) !!};
        helpers.displayAlerts(alerts, toastr);
    @endif

    @if(Session::has('message'))

    // TODO: change Controllers to use AlertsMessages trait... then remove this
    var alertType = {!! json_encode(Session::get('alert-type', 'info')) !!};
    var alertMessage = {!! json_encode(Session::get('message')) !!};
    var alerter = toastr[alertType];

    if (alerter) {
        alerter(alertMessage);
    } else {
        toastr.error("toastr alert-type " + alertType + " is unknown");
    }
    @endif
</script>
@include('ezcp::media.manager')
@include('ezcp::menu.admin_menu')
<script>
new Vue({
    el: '#adminmenu',
});
</script>
@yield('javascript')
@stack('javascript')
@if(!empty(config('ezcp.additional_js')))<!-- Additional Javascript -->
    @foreach(config('ezcp.additional_js') as $js)<script type="text/javascript" src="{{ asset($js) }}"></script>@endforeach
@endif

</body>
</html>
