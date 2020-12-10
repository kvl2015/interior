var initRate = () => {
    $(".review-rate").starRating({
        totalStars: 5,
        minRating: 1,
        starShape: 'rounded',
        starSize: 25,
        emptyColor: 'lightgray',
        hoverColor: '#ecdc9d',
        activeColor: '#ffc107',
        useGradient: false,
        useFullStars: true,
        callback: function(currentRating, $el){
            $('input[name="rate"]').val(currentRating);
            $('.error-rate').html('');
        }
      });    
} 

var initDisableRate = () => {
    $(".saved-review-rate").starRating({
        starShape: 'rounded',
        strokeColor: '#894A00',
        strokeWidth: 10,
        starSize: 25,
        disable: true
    });
} 

var emailEnquiry = (em) => {
    //emailEnquiry
    var elm = $('.sc-product-item', $('.product-view'));
    var optLength = elm.find('select.sc-product-option').length;
    var o = {};

    if (optLength > 0) {
        elm.find('select.sc-product-option')
            .each(function(key, value) {
                if (!$(this).val()) {
                    $('.option_error_'+key+'_'+$(this).attr('opt_product_id')).html('Required field');
                } else {
                    var thumb = '';
                    if ($('img[data-option-id="' +$(this).val()+ '"]').length) {
                        thumb = $('img[data-option-id="' +$(this).val()+ '"]').attr('src');
                    } 
                    o[key] = {
                        'name': $(this).find(':selected').text(), 
                        'thumb' : thumb, 
                        'label' : $(this).attr('opt-lable-name'),
                        'code' : $(this).find(':selected').val(),
                        'id' : $(this).find(':selected').attr('opt-id')
                    };
                }
            });

        if (Object.keys(o).length == optLength) {
            jQuery.ajax({
                url: "/api/get-product-review",
                method: 'get',
                data: {'product_id': $('#productId').val(), 'options' : o},
                success: function(data){
                    if (data.success) {
                        $('.product-email-block', $('#emailEnquiry')).html(data.html);
                        $('#emailEnquiry').modal('show');
                    } else {
                        console.log('Something went wrong');
                    }
                },
                error: function (error) {
                },
            });
        } else {
            
        }
    } else {
        if (Object.keys(o).length == optLength) {
            jQuery.ajax({
                url: "/api/get-product-review",
                method: 'get',
                data: {'product_id': $('#productId').val(), 'options' : {}},
                success: function(data){
                    if (data.success) {
                        $('.product-email-block', $('#emailEnquiry')).html(data.html);
                        $('#emailEnquiry').modal('show');
                    } else {
                        console.log('Something went wrong');
                    }
                },
                error: function (error) {
                },
            });
        } else {
            
        }
    }   
}

var getReview = (productId, page) => {
    $(".review-conteiner").addClass('disable-content');
    jQuery.ajax({
        url: "/api/get-review",
        method: 'get',
        data: {'product_id': productId, 'page' : page},
        success: function(data){
            if (data.success) {
                $(".review-conteiner").removeClass('disable-content');
                $(".review-conteiner").html(data.html)
                initDisableRate();
            }
        },
        error: function (error) {
        },
    });
}

var minusQty = () => {
    var qty = parseInt($('.cart-qty').val());
    qty = qty - 1;
    qty = qty <= 0 ? 1 : qty;
    
    $('.cart-qty').val(qty);
}

var plusQty = () => {
    var qty = parseInt($('.cart-qty').val()) +  1;
    $('.cart-qty').val(qty);
}

var addToWhishList = (productId) => {
    jQuery.ajax({
        url: "/api/add-to-whish",
        method: 'get',
        data: {'product_id': productId},
        success: function(data){
            if (data.success) {
                $(".review-conteiner").removeClass('disable-content');
                $(".review-conteiner").html(data.html)
                initDisableRate();
            }
            //$('#shadow-site').addClass('hide');
        },
        error: function (error) {
            //$('#shadow-site').addClass('hide');
        },
    });    
}

$(document).ready(function() {
    $('.request-link').click(function() {
        // check options selected
        emailEnquiry();
    });

    if ($('.horizontal-images')) {
        $(".horizontal-images").mCustomScrollbar({
            axis:"x",
            theme:"dark",
            autoExpandScrollbar:true,
            advanced:{autoExpandHorizontalScroll:true}
        });
    }

    $('[data-toggle="tooltip"]').tooltip({
        html:true
    })


    $('.select-css').change(function() {
        var optKey = $(this).attr('data-key');
        console.log(optKey);
        var pageReload = $(this).attr('page-reload');
        $('.error').html('');
        if (pageReload) {
            var mainSlug = $('div[data-name="product_slug_main"]').attr('data-value');
            location.href = '/'+mainSlug+'.'+$(this).attr('opt-article');
        }
        $('img', $(this).parent().parent()).removeClass('opt-thumb-selected');
        $('.option_error_'+optKey).html('');
        if (!$(this).val()) {
            $('.thumb-'+optKey).html('');
        } else {
            var thumb = $('img[data-option-id="' +$(this).val()+ '"');
            if ($(thumb).length) {
                $('.thumb-'+optKey).html('<img src="' +$(thumb).attr('src')+'" />');
                $('.thumb-'+optKey).append('<br/><a href="' +$(thumb).attr('data-big-src')+ '" data-lightbox="' +$(thumb).attr('data-opt-name')+ '" data-title="' +$(thumb).attr('data-opt-name')+ '">Enlarge</a>');
                $('.thumb-'+optKey).addClass('thumb-selected-no-empty');
                $(this).parent().addClass('select-option-container-thumb');
                $(thumb).addClass('opt-thumb-selected');
            }
        }
        // check prices for option
        var price = $(this).find(':selected').attr('opt-price-value');

        if (price > 0) {
            $('div[data-name="price"]').attr({'data-value' : price});
        }
    });


    $('.option-thumb').click(function() {
        var optKey = $(this).attr('data-option-key');
        var parent = $(this).parent();
        var pageReload = $(this).attr('page-reload');
        if (pageReload) {
            var mainSlug = $('div[data-name="product_slug_main"]').attr('data-value');
            location.href = '/'+mainSlug+'.'+$(this).attr('data-opt-article');
        }
        $('select[name="option_' +optKey+ '"]').val($(this).attr('data-option-id'));
        // $(this).addClass('opt-thumb-selected');
        console.log($(this).attr('src'));
        $('img', $('select[name="option_' +optKey+ '"]').parent().parent()).removeClass('opt-thumb-selected');
        $('.thumb-'+optKey).html('<img src="' +$(this).attr('src')+'" />');
        $('.thumb-'+optKey).append('<br/><a href="' +$(this).attr('data-big-src')+ '" data-lightbox="' +$(this).attr('data-opt-name')+ '" data-title="' +$(this).attr('data-opt-name')+ '">Enlarge</a>');
        $(this).addClass('opt-thumb-selected');
        $('.thumb-'+optKey).addClass('thumb-selected-no-empty');
        $('select[name="option_' +optKey+ '"]').parent().addClass('select-option-container-thumb');

        //check price
        //var price = $('select[name="option_' +optKey+ '"]', parent).find(':selected').attr('opt-price-value');

        //if (price > 0) {
            //$('div[data-name="price"]').attr({'data-value' : price});
        //}
    })

    $('.btn-link').click(function() {
        $(this).find('span.arrow').toggleClass('active')
    });

    if ($('.owl-carousel').length) {
        $('.owl-carousel').owlCarousel({
            loop:true,
            margin:30,
            autoplay: false,
            autoplayTimeout: 3000,
            autoplayHoverPause: true,
            responsiveClass:true,
            nav:true,
            responsive:{
                0:{
                    items:1,
                    nav:true
                },
                600:{
                    items:3,
                    nav:false
                },
                1000:{
                    items:5,
                    nav:true,
                    loop:true
                }
            }
        });
    }
    

    if ($("#send-review-form").length) {
        $("#send-review-form").validate({
            errorElement: 'span',
            rules: {
                nickname: {
                    required: true
                },
                summary: {
                    required: true
                },
                review: {
                    required: true
                }
            }
        });
        initRate();
        initDisableRate();
    }


    $('#sendReview').click(function(e){
        e.preventDefault();
        if ($("#send-review-form").valid()) {
            if (parseInt($(".review-rate").starRating('getRating')) > 0) {
                $('#shadow-site').removeClass('hide');
                //$('input[name="rate"]').val($(".review-rating").starRating('getRating')); 
                jQuery.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('input[name="_token"]').val()
                    }
                });
                jQuery.ajax({
                    url: "/api/send-review",
                    method: 'post',
                    data: $("#send-review-form").serialize(),
                    success: function(data){
                        if (data.success) {
                            $('#send-review-form').trigger("reset");
                            $('.review-rating').starRating('unload');
                            $('.form-review-conteiner').html('<div class="alert alert-success" role="alert">Thank you for your review!</div>');
                        }
                        $('#shadow-site').addClass('hide');
                    },
                    error: function (error) {
                        $('#shadow-site').addClass('hide');
                    },
        
                });
            } else {
                $('.error-rate').html('This field is required.');
            }
        }
    })

    if ($('.product-view').length) {
        $(".content-viewed").mCustomScrollbar({
            setWidth:false,
            setHeight:false,
            setTop:0,
            setLeft:0,
            axis:"y",
            scrollbarPosition:"inside",
            scrollInertia:950,
            autoDraggerLength:true,
            autoHideScrollbar:false,
            autoExpandScrollbar:false,
            alwaysShowScrollbar:0,
            snapAmount:null,
            snapOffset:0,
            mouseWheel:{
                enable:true,
                scrollAmount:"auto",
                axis:"y",
                preventDefault:false,
                deltaFactor:"auto",
                normalizeDelta:false,
                invert:false,
                disableOver:["select","option","keygen","datalist","textarea"]
            },
            scrollButtons:{
                enable:false,
                scrollType:"stepless",
                scrollAmount:"auto"
            },
            keyboard:{
                enable:true,
                scrollType:"stepless",
                scrollAmount:"auto"
            },
            contentTouchScroll:25,
            advanced:{
                autoExpandHorizontalScroll:false,
                autoScrollOnFocus:"input,textarea,select,button,datalist,keygen,a[tabindex],area,object,[contenteditable='true']",
                updateOnContentResize:true,
                updateOnImageLoad:true,
                updateOnSelectorChange:false,
                releaseDraggableSelectors:false
            },
            theme:"light",
            callbacks:{
                onInit:false,
                onScrollStart:false,
                onScroll:false,
                onTotalScroll:false,
                onTotalScrollBack:false,
                whileScrolling:false,
                onTotalScrollOffset:0,
                onTotalScrollBackOffset:0,
                alwaysTriggerOffsets:true,
                onOverflowY:false,
                onOverflowX:false,
                onOverflowYNone:false,
                onOverflowXNone:false
            },
            live:false,
            liveSelector:null
        });
    }



});