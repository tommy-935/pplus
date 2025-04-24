<?php
do_action( 'los_loop_before' );
while ( have_posts() ) :
	the_post();
	get_template_part( 'templates/content', get_post_format() );

endwhile;
do_action( 'los_loop_after' );