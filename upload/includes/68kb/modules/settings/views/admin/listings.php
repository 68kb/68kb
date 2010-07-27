<div class="grid_16">
	<h2><?php echo lang('lang_checkout_settings'); ?></h2>

	<?php if(validation_errors()) {
		echo '<div class="error"><h3>'.lang('lang_errors_occured').'</h3> '.validation_errors().'</div>';
	}
	?>

	<?php echo form_open('admin/settings/listings'); ?>
		<div id="form">
			<div class="inline">

				<p class="row1">
					<?php
						$options = array('yes' => lang('lang_yes'), 'no' => lang('lang_no'));
					?>
					<?php echo form_label(lang('lang_checkout_notification'). ':', 'checkout_notification'); ?>
					<?php echo form_dropdown('checkout_notification', $options, set_value('checkout_notification', $checkout_notification), 'id="checkout_notification"'); ?>
				</p>
				
				<p class="row2">
					<?php
						$options = array('yes' => lang('lang_yes'), 'no' => lang('lang_no'));
					?>
					<?php echo form_label(lang('lang_checkout_customer_notification'). ':', 'checkout_customer_notification'); ?>
					<?php echo form_dropdown('checkout_customer_notification', $options, set_value('checkout_customer_notification', $checkout_notification), 'id="checkout_customer_notification"'); ?>
				</p>
				
				<p class="row1">
					<?php
						$options = array('yes' => lang('lang_yes'), 'no' => lang('lang_no'));
					?>
					<?php echo form_label(lang('lang_checkout_renewal'). ':', 'checkout_allow_renewal'); ?>
					<?php echo form_dropdown('checkout_allow_renewal', $options, set_value('checkout_allow_renewal', $checkout_allow_renewal), 'id="checkout_allow_renewal"'); ?>
				</p>
				
				<p class="row2">
					<?php
						$options = array('yes' => lang('lang_yes'), 'no' => lang('lang_no'));
					?>
					<?php echo form_label(lang('lang_checkout_email_status_change'). ':', 'checkout_email_status_change'); ?>
					<?php echo form_dropdown('checkout_email_status_change', $options, set_value('checkout_email_status_change', $checkout_email_status_change), 'id="checkout_modify_status_change"'); ?>
				</p>
				
				
				<p class="row2">
					<?php echo form_label(lang('lang_checkout_allowed_html'). ':', 'checkout_allowed_html'); ?>
					<?php
					$data = array(
					              'name'        => 'checkout_allowed_html',
					              'id'          => 'checkout_allowed_html',
					              'value'       => set_value('checkout_allowed_html', $checkout_allowed_html),
					              'rows'   => '4',
					              'cols'        => '50',
					            );
					?>
					<?php echo form_textarea($data); ?>
				</p>
				
				<p class="submit"><?php echo form_submit('submit', lang('lang_save'), 'class="save"'); ?></p>
				
			</div>
		</div>
	</form>
</div>