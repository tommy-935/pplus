<div class="woofc-card">
	<div class="woofc-card__body">

		<div class="woofc-products">

			<?php foreach ( $products as $cart_item_key => $cart_item ) {
					$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
					$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

					if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
						$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
						?>
						<div class="woofc-product">

							<div class="woofc-product__img-wrapper">

								<div class="woofc-product__close" data-woofc-product-remove="<?php echo esc_html( $cart_item_key ); ?>">
									<img class="woofc-product__remove"
										src="<?php echo esc_url( WOOFC_PLUGIN_URL . 'assets/images/close.svg' ) ?>"
										alt="//"
											width="20">
								</div>

								<div class="woofc-product__img">
								<?php
									if ( 'yes' !== get_option( 'woofc_cart_hide_product_images' ) ) {

										$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

										if ( 'yes' === get_option( 'woofc_cart_remove_product_links' ) || ! $product_permalink ) {
											echo $thumbnail; // PHPCS: XSS ok.
										} else {
											printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // PHPCS: XSS ok.
										}
									}
								?>
								</div>

							</div>
							<div class="woofc-product__meta">
								<div class="woofc-product__title">
								<?php
								if ( 'yes' === get_option( 'woofc_cart_remove_product_links' ) || ! $product_permalink ) {
									echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;' );
								} else {
									echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
								}

								do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );

								// Meta data.
								echo wc_get_formatted_cart_item_data( $cart_item ); // PHPCS: XSS ok.

								// Backorder notification.
								if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
									echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woo-flying-cart' ) . '</p>', $product_id ) );
								}
								?>
								</div>

								<div class="woofc-product__qty">
									<?php
									if ( $_product->is_sold_individually() ) {
										$product_quantity = sprintf( '<input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );

										echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									} else {
										echo '<div class="woofc-product__qty-minus">-</div>';
										$product_quantity = woofc_woocommerce_quantity_input(
											array(
												'input_name'   => $cart_item_key,
												'input_value'  => $cart_item['quantity'],
												'max_value'    => $_product->get_max_purchase_quantity(),
												'min_value'    => '1',
												'product_name' => $_product->get_name(),
												'class',
											),
											$_product,
											false
										);

										echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

										echo '<div class="woofc-product__qty-plus">+</div>';
									}
									?>

								</div>

							</div>
							<div class="woofc-product__subtotal">
								<?php
									echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
								?>
							</div>
						</div>
						<?php
					}
				}
			?>

		</div>

	</div>
</div>
