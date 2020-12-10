@extends('voyager::master')

@section('page_title', 'Set main categories menu order')


@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
        Set main categories menu order
        </h1>
    </div>
@stop
@section('content')
    <div class="page-content browse container-fluid">
        @include('voyager::alerts')
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <div class="panel-heading">
                        <h3 class="panel-title"></h3>

                    </div>
                    <div class="panel-body">
                        <form class="form-edit-add" role="form" action="{{ route('voyager.main-categories.updatePageOrder', 1) }}" method="POST" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="row-fluid">
                                <div class="col-md-6">
                                    <div class="dd" id="nestable">
                                        <ol class="dd-list">
                                            @if (count($categories))
                                                @foreach($categories as $key => $category)
                                                    <li class="dd-item" data-id="{{ $key }}">
                                                        <div class="dd-handle" style="height:70px;">
                                                            @if ($category[1])
                                                                <img src={{ asset('storage/'.str_replace('\\', '/', $category[1])) }} height="40" />
                                                            @endif
                                                            {{ $category[0] }}
                                                        </div>
                                                    </li>
                                                @endforeach
                                            @else
                                                
                                            @endif
                                        </ol>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="dd" id="nestable2">
                                        @if (count($arrCategories))
                                            <ol class="dd-list">
                                                @foreach ($arrCategories as $_menu)
                                                    <li class="dd-item" data-id="<?php echo $_menu->id?>">
                                                        <div class="dd-handle" style="height:70px;">
                                                            @if ($category[1])
                                                                <img src={{ asset('storage/'.str_replace('\\', '/', $_menu->name[1])) }} height="40" />
                                                            @endif
                                                            <?php echo $_menu->name[0]?>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ol>
                                        @else
                                            
                                        @endif
                                    </div>
                                    <input type="hidden" id="nestable-output" name="nestable-output" val="">
                                    <input type="hidden" id="nestable2-output" name="nestable2-output" val="">
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-md-6">
                                    @section('submit-buttons')
                                        <button type="submit" class="btn btn-primary pull-left" onclick="updateNestable();">
                                            Сохранить
                                        </button>
                                    @stop
                                    @yield('submit-buttons')
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@stop

@section('javascript')
    <script src="/js/jquery/nestable.jquery.js" ></script>
    <script>
        function updateNestable() {
            $('#nestable2-output').val(window.JSON.stringify($('#nestable2').nestable('serialize')));
        }

        $(document).ready(function()
        {

            var updateOutput = function(e)
            {
                console.log('here');
                var list   = e.length ? e : $(e.target),
                    output = list.data('output');
                if (window.JSON) {
                    output.val(window.JSON.stringify(list.nestable('serialize')));//, null, 2));
                } else {
                    output.val('JSON browser support required for this demo.');
                }
            };

            // activate Nestable for list 1
            $('#nestable').nestable({
                group: 1
            })
                .on('change', updateOutput);

            // activate Nestable for list 2
            $('#nestable2').nestable({
                group: 1
            })
                .on('change', updateOutput);

            // output initial serialised data
            updateOutput($('#nestable').data('output', $('#nestable-output')));
            updateOutput($('#nestable2').data('output', $('#nestable2-output')));
        });

        //updateNestable
    </script>

@stop
