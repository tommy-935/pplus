<h2 class="lybp-title">
	<?php echo __('Wordpress Backup', 'lybp'); ?>
</h2>
<div class="lybp-tab">
	<div class="tab-item active" data-target="tab-add-rule">
		<?php echo __('Action', 'lybp'); ?>
	</div>
	<div class="tab-item" data-target="tab-rule-list">
		<?php echo __('Backup List', 'lybp'); ?>
	</div>
	<div class="tab-item" data-target="tab-setting">
		<?php echo __('Settings', 'lybp'); ?>
	</div>
</div>

<div class="lybp-cont">
	<div class="lybp-cont-tab tab-add-rule active">
		<form class="lybp-form">
			<div class="lybp-form-item">
				<label><?php echo __('ip', 'lybp'); ?></label>
				<input type="text" name="ip" placeholder="<?php echo __('IP Address', 'lybp'); ?>">
			</div>
			<div class="lybp-form-item">
				<label><?php echo __('email', 'lybp'); ?></label>
				<input type="text" name="email" placeholder="<?php echo __('Email Address', 'lybp'); ?>">
			</div>
			<div class="lybp-form-item">
				<!-- <button class="lybp-btn" id="lybp-btn-add" type="button"><?php echo __('Add', 'lybp'); ?></button> -->
				<button class="lybp-btn" id="lybp-btn-backup-db" type="button"><?php echo __('backup database', 'lybp'); ?></button>
				<button class="lybp-btn" id="lybp-btn-backup-file" type="button"><?php echo __('backup file', 'lybp'); ?></button>
				<button class="lybp-btn" id="lybp-btn-backup-file-content" type="button"><?php echo __('backup content file', 'lybp'); ?></button>
			</div>
		</form>
	</div>
	<div class="lybp-cont-tab tab-rule-list">
		<table class="lybp-table" id="lybp-table">
			<thead>
				<tr>
					<th>ID</th>
					<th>ip</th>
					<th>email</th>
					<th>status</th>
					<th>added date</th>
				</tr>
			</thead>
			<tbody>

			</tbody>
		</table>
		<div class="lybp-page">
			<input type="hidden" id="current-page" value="">
			<a id="lybp-page-prev" href="javascript:void(0);"><?php echo __('prev', 'lybp'); ?></a>
			<a id="lybp-page-next" href="javascript:void(0);"><?php echo __('next', 'lybp'); ?></a>
			<span><span class="total-items" id="total-items"></span>&nbsp;<?php echo __('items', 'lybp'); ?></span>
			<span><span id="page" class="page"></span>&nbsp;<?php echo __('of', 'lybp'); ?>&nbsp;<span id="total-page" class="total-page"></span></span>
		</div>
	</div>
	<div class="lybp-cont-tab tab-setting">
		<form class="lybp-form">
			<div class="lybp-form-item">
				<label><?php echo __('ip message', 'lybp'); ?></label>
				<input type="text" name="ip_message" placeholder="<?php echo __('IP Address limit show message', 'lybp'); ?>" value="<?php echo get_option('wol_ip_message'); ?>">
			</div>
			<div class="lybp-form-item">
				<label><?php echo __('email message', 'lybp'); ?></label>
				<input type="text" name="email_message" placeholder="<?php echo __('Email Address limit show message', 'lybp'); ?>" value="<?php echo get_option('wol_email_message'); ?>">
			</div>
			<div class="lybp-form-item">
				<button class="lybp-btn" id="lybp-btn-message" type="button"><?php echo __('Save', 'lybp'); ?></button>
				
			</div>
		</form>
	</div>
</div>
<div class="lybp-loading" id="lybp-loading"></div>