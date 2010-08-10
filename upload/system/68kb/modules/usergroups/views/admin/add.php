<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$('input[name=can_access_admin]:radio').change(function () {
			var opt_value = $("input[name=can_access_admin]:radio:checked").val();
			if(opt_value == "y") {
				$("#admin_options").fadeIn();
			} else {
				$('#admin_options').fadeOut();
			}
		})
	} );
</script>

<div class="grid_16">
	<h2><?php echo lang('lang_add_user_group'); ?></h2>
	

		<?php if(isset($error)) {
			echo '<div class="error">'.$error,'</div>';
		} ?>
		<?php if(validation_errors()) {
			echo '<div class="error"><h3>'.lang('lang_errors_occured').'</h3> '.validation_errors().'</div>';
		}
		?>


		<?php echo form_open('admin/usergroups/add/'); ?>
		<div id="form">
			<div class="row1">
				<?php echo form_label(lang('lang_title'). ': <em>('.lang('lang_required').')</em>', 'group_name'); ?>
				<?php echo form_input('group_name', set_value('group_name'), 'class="inputtext"'); ?>
			</div>
			<div class="row2">
				<?php echo form_label(lang('lang_description'). ':', 'group_description'); ?>
				<?php echo form_input('group_description', set_value('group_description'), 'class="inputtext" id="group_description"'); ?>
			</div>
			<div class="row1 inline">
				<?php echo form_label(lang('can_view_site'). ':', 'can_view_site'); ?>
				<?php $y = $n = FALSE; if ($can_view_site == 'y') $y = TRUE; else $n = TRUE; ?>
				<?php echo form_radio('can_view_site', 'y', $y); ?> <?php echo lang('lang_yes'); ?>
				<?php echo form_radio('can_view_site', 'n', $n); ?> <?php echo lang('lang_no'); ?>
			</div>
			
			<div class="row2 inline">
				<?php echo form_label(lang('can_access_admin'). ':', 'can_access_admin'); ?>
				<?php $y = $n = FALSE; if ($can_access_admin == 'y') $y = TRUE; else $n = TRUE; ?>
				<?php echo form_radio('can_access_admin', 'y', $y); ?> <?php echo lang('lang_yes'); ?>
				<?php echo form_radio('can_access_admin', 'n', $n); ?> <?php echo lang('lang_no'); ?>
			</div>
			
			<div id="admin_options" <?php if ($can_access_admin == 'n') echo 'style="display: none;"'; ?>>
				<fieldset>
				<div class="row1 inline">
					<?php echo form_label(lang('can_manage_articles'). ':', 'can_manage_articles'); ?>
					<?php $y = $n = FALSE; if ($can_manage_articles == 'y') $y = TRUE; else $n = TRUE; ?>
					<?php echo form_radio('can_manage_articles', 'y', $y); ?> <?php echo lang('lang_yes'); ?>
					<?php echo form_radio('can_manage_articles', 'n', $n); ?> <?php echo lang('lang_no'); ?>
				</div>
				<div class="row2 inline">
					<?php echo form_label(lang('can_delete_articles'). ':', 'can_delete_articles'); ?>
					<?php $y = $n = FALSE; if ($can_delete_articles == 'y') $y = TRUE; else $n = TRUE; ?>
					<?php echo form_radio('can_delete_articles', 'y', $y); ?> <?php echo lang('lang_yes'); ?>
					<?php echo form_radio('can_delete_articles', 'n', $n); ?> <?php echo lang('lang_no'); ?>
				</div>
				<div class="row1 inline">
					<?php echo form_label(lang('can_manage_users'). ':', 'can_manage_users'); ?>
					<?php $y = $n = FALSE; if ($can_manage_users == 'y') $y = TRUE; else $n = TRUE; ?>
					<?php echo form_radio('can_manage_users', 'y', $y); ?> <?php echo lang('lang_yes'); ?>
					<?php echo form_radio('can_manage_users', 'n', $n); ?> <?php echo lang('lang_no'); ?>
				</div>
				<div class="row2 inline">
					<?php echo form_label(lang('can_manage_categories'). ':', 'can_manage_categories'); ?>
					<?php $y = $n = FALSE; if ($can_manage_categories == 'y') $y = TRUE; else $n = TRUE; ?>
					<?php echo form_radio('can_manage_categories', 'y', $y); ?> <?php echo lang('lang_yes'); ?>
					<?php echo form_radio('can_manage_categories', 'n', $n); ?> <?php echo lang('lang_no'); ?>
				</div>
				<div class="row1 inline">
					<?php echo form_label(lang('can_delete_categories'). ':', 'can_delete_categories'); ?>
					<?php $y = $n = FALSE; if ($can_delete_categories == 'y') $y = TRUE; else $n = TRUE; ?>
					<?php echo form_radio('can_delete_categories', 'y', $y); ?> <?php echo lang('lang_yes'); ?>
					<?php echo form_radio('can_delete_categories', 'n', $n); ?> <?php echo lang('lang_no'); ?>
				</div>
				<div class="row2 inline">
					<?php echo form_label(lang('can_manage_settings'). ':', 'can_manage_settings'); ?>
					<?php $y = $n = FALSE; if ($can_manage_settings == 'y') $y = TRUE; else $n = TRUE; ?>
					<?php echo form_radio('can_manage_settings', 'y', $y); ?> <?php echo lang('lang_yes'); ?>
					<?php echo form_radio('can_manage_settings', 'n', $n); ?> <?php echo lang('lang_no'); ?>
				</div>
				<div class="row1 inline">
					<?php echo form_label(lang('can_manage_utilities'). ':', 'can_manage_utilities'); ?>
					<?php $y = $n = FALSE; if ($can_manage_utilities == 'y') $y = TRUE; else $n = TRUE; ?>
					<?php echo form_radio('can_manage_utilities', 'y', $y); ?> <?php echo lang('lang_yes'); ?>
					<?php echo form_radio('can_manage_utilities', 'n', $n); ?> <?php echo lang('lang_no'); ?>
				</div>
				<div class="row2 inline">
					<?php echo form_label(lang('can_manage_themes'). ':', 'can_manage_themes'); ?>
					<?php $y = $n = FALSE; if ($can_manage_themes == 'y') $y = TRUE; else $n = TRUE; ?>
					<?php echo form_radio('can_manage_themes', 'y', $y); ?> <?php echo lang('lang_yes'); ?>
					<?php echo form_radio('can_manage_themes', 'n', $n); ?> <?php echo lang('lang_no'); ?>
				</div>
				<div class="row1 inline">
					<?php echo form_label(lang('can_manage_modules'). ':', 'can_manage_modules'); ?>
					<?php $y = $n = FALSE; if ($can_manage_modules == 'y') $y = TRUE; else $n = TRUE; ?>
					<?php echo form_radio('can_manage_modules', 'y', $y); ?> <?php echo lang('lang_yes'); ?>
					<?php echo form_radio('can_manage_modules', 'n', $n); ?> <?php echo lang('lang_no'); ?>
				</div>
				</fieldset>
			</div>

			<p class="submit"><?php echo form_submit('submit', lang('lang_save'), 'class="save"'); ?></p>

		</div>
		<?php echo form_close(); ?>
	</div>