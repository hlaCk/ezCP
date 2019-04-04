@section('media-manager')
<div>
    <div v-if="hidden_element" class="dd">
        <ol id="files" class="dd-list">
            <li v-for="file in getSelectedFiles()" class="dd-item" :data-url="file">
                <div class="file_link selected" aria-hidden="true" data-toggle="tooltip" data-placement="auto" :title="file">
                    <div class="link_icon">
                        <template v-if="fileIs(file, 'image')">
                            <div class="img_icon" :style="imgIcon('{{ Storage::disk(config('ezcp.storage.disk'))->url('') }}'+file)"></div>
                        </template>
                        <template v-else-if="fileIs(file, 'video')">
                            <i class="icon ezcp-video"></i>
                        </template>
                        <template v-else-if="fileIs(file, 'audio')">
                            <i class="icon ezcp-music"></i>
                        </template>
                        <template v-else-if="fileIs(file, 'zip')">
                            <i class="icon ezcp-archive"></i>
                        </template>
                        <template v-else-if="fileIs(file, 'folder')">
                            <i class="icon ezcp-folder"></i>
                        </template>
                        <template v-else>
                            <i class="icon ezcp-file-text"></i>
                        </template>
                    </div>
                    <div class="details">
                        <div class="folder">
                            <h4>@{{ getFileName(file) }}</h4>
                        </div>
                    </div>
                    <i class="ezcp-x dd-nodrag" v-on:click="removeFileFromInput(file)"></i>
                </div>
            </li>
        </ol>
    </div>
    <div v-if="hidden_element">
        <div class="btn btn-sm btn-default" v-on:click="expanded = !expanded;" style="width:100%">
            <div v-if="!expanded"><i class="ezcp-double-down"></i> {{ __('ezcp::generic.open') }}</div>
            <div v-if="expanded"><i class="ezcp-double-up"></i> {{ __('ezcp::generic.close') }}</div>
        </div>
    </div>
    <div id="toolbar" v-if="showToolbar" :style="expanded ? 'display:block' : 'display:none'">
        <div class="btn-group offset-right">
            <button type="button" class="btn btn-primary" id="upload" v-if="allowUpload">
                <i class="ezcp-upload"></i>
                {{ __('ezcp::generic.upload') }}
            </button>
            <button type="button" class="btn btn-primary" v-if="allowCreateFolder" data-toggle="modal" :data-target="'#create_dir_modal_'+this._uid">
                <i class="ezcp-folder"></i>
                {{ __('ezcp::generic.add_folder') }}
            </button>
        </div>
        <button type="button" class="btn btn-default" v-on:click="getFiles()">
            <i class="ezcp-refresh"></i>
        </button>
        <div class="btn-group offset-right">
            <button type="button" v-if="showFolders && allowMove" class="btn btn-default" data-toggle="modal" :data-target="'#move_files_modal_'+this._uid">
                <i class="ezcp-move"></i>
                {{ __('ezcp::generic.move') }}
            </button>
            <button type="button" v-if="allowDelete" :disabled="selected_files.length == 0" class="btn btn-default" data-toggle="modal" :data-target="'#confirm_delete_modal_'+this._uid">
                <i class="ezcp-trash"></i>
                {{ __('ezcp::generic.delete') }}
            </button>
            <button v-if="allowCrop" :disabled="selected_files.length != 1 || !fileIs(selected_file, 'image')" type="button" class="btn btn-default" data-toggle="modal" :data-target="'#crop_modal_'+this._uid">
                <i class="ezcp-crop"></i>
                {{ __('ezcp::media.crop') }}
            </button>
        </div>
    </div>
    <div id="uploadPreview" style="display:none;" v-if="allowUpload"></div>
    <div id="uploadProgress" class="progress active progress-striped" v-if="allowUpload">
        <div class="progress-bar progress-bar-success" style="width: 0"></div>
    </div>
    <div id="content" :style="expanded ? 'display:block' : 'display:none'">
        <div class="breadcrumb-container">
            <ol class="breadcrumb filemanager">
                <li class="media_breadcrumb" v-on:click="setCurrentPath(-1)">
                    <span class="arrow"></span>
                    <strong>{{ __('ezcp::media.library') }}</strong>
                </li>
                <li v-for="(folder, i) in getCurrentPath()" v-on:click="setCurrentPath(i)">
                    <span class="arrow"></span>
                    @{{ folder }}
                </li>
            </ol>
        </div>
        <div class="flex">
            <div id="left">
                <ul id="files">
                    <li v-for="(file) in files" v-on:click="selectFile(file, $event)" v-on:dblclick="openFile(file)" v-if="filter(file)">
                        <div :class="'file_link ' + (isFileSelected(file) ? 'selected' : '')">
                            <div class="link_icon">
                                <template v-if="fileIs(file, 'image')">
                                    <div class="img_icon" :style="imgIcon(file.path)"></div>
                                </template>
                                <template v-else-if="fileIs(file, 'video')">
                                    <i class="icon ezcp-video"></i>
                                </template>
                                <template v-else-if="fileIs(file, 'audio')">
                                    <i class="icon ezcp-music"></i>
                                </template>
                                <template v-else-if="fileIs(file, 'zip')">
                                    <i class="icon ezcp-archive"></i>
                                </template>
                                <template v-else-if="fileIs(file, 'folder')">
                                    <i class="icon ezcp-folder"></i>
                                </template>
                                <template v-else>
                                    <i class="icon ezcp-file-text"></i>
                                </template>
                            </div>
                            <div class="details">
                                <div :class="file.type">
                                    <h4>@{{ file.name }}</h4>
                                    <small v-if="!fileIs(file, 'folder')">
                                        <span class="file_size">@{{ bytesToSize(file.size) }}</span>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
                <div id="file_loader" v-if="is_loading">
                    <?php $admin_loader_img = ezCP::setting('admin.loader', ''); ?>
                    @if($admin_loader_img == '')
                    <img src="{{ ezcp_asset('images/logo-icon.png') }}" alt="ezCP Loader">
                    @else
                    <img src="{{ ezCP::image($admin_loader_img) }}" alt="ezCP Loader">
                    @endif
                    <p>{{ __('ezcp::media.loading') }}</p>
                </div>

                <div id="no_files" v-if="files.length == 0">
                    <h3><i class="ezcp-meh"></i> {{ __('ezcp::media.no_files_in_folder') }}</h3>
                </div>
            </div>
            <div id="right">
                <div class="right_details">
                    <div v-if="selected_files.length > 1" class="right_none_selected">
                        <i class="ezcp-list"></i>
                        <p>@{{ selected_files.length }} {{ __('ezcp::media.files_selected') }}</p>
                    </div>
                    <div v-else-if="selected_files.length == 1" class="right_details">
                        <div class="detail_img">
                            <div v-if="fileIs(selected_file, 'image')">
                                <img :src="selected_file.path" />
                            </div>
                            <div v-else-if="fileIs(selected_file, 'video')">
                                <video width="100%" height="auto" ref="videoplayer" controls>
                                    <source :src="selected_file.path" type="video/mp4">
                                    <source :src="selected_file.path" type="video/ogg">
                                    <source :src="selected_file.path" type="video/webm">
                                    {{ __('ezcp::media.browser_video_support') }}
                                </video>
                            </div>
                            <div v-else-if="fileIs(selected_file, 'audio')">
                                <i class="ezcp-music"></i>
                                <audio controls style="width:100%; margin-top:5px;" ref="audioplayer">
                                    <source :src="selected_file.path" type="audio/ogg">
                                    <source :src="selected_file.path" type="audio/mpeg">
                                    {{ __('ezcp::media.browser_audio_support') }}
                                </audio>
                            </div>
                            <div v-else-if="fileIs(selected_file, 'zip')">
                                <i class="ezcp-archive"></i>
                            </div>
                            <div v-else-if="fileIs(selected_file, 'folder')">
                                <i class="ezcp-folder"></i>
                            </div>
                            <div v-else>
                                <i class="ezcp-file-text"></i>
                            </div>
                        </div>
                        <div class="detail_info">
                            <span>
                                <h4>{{ __('ezcp::media.title') }}:</h4>
                                <input v-if="allowRename" type="text" class="form-control" :value="selected_file.name" @keydown.enter.prevent="renameFile">
                                <p v-else>@{{ selected_file.name }}</p>
                            </span>
                            <span>
                                <h4>{{ __('ezcp::media.type') }}:</h4>
                                <p>@{{ selected_file.type }}</p>
                            </span>

                            <template v-if="!fileIs(selected_file, 'folder')">
                                <span>
                                    <h4>{{ __('ezcp::media.size') }}:</h4>
                                    <p><span class="selected_file_size">@{{ bytesToSize(selected_file.size) }}</span></p>
                                </span>
                                <span>
                                    <h4>{{ __('ezcp::media.public_url') }}:</h4>
                                    <p><a :href="selected_file.path" target="_blank">{{ __('ezcp::generic.click_here') }}</a></p>
                                </span>
                                <span>
                                    <h4>{{ __('ezcp::media.last_modified') }}:</h4>
                                    <p>@{{ dateFilter(selected_file.last_modified) }}</p>
                                </span>
                            </template>
                        </div>
                    </div>
                    <div v-else class="right_none_selected">
                        <i class="ezcp-cursor"></i>
                        <p>{{ __('ezcp::media.nothing_selected') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div class="modal fade" :id="'imagemodal_'+this._uid" v-if="selected_file && fileIs(selected_file, 'image')">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <img :src="selected_file.path" class="img img-responsive" style="margin: 0 auto;">
                </div>

                <div class="modal-footer text-left">
                    <small class="image-title">@{{ selected_file.name }}</small>
                </div>

            </div>
        </div>
    </div>
    <!-- End Image Modal -->

    <!-- New Folder Modal -->
    <div class="modal fade modal-info" :id="'create_dir_modal_'+this._uid">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><i class="ezcp-folder"></i> {{ __('ezcp::media.add_new_folder') }}</h4>
                </div>

                <div class="modal-body">
                    <input name="new_folder_name" placeholder="{{ __('ezcp::media.new_folder_name') }}" class="form-control" value="" v-model="modals.new_folder.name" />
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('ezcp::generic.cancel') }}</button>
                    <button type="button" class="btn btn-info" v-on:click="createFolder">{{ __('ezcp::media.create_new_folder') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- End New Folder Modal -->

    <!-- Delete File Modal -->
    <div class="modal fade modal-danger" :id="'confirm_delete_modal_'+this._uid" v-if="allowDelete">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><i class="ezcp-warning"></i> {{ __('ezcp::generic.are_you_sure') }}</h4>
                </div>

                <div class="modal-body">
                    <h4>{{ __('ezcp::media.delete_question') }}</h4>
                    <ul>
                        <li v-for="file in selected_files">@{{ file.name }}</li>
                    </ul>
                    <h5 class="folder_warning">
                        <i class="ezcp-warning"></i> {{ __('ezcp::media.delete_folder_question') }}
                    </h5>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('ezcp::generic.cancel') }}</button>
                    <button type="button" class="btn btn-danger" v-on:click="deleteFiles">{{ __('ezcp::generic.delete_confirm') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Delete File Modal -->

    <!-- Move Files Modal -->
    <div class="modal fade modal-warning" :id="'move_files_modal_'+this._uid" v-if="allowMove">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><i class="ezcp-move"></i> {{ __('ezcp::media.move_file_folder') }}</h4>
                </div>

                <div class="modal-body">
                    <h4>{{ __('ezcp::media.destination_folder') }}</h4>
                    <select class="form-control" v-model="modals.move_files.destination">
                        <option value="" disabled>{{ __('ezcp::media.destination_folder') }}</option>
                        <option v-if="current_folder != basePath && showFolders" value="/../">../</option>
                        <option v-for="file in files" v-if="file.type == 'folder' && !selected_files.includes(file)" :value="current_folder+'/'+file.name">@{{ file.name }}</option>
                    </select>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('ezcp::generic.cancel') }}</button>
                    <button type="button" class="btn btn-warning" v-on:click="moveFiles">{{ __('ezcp::generic.move') }}</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Move File Modal -->

    <!-- Crop Image Modal -->
    <div class="modal fade modal-warning" :id="'crop_modal_'+this._uid" v-if="allowCrop">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><i class="ezcp-warning"></i> {{ __('ezcp::media.crop_image') }}</h4>
                </div>

                <div class="modal-body">
                    <div class="crop-container">
                        <img :id="'cropping-image_'+this._uid" v-if="selected_files.length == 1 && fileIs(selected_file, 'image')" class="img img-responsive" :src="selected_file.path + '?' + selected_file.last_modified" />
                    </div>
                    <div class="new-image-info">
                        {{ __('ezcp::media.width') }} <span :id="'new-image-width_'+this._uid"></span>, {{ __('ezcp::media.height') }}<span :id="'new-image-height_'+this._uid"></span>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('ezcp::generic.cancel') }}</button>
                    <button type="button" class="btn btn-warning" v-on:click="crop(false)">{{ __('ezcp::media.crop') }}</button>
                    <button type="button" class="btn btn-warning" v-on:click="crop(true)">{{ __('ezcp::media.crop_and_create') }}</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Crop Image Modal -->
</div>
@endsection

<script>
    Vue.component('media-manager', {
        template: `@yield('media-manager')`,
        props: {
            basePath: {
                type: String,
                default: '/'
            },
            filename: {
                type: String,
                default: null
            },
            allowMultiSelect: {
                type: Boolean,
                default: true
            },
            maxSelectedFiles: {
                type: Number,
                default: 0
            },
            minSelectedFiles: {
                type: Number,
                default: 0
            },
            showFolders: {
                type: Boolean,
                default: true
            },
            showToolbar: {
                type: Boolean,
                default: true
            },
            allowUpload: {
                type: Boolean,
                default: true
            },
            allowMove: {
                type: Boolean,
                default: true
            },
            allowDelete: {
                type: Boolean,
                default: true
            },
            allowCreateFolder: {
                type: Boolean,
                default: true
            },
            allowRename: {
                type: Boolean,
                default: true
            },
            allowCrop: {
                type: Boolean,
                default: true
            },
            allowedTypes: {
                type: Array,
                default: function() {
                    return [];
                }
            },
            preSelect: {
                type: Boolean,
                default: true,
            },
            element: {
                type: String,
                default: ""
            }
        },
        data: function() {
            return {
                current_folder: this.basePath,
		  		selected_files: [],
                files: [],
		  		is_loading: true,
                hidden_element: null,
                expanded: true,
                modals: {
                    new_folder: {
                        name: ''
                    },
                    move_files: {
                        destination: ''
                    }
                }
            };
        },
        computed: {
            selected_file: function() {
                return this.selected_files[0];
            }
        },
        methods: {
            getFiles: function() {
                var vm = this;
                vm.is_loading = true;
                $.post('{{ route('ezcp.media.files') }}', { folder: vm.current_folder, _token: '{{ csrf_token() }}' }, function(data) {
                    vm.files = [];
                    for (var i = 0, file; file = data[i]; i++) {
                        if (vm.filter(file)) {
                            vm.files.push(file);
                        }
                    }
                    vm.selected_files = [];
                    if (vm.preSelect && data.length > 0) {
                        vm.selected_files.push(data[0]);
                    }
					vm.is_loading = false;
				});
            },
            selectFile: function(file, e) {
                if ((!e.ctrlKey && !e.metaKey && !e.shiftKey) || !this.allowMultiSelect) {
                    this.selected_files = [];
                }

                if (e.shiftKey && this.allowMultiSelect && this.selected_files.length == 1) {
                    var index = null;
                    var start = 0;
                    for (var i = 0, cfile; cfile = this.files[i]; i++) {
                        if (cfile === this.selected_file) {
                            start = i;
                            break;
                        }
                    }

                    var end = 0;
                    for (var i = 0, cfile; cfile = this.files[i]; i++) {
                        if (cfile === file) {
                            end = i;
                            break;
                        }
                    }

                    for (var i = start; i < end; i++) {
                        index = this.selected_files.indexOf(this.files[i]);
                        if (index === -1) {
                            this.selected_files.push(this.files[i]);
                        }
                    }
                }

                index = this.selected_files.indexOf(file);
                if (index === -1) {
                    this.selected_files.push(file);
                }

                if (this.selected_files.length == 1) {
                    var vm = this;
                    Vue.nextTick(function () {
                        if (vm.fileIs(vm.selected_file, 'video')) {
                            vm.$refs.videoplayer.load();
                        } else if (vm.fileIs(vm.selected_file, 'audio')) {
                            vm.$refs.audioplayer.load();
                        }
                    });
                }
            },
            openFile: function(file) {
                if (file.type == 'folder') {
                    this.current_folder += "/"+file.name;
                    this.getFiles();
                } else if (this.hidden_element) {
                    this.addFileToInput(file);
                } else {
                    if (this.fileIs(this.selected_file, 'image')) {
                        $('#imagemodal_' + this._uid).modal('show');
                    } else {
                        // ...
                    }
                }
            },
            isFileSelected: function(file) {
                return this.selected_files.includes(file);
            },
            fileIs: function(file, type) {
                if (typeof file === 'string') {
                    if (type == 'image') {
                        return this.endsWithAny(['jpg', 'jpeg', 'png', 'bmp'], file);
                    }
                    //Todo: add other types
                } else {
                    return file.type.includes(type);
                }

                return false;
			},
            getCurrentPath: function() {
                var path = this.current_folder.replace(this.basePath, '').split('/').filter(function (el) {
                    return el != '';
                });

                return path;
            },
            setCurrentPath: function(i) {
                if (i == -1) {
                    this.current_folder = this.basePath;
                } else {
                    var path = this.getCurrentPath();
                    path.length = i + 1;
                    this.current_folder = this.basePath+path.join('/');
                }

                this.getFiles();
            },
            filter: function(file) {
                if (this.allowedTypes.length > 0) {
                    if (file.type != 'folder') {
                        for (var i = 0, type; type = this.allowedTypes[i]; i++) {
                            if (file.type.includes(type)) {
                                return true;
                            }
                        }
                    }
                }

                if (file.type == 'folder' && this.showFolders) {
                    return true;
                } else if (file.type == 'folder' && !this.showFolders) {
                    return false;
                }
                if (this.allowedTypes.length == 0) {
                    return true;
                }

                return false;
            },
            addFileToInput: function(file) {
                if (file.type != 'folder') {
                    if (!this.allowMultiSelect) {
                        this.hidden_element.value = file.relative_path;
                    } else {
                        var content = JSON.parse(this.hidden_element.value);
                        if (content.indexOf(file.relative_path) !== -1) {
                            return;
                        }
                        if (content.length >= this.maxSelectedFiles && this.maxSelectedFiles > 0) {
                            var msg_sing = "{{ trans_choice('ezcp::media.max_files_select', 1) }}";
                            var msg_plur = "{{ trans_choice('ezcp::media.max_files_select', 2) }}";
                            if (this.maxSelectedFiles == 1) {
                                toastr.error(msg_sing);
                            } else {
                                toastr.error(msg_plur.replace('2', this.maxSelectedFiles));
                            }
                        } else {
                            content.push(file.relative_path);
                            this.hidden_element.value = JSON.stringify(content);
                        }
                    }
                }
            },
            removeFileFromInput: function(path) {
                if (this.allowMultiSelect) {
                    var content = JSON.parse(this.hidden_element.value);
                    if (content.indexOf(path) !== -1) {
                        content.splice(content.indexOf(path), 1);
                        this.hidden_element.value = JSON.stringify(content);
                        this.$forceUpdate();
                    }
                } else {
                    this.hidden_element.value = '';
                }
            },
            getSelectedFiles: function() {
                if (!this.allowMultiSelect) {
                    var content = [];
                    if (this.hidden_element.value != '') {
                        content.push(this.hidden_element.value);
                    }

                    return content;
                } else {
                    return JSON.parse(this.hidden_element.value);
                }
            },
            renameFile: function(object) {
                var vm = this;
                if (!this.allowRename || vm.selected_file.name == object.target.value) {
                    return;
                }
                $.post('{{ route('ezcp.media.rename') }}', {
                    folder_location: vm.current_folder,
                    filename: vm.selected_file.name,
                    new_filename: object.target.value,
                    _token: '{{ csrf_token() }}'
                }, function(data){
					if (data.success == true) {
						toastr.success('{{ __('ezcp::media.success_renamed') }}', "{{ __('ezcp::generic.sweet_success') }}");
						vm.getFiles();
					} else {
						toastr.error(data.error, "{{ __('ezcp::generic.whoopsie') }}");
					}
				});
            },
            createFolder: function(e) {
                if (!this.allowCreateFolder) {
                    return;
                }
                var vm = this;
                var name = this.modals.new_folder.name;
                $.post('{{ route('ezcp.media.new_folder') }}', { new_folder: vm.current_folder+'/'+name, _token: '{{ csrf_token() }}' }, function(data) {
					if(data.success == true){
						toastr.success('{{ __('ezcp::generic.successfully_created') }} ' + name, "{{ __('ezcp::generic.sweet_success') }}");
						vm.getFiles();
					} else {
						toastr.error(data.error, "{{ __('ezcp::generic.whoopsie') }}");
					}
                    vm.modals.new_folder.name = '';
					$('#create_dir_modal_'+vm._uid).modal('hide');
				});
            },
            deleteFiles: function() {
                if (!this.allowDelete) {
                    return;
                }
                var vm = this;
                $.post('{{ route('ezcp.media.delete') }}', {
                    path: vm.current_folder,
                    files: vm.selected_files,
                    _token: '{{ csrf_token() }}'
                }, function(data){
					if(data.success == true){
						toastr.success('', "{{ __('ezcp::generic.sweet_success') }}");
						vm.getFiles();
						$('#confirm_delete_modal_'+vm._uid).modal('hide');
					} else {
						toastr.error(data.error, "{{ __('ezcp::generic.whoopsie') }}");
                        vm.getFiles();
						$('#confirm_delete_modal_'+vm._uid).modal('hide');
					}
				});
            },
            moveFiles: function(e) {
                if (!this.allowMove) {
                    return;
                }
                var vm = this;
                var destination = this.modals.move_files.destination;
                if (destination === '') {
                    return;
                }
                $('#move_files_modal_'+vm._uid).modal('hide');
				$.post('{{ route('ezcp.media.move') }}', {
                    path: vm.current_folder,
                    files: vm.selected_files,
                    destination: destination,
                    _token: '{{ csrf_token() }}'
                }, function(data){
					if(data.success == true){
						toastr.success('{{ __('ezcp::media.success_moved') }}', "{{ __('ezcp::generic.sweet_success') }}");
						vm.getFiles();
					} else {
						toastr.error(data.error, "{{ __('ezcp::generic.whoopsie') }}");
					}

                    vm.modals.move_files.destination = '';
				});
            },
            crop: function(mode) {
                if (!this.allowCrop) {
                    return;
                }
                if (!mode) {
                    if (!window.confirm('{{ __('ezcp::media.crop_override_confirm') }}')) {
						return;
					}
                }

                croppedData.originImageName = this.selected_file.name;
				croppedData.upload_path = this.current_folder;
				croppedData.createMode = mode;

                var vm = this;
                var postData = Object.assign(croppedData, { _token: '{{ csrf_token() }}' });
				$.post('{{ route('ezcp.media.crop') }}', postData, function(data) {
					if (data.success) {
						toastr.success(data.message);
						vm.getFiles();
						$('#crop_modal_'+vm._uid).modal('hide');
					} else {
						toastr.error(data.error, "{{ __('ezcp::generic.whoopsie') }}");
					}
				});
            },
            bytesToSize: function(bytes) {
				var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
				if (bytes == 0) return '0 Bytes';
				var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
				return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
			},
            getFileName: function(name) {
                var name = name.split('/');
                return name[name.length -1];
            },
            imgIcon: function(path) {
                path = path.replace(/\\/g,"/");
				return 'background-size: cover; background-image: url("' + path + '"); background-repeat:no-repeat; background-position:center center;display:inline-block; width:100%; height:100%;';
			},
            dateFilter: function(date) {
                if (!date) {
                    return null;
                }
                var date = new Date(date * 1000);

                var month = "0" + (date.getMonth() + 1);
                var minutes = "0" + date.getMinutes();
                var seconds = "0" + date.getSeconds();

                var dateFormated = date.getFullYear() + '-' + month.substr(-2) + '-' + date.getDate() + ' ' + date.getHours() + ':' + minutes.substr(-2) + ':' + seconds.substr(-2);

                return dateFormated;
            },
            endsWithAny: function(suffixes, string) {
                return suffixes.some(function (suffix) {
                    return string.endsWith(suffix);
                });
            }
        },
        mounted: function() {
            this.getFiles();
            var vm = this;

            if (this.element != '') {
                this.hidden_element = document.querySelector(this.element);
                if (!this.hidden_element) {
                    console.error('Element "'+this.element+'" could not be found.');
                } else {
                    this.expanded = false;
                    if (this.maxSelectedFiles > 1 && this.hidden_element.value == '') {
                        this.hidden_element.value = '[]';
                    }
                }
            }

            //Key events
            this.onkeydown = function(evt) {
                evt = evt || window.event;
                if (evt.keyCode == 39) {
                    evt.preventDefault();
                    for (var i = 0, file; file = vm.files[i]; i++) {
                        if (file === vm.selected_file) {
                            i = i + 1; // increase i by one
                            i = i % vm.files.length;
                            vm.selectFile(vm.files[i], evt);
                            break;
                        }
                    }
                } else if (evt.keyCode == 37) {
                    evt.preventDefault();
                    for (var i = 0, file; file = vm.files[i]; i++) {
                        if (file === vm.selected_file) {
                            if (i === 0) {
                                i = vm.files.length;
                            }
                            i = i - 1;
                            vm.selectFile(vm.files[i], evt);
                            break;
                        }
                    }
                } else if (evt.keyCode == 13) {
                    evt.preventDefault();
                    if (evt.target.tagName != 'INPUT') {
                        vm.openFile(vm.selected_file, null);
                    }
                }
            };
            //Dropzone
            var dropzone = $(vm.$el).first().find('#upload').first();
            var progress = $(vm.$el).first().find('#uploadProgress').first();
            var progress_bar = $(vm.$el).first().find('#uploadProgress .progress-bar').first();
            if (this.allowUpload && !dropzone.hasClass('dz-clickable')) {
                dropzone.dropzone({
                    timeout: 180000,
                    url: '{{ route('ezcp.media.upload') }}',
                    previewsContainer: "#uploadPreview",
                    totaluploadprogress: function(uploadProgress, totalBytes, totalBytesSent) {
                        progress_bar.css('width', uploadProgress + '%');
    					if (uploadProgress == 100) {
    						progress.delay(1500).slideUp(function(){
    							progress_bar.css('width', '0%');
    						});
    					}
                    },
                    processing: function(){
                        progress.fadeIn();
                    },
                    sending: function(file, xhr, formData) {
                        formData.append("_token", '{{ csrf_token() }}');
                        formData.append("upload_path", vm.current_folder);
                        formData.append("filename", vm.filename);
                    },
                    success: function(e, res) {
                        if (res.success) {
                            toastr.success(res.message, "{{ __('ezcp::generic.sweet_success') }}");
                        } else {
                            toastr.error(res.message, "{{ __('ezcp::generic.whoopsie') }}");
                        }
                    },
                    error: function(e, res, xhr) {
                        toastr.error(res, "{{ __('ezcp::generic.whoopsie') }}");
                    },
                    queuecomplete: function() {
                        vm.getFiles();
                    }
                });
            }

            //Cropper
            if (this.allowCrop) {
                var cropper = $(vm.$el).first().find('#crop_modal_'+vm._uid).first();
                cropper.on('shown.bs.modal', function (e) {
                    if (typeof cropper !== 'undefined' && cropper instanceof Cropper) {
    					cropper.destroy();
    				}
    				var croppingImage = document.getElementById('cropping-image_'+vm._uid);
    				cropper = new Cropper(croppingImage, {
    					crop: function(e) {
    						document.getElementById('new-image-width_'+vm._uid).innerText = Math.round(e.detail.width) + 'px';
    						document.getElementById('new-image-height_'+vm._uid).innerText = Math.round(e.detail.height) + 'px';
    						croppedData = {
    							x: Math.round(e.detail.x),
    							y: Math.round(e.detail.y),
    							height: Math.round(e.detail.height),
    							width: Math.round(e.detail.width)
    						};
    					}
    				});
    			});
            }

            $(document).ready(function () {
                $(".form-edit-add").submit(function (e) {
                    if (vm.hidden_element) {
                        if (vm.maxSelectedFiles > 1) {
                            var content = JSON.parse(vm.hidden_element.value);
                            if (content.length < vm.minSelectedFiles) {
                                e.preventDefault();
                                var msg_sing = "{{ trans_choice('ezcp::media.min_files_select', 1) }}";
                                var msg_plur = "{{ trans_choice('ezcp::media.min_files_select', 2) }}";
                                if (vm.minSelectedFiles == 1) {
                                    toastr.error(msg_sing);
                                } else {
                                    toastr.error(msg_plur.replace('2', vm.minSelectedFiles));
                                }
                            }
                        } else {
                            if (vm.minSelectedFiles > 0 && vm.hidden_element.value == '') {
                                e.preventDefault();
                                toastr.error("{{ trans_choice('ezcp::media.min_files_select', 1) }}");
                            }
                        }
                    }
                });

                //Nestable
                $('.dd').nestable({
                    maxDepth: 1,
                    handleClass: 'file_link',
                    collapseBtnHTML: '',
                    expandBtnHTML: '',
                    emptyClass : '',
                    callback: function(l, e) {
                        if (vm.allowMultiSelect) {
                            var new_content = [];
                            var object = $('.dd').nestable('serialize');
                            for (var key in object) {
                                new_content.push(object[key].url);
                            }
                            vm.hidden_element.value = JSON.stringify(new_content);
                        }
                    }
                });

                $('#create_dir_modal_' + vm._uid).on('hidden.bs.modal', function () {
                    vm.modals.new_folder.name = '';
                });

                $('#move_files_modal_' + vm._uid).on('hidden.bs.modal', function () {
                    vm.modals.move_files.destination = '';
                });
            });
        },
    });
</script>
