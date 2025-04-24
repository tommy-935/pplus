<div class="woofc-card">
	<h3 class="woofc-card__title"><?php echo esc_html( get_option( 'woofc_cart_cross_sells_products_text' ) ); ?></h3>

	<div class="woofc-card__body">

		<div class="woofc-related-products">

			<?php foreach ( $cross_sells_products as $cross_sells_product ) : ?>

			<div class="woofc-related-product">

				<?php if ( 'simple' === $cross_sells_product->get_type() ) : ?>
					<a
						href="<?php echo $cross_sells_product->add_to_cart_url() ?>"
						class="woofc-related-product__add-to-cart js-woofc-related-product-add add_to_cart_button ajax_add_to_cart"
						data-quantity="1"
						data-product_id="<?php echo $cross_sells_product->get_id(); ?>" >
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
							<path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
						</svg>
					</a>
				<?php endif; ?>

				<a href="<?php echo $cross_sells_product->get_permalink() ?>" class="woofc-related-product__link">
					<img src="<?php echo wp_get_attachment_image_url( $cross_sells_product->get_image_id(), 'medium' ); ?>" alt="//" class="woofc-related-product__image">

					<div class="woofc-related-product__name"><?php echo esc_html( $cross_sells_product->get_name() ); ?></div>
					<div class="woofc-related-product__price"><?php echo $cross_sells_product->get_price_html(); ?></div>
				</a>
			</div>

			<?php endforeach; ?>

		</div>

	</div>

</div>
