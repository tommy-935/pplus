<header class="entry-header">
<?php
	do_action( 'los_post_header_before' );
	if ( is_single() ) {
		the_title( '<h1 class="entry-title">', '</h1>' );
	} else {
		the_title( sprintf( '<h2 class="alpha entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' );
	}

	do_action( 'los_post_header_after' );
?>
</header>
