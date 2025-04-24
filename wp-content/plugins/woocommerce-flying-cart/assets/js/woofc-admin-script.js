( function( $ ) {
	"use strict";

	// Init select2
	jQuery('.woofc-select2').select2();

	// Init WordPress color picker
	jQuery( '.woofc-colorpicker' ).wpColorPicker();

	// Cart icon image selector
	jQuery( '[data-woofc-cart-icon-url]' ).on( 'click', function() {
		var cartIconURL = jQuery( this ).attr( 'data-woofc-cart-icon-url' );

		jQuery( '.woofc-admin-cart-icons > div' ).removeClass( 'woofc-admin-cart-icon--select' );
		jQuery( this ).addClass( 'woofc-admin-cart-icon--select' );

		jQuery( '#woofc_cart_icon' ).val( cartIconURL );
	} );

	jQuery( document ).on( 'click', '.woofc-hide-follow-us', function() {
		jQuery.ajax({
			url: woofcAdminObj.ajax_url,
			type: 'post',
			data: {
				'action':   'woofc_admin_noice_hide_follow_us',
			},
		});
	} );

} )( jQuery );
