<?php
	$color = get_theme_mod( 'background_color' );
	$home_url = home_url();
	$topbar = get_theme_mod('topbar-content');
	$container_val = get_theme_mod('container-value');
	$container_class = 'container';
	if($container_val){
		$container_class = $container_val;
	}
	$background_color = get_theme_mod('background_color');
	$body_style = '';
	if($background_color){
		$body_style .= 'background-color: #' . $background_color . '; ';
	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<?php wp_head(); ?>
</head>
<body style="<?php echo $body_style; ?>">
	<div class="los-page-content <?php echo $container_class; ?>">
	
	<?php 
		if($topbar){
	?>
	<div class="los-topbar">
		<?php echo $topbar; ?>
	</div>
	<?php 
		}
	?>
	<header class="los-header sticky los-header-white">
		<div class="los-header-item los-header-left">
			<div class="los-logo">
				<a href="<?php echo $home_url; ?>">
					<img class="site-logo" src="<?php echo get_theme_mod('site-logo'); ?>">
				</a>
			</div>
			<div class="los-title">
				<a href="<?php echo $home_url; ?>">
					<h2><?php echo get_bloginfo( 'name' ); ?></h2>
					<span><?php echo get_bloginfo('description'); ?></span>
				</a>
			</div>
		</div>
		<div class="los-header-item los-header-center">
		<?php 
		wp_nav_menu(array(
					'theme_location'  => 'primary',
					'container_class' => 'primary-navigation',
				)); 

/*
wp_nav_menu(
				array(
					'theme_location'  => 'handheld',
					'container_class' => 'handheld-navigation',
				)
			);
			*/
				?>
		</div>
		<div class="los-header-item los-header-right">
			<?php do_action('los-header-right'); ?>
		</div>
	</header>
	<div class="mobile-menu-dropdown" id="mobile-menu-dropdown">
		<?php
			wp_nav_menu(array(
				'theme_location'  => 'primary',
				'container_class' => 'mobile-primary-navigation',
			));
		?>
	</div>
