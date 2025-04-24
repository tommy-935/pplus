<div class="woofc-overlay" data-woofc-trigger></div>

<!-- .woofc -->
<div class="woofc <?php echo implode( ' ', apply_filters( 'woofc_classes', array() ) ); ?>">

	<!-- woofc-cart -->
	<div class="woofc-cart" data-woofc-cart-popup-stage="0">

		<!-- woofc-header -->
		<div class="woofc-cart__header">
			<div class="woofc-cart__header-title"><?php echo esc_html( get_option( 'woofc_cart_title_text' ) ); ?></div>
			<div class="woofc-cart__header-close" data-woofc-trigger>
				<img src="<?php echo esc_url( WOOFC_PLUGIN_URL . 'assets/images/close.svg' ); ?>" alt="//" width="20">
			</div>
		</div><!-- .woofc-header -->

		<!-- woofc-body -->
		<div class="woofc-cart__body" id="woofc-cart-body">

			<?php do_action( 'woocommerce_before_mini_cart' ); ?>

			<?php
			/**
			 * Functions hooked into woofc_cart_body action.
			 *
			 * @hooked woofc_the_cart_products_loop  - 10
			 * @hooked woofc_the_cart_coupon_form    - 20
			 * @hooked woofc_the_cart_review_html    - 30
			 * @hooked woofc_the_empty_cart_message  - 80
			 *
			 */
			do_action( 'woofc_cart_body' ); ?>

			<?php do_action( 'woocommerce_after_mini_cart' ); ?>

		</div><!-- .woofc-body -->

		<!-- woofc-footer -->
		<div class="woofc-cart__footer">

			<?php
			/**
			 * Functions hooked into woofc_cart_footer action.
			 *
			 * @hooked woofc_cart_footer_call_to_actions  - 10
			 */
			do_action( 'woofc_cart_footer' ); ?>

		</div><!-- .woofc-footer -->

		<!-- WOOFC cart loader -->
		<div class="woofc-cart__loader">
			<div class="woofc-spinner"></div>
		</div><!-- .WOOFC cart loader -->

		<!-- WOOFC cart notice -->
		<div class="woofc-cart-notice"></div><!-- .WOOFC cart notice -->

	</div><!-- .woofc-cart -->

	<div class="woofc-cart-trigger" data-woofc-trigger>
		<div class="woofc-cart-trigger__counter">
			<span id="woofc-cart-trigger-counter"><?php echo $this->get_products_count(); ?></span>
		</div>
		<img src="<?php echo esc_url( get_option( 'woofc_cart_icon' ) ); ?>" alt="//" width="26">
	</div>

</div><!-- .woofc -->
