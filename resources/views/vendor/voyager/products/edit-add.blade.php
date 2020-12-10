@php
    $edit = !is_null($dataTypeContent->getKey());
    $add  = is_null($dataTypeContent->getKey());
@endphp

@extends('voyager::master')

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('page_title', __('voyager::generic.'.($edit ? 'edit' : 'add')).' '.$dataType->getTranslatedAttribute('display_name_singular'))

@section('page_header')
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i>
        {{ __('voyager::generic.'.($edit ? 'edit' : 'add')).' '.$dataType->getTranslatedAttribute('display_name_singular') }}
    </h1>
    @include('voyager::multilingual.language-selector')
@stop

@section('content')
    <div class="page-content edit-add container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="panel panel-bordered">
                    <!-- form start -->
                    <form role="form"
                            class="form-edit-add"
                            action="{{ $edit ? route('voyager.'.$dataType->slug.'.update', $dataTypeContent->getKey()) : route('voyager.'.$dataType->slug.'.store') }}"
                            method="POST" enctype="multipart/form-data">
                        <!-- PUT Method if we are editing -->
                        @if($edit)
                            {{ method_field("PUT") }}
                        @endif

                        <!-- CSRF TOKEN -->
                        {{ csrf_field() }}

                        <div class="panel-body">

                            @if (count($errors) > 0)
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            
                            <!-- Adding / Editing -->
                            @php
                                $dataTypeRows = $dataType->{($edit ? 'editRows' : 'addRows' )};
                            @endphp
                                <div class="col-md-8">
                                    <div class="bd-callout bd-callout-info">
                                        <h4>Common information</h4>
                                        @foreach($dataTypeRows as $row)
                                            @if (in_array($row->field, array('meta_title', 'meta_keyword', 'meta_description', 'seo_title', 'seo_content')))
                                                @continue
                                            @endif
                                            <!-- GET THE DISPLAY OPTIONS -->
                                                @php
                                                    $display_options = $row->details->display ?? NULL;
                                                    if ($dataTypeContent->{$row->field.'_'.($edit ? 'edit' : 'add')}) {
                                                        $dataTypeContent->{$row->field} = $dataTypeContent->{$row->field.'_'.($edit ? 'edit' : 'add')};
                                                    }
                                                @endphp
                                                @if (isset($row->details->legend) && isset($row->details->legend->text))
                                                    <legend class="text-{{ $row->details->legend->align ?? 'center' }}" style="background-color: {{ $row->details->legend->bgcolor ?? '#f0f0f0' }};padding: 5px;">{{ $row->details->legend->text }}</legend>
                                                @endif

                                                <div class="form-group @if($row->type == 'hidden') hidden @endif col-md-{{ $display_options->width ?? 12 }} {{ $errors->has($row->field) ? 'has-error' : '' }}" @if(isset($display_options->id)){{ "id=$display_options->id" }}@endif>
                                                    {{ $row->slugify }}
                                                    <label class="control-label" for="name">{{ $row->getTranslatedAttribute('display_name') }}</label>
                                                    @include('voyager::multilingual.input-hidden-bread-edit-add')
                                                    @if (isset($row->details->view))
                                                        @include($row->details->view, ['row' => $row, 'dataType' => $dataType, 'dataTypeContent' => $dataTypeContent, 'content' => $dataTypeContent->{$row->field}, 'action' => ($edit ? 'edit' : 'add'), 'view' => ($edit ? 'edit' : 'add'), 'options' => $row->details])
                                                    @elseif ($row->type == 'relationship')
                                                        @include('voyager::formfields.relationship', ['options' => $row->details])
                                                    @else
                                                        {!! app('voyager')->formField($row, $dataType, $dataTypeContent) !!}
                                                    @endif

                                                    @foreach (app('voyager')->afterFormFields($row, $dataType, $dataTypeContent) as $after)
                                                        {!! $after->handle($row, $dataType, $dataTypeContent) !!}
                                                    @endforeach
                                                    @if ($errors->has($row->field))
                                                        @foreach ($errors->get($row->field) as $error)
                                                            <span class="help-block">{{ $error }}</span>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            @endforeach
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="bd-callout bd-callout-info">
                                        <h4>Seo block</h4>
                                        @foreach($dataTypeRows as $row)
                                            @if (!in_array($row->field, array('meta_title', 'meta_keyword', 'meta_description', 'seo_title', 'seo_content')))
                                                @continue
                                            @endif
                                            <!-- GET THE DISPLAY OPTIONS -->
                                                @php
                                                    $display_options = $row->details->display ?? NULL;
                                                    if ($dataTypeContent->{$row->field.'_'.($edit ? 'edit' : 'add')}) {
                                                        $dataTypeContent->{$row->field} = $dataTypeContent->{$row->field.'_'.($edit ? 'edit' : 'add')};
                                                    }
                                                @endphp
                                                @if (isset($row->details->legend) && isset($row->details->legend->text))
                                                    <legend class="text-{{ $row->details->legend->align ?? 'center' }}" style="background-color: {{ $row->details->legend->bgcolor ?? '#f0f0f0' }};padding: 5px;">{{ $row->details->legend->text }}</legend>
                                                @endif

                                                <div class="form-group @if($row->type == 'hidden') hidden @endif col-md-{{ $display_options->width ?? 12 }} {{ $errors->has($row->field) ? 'has-error' : '' }}" @if(isset($display_options->id)){{ "id=$display_options->id" }}@endif>
                                                    {{ $row->slugify }}
                                                    <label class="control-label" for="name">{{ $row->getTranslatedAttribute('display_name') }}</label>
                                                    @include('voyager::multilingual.input-hidden-bread-edit-add')
                                                    @if (isset($row->details->view))
                                                        @include($row->details->view, ['row' => $row, 'dataType' => $dataType, 'dataTypeContent' => $dataTypeContent, 'content' => $dataTypeContent->{$row->field}, 'action' => ($edit ? 'edit' : 'add'), 'view' => ($edit ? 'edit' : 'add'), 'options' => $row->details])
                                                    @elseif ($row->type == 'relationship')
                                                        @include('voyager::formfields.relationship', ['options' => $row->details])
                                                    @else
                                                        {!! app('voyager')->formField($row, $dataType, $dataTypeContent) !!}
                                                    @endif

                                                    @foreach (app('voyager')->afterFormFields($row, $dataType, $dataTypeContent) as $after)
                                                        {!! $after->handle($row, $dataType, $dataTypeContent) !!}
                                                    @endforeach
                                                    @if ($errors->has($row->field))
                                                        @foreach ($errors->get($row->field) as $error)
                                                            <span class="help-block">{{ $error }}</span>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            @endforeach
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                        </div><!-- panel-body -->

                        <div class="panel-footer">
                            @section('submit-buttons')
                                <button type="submit" class="btn btn-primary save">{{ __('voyager::generic.save') }}</button>
                            @stop
                            @yield('submit-buttons')
                        </div>
                    </form>

                    <iframe id="form_target" name="form_target" style="display:none"></iframe>
                    <form id="my_form" action="{{ route('voyager.upload') }}" target="form_target" method="post"
                            enctype="multipart/form-data" style="width:0;height:0;overflow:hidden">
                        <input name="image" id="upload_file" type="file"
                                 onchange="$('#my_form').submit();this.value='';">
                        <input type="hidden" name="type_slug" id="type_slug" value="{{ $dataType->slug }}">
                        {{ csrf_field() }}
                    </form>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade modal-danger" id="confirm_delete_modal">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><i class="voyager-warning"></i> {{ __('voyager::generic.are_you_sure') }}</h4>
                </div>

                <div class="modal-body">
                    <h4>{{ __('voyager::generic.are_you_sure_delete') }} '<span class="confirm_delete_name"></span>'</h4>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                    <button type="button" class="btn btn-danger" id="confirm_delete">{{ __('voyager::generic.delete_confirm') }}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="product-add-options">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Add option</h4>
                </div>

                <div class="modal-body" style="font-size: 13px;">
                    
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                    <button type="button" class="btn btn-info" onClick="submitAddOptions()">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="product-add-options-group">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Add group</h4>
                </div>

                <div class="modal-body" style="font-size: 13px;">
                    <div class="group-select-data"></div>
                    <div class="group-select-option"></div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                    <button type="button" class="btn btn-info" onClick="submitAddGroup()">Submit</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Delete File Modal -->
@stop

@section('javascript')
    <script>
        var params = {};
        var $file;

        function deleteHandler(tag, isMulti) {
          return function() {
            $file = $(this).siblings(tag);

            params = {
                slug:   '{{ $dataType->slug }}',
                filename:  $file.data('file-name'),
                id:     $file.data('id'),
                field:  $file.parent().data('field-name'),
                multi: isMulti,
                _token: '{{ csrf_token() }}'
            }

            $('.confirm_delete_name').text(params.filename);
            $('#confirm_delete_modal').modal('show');
          };
        }

        function addGroup() {
            $.get('/admin/products/get-groups', {}, function (response) {
                if ( response.success  ) {
                    $('.group-select-data', $('#product-add-options-group')).html(response.html);
                    $('#product-add-options-group').modal('show');

                    $('#groups', $('#product-add-options-group')).change(function() {
                        $.get('/admin/products/add-options', {'groupId' : $(this).val(), 'ids' : []}, function (response) {
                            if ( response.success  ) {
                                $('.group-select-option', $('#product-add-options-group')).html(response.html);
                            } 
                        });          

                    })
                } 
            });
        }


        function submitAddGroup() {
            var ids = [];
            var groupId = $('#groups', $('#product-add-options-group')).val()
            if ($('input[name*="ids"]', $('#product-add-options-group')).length > 0) {
                $('input[name*="ids"]', $('#product-add-options-group')).each(function (idx, elt) {
                    if ($(elt).prop('checked'))
                        ids.push($(elt).val());
                });
                $.get('/admin/products/get-options', {'groupId' : groupId, 'ids' : ids, 'type': 'full-table'}, function (response) {
                    if ( response.success  ) {
                            $('.options-block').append(response.html);
                            $('#product-add-options-group').modal('hide');
                    } 
                });
                
            }
        }


        function addOptionGroup(groupId) {
            var ids = [];
            if ($('input[name*="ids[' +groupId+ ']"]').length > 0) {
                $('input[name*="ids[' +groupId+ ']"]').each(function (idx, elt) {
                    ids.push($(elt).val());
                })
            }
            $.get('/admin/products/add-options', {'groupId' : groupId, 'ids' : ids}, function (response) {
                if ( response.success  ) {
                    $('.modal-body', $('#product-add-options')).html(response.html);
                    $('#product-add-options').modal('show');
                } 
            });          
        }


        function submitAddOptions() {
            var ids = [];
            var extIds = [];
            var groupId = $('#groupId', $('#product-add-options')).val()
            if ($('input[name*="ids[' +groupId+ ']"]').length > 0) {
                $('input[name*="ids[' +groupId+ ']"]').each(function (idx, elt) {
                    extIds.push($(elt).val());
                })
            }
            if ($('input[name*="ids"]', $('#product-add-options')).length > 0) {
                $('input[name*="ids"]', $('#product-add-options')).each(function (idx, elt) {
                    if ($(elt).prop('checked'))
                        ids.push($(elt).val());
                });
                $.get('/admin/products/get-options', {'groupId' : groupId, 'ids' : ids, 'extIds' : extIds}, function (response) {
                    //console.log(response.success);
                    if ( response.success  ) {
                        $('tbody', $('#group_'+groupId)).append(response.html);
                        $('#product-add-options').modal('hide');
                        $('input[name*="optphoto"]', $('#group_'+groupId)).change(function() {
                            console.log($(this).parent().find('span').attr('data-id'));
                            var container = $(this).parent().find('span');
                            readMainURL(this, container);
                        });
                        
                    } else {
                    }
                });
                
            }
        }      


        $('document').ready(function () {
            $('.toggleswitch').bootstrapToggle();
            //Init datepicker for date fields if data-datepicker attribute defined
            //or if browser does not handle date inputs
            $('.form-group input[type=date]').each(function (idx, elt) {
                if (elt.hasAttribute('data-datepicker')) {
                    elt.type = 'text';
                    $(elt).datetimepicker($(elt).data('datepicker'));
                } else if (elt.type != 'date') {
                    elt.type = 'text';
                    $(elt).datetimepicker({
                        format: 'L',
                        extraFormats: [ 'YYYY-MM-DD' ]
                    }).datetimepicker($(elt).data('datepicker'));
                }
            });

            @if ($isModelTranslatable)
                $('.side-body').multilingual({"editing": true});
            @endif

            $('.side-body input[data-slug-origin]').each(function(i, el) {
                $(el).slugify();
            });

            $('.form-group').on('click', '.remove-multi-image', deleteHandler('img', true));
            $('.form-group').on('click', '.remove-single-image', deleteHandler('img', false));
            $('.form-group').on('click', '.remove-multi-file', deleteHandler('a', true));
            $('.form-group').on('click', '.remove-single-file', deleteHandler('a', false));

            $('#confirm_delete').on('click', function(){
                $.post('{{ route('voyager.'.$dataType->slug.'.media.remove') }}', params, function (response) {
                    if ( response
                        && response.data
                        && response.data.status
                        && response.data.status == 200 ) {

                        toastr.success(response.data.message);
                        $file.parent().fadeOut(300, function() { $(this).remove(); })
                    } else {
                        toastr.error("Error removing file.");
                    }
                });

                $('#confirm_delete_modal').modal('hide');
            });
            $('[data-toggle="tooltip"]').tooltip();

            //options function
            $('.add-option-row').click(function() {
                var body = $(this).closest('table.options-table-content').find('tbody');
                var new_row = $('.clonable', $(body).parent()).clone().removeClass('clonable').removeClass('hide');
                $(body).append(new_row);
            });

            //preview image function
            $('input[name*="optphoto"]').change(function() {
                console.log($(this).parent().find('span').attr('data-id'));
                var container = $(this).parent().find('span');
                readMainURL(this, container);
            });
        });

        function readMainURL(input, container) {
            
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var objDiv = '';
                    //objDiv += '<h4>Превью основного изображения</h4>';
                    objDiv += '<div class="" style="height:auto;max-width: 75px;">';
                    objDiv += '<img src="' +e.target.result+ '" style="height:75px; max-width: 150px; clear:both; display:block; padding:2px; border:1px solid #ddd; margin-bottom:5px;" class="img-fluid"/>';
                    objDiv += '<div class="clearfix"></div>';
                    objDiv += '</div>';
                    $(container).html(objDiv);
                    //$('#previewHolder').attr('src', e.target.result);
                    //$('#preview_image').css({'display':'block'});
                }

                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@stop
