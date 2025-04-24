<div class="wrap">
	<h1><?php esc_html_e( 'WooCommerce Flying Cart', 'woo-flying-cart' ); ?></h1>
	<?php settings_errors(); ?>
	<?php do_action( 'woofc_admin_notifications' ); ?>
	<hr>

	<h2 class="nav-tab-wrapper">

		<a href="?page=woocommerce-flying-cart" class="nav-tab <?php echo ( ! $tab ) ? 'nav-tab-active' : ''; ?>">
			<?php esc_html_e( 'Appearance', 'woo-flying-cart' ); ?>
		</a>
		<a href="?page=woocommerce-flying-cart&tab=basic_settings" class="nav-tab <?php echo ( 'basic_settings' === $tab ) ? 'nav-tab-active' : ''; ?>">
			<?php esc_html_e( 'Basic Settings', 'woo-flying-cart' ); ?>
		</a>
		<a href="?page=woocommerce-flying-cart&tab=flying_cart_settings" class="nav-tab <?php echo ( 'flying_cart_settings' === $tab ) ? 'nav-tab-active' : ''; ?>">
			<?php esc_html_e( 'Flying Cart Settings', 'woo-flying-cart' ); ?>
		</a>
	</h2>

	<form id="woofc-admin-form" action="options.php" method="post">
	<?php
		if ( ! $tab && 'woocommerce-flying-cart' === $_GET['page'] ) {
			WOOFC_Settings::do_settings( 'woofc_appearance_settings' );
			submit_button();
		}
		if ( 'basic_settings' === $tab && 'woocommerce-flying-cart' === $_GET['page'] ) {
			WOOFC_Settings::do_settings( 'woofc_basic_settings' );
			submit_button();
		}
		if ( 'flying_cart_settings' === $tab && 'woocommerce-flying-cart' === $_GET['page'] ) {
			WOOFC_Settings::do_settings( 'woofc_flying_cart_settings' );
			submit_button();
		}
		if ( 'developer' === $tab && 'woocommerce-flying-cart' === $_GET['page'] ) {
			WOOFC_Settings::do_settings( 'woofc_developer_settings' );
			submit_button();
		}
	?>
	</form>

</div>
