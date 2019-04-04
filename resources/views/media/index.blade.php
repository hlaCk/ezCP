@extends('ezcp::master')

@section('page_title', __('ezcp::generic.media'))

@section('content')
    <div class="page-content container-fluid">
        @include('ezcp::alerts')
        <div class="row">
            <div class="col-md-12">

                <div class="admin-section-title">
                    <h3><i class="ezcp-images"></i> {{ __('ezcp::generic.media') }}</h3>
                </div>
                <div class="clear"></div>
                <div id="filemanager">
                    <media-manager
                        base-path="{{ config('ezcp.media.path', '/') }}"
                        :show-folders="{{ config('ezcp.media.show_folders', true) ? 'true' : 'false' }}"
                        :allow-upload="{{ config('ezcp.media.allow_upload', true) ? 'true' : 'false' }}"
                        :allow-move="{{ config('ezcp.media.allow_move', true) ? 'true' : 'false' }}"
                        :allow-delete="{{ config('ezcp.media.allow_delete', true) ? 'true' : 'false' }}"
                        :allow-create-folder="{{ config('ezcp.media.allow_create_folder', true) ? 'true' : 'false' }}"
                        :allow-rename="{{ config('ezcp.media.allow_rename', true) ? 'true' : 'false' }}"
                        :allow-crop="{{ config('ezcp.media.allow_crop', true) ? 'true' : 'false' }}"
                        ></media-manager>
                </div>
            </div><!-- .row -->
        </div><!-- .col-md-12 -->
    </div><!-- .page-content container-fluid -->
@stop

@section('javascript')
<script>
new Vue({
    el: '#filemanager'
});
</script>
@endsection
