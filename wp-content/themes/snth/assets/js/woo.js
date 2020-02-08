/**
 * Functions from One Page Shop
 */
(function($) {
    $(document).ready(function() {
        var xhr,
            checkoutContent = $( '#checkout-content' );

        removeUnusedmarkup();

        /**
         * Click on "Add to Cart" icon, displayed on product list
         */
        $(document).on('click', '.add-to-cart', function (event) {
            var id = $(this).attr('data-id');

            var ajax_data = {
                action: 'add-to-cart',
                id: id
            };
            ajax_data[SnthObj.nonce_post_id] = SnthObj.nonce;

            ajax_request($(event.target).closest('.shop-item'), ajax_data, true, false, true);
        });

        /**
         * Click on remove product from cart icon, on product page
         */
        $(document).on( 'click', '.product-remove > a', function(event) {
            var parts = this.search.split( 'remove_item=' );

            if ( parts[1] !== undefined ) {
                parts = parts[1].split( '&' );
                if ( parts[0] !== undefined ) {

                    var ajax_data = {
                        action: 'remove-from-cart',
                        cart_item: parts[0]
                    };
                    ajax_data[SnthObj.nonce_post_id] = SnthObj.nonce;

                    ajax_request($(event.target).closest('form'), ajax_data, true, false, true);

                    return false;
                }
            }
        });

        /**
         * Click on "Update Cart" button
         */
        $(document).on('click', 'input[name=update_cart]', function(event) {
            var match,
                pattern = /\[(\w*)\]\[(\w*)\]/;

            var ajax_data = {
                action: 'update-cart',
                cart: new Object()
            };
            ajax_data[SnthObj.nonce_post_id] = SnthObj.nonce;

            $( this ).parents( 'form' ).find( 'input[name^=cart]' ).each( function() {
                match = $( this ).attr( 'name' ).match( pattern );

                if ( ajax_data.cart[match[1]] === undefined ) {
                    ajax_data.cart[match[1]] = new Object();
                }
                ajax_data.cart[match[1]][match[2]] = $( this ).val();
            });

            ajax_request( $(event.target), ajax_data, true, false, true );

            return false;
        });

        /**
         * Function is different than original by possibility to work under and over WC version 2.1
         */
        function update_checkout() {
            if ( xhr ) {
                xhr.abort();
            }

            var method = [],
                shipping_methods = [];

            var security,
                methods,
                optart_url;

            $('select#shipping_method, input[name^=shipping_method][type=radio]:checked, input[name^=shipping_method][type=hidden]')
                .each(function(index, input) {
                    shipping_methods[$(this).data('index')] = $(this).val();
                });

            var payment_method 	= $('#order_review input[name=payment_method]:checked').val();
            var country 		= $('#billing_country').val();
            var state 			= $('#billing_state').val();
            var postcode 		= $('input#billing_postcode').val();
            var city	 		= $('input#billing_city').val();
            var address	 		= $('input#billing_address_1').val();
            var address_2	 	= $('input#billing_address_2').val();

            var s_country,
                s_state,
                s_postcode,
                s_city,
                s_address,
                s_address_2;

            if ( $('#ship-to-different-address input').is(':checked') || $('#ship-to-different-address input').size() == 0 ) {
                s_country 	= $('#shipping_country').val();
                s_state 	= $('#shipping_state').val();
                s_postcode 	= $('input#shipping_postcode').val();
                s_city 		= $('input#shipping_city').val();
                s_address 	= $('input#shipping_address_1').val();
                s_address_2	= $('input#shipping_address_2').val();
            } else {
                s_country 	= country;
                s_state 	= state;
                s_postcode 	= postcode;
                s_city 		= city;
                s_address 	= address;
                s_address_2	= address_2;
            }
            security = SnthObj.update_order_review_nonce;
            methods = shipping_methods;

            var data = {
                action: 			'woocommerce_update_order_review',
                security: 			security,
                shipping_method: 	methods,
                payment_method:		payment_method,
                country: 			country,
                state: 				state,
                postcode: 			postcode,
                city:				city,
                address:			address,
                address_2:			address_2,
                s_country: 			s_country,
                s_state: 			s_state,
                s_postcode: 		s_postcode,
                s_city:				s_city,
                s_address:			s_address,
                s_address_2:		s_address_2,
                post_data:			$('form.checkout').serialize()
            };

            xhr = $.ajax({
                type: 'POST',
                url: SnthObj.wc_ajaxurl,
                data: data,
                success: function(response) {

                    // Always update the fragments
                    if (response && response.fragments) {
                        $.each(response.fragments, function (key, value) {
                            $(key).replaceWith(value);
                            $(key).unblock();
                        } );
                    }
                    else {
                        var markup = response.html === undefined ? response : response.html;
                        $('#order_review').html(markup);
                    }
                }
            });
        }

        var createAccount = $( 'div.create-account' );
        createAccount.hide();

        $( 'input#createaccount' ).change( function() {
            createAccount.hide();

            if ( $( this ).is( ':checked' ) ) {
                createAccount.slideDown();
            }
        }).change();

        // Event for updating the checkout
        $('body').bind('update_checkout', function() {
            update_checkout();
        });

        // Inputs/selects which update totals instantly
        $(document).on('input change',
            'select#shipping_method, input[name^=shipping_method], input[name=shipping_method], #shiptobilling input, .update_totals_on_change select, #ship-to-different-address input',
            function() {
                $('body').trigger('update_checkout');
            }
        );

        $(document).on('submit', 'form.checkout', function() {
            var $form = $( this );

            if ( $form.is( '.processing' ) ) {
                return false;
            }

            if ($form.triggerHandler( 'checkout_place_order') !== false && $form.triggerHandler( 'checkout_place_order_' + $( '#order_review input[name=payment_method]:checked' ).val() ) !== false ) {
                $('#cartModal').block({
                    message: null,
                    overlayCSS: {
                        background: '#fff',
                        opacity: 0.6
                    }
                });

                $form.addClass( 'processing' );

                var form_data = $form.data();

                $.ajax({
                    type:		'POST',
                    url:		SnthObj.wc_ajaxurl + '?action=woocommerce_checkout',
                    data:		$form.serialize(),
                    success:	function(code) {
                        var result = '';
                        try {
                            // Get the valid JSON only from the returned string
                            if (code.indexOf( '<!--WC_START-->' ) >= 0 )
                                code = code.split( '<!--WC_START-->' )[1]; // Strip off before after WC_START

                            if ( code.indexOf( '<!--WC_END-->' ) >= 0 )
                                code = code.split( '<!--WC_END-->' )[0]; // Strip off anything after WC_END

                            // Parse
                            result = $.parseJSON(code);

                            if ( result.result === 'success' ) {
                                $.ajax({
                                    type:		'POST',
                                    url:		result.redirect,
                                    success:	function(page) {
                                        $('#cart-header').remove();
                                        $('#cart-content').remove();
                                        var new_checkout_content = $('#checkout-content', page).html();
                                        $('#checkout-content').html(new_checkout_content);
                                        $('#cartModal').unblock();
                                    },
                                    dataType: 'html'
                                });

                                //window.location = decodeURI( result.redirect );
                            } else if ( result.result === 'failure' ) {
                                $('#cartModal').unblock();
                                throw 'Result failure';
                            } else {
                                $('#cartModal').unblock();
                                throw 'Invalid response';
                            }
                        }
                        catch( err ) {
                            if (result.reload === 'true') {
                                window.location.reload();
                                return;
                            }

                            // Remove old errors
                            $( '.woocommerce-error, .woocommerce-message' ).remove();

                            // Add new errors
                            if ( result.messages ) {
                                $form.prepend( result.messages );
                            } else {
                                $form.prepend(code);
                            }

                            // Cancel processing
                            $form.removeClass('processing').unblock();

                            // Lose focus for all fields
                            $form.find('.input-text, select').blur();

                            // Scroll to top
                            $( 'html, body' ).animate({
                                scrollTop: ( $( 'form.checkout' ).offset().top - 100 )
                            }, 1000 );

                            // Trigger update in case we need a fresh nonce
                            if ( result.refresh === 'true' )
                                $('body').trigger('update_checkout');

                            $('body').trigger('checkout_error');
                        }
                    },
                    dataType: 'html'
                });
            }

            return false;
        });

        /**
         * Function makes an AJAX request and puts the response into cart container
         * @param $element
         * @param ajax_data
         * @param update_cart
         * @param scroll_to
         * @param update_checkout
         */
        function ajax_request($element, ajax_data, update_cart, scroll_to, update_checkout) {
            $element.block({
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            });

            $.ajax({
                type: 'post',
                url: SnthObj.ajaxurl,
                data: ajax_data,
                dataType: 'json',

                success: function(response) {
                    $element.unblock();

                    if (update_cart) {
                        $('#cart-content').html(response.cart);
                        $('.menu-item-woo-cart').html(response.miniCart);
                        if (!response.isEmpty) {
                            $('.menu-item-woo-cart a').attr('data-open', 'cartModal');
                            $('#cartModal').foundation('open');
                        } else {
                            $('#cartModal').foundation('close');
                        }
                    }

                    // update checkout container if needed
                    if (update_checkout === true) {
                        var order_review = $( '#order_review');

                        if (order_review.length && order_review.is(':visible')) {
                            $('body').trigger('update_checkout');
                        } else {

                            // Used since WooCommerce 2.1;
                            // get the current value of "Ship to billing address" checkbox
                            var ship_to_different_val = $( '#ship-to-different-address-checkbox' ).length ?
                                $( '#ship-to-different-address-checkbox' ).is(':checked') :
                                SnthObj.ship_to_different_def !== '0';

                            $( '#checkout-content' ).show();
                            var checkout_ajax_data = {
                                action: 'update-checkout'
                            };
                            checkout_ajax_data[SnthObj.nonce_post_id] = SnthObj.nonce;

                            $.ajax({
                                type: 'post',
                                url: SnthObj.ajaxurl,
                                data: checkout_ajax_data,
                                dataType: 'json',
                                success: function(checkout_response) {
                                    $('#checkout-content').html(checkout_response.checkout);

                                    if (!checkout_response.isEmpty) {
                                        $('.menu-item-woo-cart a').attr('data-open', 'cartModal');
                                        $('#cartModal').foundation('open');
                                    }
                                }
                            });
                        }
                    }

                    removeUnusedmarkup();
                }
            });
        }
    });

    function removeUnusedmarkup() {
        var cartCollaterals = $('#cart-content .cart-collaterals');

        if (cartCollaterals.length !== 0) {
            cartCollaterals.remove();
        }
    }
})(jQuery);