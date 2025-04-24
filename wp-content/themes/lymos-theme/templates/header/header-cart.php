<?php
$class = '';
if ( is_cart() ) {
	$class = 'current-menu-item';
}
?>
<ul id="site-header-cart" class="site-header-cart menu">
	<li class="min-cart-li <?php echo esc_attr( $class ); ?>">
		<a class="cart-contents" href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php esc_attr_e( 'View your shopping cart', 'lymos-theme' ); ?>">
		<?php echo wp_kses_post( WC()->cart->get_cart_subtotal() ); ?> <span class="count"><?php echo wp_kses_data( sprintf( _n( '%d item', '%d items', WC()->cart->get_cart_contents_count(), 'lymos-theme' ), WC()->cart->get_cart_contents_count() ) ); ?></span>
		</a>
		<span class="min-cart-icon">
		</span>
	</li>
	<li id="min-cart-content" class="min-cart-content">
		<?php the_widget( 'WC_Widget_Cart', 'title=' ); ?>
	</li>
	
</ul>