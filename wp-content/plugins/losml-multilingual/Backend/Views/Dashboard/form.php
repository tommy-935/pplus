<div class="losml-cont-tab tab-setting">
		<form class="losml-form">
		<input type="hidden" name="_wpnonce" id="lymos-email-nonce2" value="<?php echo esc_html($nonce); ?>">
			<div class="losml-form-item">
				<label><?php echo esc_html(__('To', 'losml-multilingual')); ?></label>
				<input type="text" name="losml_multilingual_to" placeholder="<?php echo esc_html(__('send email to', 'losml-multilingual')); ?>" value="">
			</div>
			<div class="losml-form-item">
				<label><?php echo esc_html(__('Subject', 'losml-multilingual')); ?></label>
				<input type="text" name="losml_multilingual_subject" placeholder="<?php echo esc_html(__('Email Subject', 'losml-multilingual')); ?>" value="">
			</div>
            <div class="losml-form-item">
				<label><?php echo esc_html(__('Message', 'losml-multilingual')); ?></label>
				<textarea type="text" name="losml_multilingual_message" placeholder="<?php echo esc_html(__('Email Body', 'losml-multilingual')); ?>" value=""></textarea>
			</div>
			<div class="losml-form-item">
				<button class="losml-btn" id="losml-btn-message" type="button"><?php echo esc_html(__('Send', 'losml-multilingual')); ?></button>
			</div>
		</form>
	</div>