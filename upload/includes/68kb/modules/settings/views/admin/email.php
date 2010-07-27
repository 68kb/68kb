<script type="text/javascript" charset="utf-8"> 
$(document).ready(function() {
	$("#protocol").change(function () {
		var opt_value = $("#protocol").val();
		if(opt_value == "sendmail") {
			$("#smtp_val").fadeOut();
			$("#sendmail_val").fadeIn();
		} else if (opt_value == 'smtp') {
			$("#sendmail_val").hide();
			$("#smtp_val").fadeIn();
		} else {
			$('#sendmail_val').fadeOut();
			$('#smtp_val').fadeOut();
		}
	})
});
</script>

<div class="grid_16">
	<h2><?php echo lang('lang_email_settings'); ?></h2>

	<?php if(validation_errors()) {
		echo '<div class="error"><h3>'.lang('lang_errors_occured').'</h3> '.validation_errors().'</div>';
	}
	?>

<?php echo form_open('admin/settings/email'); ?>
	<div id="form">
		<div class="grid_7">
			<div class="inline">
				
				<p class="row1">
					<?php echo form_label(lang('lang_email_protocol'). ': <em>('.lang('lang_required').')</em>', 'protocol'); ?>
					<?php $options = array('mail' => 'mail', 'sendmail' => 'sendmail', 'smtp' => 'smtp'); ?>
					<?php echo form_dropdown('protocol', $options, set_value('protocol', $protocol), 'id="protocol"'); ?>
				</p>
				<p class="row2">
					<?php echo form_label(lang('lang_email_wordwrap'). ':', 'wordwrap'); ?>
					<?php $options = array('yes' => lang('lang_yes'), 'no' => lang('lang_no')); ?>
					<?php echo form_dropdown('wordwrap', $options, set_value('wordwrap', $wordwrap), 'id="wordwrap"'); ?>
				</p>
				<p class="row1">
					<?php echo form_label(lang('lang_email_wrapchars'). ':', 'wrapchars'); ?>
					<?php echo form_input('wrapchars', set_value('wrapchars', $wrapchars), 'id="wrapchars"'); ?>
				</p>
				<p class="row2">
					<?php echo form_label(lang('lang_email_mailtype'). ':', 'mailtype'); ?>
					<?php $options = array('html' => 'html', 'text' => 'text'); ?>
					<?php echo form_dropdown('mailtype', $options, set_value('mailtype', $mailtype), 'id="mailtype"'); ?>
				</p>
				<p class="row1">
					<?php echo form_label(lang('lang_email_charset'). ':', 'charset'); ?>
					<?php echo form_input('charset', set_value('charset', $charset), 'id="charset"'); ?>
				</p>
				<p class="row2">
					<?php echo form_label(lang('lang_email_crlf'). ':', 'crlf'); ?>
					<?php echo form_input('crlf', set_value('crlf', $crlf), 'id="crlf"'); ?>
				</p>
				<p class="row1">
					<?php echo form_label(lang('lang_email_newline'). ':', 'newline'); ?>
					<?php echo form_input('newline', set_value('newline', $newline), 'id="newline"'); ?>
				</p>
				
				
				<p class="submit"><?php echo form_submit('submit', lang('lang_save'), 'class="save"'); ?></p>
			
			</div>
		</div>
		<div class="grid_9">
			<div class="inline">
				<p id="sendmail_val" class="row1" <?php if ($protocol != 'sendmail') echo 'style="display: none;"'; ?>>
					<?php echo form_label(lang('lang_email_mailpath'). ':', 'status_internal'); ?>
					<?php echo form_input('mailpath', set_value('mailpath', $mailpath), 'id="mailpath"'); ?>
				</p>
			
				<div id="smtp_val" <?php if ($protocol != 'smtp') echo 'style="display: none;"'; ?>>
					<p class="row1">
						<?php echo form_label(lang('lang_email_smtp_host'). ':', 'smtp_host'); ?>
						<?php echo form_input('smtp_host', set_value('smtp_host', $smtp_host), 'id="smtp_host"'); ?>
					</p>
					<p class="row2">
						<?php echo form_label(lang('lang_email_smtp_user'). ':', 'smtp_user'); ?>
						<?php echo form_input('smtp_user', set_value('smtp_user', $smtp_user), 'id="smtp_user"'); ?>
					</p>
					<p class="row1">
						<?php echo form_label(lang('lang_email_smtp_pass'). ':', 'smtp_pass'); ?>
						<?php echo form_input('smtp_pass', set_value('smtp_pass', $smtp_pass), 'id="smtp_pass"'); ?>
					</p>
				
					<p class="row2">
						<?php echo form_label(lang('lang_email_smtp_port'). ':', 'smtp_port'); ?>
						<?php echo form_input('smtp_port', set_value('smtp_port', $smtp_port), 'id="smtp_port"'); ?>
					</p>
					<p class="row1">
						<?php echo form_label(lang('lang_email_smtp_timeout'). ':', 'smtp_timeout'); ?>
						<?php echo form_input('smtp_timeout', set_value('smtp_timeout', $smtp_timeout), 'id="smtp_timeout"'); ?>
						<span class="characters"><?php echo lang('lang_in_seconds'); ?></span>
					</p>
				</div>
			</div>
		</div>
	</div>
</form>
