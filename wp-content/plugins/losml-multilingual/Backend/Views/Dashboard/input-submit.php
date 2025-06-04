<div class="losml-cont-tab tab-template">
		<?php 
		if($license_status == 'valid'){
		?>
			<form class="losml-form">
			<input type="hidden" name="_wpnonce" id="lymos-email-nonce3" value="<?php echo esc_html($nonce); ?>">
				<div class="losml-form-item">
					<label><?php echo esc_html(__('License Key', 'losml-multilingual')); ?></label>
					<input type="text" name="losml_multilingual_license" placeholder="<?php echo esc_html(__('License Key', 'losml-multilingual')); ?>" value="">
				</div>
				<div class="losml-form-item">
					<button class="losml-btn" id="losml-btn-license" type="button"><?php echo esc_html(__('Upgrade Pro', 'losml-multilingual')); ?></button>
				</div>
			</form>
		<?php 
		}else{ 
		?>
			<div class="losml-form-item">
				<button class="losml-btn" id="losml-btn-license" type="button"><?php echo esc_html(__('Upgrade Pro', 'losml-multilingual')); ?></button>
			</div>
		<?php
		}
		?>
	</div>