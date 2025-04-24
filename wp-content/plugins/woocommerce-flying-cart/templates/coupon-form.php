<div class="woofc-card">
	<div class="woofc-card__body">

		<div class="woofc-coupon woofc-coupon--close">

			<a href="javascript:;" id="woofc-coupon-trigger" class="woofc-coupon__trigger">
				<?php esc_html_e( 'Have a coupon?', 'woo-flying-cart' ); ?>

				<svg class="woofc-coupon__trigger-minus" width="14" height="1" viewBox="0 0 14 1" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
					<path d="M13.5251 0.0251159C13.7874 0.025116 14 0.237724 14 0.49999C14 0.762256 13.7874 0.974864 13.5251 0.974864L1.1784 0.974864C0.916131 0.974864 0.703522 0.762255 0.703522 0.49999C0.703522 0.237724 0.91613 0.0251154 1.1784 0.0251154L13.5251 0.0251159Z" fill="#999999"/>
				</svg>

				<svg class="woofc-coupon__trigger-plus" width="14" height="15" viewBox="0 0 14 15" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
					<path fill-rule="evenodd" clip-rule="evenodd" d="M13.2965 7.47487C13.2965 7.21261 13.0839 7 12.8216 7L7.12315 7L7.12315 1.30154C7.12315 1.03927 6.91054 0.82666 6.64827 0.82666C6.38601 0.82666 6.1734 1.03927 6.1734 1.30154L6.1734 7L0.474904 7C0.212638 7 2.96572e-05 7.21261 2.96458e-05 7.47487C2.96343e-05 7.73714 0.212639 7.94975 0.474904 7.94975L6.1734 7.94975L6.1734 13.6483C6.1734 13.9105 6.38601 14.1231 6.64827 14.1231C6.91054 14.1231 7.12315 13.9105 7.12315 13.6483L7.12315 7.94975L12.8216 7.94975C13.0839 7.94975 13.2965 7.73714 13.2965 7.47487Z" fill="#999999"/>
				</svg>

			</a>

			<div class="woofc-coupon-box">

				<form id="woofc-coupon-form" class="woofc-coupon__form" action="#" method="post">

					<p><?php esc_html_e( 'If you have a coupon code, please apply it below.', 'woo-flying-cart' ); ?></p>

					<div class="woofc-coupon__input-wrapper">
						<input type="text" class="woofc-coupon__code" name="coupon_code" placeholder="<?php esc_attr_e( 'Coupon code', 'woo-flying-cart' ); ?>" required>
						<input type="submit" class="woofc-coupon__submit" value="<?php esc_attr_e( 'Apply coupon', 'woo-flying-cart' ); ?>">
					</div>

				</form>

				<?php if ( $coupons && 'yes' === get_option( 'woofc_cart_coupons_status' ) ) : ?>
				<!-- Coupons -->
				<div class="woofc-coupons-list">

					<?php
						foreach ( $coupons as $coupon ) :
							if( WC()->cart->has_discount( $coupon->get_code() ) ) {
								continue;
							}
							if ( ! woofc_is_coupon_valid( $coupon->get_code() ) ) {
								continue;
							}

							$expiry_date = false;

							if ( $coupon->get_date_expires() ) {
								$expiry_date = $coupon->get_date_expires();
								$expiry_date = $expiry_date->date( 'M d, Y' );
							}

						?>
						<div class="woofc-coupons-list__item">
							<div class="woofc-coupons-list__item-code"><?php echo esc_html( strtoupper( $coupon->get_code() ) ); ?></div>
							<div class="woofc-coupons-list__item-description"><?php echo wp_kses_post( $coupon->get_description() ); ?></div>

							<?php if ( $expiry_date && 'yes' === get_option( 'woofc_cart_coupons_expiry_date_status' ) ) : ?>
							<div class="woofc-coupons-list__item-valid">
								<span><?php echo esc_html( str_replace( '{{expiry_date}}', $expiry_date, get_option( 'woofc_cart_coupons_expiry_date_text' ) ) ); ?></span>
							</div>
							<?php endif; ?>

							<a href="javascript:;" class="js-woofc-apply-available-coupon woofc-coupons-list__item-apply" data-woofc-available-coupon="<?php echo esc_html( strtoupper( $coupon->get_code() ) ); ?>">
								<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
									<path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
								</svg>
							</a>
							<svg width="69" height="49" viewBox="0 0 69 49" fill="none" xmlns="http://www.w3.org/2000/svg" class="woofc-coupons-list__item-graphic">
								<path d="M65.0448 0.784668L3.84482 0.784668C2.94309 0.784668 2.07828 1.14589 1.44066 1.78887C0.803037 2.43186 0.444824 3.30393 0.444824 4.21324L0.444824 17.9275H3.48102C6.86742 17.9275 10.009 20.2624 10.553 23.6327C10.7187 24.6168 10.6695 25.6255 10.4087 26.5885C10.148 27.5515 9.68197 28.4454 9.04323 29.208C8.4045 29.9707 7.60843 30.5835 6.71058 31.0039C5.81272 31.4242 4.83472 31.6419 3.84482 31.6418H0.444824L0.444824 45.3561C0.444824 46.2654 0.803037 47.1375 1.44066 47.7805C2.07828 48.4234 2.94309 48.7847 3.84482 48.7847L65.0448 48.7847C65.9466 48.7847 66.8114 48.4234 67.449 47.7805C68.0866 47.1375 68.4448 46.2654 68.4448 45.3561V31.6418H65.0448C64.0549 31.6419 63.0769 31.4242 62.1791 31.0039C61.2812 30.5835 60.4852 29.9707 59.8464 29.208C59.2077 28.4454 58.7417 27.5515 58.4809 26.5885C58.2201 25.6255 58.1709 24.6168 58.3366 23.6327C58.8806 20.2624 62.0222 17.9275 65.4086 17.9275H68.4448V4.21324C68.4448 3.30393 68.0866 2.43186 67.449 1.78887C66.8114 1.14589 65.9466 0.784668 65.0448 0.784668ZM31.0448 41.9275H24.2448V35.0704H31.0448V41.9275ZM31.0448 28.2132H24.2448V21.3561H31.0448V28.2132ZM31.0448 14.499H24.2448V7.64181L31.0448 7.64181V14.499Z" fill="currentColor" fill-opacity="1"/>
							</svg>
						</div>
					<?php endforeach; ?>

				</div>
				<!-- .Coupons -->
				<?php endif; ?>

			</div>

		</div>

	</div>
</div>
