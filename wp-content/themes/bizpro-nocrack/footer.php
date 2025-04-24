<?php 

	$custom_footer = Xtra_Theme::option( 'footer_elementor' );

	// Footer
	if ( is_404() ) {

		$_404 = get_page_by_title( '404' );
		if ( ! empty( $_404->ID ) ) {
			$footer = $_404;
		} else {
			$_404 = get_page_by_path( 'page-404' );
			if ( ! empty( $_404->ID ) ) {
				$footer = $_404;
			}
		}

		$footer = isset( $footer->ID ) ? !Xtra_Theme::meta( $footer->ID, 'hide_footer' ) : 1;

	} else if ( is_single() || is_page() ) {

		$footer = !Xtra_Theme::meta( false, 'hide_footer' );
		$custom_footer = Xtra_Theme::meta( false, 'custom_footer', $custom_footer );

	} else {

		$footer = 1;

	}

	// Footer.
	do_action( 'xtra/before_footer' );

	if ( $custom_footer ) {

		echo '<footer class="page_footer' . esc_attr( Xtra_Theme::option( 'fixed_footer' ) ? ' cz_fixed_footer' : '' ) . '" role="contentinfo">';

			echo '<div class="row clr">';

				Xtra_Theme::get_page_as_element( $custom_footer );

			echo '</div>';

		echo '</footer>';

	} else if ( $footer ) {

		echo '<footer class="page_footer' . esc_attr( Xtra_Theme::option( 'fixed_footer' ) ? ' cz_fixed_footer' : '' ) . '" role="contentinfo">';

		// Focus to section.
		if ( Xtra_Theme::$preview ) {
			echo '<i class="xtra-section-focus fas fa-cog" data-section="footer_widgets" aria-hidden="true"></i>';
		}

		// Row before footer
		Xtra_Theme::row([
			'id'		=> 'footer_',
			'nums'		=> [ '1' ],
			'row'		=> 1,
			'left'		=> '_left',
			'right'		=> '_right',
			'center'	=> '_center'
		]);

		// Footer widgets
		$footer_layout = Xtra_Theme::option( 'footer_layout' );
		if ( $footer_layout ) {
			$layout = explode( ',', $footer_layout );
			$count = count( $layout );
			$is_widget = 0;

			foreach ( $layout as $num => $col ) {
				$num++;
				if ( is_active_sidebar( 'footer-' . $num ) ) {
					$is_widget = 1;
				}
			}

			foreach ( $layout as $num => $col ) {

				$num++;

				if ( ! $is_widget ) {
					break;
				}

				if ( $num === 1 ) {
					echo '<div class="cz_middle_footer"><div class="row clr">';
				}

				if ( is_active_sidebar( 'footer-' . $num ) ) {
					echo '<div class="col ' . esc_attr( $col ) . ' sidebar_footer-' . esc_attr( $num ) . ' clr">';
					dynamic_sidebar( 'footer-' . $num );  
					echo '</div>';
				} else {
					echo '<div class="col ' . esc_attr( $col ) . ' sidebar_footer-' . esc_attr( $num ) . ' clr">&nbsp;</div>';
				}

				if ( $num === $count ) {
					echo '</div></div>';
				}

			}

		}

		// Row after footer
		Xtra_Theme::row([
			'id'		=> 'footer_',
			'nums'		=> [ '2' ],
			'row'		=> 1,
			'left'		=> '_left',
			'right'		=> '_right',
			'center'	=> '_center'
		]);

		echo '</footer>';
	}

	do_action( 'xtra/after_footer' );

	echo '</div></div>'; // layout
?>

		<?php wp_footer(); ?>

	</body>
	
</html>