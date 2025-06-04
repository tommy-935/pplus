<div class="losml-cont-tab tab-record">
		<div class="losml-form-item">
			<input type="text" id="losml-keyword" name="lymos_email" placeholder="<?php echo esc_html(__('Email', 'losml-multilingual')); ?>" value="">
			<button class="losml-btn losml-btn-search" id="losml-list-search" type="button"><?php echo esc_html(__('Search', 'losml-multilingual')); ?></button>
		</div>
		<table class="wp-list-table widefat fixed striped table-view-list posts losml-email-table" id="losml-table">
			<thead>
				<tr>
					<td><?php echo esc_html(__('ID', 'losml-multilingual')); ?></td>
					<td><?php echo esc_html(__('Email', 'losml-multilingual')); ?></td>
					<td><?php echo esc_html(__('Subject', 'losml-multilingual')); ?></td>
					<td><?php echo esc_html(__('Body', 'losml-multilingual')); ?></td>
					<td><?php echo esc_html(__('Sent Time', 'losml-multilingual')); ?></td>
					<td><?php echo esc_html(__('Opened', 'losml-multilingual')); ?></td>
					<td><?php echo esc_html(__('Staus', 'losml-multilingual')); ?></td>
					<td><?php echo esc_html(__('Resend', 'losml-multilingual')); ?></td>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>1</td>
					<td>1</td>
					<td>1</td>
					<td>1</td>
					<td>1</td>
					<td>1</td>
					<td>1</td>
					<td>1</td>
				</tr>
			</tbody>
		</table>

		<div class="losml-page">
			<input type="hidden" id="current-page" value="">
			<a id="losml-page-prev" href="javascript:void(0);"><?php echo esc_html(__('prev', 'losml-multilingual')); ?></a>
			<a id="losml-page-next" href="javascript:void(0);"><?php echo esc_html(__('next', 'losml-multilingual')); ?></a>
			<span><span class="total-items" id="total-items"></span>&nbsp;<?php echo esc_html(__('items', 'losml-multilingual')); ?></span>
			<span><span id="page" class="page"></span>&nbsp;<?php echo esc_html(__('of', 'losml-multilingual')); ?>&nbsp;<span id="total-page" class="total-page"></span></span>
		</div>
	</div>