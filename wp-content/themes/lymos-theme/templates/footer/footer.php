<?php 
$copy = get_theme_mod( 'lymos_footer_copyright_text', 'Copyright - Lymos Theme' );
$custom_footer_widget = false;
$this->widget_class->widgetInit();
$custom_footer = [];
?>
<footer class="los-footer">
	<div class="footer-widgets">
		<?php
			if($custom_footer){
		?>
			custom footer widget
		<?php
			}else{
				dynamic_sidebar('footer-1');
			//	dynamic_sidebar('footer-2');
			//	dynamic_sidebar('footer-3');
			//	dynamic_sidebar('footer-4');
				dynamic_sidebar('mytest-1');
			}
		?>

	</div>
	<div class="los-copyright">
		<?php echo $copy; ?>
	</div>
</footer>
