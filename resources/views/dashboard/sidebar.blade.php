<div class="side-menu sidebar-inverse">
    <nav class="navbar navbar-default" role="navigation">
        <div class="side-menu-container">
            <div class="navbar-header">
                <a class="navbar-brand" href="{{ route('ezcp.dashboard') }}">
                    <div class="logo-icon-container">
                        <?php $admin_logo_img = ezCP::setting('admin.icon_image', ''); ?>
                        @if($admin_logo_img == '')
                            <img src="{{ ezcp_asset('images/logo-icon-light.png') }}" alt="Logo Icon">
                        @else
                            <img src="{{ ezCP::image($admin_logo_img) }}" alt="Logo Icon">
                        @endif
                    </div>
                    <div class="title">{{ezCP::setting('admin.title', 'Eazy CPanel')}}</div>
                </a>
            </div><!-- .navbar-header -->

            <div class="panel widget center bgimage"
                 style="background-image:url({{ ezCP::image( ezCP::setting('admin.bg_image'), ezcp_asset('images/bg.jpg') ) }}); background-size: cover; background-position: 0px;">
                <div class="dimmer"></div>
                <div class="panel-content">
                    <img src="{{ $user_avatar }}" class="avatar" alt="{{ app('ezCPAuth')->user()->name }} avatar">
                    <h4>{{ ucwords(app('ezCPAuth')->user()->name) }}</h4>
                    <p>{{ app('ezCPAuth')->user()->email }}</p>

                    <a href="{{ route('ezcp.profile') }}" class="btn btn-primary">{{ __('ezcp::generic.profile') }}</a>
                    <div style="clear:both"></div>
                </div>
            </div>

        </div>
        <div id="adminmenu">
            <admin-menu :items="{{ menu('admin', '_json') }}"></admin-menu>
        </div>
    </nav>
</div>
