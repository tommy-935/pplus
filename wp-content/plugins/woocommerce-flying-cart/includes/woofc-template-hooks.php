<?php

/**
 * Cart body.
 */
add_action( 'woofc_cart_body', 'woofc_the_cart_products_loop' );
add_action( 'woofc_cart_body', 'woofc_the_cart_related_products', 15 );
add_action( 'woofc_cart_body', 'woofc_the_cart_cross_sells_products', 16 );
add_action( 'woofc_cart_body', 'woofc_the_cart_coupon_form', 20 );
add_action( 'woofc_cart_body', 'woofc_the_cart_review', 30 );
add_action( 'woofc_cart_body', 'woofc_the_empty_cart_message', 80 );

/**
 * Cart footer.
 */
add_action( 'woofc_cart_footer', 'woofc_the_cart_footer_call_to_actions' );
