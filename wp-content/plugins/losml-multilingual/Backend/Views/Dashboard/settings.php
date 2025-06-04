<div class="losml-cont-tab tab-settings active">
		<form class="losml-form">
			<input type="hidden" name="_wpnonce" id="losml_nonce_field" value="<?php echo esc_html($nonce); ?>">
			<div class="losml-form-item" style="display: none;">
				<label class="losml-label"><?php echo esc_html(__('Address', 'losml-multilingual')); ?></label>
				<input type="text" name="" placeholder="<?php echo esc_html(__(' Address', 'losml-multilingual')); ?>" value="<?php echo esc_attr(get_option('_from_email')); ?>">
			</div>
			
			<div class="losml-form-item" style="display: none;">
				<label><?php echo esc_html(__('BCC Email Address', 'losml-multilingual')); ?></label>
				<input type="text" name="xxxx" placeholder="<?php echo esc_html(__('BCC Email Address', 'losml-multilingual')); ?>" value="<?php echo esc_attr(get_option('lymos_bcc_email')); ?>">
			</div>
			<div class="losml-form-tip" style="display: none;">
				<?php echo esc_html(__('Optional. This email address will be used in the "BCC" field of the outgoing emails.', 'losml-multilingual')); ?>
		
			</div>
			
			<div class="losml-form-item" style="display: none;">
            	<label><?php echo esc_html(__('SSL/TLS On', 'losml-multilingual')); ?></label>
            	<input type="checkbox" name="losml_multilingual_ssl" <?php echo get_option('losml_multilingual_ssl') ? 'checked' : ''; ?>>
            </div>
			<div class="losml-form-item">
            	<label class="losml-label"><?php echo esc_html(__('Domain Type', 'losml-multilingual')); ?></label>
                <label class="losml-radio-group">
                    <input type="radio" name="losml_domain_type" value="subdomain" <?php echo get_option('losml_domain_type') == 'subdomain' || ! get_option('losml_domain_type') ? 'checked' : ''; ?>>
                    <span><?php echo esc_html(__('subdomain', 'losml-multilingual')); ?></span>
                </label>
                <label class="losml-radio-group">
                    <input type="radio" name="losml_domain_type" value="subfolder" <?php echo get_option('losml_domain_type') == 'subfolder' ? 'checked' : ''; ?>>
                    <span><?php echo esc_html(__('subfolder', 'losml-multilingual')); ?></span>
                </label>
            </div>
            
			<div class="losml-form-item">
				<button class="losml-btn" id="losml-btn-setting" type="button"><?php echo esc_html(__('Save', 'losml-multilingual')); ?></button>
			</div>
		</form>
	</div>