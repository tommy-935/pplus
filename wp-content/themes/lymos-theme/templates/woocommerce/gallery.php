<?php
defined( 'ABSPATH' ) || exit;
global $product;
$post_thumbnail_id = $product->get_image_id();
$html = wc_get_gallery_image_html( $post_thumbnail_id, true );
$attachment_ids = $product->get_gallery_image_ids();

?>
<div class="wpi-image-box">
	<div class="wpi-image-gallery" id="wpi-image-gallery">
		<div class="wpi-gallery-box" id="wpi-gallery-box">
			<?php
			if ( $attachment_ids && $post_thumbnail_id ) {
				foreach ( $attachment_ids as $attachment_id ) {
					echo  wc_get_gallery_image_html( $attachment_id, true);
				}
			}
			?>
		</div>
		<div id="wpi-fd" class="wpi-fd"></div>
	</div>
	<div class="wpi-image-thumb" id="wpi-image-thumb">
		<?php
		if ( $attachment_ids && $post_thumbnail_id ) {

			foreach ( $attachment_ids as $attachment_id ) {
				$image = wp_get_attachment_image($attachment_id, 'woocommerce_single');
				echo '<div class="wpi-image-item">' . $image . '</div>';
			}
		}
		?>
	</div>
	<div id="wpi-max" class="wpi-max"><img src=""></div>
</div>