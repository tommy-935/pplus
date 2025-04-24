<div class="entry-content">
<?php
	do_action( 'los_post_content_before' );
	the_content(
		sprintf(
			__( 'Continue reading %s', 'lymos-theme' ),
				'<span class="screen-reader-text">' . get_the_title() . '</span>'
		)
	);
	do_action( 'los_post_content_after' );
	wp_link_pages(
		array(
			'before' => '<div class="page-links">' . __( 'Pages:', 'lymos-theme' ),
			'after'  => '</div>',
		)
	);
?>
</div>