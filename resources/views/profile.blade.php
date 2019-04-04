@extends('ezcp::master')

@section('css')
    <style>
        .user-email {
            font-size: .85rem;
            margin-bottom: 1.5em;
        }
    </style>
@stop

@section('content')
    <div style="background-size:cover; background-image: url({{ ezCP::image( ezCP::setting('admin.bg_image'), ezcp_asset('/images/bg.jpg')) }}); background-position: center center;position:absolute; top:0; left:0; width:100%; height:300px;"></div>
    <div style="height:160px; display:block; width:100%"></div>
    <div style="position:relative; z-index:9; text-align:center;">
        <img src="@if( !filter_var(app('ezCPAuth')->user()->avatar, FILTER_VALIDATE_URL)){{ ezCP::image( app('ezCPAuth')->user()->avatar ) }}@else{{ app('ezCPAuth')->user()->avatar }}@endif"
             class="avatar"
             style="border-radius:50%; width:150px; height:150px; border:5px solid #fff;"
             alt="{{ app('ezCPAuth')->user()->name }} avatar">
        <h4>{{ ucwords(app('ezCPAuth')->user()->name) }}</h4>
        <div class="user-email text-muted">{{ ucwords(app('ezCPAuth')->user()->email) }}</div>
        <p>{{ app('ezCPAuth')->user()->bio }}</p>
        <a href="{{ route('ezcp.users.edit', app('ezCPAuth')->user()->getKey()) }}" class="btn btn-primary">{{ __('ezcp::profile.edit') }}</a>
    </div>
@stop
