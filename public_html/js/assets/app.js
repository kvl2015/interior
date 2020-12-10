var changeProductPage = (url, page, perPage, sort) => { 
    jQuery.ajax({
        url: url,
        method: 'get',
        data: {
            page: page,
            perPage: perPage,
            sort: sort
        },
        success: function(data){
            $("#data-content").removeClass('disable-content');
            $('#data-content').html(data.html);
            
            App.createPagination(data.total, 'pag-top');
            App.createPagination(data.total, 'pag-bottom');


            $('#sourcesPage').selectpicker();
            $('#sourcesSort').selectpicker();
            
            $('#sourcesPage').change(function() {
                $("#data-content").addClass('disable-content');
                changeProductPage(window.location.pathname, 1, $('#sourcesPage').val(), $('#sourcesSort').val());         
            });

            $('#sourcesSort').change(function() {
                $("#data-content").addClass('disable-content');
                changeProductPage(window.location.pathname, 1, $('#sourcesPage').val(), $('#sourcesSort').val());         
            });

            $('select[name=sourcesSort]').val(sort);
            $('select[name=sourcesPage]').val(perPage);
            $('.selectpicker').selectpicker('refresh');

            $('#pag-top').bootpag({page: data.currentPage});
            $('#pag-bottom').bootpag({page: data.currentPage});
            $('#smartcart').smartCart('reinit');
            showProductInfo();

            $([document.documentElement, document.body]).animate({
                scrollTop: ($("#data-content").offset().top - 100)
            }, 1000);
        }
    })
};

var showProductInfo = () => {
    if ($( window ).width() > 1199) {
        $.each($('.product'), function( key, value ) {
            $(value).mouseover(function() {
                $('.mouse-over', $(this)).addClass('shown');
                if ($('#viewed').length) {
                    $('#mCSB_1_container').css({'height': (parseInt($('.mouse-over', $(this)).height()) + 50)+'px'});
                }
            })
            $(value).mouseleave(function() {
                $('.mouse-over', $(this)).removeClass('shown')
                $('.error', $(this)).html('');
                if ($('#viewed').length) {
                    $('#mCSB_1_container').css({'height': 'auto'});
                }
            })
            $('.variant', $('.tracking-parent')).mouseover(function() {
                var parent = $(this).parent().parent();
                var img = $('img', $(this));
                $('.main-previw-container', $(parent)).attr({'src' : $(img).attr('cropped-src')});
            });        
        });
    }
};

var getCupone = (code) =>  {
    jQuery.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    jQuery.ajax({
        beforeSend: function( xhr ) {
            $('.checkout-data').addClass('disable-content');
            xhr.overrideMimeType( "text/plain; charset=x-user-defined" );
        },
        url: '/get-cupone',
        method: 'post',
        data: {code: code},
        success: function(response){
            var data = jQuery.parseJSON(response);
            if (data.success) {
                // $.fn.smartCart({discount : data.data[1]});
                //$('#smartcart').smartCart({discount : data.data[1]});
                $('.sc-amount-summary').html('€'+data.amounts.summary);
                $('.sc-amount-subtotal').html('€'+data.amounts.subtotal);
                $('.sc-amount-shipping').html('€'+data.amounts.shipping);
                $('.sc-amount-discount').html('€'+data.amounts.discount);
                $('.sc-amount-grand-total').html('€'+data.amounts.grand);
                if (data.amounts.discount > 0) {
                    $('.discount-row').removeClass('hide');
                } else {
                    $('.discount-row').addClass('hide');
                }
            }
            $('.checkout-data').removeClass('disable-content');
        }
    })
}

var changeFilterLetter = (letter) =>  {
    if (letter != 'all') {
        $('.letter-filter__interval-item').addClass('hide');
        $('li[data-letter="' +letter+ '"]').removeClass('hide');
    } else {
        $('.letter-filter__interval-item').removeClass('hide');
    }
}

var setupSettings = () => {
    //setup currency
    jQuery.ajax({
        beforeSend: function( xhr ) {
            $('.checkout-data').addClass('disable-content');
            xhr.overrideMimeType( "text/plain; charset=x-user-defined" );
        },
        url: '/change-currency',
        method: 'get',
        data: {currency: $('.avail-currency-select.active').val()},
        success: function(response){
            var data = jQuery.parseJSON(response);
            $('#selector-modal').modal('hide');
            location.href = $('.avail-languages-select.active').find(':selected').attr('data-url');
        }
    })
}

var changeCurrencySetting = (currency) => {
    //setup currency
    jQuery.ajax({
        beforeSend: function( xhr ) {
            $('.checkout-data').addClass('disable-content');
            xhr.overrideMimeType( "text/plain; charset=x-user-defined" );
        },
        url: '/change-currency',
        method: 'get',
        data: {currency: currency},
        success: function(response){
            var data = jQuery.parseJSON(response);
            //$('#selector-modal').modal('hide');
            location.reload();
        }
    })
}

App = {
    init: function () {
        $('span', $('.langs')).click( function() {
            App._changeLang($(this).data('lang'));
        })
        this.page = 1;

        $(window).scroll(function(){
            if ($( window ).width() > 1199) {
                if ($(window).scrollTop() >= 50) {
                    $('.header-fixed').addClass('sticky');
                    $('.dropdown-menu', $('.header-fixed')).css({'top' : '63px'});
                    $('.gray-line').hide('slow');
                    $('.contact-header').hide('slow');
                    $('.search-heder').hide('slow');
                }
                else {
                    $('.header-fixed').removeClass('sticky');
                    $('.dropdown-menu', $('.header-fixed')).css({'top' : '65px'});
                    $('.gray-line').show('slow');
                    $('.contact-header').show('slow');
                    $('.search-heder').show('slow');
                }
            } else {
                if ($(window).scrollTop() >= 50) {
                    $('.header-fixed').addClass('m-fixed');
                    $('.gray-line').hide();
                    $('.contact-header').hide();
                    //$('.search-heder').hide('slow');                    
                } else {
                    $('.header-fixed').removeClass('m-fixed');
                    $('.gray-line').show();
                    $('.contact-header').show();
                    //$('.search-heder').show('slow');
                }
            }
        });

        // scroll to top
        $(window).scroll(function () {
            if ($(this).scrollTop() > 0) {
                $('#scroller').fadeIn();
            } else {
                $('#scroller').fadeOut();
            }
        });
        $('#scroller').click(function () {
            $('body,html').animate({
                scrollTop: 0
            }, 400);
            return false;
        });        

        $('#sourcesPage').change(function() {
            $("#data-content").addClass('disable-content');
            changeProductPage(window.location.pathname, 1, $('#sourcesPage').val(), $('#sourcesSort').val());         
        })

        $('#sourcesSort').change(function() {
            $("#data-content").addClass('disable-content');
            changeProductPage(window.location.pathname, 1, $('#sourcesPage').val(), $('#sourcesSort').val());         
        })

        $('a.list').click(function() {
            $('a.grid').removeClass('active');
            $('a.list').addClass('active');
            $('#products-list').removeClass('grid');
            $('#products-list').addClass('list');
        })
        $('a.grid').click(function() {
            $('a.list').removeClass('active');
            $('a.grid').addClass('active');
            $('#products-list').removeClass('list');
            $('#products-list').addClass('grid');
        });


        $(document).ready(function(){
            $.extend($.lazyLoadXT, {
                edgeY:  200,
                srcAttr: 'data-src'
              });

            showProductInfo();

            //modal selector
            if (!$.cookie('settingsPopup')) {
                $('#selector-modal').modal('show');
                $.cookie('settingsPopup', '1');
            }

            $('#country-container').mCustomScrollbar({ 
                theme:"dark-3"        
            });

            $('a', $('#country-container')).hover(function() {
                var countryId = $(this).attr('data-id');
                $('.avail-languages').removeClass('active');
                $('#lang-'+countryId).addClass('active');;
                $('#currency-'+countryId).addClass('active');;

            });

            $('#country-selector-dropdown').change(function() {
                var countryId = $(this).val();
                $('.avail-languages-select').removeClass('active');
                $('#selectlang-'+countryId).addClass('active');
                $('#selectcurrency-'+countryId).addClass('active');;
            })

            //dropdown function for mobile category menu
            $('.shevron-1').click(function() {
                $(this).parent().find('.dropdown-menu').toggle();
            });

            $('.shevron-2').click(function() {
               $(this).parent().find('.menu-level-2').toggle();
            })
    
        });
    
        
    },

    _changeLang: function(lang) {
        var path = '/'+this.currPath;
        var prevLang = lang == 'ru' ? 'uk' : 'ru';
    },

    createPagination: function(total, position, page) {
        $('#'+position).bootpag({
            total: total,
            page: page,
            maxVisible: 5,
            leaps: true,
            firstLastUse: true,
            first: '←',
            last: '→',
            wrapClass: 'pagination',
            activeClass: 'rc-pagination-item-active',
            disabledClass: 'disabled',
            nextClass: 'next',
            prevClass: 'prev',
            lastClass: 'last',
            firstClass: 'first'
        }).on("page", function(event, num){
            $("#data-content").addClass('disable-content');
            var pagUrl = window.location.pathname;
            App.page = num;
            /*if (pagUrl == '/search-results' || pagUrl == '/top-search-results') {
                pagUrl += window.location.search;
                var result = pagUrl.match(/page=[0-9]/);
                if (result) {
                    pagUrl = pagUrl.replace(result[0], 'page='+num);
                    window.history.replaceState(null, null, pagUrl);
                }
            }*/
            changeProductPage(pagUrl, num, $('#sourcesPage').val(), $('#sourcesSort').val());
        });
    }

    
}
