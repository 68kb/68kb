<div class="grid_16">
	<h2><?php echo lang('lang_general_settings'); ?></h2>

	<?php if(validation_errors()) {
		echo '<div class="error"><h3>'.lang('lang_errors_occured').'</h3> '.validation_errors().'</div>';
	}
	?>

	<?php echo form_open('admin/settings/general'); ?>
		<div id="form">
			<div class="inline">
				
				<p class="row1">
					<?php echo form_label(lang('lang_title'). ': <em>('.lang('lang_required').')</em>', 'site_name'); ?>
					<?php echo form_input('site_name', set_value('site_name', $site_name), 'id="site_name"'); ?>
				</p>
				
				<p class="row2">
					<?php echo form_label(lang('lang_site_email'). ': <em>('.lang('lang_required').')</em>', 'site_email'); ?>
					<?php echo form_input('site_email', set_value('site_email', $site_email), 'id="site_email"'); ?>
				</p>
				
				<p class="row1">
					<?php echo form_label(lang('lang_site_meta_keywords'). ':', 'site_keywords'); ?>
					<?php echo form_input('site_keywords', set_value('site_keywords', $site_keywords), 'id="site_keywords" size="50"'); ?>
				</p>
			
			
				<p class="row2">
					<?php echo form_label(lang('lang_site_meta_description'). ': '.tooltip(lang('lang_promo_desc')), 'site_description'); ?>
					<?php
					$data = array(
					              'name'        => 'site_description',
					              'id'          => 'site_description',
					              'value'       => set_value('site_description', $site_description),
					              'rows'   => '4',
					              'cols'        => '50',
					            );
					?>
					<?php echo form_textarea($data); ?>
				</p>
			
				<p class="row1">
					<?php echo form_label(lang('lang_site_max_search'). ':', 'site_max_search'); ?>
					<?php echo form_input('site_max_search', set_value('site_max_search', $site_max_search), 'id="site_max_search" size="10"'); ?>
				</p>
			
				<p class="row2">
					<?php echo form_label(lang('lang_cache_time'). ':', 'site_cache_time'); ?>
					<?php echo form_input('site_cache_time', set_value('site_cache_time', $site_cache_time), 'id="site_cache_time" size="10"'); ?>
				</p>
			
				<div class="row1">
					<?php echo form_label(lang('lang_badwords'). ': <em>('.lang('lang_badwords_txt').')</em>', 'site_bad_words'); ?>
					<?php
					$data = array(
					              'name'        => 'site_bad_words',
					              'id'          => 'site_bad_words',
					              'value'       => set_value('site_bad_words', $site_bad_words),
					              'rows'   => '4',
					              'cols'        => '50',
					            );
					?>
					<?php echo form_textarea($data); ?>
				</div>
				
				<p class="submit"><?php echo form_submit('submit', lang('lang_save'), 'class="save"'); ?></p>
			</div>
		</div>
	</form>
</div>
