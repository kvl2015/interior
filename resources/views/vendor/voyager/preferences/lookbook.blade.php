@extends('voyager::master')

@section('page_title', 'Set lookbook for slide')

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
        Set lookbook for slide
        </h1>
    </div>
@stop
@section('content')
<div class="page-content browse container-fluid">
    <form action="{{ route('voyager.preferences.updateLook', $id) }}" method="post">
    {{ csrf_field() }}
    @include('voyager::alerts')
    <div class="row">
        <div class="col-md-12">
            <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title"></h3>
                </div>
                <div class="panel-body">
                    <div class="col-md-8">
                        <div class="bd-callout bd-callout-info">
                        <div class="d-flex justify-content-center w-100 preference-main">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="slide-container position-relative">
                                        <img id="myImgId" src="{{ asset('storage/'.str_replace('\\', '/', $preference->image)) }} " />
                                        <div class="circle-look pin-circle clonable-circle hide"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                            
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="bd-callout bd-callout-info">
                            <table class="lookbook-table table">
                                <tr class="clonable">
                                    <td><span class="num"></span></td>
                                    <td>
                                        <input type="text" class="form-control product-fill" name="product[]" />
                                        <input type="hidden" name="posX[]" />
                                        <input type="hidden" name="posY[]" />
                                        <input type="hidden" name="connected[]" />
                                    </td>
                                    <td><a href="javascript:;" onClick="removeLook($(this))">Remove</a></td>
                                </tr>
                                @if (count($looks) > 0)
                                    @foreach($looks as $key => $look)
                                        <tr id="book_{{ $key + 1 }}">
                                            <td><span class="num">{{ $key + 1 }}</span></td>
                                            <td>
                                                <input type="text" class="form-control product-fill" name="product[]" value="{{ $look->product->name }}" />
                                                <input type="hidden" name="posX[]" value="{{ $look->pos_x }}" />
                                                <input type="hidden" name="posY[]" value="{{ $look->pos_y }}" />
                                                <input type="hidden" name="connected[]" value={{ $look->product_id }} />
                                            </td>
                                            <td><a href="javascript:;" onClick="removeLook($(this))">Remove</a></td>
                                        </tr>
                                    @endforeach
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary save">{{ __('voyager::generic.save') }}</button>
    </form>
</div>
@endsection

@section('javascript')
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        var num = '{{ count($looks) > 0 ? (count($looks) + 1) : 1}}';

        $('document').ready(function () {
            $('tr', $('.lookbook-table')).each(function(key, value) {
                if (!$(this).hasClass('clonable')) {
                    var circle = $('.clonable-circle').clone().removeClass('clonable-circle').removeClass('hide');
                    $(circle).appendTo($('.slide-container'));
                    $(circle).css({'top' : parseInt($('input[name*="posY"]', value).val())+'px', 'left' : parseInt($('input[name*="posX"]', value).val())+'px', 'position' : 'absolute'}).attr({'id': 'circle_book_'+(key)});
                    $(circle).html(key);
                    initAutocomplete(value);
                }
            });
        })
        
        function removeLook(oElement) {
            var id = $(oElement).parent().parent().attr('id');
            $('#'+id).remove();
            $('#circle_'+id).remove();
        }

        function FindPosition(oElement)
        {
            if(typeof( oElement.offsetParent ) != "undefined")
            {
                for(var posX = 0, posY = 0; oElement; oElement = oElement.offsetParent)
                {
                    posX += oElement.offsetLeft;
                    posY += oElement.offsetTop;
                }
                    return [ posX, posY ];
            }
            else
            {
                return [ oElement.x, oElement.y ];
            }
        }

        function initAutocomplete(elm) {
            $('.product-fill', elm).autocomplete({
                source: function (request, response) {
                    jQuery.get("/admin/preferences/get-products", {
                        query: request.term
                    }, function (data) {
                        // assuming data is a JavaScript array such as
                        // ["one@abc.de", "onf@abc.de","ong@abc.de"]
                        // and not a string
                        response(data);
                    });
                },
                select: function(event, ui){
                    $(this).val("");
                    $(this).val(ui.item.label);
                    var row1 = $(this).parent().parent();
                    row1.find('input[name*="connected"]').val(ui.item.id);
                   
                    event.preventDefault();
                },
                minLength: 5
            });
        }

        function GetCoordinates(e)
        {
            var PosX = 0;
            var PosY = 0;
            var ImgPos;
            ImgPos = FindPosition(myImg);
            if (!e) var e = window.event;
            if (e.pageX || e.pageY)
            {
                PosX = e.pageX;
                PosY = e.pageY;
            }
            else if (e.clientX || e.clientY)
            {
                PosX = e.clientX + document.body.scrollLeft
                    + document.documentElement.scrollLeft;
                PosY = e.clientY + document.body.scrollTop
                    + document.documentElement.scrollTop;
            }
            PosX = PosX - ImgPos[0];
            PosY = PosY - ImgPos[1];

            // clone circle and add it
            var circle = $('.clonable-circle').clone().removeClass('clonable-circle').removeClass('hide');
            $(circle).appendTo($('.slide-container'));
            $(circle).css({'top' : PosY+'px', 'left' : PosX+'px', 'position' : 'absolute'}).attr({'id': 'circle_book_'+num});
            $(circle).html(num);

            // add row to lookbook table
            row = $('.lookbook-table').find('.clonable').clone(true).removeClass('hide').removeClass('clonable').attr({'id': 'book_'+num});
            $('.lookbook-table').append(row);
            row.find('input[name*="posX"]').val(PosX);
            row.find('input[name*="posY"]').val(PosY);
            $('.num', row).html(num);
            initAutocomplete(row);
            /*$('.product-fill', row).autocomplete({
                source: function (request, response) {
                    jQuery.get("/admin/preferences/get-products", {
                        query: request.term
                    }, function (data) {
                        // assuming data is a JavaScript array such as
                        // ["one@abc.de", "onf@abc.de","ong@abc.de"]
                        // and not a string
                        response(data);
                    });
                },
                select: function(event, ui){
                    $(this).val("");
                    $(this).val(ui.item.label);
                    var row1 = $(this).parent().parent();
                    row1.find('input[name*="connected"]').val(ui.item.id);
                   
                    event.preventDefault();
                },
                minLength: 5
            });*/
            num++;
        }

        

        var myImg = document.getElementById("myImgId");
        myImg.onmousedown = GetCoordinates;

    </script>
@endsection