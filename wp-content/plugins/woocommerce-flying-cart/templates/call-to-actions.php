<div class="woofc-cart-cta">

	<?php if ( $cart_button ) : ?>

		<a href="<?php echo get_the_permalink( $cart_button ); ?>" class="woofc-cart-cart woofc-primary-bg-color woofc-primary-text-color">
			<?php echo esc_html( get_option( 'woofc_cart_button_text' ) ); ?>
		</a>

	<?php endif; ?>

	<?php if ( $checkout_button ) : ?>

		<a href="<?php echo get_the_permalink( $checkout_button ); ?>" class="woofc-cart-checkout woofc-primary-bg-color woofc-primary-text-color">
			<?php echo esc_html( get_option( 'woofc_checkout_button_text' ) ); ?>

			<?php if ( 'yes' !== get_option( 'woofc_cart_hide_total_price' ) ) : ?>
				<span class="woofc-checkout-and-total-price-separator"><?php echo apply_filters( 'woofc_checkout_and_total_price_separator', ':' ); ?></span> <span id="woofc-cart-total"><?php echo woofc_get_cart_total(); ?></span>
			<?php endif; ?>

		</a>

	<?php endif; ?>

</div>
