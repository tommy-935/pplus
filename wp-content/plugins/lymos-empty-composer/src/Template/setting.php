<h2 class="lwc-title">
	<?php echo __('Setting', 'por'); ?>
</h2>
<div class="lwc-tab">
	<div class="tab-item active" data-target="tab-add-rule">
		<?php echo __('Common', 'por'); ?>
	</div>
</div>

<div class="lwc-cont">
	<div class="lwc-cont-tab tab-add-rule active">
		<form class="lwc-form">
			<div class="lwc-form-item">
				<label><?php echo __('target host', 'por'); ?></label>
				<input type="text" name="por_host" placeholder="<?php echo __('https://google.com', 'por'); ?>" value="<?php echo $host_val; ?>">
			</div>
			<div class="lwc-form-item">
				<label><?php echo __('api key', 'por'); ?></label>
				<input type="text" name="por_api_key" id="por_api_key" value="<?php echo $api_key; ?>">
				<input type="button" class="lwc-btn" style="margin-left: 10px; " id="lwc-gen-api" value="generate key">
			</div>
			<div class="lwc-form-item">
				<button class="lwc-btn" id="lwc-btn-save" type="button"><?php echo __('Save', 'por'); ?></button>
			</div>
		</form>
	</div>
	
</div>
<div class="lwc-loading" id="lwc-loading"></div>