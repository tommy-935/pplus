<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if(! defined('LYMOS_SMTP_NONCE')){
	define('LYMOS_SMTP_NONCE', 'lymos-smtp-email');
}
$nonce = wp_create_nonce( LYMOS_SMTP_NONCE );
?>
<h2 class="lse-title">
	<?php echo esc_html(__('Lymos Smtp Email', 'lymos-smtp-email')); ?>
</h2>
<div class="lse-tab">
	<div class="tab-item active" data-target="tab-add-rule">
		<?php echo esc_html(__('Settings', 'lymos-smtp-email')); ?>
	</div>
    <!--
	<div class="tab-item" data-target="tab-rule-list">
		<?php echo esc_html(__('rule list', 'lymos-smtp-email')); ?>
	</div>
-->
	<div class="tab-item" data-target="tab-setting">
		<?php echo esc_html(__('Test Email', 'lymos-smtp-email')); ?>
	</div>
	<div class="tab-item" data-target="tab-record">
		<?php echo esc_html(__('Email Records', 'lymos-smtp-email')); ?>
	</div>
</div>

<div class="lse-cont">
	<div class="lse-cont-tab tab-add-rule active">
		<form class="lse-form">
			<input type="hidden" name="_wpnonce" id="lymos-email-nonce" value="<?php echo esc_html($nonce); ?>">
			<div class="lse-form-item">
				<label><?php echo esc_html(__('From Email Address', 'lymos-smtp-email')); ?></label>
				<input type="text" name="lymos_from_email" placeholder="<?php echo esc_html(__('From Email Address', 'lymos-smtp-email')); ?>" value="<?php echo esc_attr(get_option('lymos_from_email')); ?>">
			</div>
			<div class="lse-form-item">
				<label><?php echo esc_html(__('From Name', 'lymos-smtp-email')); ?></label>
				<input type="text" name="lymos_from_name" placeholder="<?php echo esc_html(__('From Name', 'lymos-smtp-email')); ?>" value="<?php echo esc_attr(get_option('lymos_from_name')); ?>">
			</div>
			<div class="lse-form-item">
				<label><?php echo esc_html(__('BCC Email Address', 'lymos-smtp-email')); ?></label>
				<input type="text" name="lymos_bcc_email" placeholder="<?php echo esc_html(__('BCC Email Address', 'lymos-smtp-email')); ?>" value="<?php echo esc_attr(get_option('lymos_bcc_email')); ?>">
			</div>
			<div class="lse-form-tip">
				<?php echo esc_html(__('Optional. This email address will be used in the "BCC" field of the outgoing emails.', 'lymos-smtp-email')); ?>
		
			</div>
			<div class="lse-form-item">
            	<label><?php echo esc_html(__('SMTP Host', 'lymos-smtp-email')); ?></label>
            	<input type="text" name="lymos_smtp_host" placeholder="<?php echo esc_html(__('SMTP Host', 'lymos-smtp-email')); ?>" value="<?php echo esc_attr(get_option('lymos_smtp_host')); ?>">
            </div>
            <div class="lse-form-item">
            	<label><?php echo esc_html(__('SMTP Port', 'lymos-smtp-email')); ?></label>
            	<input type="text" name="lymos_smtp_port" placeholder="<?php echo esc_html(__('SMTP Port', 'lymos-smtp-email')); ?>" value="<?php echo esc_attr(get_option('lymos_smtp_port')); ?>">
            </div>
			<div class="lse-form-item">
            	<label><?php echo esc_html(__('SSL/TLS On', 'lymos-smtp-email')); ?></label>
            	<input type="checkbox" name="lymos_smtp_ssl" <?php echo get_option('lymos_smtp_ssl') ? 'checked' : ''; ?>>
            </div>
			<div class="lse-form-item">
            	<label><?php echo esc_html(__('Record Logs On', 'lymos-smtp-email')); ?></label>
            	<input type="checkbox" name="lymos_smtp_record" <?php echo get_option('lymos_smtp_record') ? 'checked' : ''; ?>>
            </div>
            <div class="lse-form-item">
            	<label><?php echo esc_html(__('SMTP Username', 'lymos-smtp-email')); ?></label>
            	<input type="text" name="lymos_smtp_username" placeholder="<?php echo esc_html(__('SMTP Username', 'lymos-smtp-email')); ?>" value="<?php echo esc_attr(get_option('lymos_smtp_username')); ?>">
            </div>
            <div class="lse-form-item">
            	<label><?php echo esc_html(__('SMTP Password', 'lymos-smtp-email')); ?></label>
            	<input type="password" name="lymos_smtp_password" placeholder="<?php echo esc_html(__('SMTP Password', 'lymos-smtp-email')); ?>" value="<?php echo esc_attr(get_option('lymos_smtp_password')); ?>">
            </div>
			<div class="lse-form-item">
				<button class="lse-btn" id="lse-btn-add" type="button"><?php echo esc_html(__('Save', 'lymos-smtp-email')); ?></button>
			</div>
		</form>
	</div>
	<div class="lse-cont-tab tab-rule-list">
	</div>
	<div class="lse-cont-tab tab-setting">
		<form class="lse-form">
		<input type="hidden" name="_wpnonce" id="lymos-email-nonce2" value="<?php echo esc_html($nonce); ?>">
			<div class="lse-form-item">
				<label><?php echo esc_html(__('To', 'lymos-smtp-email')); ?></label>
				<input type="text" name="lymos_smtp_to" placeholder="<?php echo esc_html(__('send email to', 'lymos-smtp-email')); ?>" value="">
			</div>
			<div class="lse-form-item">
				<label><?php echo esc_html(__('Subject', 'lymos-smtp-email')); ?></label>
				<input type="text" name="lymos_smtp_subject" placeholder="<?php echo esc_html(__('Email Subject', 'lymos-smtp-email')); ?>" value="">
			</div>
            <div class="lse-form-item">
				<label><?php echo esc_html(__('Message', 'lymos-smtp-email')); ?></label>
				<textarea type="text" name="lymos_smtp_message" placeholder="<?php echo esc_html(__('Email Body', 'lymos-smtp-email')); ?>" value=""></textarea>
			</div>
			<div class="lse-form-item">
				<button class="lse-btn" id="lse-btn-message" type="button"><?php echo esc_html(__('Send', 'lymos-smtp-email')); ?></button>
			</div>
		</form>
	</div>
	<div class="lse-cont-tab tab-record">
		<div class="lse-form-item">
			<input type="text" id="lse-keyword" name="lymos_email" placeholder="<?php echo esc_html(__('Email', 'lymos-smtp-email')); ?>" value="">
			<button class="lse-btn lse-btn-search" id="lse-list-search" type="button"><?php echo esc_html(__('Search', 'lymos-smtp-email')); ?></button>
		</div>
		<table class="wp-list-table widefat fixed striped table-view-list posts lse-email-table" id="lse-table">
			<thead>
				<tr>
					<td><?php echo esc_html(__('ID', 'lymos-smtp-email')); ?></td>
					<td><?php echo esc_html(__('Email', 'lymos-smtp-email')); ?></td>
					<td><?php echo esc_html(__('Subject', 'lymos-smtp-email')); ?></td>
					<td><?php echo esc_html(__('Body', 'lymos-smtp-email')); ?></td>
					<td><?php echo esc_html(__('Sent Time', 'lymos-smtp-email')); ?></td>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>1</td>
					<td>1</td>
					<td>1</td>
					<td>1</td>
					<td>1</td>
				</tr>
			</tbody>
		</table>

		<div class="lse-page">
			<input type="hidden" id="current-page" value="">
			<a id="lse-page-prev" href="javascript:void(0);"><?php echo esc_html(__('prev', 'lymos-smtp-email')); ?></a>
			<a id="lse-page-next" href="javascript:void(0);"><?php echo esc_html(__('next', 'lymos-smtp-email')); ?></a>
			<span><span class="total-items" id="total-items"></span>&nbsp;<?php echo esc_html(__('items', 'lymos-smtp-email')); ?></span>
			<span><span id="page" class="page"></span>&nbsp;<?php echo esc_html(__('of', 'lymos-smtp-email')); ?>&nbsp;<span id="total-page" class="total-page"></span></span>
		</div>
	</div>
</div>
<div class="lse-loading" id="lse-loading"></div>