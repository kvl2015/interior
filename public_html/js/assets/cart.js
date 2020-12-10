/*!
 * jQuery Smart Cart v3.0.1
 * The smart interactive jQuery Shopping Cart plugin with PayPal payment support
 * http://www.techlaboratory.net/smartcart
 *
 * Created by Dipu Raj
 * http://dipuraj.me
 *
 * Licensed under the terms of the MIT License
 * https://github.com/techlab/SmartCart/blob/master/LICENSE
 */

;(function ($, window, document, undefined) {
    "use strict";
    // Default options
    var defaults = {
        cart: [], // initial products on cart
        resultName: 'cart_list', // Submit name of the cart parameter
        theme: 'default', // theme for the cart, related css need to include for other than default theme
        combineProducts: true, // combine similar products on cart
        highlightEffect: true, // highlight effect on adding/updating product in cart
        cartItemTemplate: '<img class="img-responsive pull-left" src="{product_image}" /><h4 class="list-group-item-heading">{product_name}</h4><p class="list-group-item-text">{product_desc}</p>',
        cartItemQtyTemplate: '{display_price} × {display_quantity} = {display_amount}',
        productContainerSelector: '.sc-product-item',
        checkoutContainerSelector: '.product-item',
        productElementSelector: '*', // input, textarea, select, div, p
        addCartPlus: '.sc-plus-to-cart',
        addCartMinus: '.sc-minus-to-cart',
        addCartSelector: '.sc-add-to-cart',
        addBarSelector: '.sc-qty-bar',
        productOptionSelector: 'select.sc-product-option',
        cartCounterSelector: '.sc-cart-counter',
        openCartSelector: '.sc-open-cart',
        closeCartSelector: '.sc-close-cart',
        cartContentSelector: '.sc-cart-content',
        discount: 0,
        amountSettings: { // Map for control amount cart
            amountSummary: '.sc-amount-summary',
            amountSubtotal: '.sc-amount-subtotal',
            amountShipping: '.sc-amount-shipping',
            amountDiscount: '.sc-amount-discount',
            amountGrandTotal: '.sc-amount-grand-total'
        },
        paramSettings : { // Map the paramters
            productPrice: 'price',
            productQuantity: 'product_quantity',
            productName: 'product_name',
            productId: 'product_id',
        },
        lang: {  // Language variables
            cartEmpty: 'Cart is empty',
            cartProducts: 'items',
            cartAmount: 'Amount',
            checkoutBtn: '',
            checkoutCartHeader: ''
        },
        submitSettings: {
            submitType: 'form', // form, paypal, ajax
            ajaxURL: '', // Ajax submit URL
            ajaxSettings: {} // Ajax extra settings for submit call
        },
        currencySettings: {
            locales: 'en-US', // A string with a BCP 47 language tag, or an array of such strings
            currencyOptions:  {
                style: 'currency',
                currency: 'UAH',
                currencyDisplay: 'symbol'
            } 
        },
        toolbarSettings: {
            showToolbar: true,
            showCheckoutButton: true,
            showClearButton: true,
            showCartSummary:true,
            checkoutButtonStyle: 'default', // default, paypal, image
            checkoutButtonImage: '', // image for the checkout button
            toolbarExtraButtons: [] // Extra buttons to show on toolbar, array of jQuery input/buttons elements
        },
        debug: false
    };

    // The plugin constructor
    function SmartCart(element, options) {

        // Merge user settings with default, recursively
        this.options = $.extend(true, {}, defaults, options);
        // Cart array
        let cartData = JSON.parse(localStorage.getItem('scartItems'));
        this.cart = cartData ? cartData : [];
        $(this.options.cartCounterSelector).html(this._get_cartqty());
        
        // Cart element
        this.cart_element = $(element);

        // Call initial method
        this.init();
    }

    $.extend(SmartCart.prototype, {

        init: function () {
            this._checkout_cart();
//alert(this.options.discount);
            // Assign plugin events
            this._setEvents();

            // Set initial products
            var mi = this;
            $(this.options.cart).each(function(i, p) {
console.log(p);
                p = mi._addToCart(p);
            });

            // Call UI sync
            this._hasCartChange();

            $(this.options.openCartSelector).click(function() {
                if (mi.cart.length)
                    $('#smartcart').addClass('active')
            })
            $(this.options.closeCartSelector).click(function() {
                $('#smartcart').removeClass('active')
            })            

        },

// PRIVATE FUNCTIONS
        /*
         * Set events for the cart
         */
        _setEvents: function () {
            var mi = this;


            // Capture add to cart button events
            $(this.options.addCartSelector).on( "click", function(e) {
                e.preventDefault();

                var p = mi._getProductDetails($(this));
                var optLength = mi._checkProductOptions($(this));
                if (optLength > 0) {
                    var o = mi._getProductOptions($(this));
                    if (Object.keys(o).length == optLength) {
                        p.options = o;
                        p = mi._addToCart(p);
                    }
                    //var t = mi._getProductOptions($(this));
                } else {
                    p = mi._addToCart(p);
                }
                
                
console.log(mi.cart);
            });

            // Capture click "+"
            /*$(this.options.addCartPlus).on( "click", function(e) {
                e.preventDefault();

                var p = mi._getProductDetails($(this)); 
                console.log(p.unique_key);
                p = mi._addToCart(p);

                // hide button selector and show qty bar selector
                var btnSelector = $(this).closest(mi.options.productContainerSelector).find(mi.options.addCartSelector);
                var qtySelector = $(this).closest(mi.options.productContainerSelector).find(mi.options.addBarSelector);
                btnSelector.addClass('hide');
                qtySelector.removeClass('hide');
                qtySelector.find('.btn-qty-order>span').html(p.product_quantity)

                $(this).parents(mi.options.productContainerSelector).addClass('sc-added-item').attr('data-product-unique-key', p.unique_key);
            });

            // Capture click "-"
            $(this.options.addCartMinus).on( "click", function(e) {
                e.preventDefault();
                var p = mi._getProductDetails($(this));
                // check select color
                p = mi._reduceCart(p);

                // hide button selector and show qty bar selector
                var btnSelector = $(this).closest(mi.options.productContainerSelector).find(mi.options.addCartSelector);
                var qtySelector = $(this).closest(mi.options.productContainerSelector).find(mi.options.addBarSelector);

                if (Object.keys(p).length > 0) {
                    qtySelector.find('.btn-qty-order>span').html(p.product_quantity)
                } else {
                    btnSelector.removeClass('hide');
                    qtySelector.addClass('hide');
                    qtySelector.find('.btn-qty-order>span').html(1)
                }

                // $(this).parents(mi.options.productContainerSelector).addClass('sc-added-item').attr('data-product-unique-key', p.unique_key);
            });*/

            // add for checkout page
            $(this.options.addCartPlus, $('.checkout-data')).on( "click", function(e) {
                e.preventDefault();
                var parent = $(this).parent();
                var itemBlock = parent.closest('.item-data');
                var plusQty = parseInt($('.qty', parent).val()) + 1;

                mi._updateCartQuantity(parent.data('unique_key'), plusQty)

                var item = mi._getByKey(parent.data('unique_key'));
                $('.qty', parent).val(plusQty);
                $('.item-total b', itemBlock).html('€'+plusQty*item.price);

                // mi._update_cart_amount();
                mi._send_server_data('checkout');

                $(mi.options.cartCounterSelector).html(mi._get_cartqty());
            });

            // minus for checkout page
            $(this.options.addCartMinus, $('.checkout-data')).on( "click", function(e) {
                e.preventDefault();
                var parent = $(this).parent();
                var itemBlock = parent.closest('.item-data');
                var plusQty = parseInt($('.qty', parent).val()) - 1;

                if (plusQty > 0) {
                    mi._updateCartQuantity(parent.data('unique_key'), plusQty)
                    $('.qty', parent).val(plusQty);
                } else {
                    mi._removeFromCart(parent.data('unique_key'))
                    parent.closest(mi.options.checkoutContainerSelector).remove();
                }
                var item = mi._getByKey(parent.data('unique_key'));
                $('.qty', parent).val(plusQty);
                $('.item-total b', itemBlock).html('€'+plusQty*item.price);

                // mi._update_cart_amount();
                mi._send_server_data('checkout');
                $(mi.options.cartCounterSelector).html(mi._get_cartqty());
            });            

            // remove from checkout page
            $('.checkout-data').on( "click", '.sc-cart-remove', function(e) {
                e.preventDefault();
                var parent = $(this).closest(mi.options.checkoutContainerSelector);
                mi._removeFromCart($(this).data('unique_key'));
                parent.remove();
                $('.cart-amount span').html(mi._getCartSubtotal());
                $(mi.options.cartCounterSelector).html(mi._get_cartqty());
                mi._send_server_data('checkout');
            });

            // Item remove event
            $(this.cart_element).on( "click", '.sc-cart-remove', function(e) {
                e.preventDefault();
                $(this).parents('.sc-cart-item').fadeOut( "normal", function() {
                    mi._removeFromCart($(this).data('unique-key'));

                    // redefined buttons
                    var parent = $('div[data-value="'+$(this).data('product-id')+ '"]').parent();
                    $(mi.options.addCartSelector, parent).removeClass('hide');
                    $(mi.options.addBarSelector, parent).addClass('hide');

                    $(this).remove();
                    mi._hasCartChange();
                    mi._send_server_data('checkout');
                });
            });

            // Item quantity change event
            $(this.cart_element).on( "change", '.sc-cart-item-qty', function(e) {
                e.preventDefault();
                mi._updateCartQuantity($(this).parents('.sc-cart-item').data('unique-key'), $(this).val());
            });

            // Cart checkout event
            $(this.cart_element).on( "click", '.sc-cart-checkout', function(e) {
                if($(this).hasClass('disabled')) { return false; }
                e.preventDefault();
                mi._submitCart();
            });

            // Cart clear event
            $(this.cart_element).on( "click", '.sc-cart-clear', function(e) {
                if($(this).hasClass('disabled')) { return false; }
                e.preventDefault();
                $('.sc-cart-item-list > .sc-cart-item', this.cart_element).fadeOut( "normal", function() {
                    $(this).remove();
                    mi._clearCart();
                    mi._hasCartChange();
                });
            });
        },

        _get_cartqty: function() {
            if (this.cart.length > 0) {
                var tQty = 0;
                $.each(this.cart, function( i, p ) {
                    tQty += parseInt(p.product_quantity);
                });
                return tQty;
            } else {
                return 0;
            }
        },

        _checkout_cart: function() {
            var mi = this;

            //$('.checkout-data').html('');
            $.each(this.cart, function( i, p ) {
                var option = '';
                if (p.options) {
                    if (Object.keys(p.options).length) {
                        option += '<ul>'
                        $.each(p.options, function( j, o ) {
                            if (o.thumb)
                                option += '<li><span class="option-label">' +o.label+ '</span><img src="' +o.thumb+ '" /><span class="option-name">'+o.name+'</span><span class="clearfix"></span></li>';
                            else
                                option += '<li><span class="option-label">' +o.label+ '</span>'+o.name+'<span class="clearfix"></span></li>';
                        });
                        option += '</ul>'
                    }
                }

                var itemList = '<div class="product-item sc-cart-item row">\n' +
                    '<div class="col-md-3 col-lg-3 col-xl-3 product-image">\n' +
                    '   <span><img src=' +p.product_image+ ' class="img-fluid" /></span>\n' +
                    '   <span class="btn btn-xs remove-item sc-cart-remove" data-unique_key="' +p.unique_key+ '">\n' +
                    '       <i class="mob-trash"></i>\n' +
                    '   </span>\n' +
                    '</div>\n' +
                    '<div class="col-md-3 col-lg-9 col-xl-9 product-data">\n' +
                    '    <div class="item-data">\n' +
                    '      <div class="part-1">\n' +
                    '        <span class="name"><a href="/' +p.product_slug+ '">' +p.product_name+ '</a></span>\n' +
                    '        <span class="brand">by <b>' +p.brand_name+ '</b></span>';
                    if (p.designer_name) {
                        itemList += '<span class="brand">Brand origin country <b>' +p.brand_country+ '</b></span>';
                    }
                    if (p.designer_name) {
                        itemList += '<span class="brand">Designer: <b>' +p.designer_name+ '</b></span>';
                    }
                    if (p.collection_name) {
                        itemList += '<span class="brand">Collection: <b>' +p.collection_name+ '</b></span>';
                    }
                    itemList += 
                    '        <div class="options">' +option+ '</div>\n' +
                    '      </div>\n' +
                    '      <div class="part-2"><div class="float-right">\n' +
                   '         <span class="item-price"><b>€' +(p.price)+ ' </b></span>\n' +
                    '        <div class="qty-bar sc-qty-bar">\n' +
                    '          <div class="btn-group" role="group" data-unique_key="' +p.unique_key+ '">\n' +
                    '            <button type="button" class="btn sc-minus-to-cart">-</button>\n' +
                    '            <input type="text" class="qty sc-qty-cart" value="' +p.product_quantity+ '"></>\n' +
                    '            <button type="button" class="btn sc-plus-to-cart">+</button>\n' +
                    '          </div>\n' +
                    '         </div>\n' +
                    '         <span class="item-total"><b>€' +(p.product_quantity*p.price)+ ' </b></span></div>\n' +
                    '      </div>\n' +
                    '    </div>\n' +
                    '</div></div>';
                //$('.checkout-data').append(itemList);
            });

            mi._update_cart_amount();
        },
        _update_cart_amount: function() {
            /*var mi = this;
            var total = 0;
            var discount = 0;
            var shipping = 0;
            $.each(this.cart, function( i, p ) {
                total += p.product_quantity*p.price;
            });
            $(mi.options.amountSettings.amountSummary).html('€'+total);
            $(mi.options.amountSettings.amountSubtotal).html('€'+total);
            $(mi.options.amountSettings.amountShipping).html('€0');
            if (discount == 0) {
                $('.discount-row').addClass('hide');
            } else {
                $('.discount-row').removeClass('hide'); 
            }
            $(mi.options.amountSettings.amountGrandTotal).html('€'+(total + shipping - discount));*/
        },

        /*
         * Get the parameters of a product by seaching elements with name attribute/data.
         * Product details will be return as an object
         */
        _getProductDetails: function (elm) {
            var mi = this;
            var p = {};
            elm.parents(this.options.productContainerSelector)
                .find(this.options.productElementSelector)
                .each(function() {
                    if ($(this).is('[name]') === true || typeof $(this).data('name') !== typeof undefined) {
                        var key = $(this).attr('name') ? $(this).attr('name') : $(this).data('name');
                        var val = mi._getContent($(this));
                        if(key && val){
                            p[key] = val;
                        }
                    }
                });
            return p;
        },
/*
         * Get the parameters of a product by seaching elements with name attribute/data.
         * Product details will be return as an object
         */
        _checkProductOptions: function(elm) {
            var mi = this;
            var o = {};
            return elm.parents(this.options.productContainerSelector).find(this.options.productOptionSelector).length
        }, 
        _getProductOptions: function (elm) {
            var mi = this;
            var o = {};
            // console.log(this.options.productContainerSelector);
            elm.parents(this.options.productContainerSelector)
                .find(this.options.productOptionSelector)
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
            return o;
        },   
        _getOptionThumbs: function (elm) {

        } ,    
        /*
         * Add the product object to the cart
         */
        _addToCart: function (p) {
            var mi = this;

            if (!p.hasOwnProperty(this.options.paramSettings.productPrice)) {
                this._logError('Price is not set for the item');
                return false;
            }
            if (!p.hasOwnProperty(this.options.paramSettings.productQuantity)) {
                this._logMessage('Quantity not found, default to 1');
                p[this.options.paramSettings.productQuantity] = 1;
            }

            if (!p.hasOwnProperty('unique_key')) {
                p.unique_key =  this._getUniqueKey();
            }
            if(this.options.combineProducts){
                var pf = $.grep(this.cart, function(n, i){
                    //console.log(mi._isObjectsEqual(n, p));
                    return mi._isObjectsEqual(n, p);
                });
                if(pf.length > 0){
                    var idx = this.cart.indexOf(pf[0]);
                    this.cart[idx][this.options.paramSettings.productQuantity] = (this.cart[idx][this.options.paramSettings.productQuantity] - 0) + (p[this.options.paramSettings.productQuantity] - 0);
                    p = this.cart[idx];
                    // Trigger "itemUpdated" event
                    this._triggerEvent("itemUpdated", [p]);
                }else{
                    this.cart.push(p);
                    // Trigger "itemAdded" event
                    this._triggerEvent("itemAdded", [p]);
                }
            }else{
                this.cart.push(p);
                // Trigger "itemAdded" event
                this._triggerEvent("itemAdded", [p]);
            }
            // add cart to storage
            localStorage.setItem('scartItems', JSON.stringify(this.cart));

            $(this.options.cartCounterSelector).html(this._get_cartqty());

            $('.message-success-add',  $('#success-msg-'+p.product_id)).html('Product was successfully added').removeClass('hide').addClass('show');
            $('#success-msg-'+p.product_id).removeClass('hide').addClass('show');
            
            //$('#order-add-modal').modal('show');

            this._addUpdateCartItem(p);
            this._send_server_data('add');
            return p;
        },

        _reduceCart: function (p) {
            var mi = this;

            if (!p.hasOwnProperty('unique_key')) {
                p.unique_key =  this._getUniqueKey();
            }
            if(this.options.combineProducts){
                var pf = mi.cart.filter(obj => obj.product_id == p.product_id && obj.color_id == p.color_id);

                if(pf.length > 0){
                    var idx = this.cart.indexOf(pf[0]);
                    var newQty = this.cart[idx][this.options.paramSettings.productQuantity] - 1;
                    if (newQty > 0) {
                        this.cart[idx][this.options.paramSettings.productQuantity] = newQty;
                        p = this.cart[idx];
                    }
                    else {
                        // need to remove item
                        this._removeFromCart(pf[0].unique_key);
                        return false;
                    }

                    // Trigger "itemUpdated" event
                    //this._triggerEvent("itemUpdated", [p]);
                }
            }

            // add cart to storage
            localStorage.setItem('scartItems', JSON.stringify(this.cart));

            this._addUpdateCartItem(p);
            return p;
        },

        /*
         * Remove the product object from the cart
         */
        _removeFromCart: function (unique_key) {
            var mi = this;

            $.each( this.cart, function( i, n ) {
                if(n.unique_key === unique_key){
                    var itemRemove = mi.cart[i];
                    mi.cart.splice(i, 1);
                    $('*[data-product-unique-key="' + unique_key + '"]').removeClass('sc-added-item');
                    mi._hasCartChange();
                    // Trigger "itemRemoved" event
                    mi._triggerEvent("itemRemoved", [itemRemove]);

                    localStorage.setItem('scartItems', JSON.stringify(mi.cart));
                    return false;
                }
            });
        },
        /*
         * Clear all products from the cart
         */
        _clearCart: function () {
            this.cart = [];
            // Trigger "cartCleared" event
            this._triggerEvent("cartCleared");
            this._hasCartChange();
        },
        /*
         * Update the quantity of an item in the cart
         */
        _updateCartQuantity: function (unique_key, qty) {
            var mi = this;
            var qv = this._getValidateNumber(qty);
            $.each( this.cart, function( i, n ) {
                if(n.unique_key === unique_key){
                    if(qv){
                        mi.cart[i][mi.options.paramSettings.productQuantity] = qty;
                    }
                    mi._addUpdateCartItem(mi.cart[i]);

                    localStorage.setItem('scartItems', JSON.stringify(mi.cart));

                    return false;
                }
            });
        },
        /*
         * Update the UI of the cart list
         */
        _addUpdateCartItem: function (p) {
            var productAmount = (p[this.options.paramSettings.productQuantity] - 0) * (p[this.options.paramSettings.productPrice] - 0);
            var cartList = $('.sc-cart-item-list',this.cart_element);
            var elmMain = cartList.find("[data-unique-key='" + p.unique_key + "']");
            if(elmMain && elmMain.length > 0){
                elmMain.find(".sc-cart-item-qty").val(p[this.options.paramSettings.productQuantity]);
                elmMain.find(".sc-cart-item-amount").text(this._getMoneyFormatted(productAmount));
            }else{
                elmMain = $('<div></div>').addClass('sc-cart-item list-group-item');
                elmMain.append('<button type="button" class="sc-cart-remove">' + this.options.lang.cartRemove + '</button>');
                elmMain.attr('data-unique-key', p.unique_key);

                elmMain.append(this._formatTemplate(this.options.cartItemTemplate, p));

                var itemSummary = '<div class="sc-cart-item-summary"><span class="sc-cart-item-price">' + this._getMoneyFormatted(p[this.options.paramSettings.productPrice]) + '</span>';
                itemSummary += ' × <input type="number" min="1" max="1000" class="sc-cart-item-qty" value="' + this._getValueOrEmpty(p[this.options.paramSettings.productQuantity]) + '" />';
                itemSummary += ' = <span class="sc-cart-item-amount">' + this._getMoneyFormatted(productAmount) + '</span></div>';

                elmMain.append(itemSummary);
                cartList.append(elmMain);
            }

            // Apply the highlight effect
            if(this.options.highlightEffect === true){
                elmMain.addClass('sc-highlight');
                setTimeout(function() {
                    elmMain.removeClass('sc-highlight');
                },500);
            }

            this._hasCartChange();
        },
        /*
         * Handles the changes in the cart
         */
        _hasCartChange: function () {

            $('.sc-cart-count',this.cart_element).text(this.cart.length);
            $('.sc-cart-subtotal',this.element).text(this._getCartSubtotal());
            var mi = this;
            if(this.cart.length === 0){
                $(this.options.cartCounterSelector).html(0);
                $('#smartcart').removeClass('active');
                // $('.cart-total').html(this.options.lang.cartEmpty);

                // Trigger "cartEmpty" event
                this._triggerEvent("cartEmpty");
            }else{
                //$('.cart-total').removeClass('empty-cart');
                $('.cart-total').html(this.options.lang.cartProducts+' ' + this.cart.length+'<br/> ' +this.options.lang.cartAmount+ '  '+this._getCartSubtotal());
                var itemList = '';
                var total = this._getCartSubtotal();
                var cartBtns = '';
                cartBtns += '<a href="/checkout/cart" class="btn btn-checkout btn-info">Go to shopping cart</a><br/><a href="javascript:;" class="btn btn-checkout btn-info">To secure checkout</a>';
                $.each(this.cart, function( i, p ) {
                    var option = '';
                    if (p.options) {
                        if (Object.keys(p.options).length) {
                            option += '<ul>'
                            $.each(p.options, function( j, o ) {
                                if (o.thumb)
                                    option += '<li><span class="option-label">' +o.label+ '</span><img src="' +o.thumb+ '" /><span class="option-name">'+o.name+'</span></li>';
                                else
                                    option += '<li><span class="option-label">' +o.label+ '</span>'+o.name+'</li>';
                            });
                            option += '</ul>'
                        }
                    }
                    itemList += '<li>\n' +
                        '<table class="table">\n' +
                        '  <tbody>\n' +
                        '    <tr class="sc-cart-item" data-unique-key="' +p.unique_key+'" data-product-id="' +p.product_id+ '">\n' +
                        '      <td class="image">\n' +
                        '        <img src=' +p.product_image+ ' class="img-responsive" width="90" height="120" />\n' +
                        '      </td>\n' +
                        '      <td>\n' +
                        '        <div class="cart-item-name">' +p.product_name+ '</div>\n' +
                        '        <div class="cart-item-article">' +p.article+ '</div>\n' +
                        '        <div class="price-cart">\n' +
                        '          <span class="cart-item-qty">' +p.product_quantity+ '<i> x </i></span> €' +(p.price)+ '\n' +
                        '        </div>\n' +
                        '        <div class="options">' +option+ '</div>\n' +
                        '      </td>\n' +
                        '      <td>\n' +
                        '      <a href="javascript:;" class="sc-cart-remove" data-unique_key="' +p.unique_key+ '">X</a>\n' +
                        '      </td>\n' +
                        '    </tr>\n' +
                        '  </tbody>\n' +
                        '</table></li>'+
                        '';
                });
                itemList += '<li><span class="cart-total">Total: €' +total+ '</span><span class="vat"></span>incl. VAT</li>';
                $(this.options.cartContentSelector).html('<ul class="pull-right toggle-cont" id="cartMenu">' +itemList+ '</ul><br/>'+cartBtns);
            }

            
        },
        
        _send_server_data: function(type) {
            var mi = this;
            //send data to server
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
                url: '/update-cart',
                method: 'post',
                data: {
                    items: JSON.stringify(this.cart),
                    type: type
                },
                success: function(response) {
                    var data = jQuery.parseJSON(response);
                    if (data.success) {
                        if (type == 'checkout') {
                            $('.checkout-data').html(data.html);
                            $(mi.options.amountSettings.amountSummary).html('€'+data.amounts.summary);
                            $(mi.options.amountSettings.amountSubtotal).html('€'+data.amounts.subtotal);
                            $(mi.options.amountSettings.amountShipping).html('€'+data.amounts.shipping);
                            $(mi.options.amountSettings.amountDiscount).html('€'+data.amounts.discount);
                            $(mi.options.amountSettings.amountGrandTotal).html('€'+data.amounts.grand);
                            $('.discount-row').addClass('hide');
                            mi.init();
                            
                            //this._getMoneyFormatted
                        }
                    }
                    $('.checkout-data').removeClass('disable-content');
                }
            });
        },
        /*
         * Calculates the cart subtotal
         */
        _getCartSubtotal: function () {
            var mi = this;
            var subtotal = 0;
            $.each(this.cart, function( i, p ) {
                if(mi._getValidateNumber(p[mi.options.paramSettings.productPrice])){
                    subtotal += (p[mi.options.paramSettings.productPrice] - 0) * (p[mi.options.paramSettings.productQuantity] - 0);
                }
            });
            return this._getMoneyFormatted(subtotal);
        },
        /*
         * Cart submit functionalities
         */
        _submitCart: function () {
            var mi = this;
            var formElm = this.cart_element.parents('form');
            if(!formElm){
                this._logError( 'Form not found to submit' );
                return false;
            }

            switch(this.options.submitSettings.submitType){
                case 'ajax':
                    var ajaxURL = (this.options.submitSettings.ajaxURL && this.options.submitSettings.ajaxURL.length > 0) ? this.options.submitSettings.ajaxURL : formElm.attr( 'action' );

                    var ajaxSettings = $.extend(true, {}, {
                        url: ajaxURL,
                        type: "POST",
                        data: formElm.serialize(),
                        beforeSend: function(){
                            mi.cart_element.addClass('loading');
                        },
                        error: function(jqXHR, status, message){
                            mi.cart_element.removeClass('loading');
                            mi._logError(message);
                        },
                        success: function(res){
                            mi.cart_element.removeClass('loading');
                            mi._triggerEvent("cartSubmitted", [mi.cart]);
                            mi._clearCart();
                        }
                    }, this.options.submitSettings.ajaxSettings);

                    $.ajax(ajaxSettings);

                    break;
                case 'paypal':
                    formElm.children('.sc-paypal-input').remove();
                    // Add paypal specific fields for cart products
                    $.each(this.cart, function( i, p ) {
                        var itemNumber = i + 1;
                        formElm.append('<input class="sc-paypal-input" name="item_number_' + itemNumber + '" value="' + mi._getValueOrEmpty(p[mi.options.paramSettings.productId]) + '" type="hidden">')
                            .append('<input class="sc-paypal-input" name="item_name_' + itemNumber + '" value="' + mi._getValueOrEmpty(p[mi.options.paramSettings.productName]) + '" type="hidden">')
                            .append('<input class="sc-paypal-input" name="amount_' + itemNumber + '" value="' + mi._getValueOrEmpty(p[mi.options.paramSettings.productPrice]) + '" type="hidden">')
                            .append('<input class="sc-paypal-input" name="quantity_' + itemNumber + '" value="' + mi._getValueOrEmpty(p[mi.options.paramSettings.productQuantity]) + '" type="hidden">');
                    });

                    formElm.submit();
                    this._triggerEvent("cartSubmitted", [this.cart]);

                    break;
                default:
                    formElm.submit();
                    this._triggerEvent("cartSubmitted", [this.cart]);

                    break;
            }

            return true;
        },

// HELPER FUNCTIONS
        /*
         * Get the content of an HTML element irrespective of its type
         */
        _getContent: function (elm) {
            if(elm.is(":checkbox, :radio")){
                return elm.is(":checked") ? elm.val() : '';
            } else if (elm.is("[value], select")){
                return elm.val();
            } else if (elm.is("img")){
                return elm.attr('src');
            } else if (elm.is("[value], input")) {
                return elm.val();
            } else {
                // return elm.text();
                return elm.attr('data-value');
            }
            return '';
        },
        _getByKey: function (key) {
            var item = {};
            $.each(this.cart, function( i, p ) {
                if(p.unique_key === key){
                    item =  p;
                }
            });

            return item;
        },
        /*
         * Compare equality of two product objects
         */
        _isObjectsEqual: function (o1, o2) {
            for (var p in o1) {
                if(p === 'unique_key' || p === this.options.paramSettings.productQuantity) {
                    continue;
                }
                if (p === 'quantity') {
                    continue;
                }

                if (typeof o1[p] === typeof undefined && typeof o2[p] === typeof undefined) {
                    continue;
                }
                if (p === 'options') {
                    if (JSON.stringify(o1[p]) != JSON.stringify(o2[p])) {
                        return false;
                    }
                    continue;    
                }
                if (o1[p] !== o2[p]){
                    return false;
                }
            }
            return true;
        },
        /*
         * Format money
         */
        _getMoneyFormatted: function (n) {
            n = n - 0;
            // return Number(n.toFixed(0)).toLocaleString(this.options.currencySettings.locales, this.options.currencySettings.currencyOptions);
            return Number(n.toFixed(0));
        },
        /*
         * Get the value of an element and empty value if the element not exists
         */
        _getValueOrEmpty: function (v) {
            return (v && typeof v !== typeof undefined) ? v : '';
        },
        /*
         * Validate Number
         */
        _getValidateNumber: function (n) {
            n = n - 0;
            if(n && n > 0){
                return true;
            }
            return false;
        },
        /*
         * Small templating function
         */
        _formatTemplate: function (t, o){
            var r = t.split("{"), fs = '';
            for(var i=0; i < r.length; i++){
                var vr = r[i].substring(0, r[i].indexOf("}"));
                if(vr.length > 0){
                    fs += r[i].replace(vr + '}', this._getValueOrEmpty(o[vr]));
                }else{
                    fs += r[i];
                }
            }
            return fs;
        },
        /*
         * Event raiser
         */
        _triggerEvent: function (name, params) {
            // Trigger an event
            var e = $.Event(name);
            this.cart_element.trigger(e, params);
            if (e.isDefaultPrevented()) { return false; }
            return e.result;
        },
        /*
         * Get unique key
         */
        _getUniqueKey: function () {
            var d = new Date();
            return d.getTime();
        },
        /*
         * Log message to console
         */
        _logMessage: function (msg) {
            if(this.options.debug !== true) { return false; }
            // Log message
            console.log(msg);
        },
        /*
         * Log error to console and terminate execution
         */
        _logError: function (msg) {
            if(this.options.debug !== true) { return false; }
            // Log error
            $.error(msg);
        },

// PUBLIC FUNCTIONS
        /*
         * Public function to sumbit the cart
         */
        submit: function () {
            this._submitCart();
        },
        /*
         * Public function to clear the cart
         */
        clear: function () {
            this._clearCart();
        },

        reinit: function() {
            this.init();
        },

    });

    // Wrapper for the plugin
    $.fn.smartCart = function(options) {
        var args = arguments;
        var instance;

        if (options === undefined || typeof options === 'object') {
            return this.each( function() {
                if ( !$.data( this, "smartCart") ) {
                    $.data( this, "smartCart", new SmartCart( this, options ) );
                }
            });
        } else if (typeof options === 'string' && options[0] !== '_' && options !== 'init') {
            instance = $.data(this[0], 'smartCart');

            if (options === 'destroy') {
                $.data(this, 'smartCart', null);
            }

            if (instance instanceof SmartCart && typeof instance[options] === 'function') {
                return instance[options].apply( instance, Array.prototype.slice.call( args, 1 ) );
            } else {
                return this;
            }
        }
    };

})(jQuery, window, document);
