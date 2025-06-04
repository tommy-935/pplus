<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if(! defined('LOSML_NONCE')){
	define('LOSML_NONCE', 'losml-multilingual');
}
/*
$license_obj = new \Lopss\License\checkLicense();
$license_data = $license_obj->check();
$license_status = $license_data['status'];
*/
$nonce = wp_create_nonce( LOSML_NONCE );
$license_status = true;
$license_data = [];
?>
<h2 class="losml-title">
	<?php echo esc_html(__('Losml Multilingual', 'losml-multilingual')); ?>
</h2>
<div class="losml-tab">
	<div class="tab-item active" data-target="tab-settings">
		<?php echo esc_html(__('Settings', 'losml-multilingual')); ?>
	</div>
	
	<div class="tab-item" data-target="tab-upgrade-pro" style="display: none;">
		<?php echo esc_html(__('Upgrade Pro', 'losml-multilingual')); ?>
	</div>
</div>

<div class="losml-cont">
	<?php include_once('settings.php'); ?>
    <?php include_once('upgrades.php'); ?>
</div>
<div class="losml-loading" id="losml-loading"></div>
<script>
	const losml_license = {status: '<?php echo esc_html($license_status); ?>'};
</script>