<?php 
if ($this->events->active_hook('articles/form')) 
{
	$this->events->trigger('articles/form');
} 
else 
{
	echo '<script src="js/js-quicktags/js_quicktags.js" type="text/javascript"></script>';
} 
?>
<div class="grid_16">
	<h2><?php echo lang('lang_manage_glossary'); ?></h2>

	<?php if(validation_errors()) {
		echo '<div class="error"><h3>'.lang('lang_errors_occured').'</h3> '.validation_errors().'</div>';
	}
	?>

	<?php echo form_open($this->uri->uri_string(), 'class="crud"'); ?>
		<div id="form">
			<div class="inline">
				
				<p class="row1">
					<?php echo form_label(lang('lang_title'). ': <em>('.lang('lang_required').')</em>', 'g_term'); ?>
					<?php echo form_input('g_term', set_value('g_term', @$row['g_term']), 'id="g_term"'); ?>
				</p>
				
				<div class="row2">
					<?php echo form_label(lang('lang_definition'). ':', 'g_definition'); ?>
					<div class="toolbar"><script type="text/javascript">if(typeof edToolbar=='function') edToolbar('g_definition');</script></div>
					<?php echo form_textarea('g_definition', set_value('g_definition', @$row['g_definition']), 'id="g_definition" class="inputtext"'); ?>
				</div>
				
				<p class="submit"><?php echo form_submit('submit', lang('lang_save'), 'class="save"'); ?></p>
			</div>
		</div>
	</form>
</div>
