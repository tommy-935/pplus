/**
 * WooCommerce Flying Cart object.
 *
 * @package woofc
 *
 * @since 1.1.8
 */
 const woofcCart = {
	/**
	 * Check whether cart is open or closed.
	 *
	 * @since 1.1.8
	 */
	is_open() {
		return '1' === jQuery( '.woofc-cart' ).attr( 'data-woofc-cart-popup-stage' ) ? true : false;
	},

	/**
	 * Open cart window.
	 *
	 * @since 1.1.8
	 */
	open: function() {
		jQuery( '.woofc-cart' ).attr( 'data-woofc-cart-popup-stage', '1' );
		jQuery( '.woofc-cart' ).addClass( 'woofc-cart--open' );
		jQuery( '.woofc-overlay' ).addClass( 'woofc-overlay--open ' );
		jQuery( '.woofc-cart-trigger' ).hide();

		this.hide_trigger();

		/**
		 * Trigger when cart window open.
		 */
		jQuery( document.body ).trigger( 'woofc_cart_open' );
	},

	/**
	 * Close cart window.
	 *
	 * @since 1.1.8
	 */
	close: function() {
		jQuery( '.woofc-cart' ).attr( 'data-woofc-cart-popup-stage', '0' );
		jQuery( '.woofc-cart' ).removeClass( 'woofc-cart--open' );
		jQuery( '.woofc-overlay' ).removeClass( 'woofc-overlay--open ' );

		this.show_trigger();

		/**
		 * Trigger when cart window close.
		 */
		jQuery( document.body ).trigger( 'woofc_cart_close' );
	},

	/**
	 * Hide trigger icon.
	 *
	 * @since 1.1.8
	 */
	hide_trigger: function() {
		jQuery( '.woofc-cart-trigger' ).hide();
	},

	/**
	 * Show trigger icon.
	 *
	 * @since 1.1.8
	 */
	show_trigger: function() {
		jQuery( '.woofc-cart-trigger' ).show();

	},

	/**
	 * Animate trigger icon.
	 *
	 * @since 1.1.8
	 */
	animate_trigger() {
		var animatin_type = woofcObj.add_to_cart_animation;

		jQuery( '.woofc-cart-trigger' ).addClass( 'woofc-' + animatin_type );

		setTimeout( function() {
			jQuery( '.woofc-cart-trigger' ).removeClass( 'woofc-'+animatin_type+'' );
		}, 800 );
	},

	/**
	 * Cart loader.
	 *
	 * @since 1.3.0
	 */
	loader( state ) {
		var $loader = jQuery( '.woofc-cart .woofc-cart__loader' );

		if ( 'show' === state ) {
			$loader.addClass( 'woofc-cart__loader--active' );
		} else {
			$loader.removeClass( 'woofc-cart__loader--active' );
		}
	},

	show_notice( message, type = 'success' ) {
		var $notice = jQuery( '.woofc-cart .woofc-cart-notice' );

		$notice.html( message );

		if ( 'error' === type ) {
			$notice.addClass( 'woofc-cart-notice--error' );
		} else {
			$notice.addClass( 'woofc-cart-notice--success' );
		}

		$notice.addClass( 'woofc-cart-notice--show' );

		setTimeout( function() {
			woofcCart.hide_notice();
		}, 3000 )
	},

	hide_notice() {
		var $notice = jQuery( '.woofc-cart .woofc-cart-notice' );

		$notice
			.html( '' )
			.removeClass( 'woofc-cart-notice--error' )
			.removeClass( 'woofc-cart-notice--success' )
			.removeClass( 'woofc-cart-notice--show' );
	}
};

( function( $ ) {
	"use strict";

	var ajaxRequest = null;

	/**
	 * Animate cart.
	 */
	function animateCart() {
		jQuery( '.woofc-cart-trigger' ).addClass( 'woofc-'+woofcObj.add_to_cart_animation+'' );

		setTimeout( function() {
			jQuery( '.woofc-cart-trigger' ).removeClass( 'woofc-'+woofcObj.add_to_cart_animation+'' );
		}, 800 );
	}

	/**
	 * Add to cart function.
	 *
	 * @since 1.1.9
	 */
	function add_to_cart( atc_btn, form_data, form ) {

		// Trigger event.
		$( document.body ).trigger( 'adding_to_cart', [ atc_btn, form_data ] );

		$.ajax( {
			url: woofcObj.wc_ajax_url.toString().replace( '%%endpoint%%', 'woofc_add_to_cart' ),
			type: 'POST',
			data: $.param( form_data ),
			beforeSend: function() {
				// Block form.
				form.block( { message: null, overlayCSS: { background: '#fff', opacity: 0.6 } } );
			},
			complete: function() {
				// Unlock form.
				form.unblock();
			},
			success: function( response ) {
				if ( response.fragments ) {

					// Trigger event so themes can refresh other areas.
					$( document.body ).trigger( 'added_to_cart', [ response.fragments, response.cart_hash, atc_btn ] );
				}
			}
		} );
	}

	/**
	 * Handel product quantity increment button.
	 */
	jQuery( document ).on( 'click', '.woofc-product__qty-plus', function() {
		var spinner  = jQuery( this ).closest( '.woofc-product__qty' ),
			input    = spinner.find( 'input' ),
			max      = input.attr( 'max' ) || 999,
			oldValue = parseFloat( input.val() );

		if ( woofcObj.is_ajax_cart_loader ) {
			woofcCart.loader( 'show' );
		}

		if ( oldValue >= max ) {
			var newVal = oldValue;
		} else {
			var newVal = oldValue + 1;
		}

		input.val( newVal );
		input.trigger( "change" );
	} );

	/**
	 * Handel product quantity decrement button.
	 */
	jQuery( document ).on( 'click', '.woofc-product__qty-minus', function() {
		var spinner  = jQuery( this ).closest( '.woofc-product__qty' ),
			input    = spinner.find( 'input' ),
			min      = input.attr( 'min' ) || 0,
			oldValue = parseFloat( input.val() );

		if ( woofcObj.is_ajax_cart_loader ) {
			woofcCart.loader( 'show' );
		}

		if ( oldValue <= min ) {
			var newVal = oldValue;
		} else {
			var newVal = oldValue - 1;
		}

		input.val( newVal );
		input.trigger( "change" );
	} );

	/**
	 * Flying cart trigger.
	 */
	jQuery( document ).on( 'click', '[data-woofc-trigger]', function() {
		if ( woofcCart.is_open() ) {
			woofcCart.close();
		} else {
			woofcCart.open();
		}
	} );

	/**
	 * Single page add to cart popup.
	 *
	 * @since 1.1.9
	 */
	$( document ).on( 'submit', 'form.cart', function( e ){
		var form      = $( this );
		var atc_btn   = form.find( 'button[type="submit"]');
		var form_data = form.serializeArray();

		// if button as name add-to-cart get it and add to form
		if ( atc_btn.attr( 'name' ) && atc_btn.attr( 'name' ) === 'add-to-cart' && atc_btn.attr( 'value' ) ) {
			form_data.push( { name: 'add-to-cart', value: atc_btn.attr( 'value' ) } );
		}

		var is_valid = false;

		$.each( form_data, function( index, data ){
			if( data.name === 'add-to-cart' ){
				is_valid = true;
				return false;
			}
		} );

		if ( is_valid ) {
			e.preventDefault();
		} else {
			return;
		}

		form_data.push( { name: 'action', value: 'woofc_add_to_cart' } );

		add_to_cart( atc_btn, form_data, form ); // Ajax add to cart
	} );

	/**
	 * Remove product from flying cart.
	 */
	jQuery( document ).on( 'click', '[data-woofc-product-remove]', function() {
		var cartItemKey      = jQuery( this ).attr( 'data-woofc-product-remove' ),
			productContainer = jQuery( this ).closest( '.woofc-product' );

		productContainer.addClass( 'woofc-product--removed' );

		setTimeout( function() {
			productContainer.remove();
		}, 500 );

		if ( woofcObj.is_ajax_cart_loader ) {
			woofcCart.loader( 'show' );
		}

		jQuery.ajax({
			url: woofcObj.wc_ajax_url.toString().replace( '%%endpoint%%', 'woofc_remove_product' ),
			type: 'post',
			dataType: 'json',
			data: {
				'cart_item_key': cartItemKey,
			},
			success: function( response ) {
				if ( ! response ) {
					return;
				}

				// Trigger event so themes can refresh other areas
				jQuery( document.body ).trigger( 'added_to_cart', [ response.fragments, response.cart_hash ] );
			}
		} ).done(function() {
			woofcCart.loader( 'hide' );
		} );
	} );

	/**
	 * Update flying cart by updating product quantity.
	 */
	jQuery( document ).on( 'change', '.woofc-product__qty input', function() {
		var $this       = $( this );
		var cartItemKey = jQuery( this ).attr( 'name' );
		var quantity    = parseFloat( jQuery( this ).val() );
		var min         = parseFloat( $this.attr( 'min' ) );
		var max         = parseFloat( $this.attr( 'max' ) );

		if ( woofcObj.is_ajax_cart_loader ) {
			woofcCart.loader( 'show' );
		}

		if ( max && quantity >= max ) {
			quantity = max;
		}
		if ( min && quantity <= min ) {
			quantity = min;
		}

		ajaxRequest = jQuery.ajax({
			url: woofcObj.wc_ajax_url.toString().replace( '%%endpoint%%', 'woofc_update_product_quantity' ),
			type: 'post',
			dataType: 'json',
			data: {
				'cart_item_key': cartItemKey,
				'quantity': quantity,
			},
			beforeSend : function()    {
				if( null != ajaxRequest ) {
					ajaxRequest.abort();
				}
			},
			success: function( response ) {
				if ( ! response ) {
					return;
				}

				// Trigger event so themes can refresh other areas
				jQuery( document.body ).trigger( 'added_to_cart', [ response.fragments, response.cart_hash ] );
			}
		} ).done( function() {
			woofcCart.loader( 'hide' );
		} );
	} );

	/**
	 * Remove coupon from flying cart.
	 */
	jQuery( document ).on( 'click', '[data-woofc-coupon-code]', function( e ) {
		var couponCode = jQuery( this ).data( 'woofc-coupon-code' ),
			parentRow  = jQuery( this ).closest( '.cart-discount' );

		parentRow.addClass( 'cart-discount--removed' );

		setTimeout( function() {
			parentRow.remove();
		}, 500 );

		if ( woofcObj.is_ajax_cart_loader ) {
			woofcCart.loader( 'show' );
		}

		jQuery.ajax( {
			url: woofcObj.wc_ajax_url.toString().replace( '%%endpoint%%', 'woofc_remove_coupon' ),
			type: 'post',
			dataType: 'json',
			data: {
				'coupon_code': couponCode,
			},
			success: function( response ) {
				if ( ! response ) {
					return;
				}

				woofcCart.show_notice( woofcObj.coupon_removed_text );

				// Trigger event so themes can refresh other areas
				jQuery( document.body ).trigger( 'added_to_cart', [ response.fragments, response.cart_hash ] );
			}
		} ).done( function() {
			woofcCart.loader( 'hide' );
		} );
	} );

	/**
	 * Apply coupon.
	 */
	jQuery( document ).on( 'submit', '#woofc-coupon-form', function( e ) {
		e.preventDefault();

		var formData = jQuery( this ).serialize();

		woofcCart.loader( 'show' );

		jQuery.ajax({
			url: woofcObj.wc_ajax_url.toString().replace( '%%endpoint%%', 'woofc_apply_coupon' ),
			type: 'post',
			dataType: 'json',
			data: formData,
			success: function( response ) {
				if ( ! response ) {
					return;
				} else if ( response.coupon_error ) {
					woofcCart.show_notice( response.coupon_error, 'error' );
				} else {
					woofcCart.show_notice( woofcObj.coupon_appled_text );

					// Trigger event so themes can refresh other areas
					jQuery( document.body ).trigger( 'added_to_cart', [ response.fragments, response.cart_hash ] );
				}
			}
		} ).done( function() {
			woofcCart.loader( 'hide' );
		} );
	} );

	/**
	 * Handel shipping change.
	 */
	$( document ).on( 'change', '.woofc :input[name^=shipping_method]', function() {
		var selectedShipping = jQuery( this ).val();

		if ( woofcObj.is_ajax_cart_loader ) {
			woofcCart.loader( 'show' );
		}

		jQuery.ajax({
			url: woofcObj.wc_ajax_url.toString().replace( '%%endpoint%%', 'woofc_update_shipping_method' ),
			type: 'post',
			data: {
				shipping_method: selectedShipping
			},
			dataType: 'json',
			success: function( response ) {

				// Trigger event so themes can refresh other areas
				jQuery( document.body ).trigger( 'added_to_cart', [ response.fragments, response.cart_hash ] );

			}
		} ).done( function() {
			woofcCart.loader( 'hide' );
		} );
	} );

	/**
	 * Show and hide apply coupon form.
	 */
	jQuery( document ).on( 'click', '#woofc-coupon-trigger', function() {
		var couponBox = jQuery( this ).closest( '.woofc-coupon' );

		if ( couponBox.hasClass( 'woofc-coupon--open' ) ) {
			couponBox
				.addClass( 'woofc-coupon--close' )
				.removeClass( 'woofc-coupon--open' );
		} else {
			couponBox
				.addClass( 'woofc-coupon--open' )
				.removeClass( 'woofc-coupon--close' );
		}
	} );

	/**
	 * Run with WooCommerce add to cart event.
	 */
	jQuery( document ).on( 'added_to_cart', function() {
		animateCart();

		// Open flying cart after product add to cart.
		if ( 'yes' === woofcObj.open_after_add_to_cart ) {
			if ( ! woofcCart.is_open() ) {
				woofcCart.open();
			}
		}

		// Hide cart when cart empty.
		if ( woofcObj.hide_cart_when_empty ) {
			var isCartEmpty = 0 === jQuery( '.woofc .woofc-products' ).length ? true : false;

			if ( isCartEmpty ) {
				$( '.woofc' ).addClass( 'woofc--hide' );
				woofcCart.close();
			} else {
				$( '.woofc' ).removeClass( 'woofc--hide' );
			}
		}

		woofcCart.loader( 'hide' );
	} );

	/**
	 * Run with WooCommerce update cart event.
	 */
	jQuery( document ).on( 'updated_cart_totals', function() {
		animateCart();
	} );

	/**
	 * Add related products to cart.
	 *
	 * @since 1.5.1
	 */
	$( document ).on( 'click', '.js-woofc-related-product-add', function() {
		if ( woofcObj.is_ajax_cart_loader ) {
			woofcCart.loader( 'show' );
		}
	} );

	/**
	 * Apply available coupons.
	 *
	 * @since 1.6.0
	 */
	$( document ).on( 'click', '.js-woofc-apply-available-coupon', function( e ) {
		e.preventDefault();

		var couponCode = $( this ).attr( 'data-woofc-available-coupon' );

		if ( ! couponCode ) {
			return;
		}

		$( '.woofc form#woofc-coupon-form [name="coupon_code"]' ).val( couponCode ).trigger( 'change' );
		$( '.woofc form#woofc-coupon-form' ).trigger( 'submit' );
	} );

} )( jQuery );
