<?php
get_header(); 
?>
<div id="content" class="site-content" tabindex="-1">
	<div class="site-inner">
		<div id="primary" class="content-area">
			<main id="main" class="site-main" role="main">
			<div class="los-page-left">
				<?php
					// Add the default sidebar
					get_sidebar();
				?>
			</div>
			<div class="los-page-right">
			<?php
			
				while ( have_posts() ) :
					the_post();
					do_action( 'los_page_before' );
					get_template_part( 'templates/content', 'page' );
					do_action( 'los_page_after' );
				endwhile;
			?>
			</div>
			</main>
		</div>
	</div>
</div>
<?php
do_action( 'storefront_sidebar' );
get_footer();
