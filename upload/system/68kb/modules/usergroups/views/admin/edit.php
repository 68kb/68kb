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
	
	
	<?php echo form_open('admin/usergroups/edit/'.$group_id); ?>
	<div id="form">
		<div class="row1">
			<?php echo form_label(lang('lang_title'). ': <em>('.lang('lang_required').')</em>', 'group_name'); ?>
			<?php echo form_input('group_name', set_value('group_name', $group_name), 'class="inputtext"'); ?>
		</div>
		<div class="row2">
			<?php echo form_label(lang('lang_description'). ':', 'group_description'); ?>
			<?php echo form_input('group_description', set_value('group_description', $group_description), 'class="inputtext" id="group_description"'); ?>
		</div>
		
		<?php if ($group_id != 1) { ?>
		<div class="row1 inline">
			<?php echo form_label(lang('can_view_site'). ':', 'can_view_site'); ?>
			<?php $y = $n = FALSE; if ($can_view_site == 'y') $y = TRUE; else $n = TRUE; ?>
			<?php echo form_radio('can_view_site', 'y', $y); ?> <?php echo lang('lang_yes'); ?>
			<?php echo form_radio('can_view_site', 'n', $n); ?> <?php echo lang('lang_no'); ?>
		</div>
		<div class="row2 inline">
			<?php echo form_label(lang('can_place_ads'). ':', 'can_place_ads'); ?>
			<?php $y = $n = FALSE; if ($can_place_ads == 'y') $y = TRUE; else $n = TRUE; ?>
			<?php echo form_radio('can_place_ads', 'y', $y); ?> <?php echo lang('lang_yes'); ?>
			<?php echo form_radio('can_place_ads', 'n', $n); ?> <?php echo lang('lang_no'); ?>
		</div>
		<div class="row1 inline">
			<?php echo form_label(lang('manually_approve_ads'). ':', 'manually_approve_ads'); ?>
			<?php $y = $n = FALSE; if ($manually_approve_ads == 'y') $y = TRUE; else $n = TRUE; ?>
			<?php echo form_radio('manually_approve_ads', 'y', $y); ?> <?php echo lang('lang_yes'); ?>
			<?php echo form_radio('manually_approve_ads', 'n', $n); ?> <?php echo lang('lang_no'); ?>
		</div>
		<div class="row2 inline">
			<?php echo form_label(lang('can_search'). ':', 'can_search'); ?>
			<?php $y = $n = FALSE; if ($can_search == 'y') $y = TRUE; else $n = TRUE; ?>
			<?php echo form_radio('can_search', 'y', $y); ?> <?php echo lang('lang_yes'); ?>
			<?php echo form_radio('can_search', 'n', $n); ?> <?php echo lang('lang_no'); ?>
		</div>
		
		<div class="row1 inline">
			<?php echo form_label(lang('can_access_admin'). ':', 'can_access_admin'); ?>
			<?php $y = $n = FALSE; if ($can_access_admin == 'y') $y = TRUE; else $n = TRUE; ?>
			<?php echo form_radio('can_access_admin', 'y', $y); ?> <?php echo lang('lang_yes'); ?>
			<?php echo form_radio('can_access_admin', 'n', $n); ?> <?php echo lang('lang_no'); ?>
		</div>
		<div id="admin_options" <?php if ($can_access_admin == 'n') echo 'style="display: none;"'; ?>>
			<fieldset>
			<div class="row1 inline">
				<?php echo form_label(lang('can_manage_orders'). ':', 'can_manage_orders'); ?>
				<?php $y = $n = FALSE; if ($can_manage_orders == 'y') $y = TRUE; else $n = TRUE; ?>
				<?php echo form_radio('can_manage_orders', 'y', $y); ?> <?php echo lang('lang_yes'); ?>
				<?php echo form_radio('can_manage_orders', 'n', $n); ?> <?php echo lang('lang_no'); ?>
			</div>
			<div class="row2 inline">
				<?php echo form_label(lang('can_delete_orders'). ':', 'can_delete_orders'); ?>
				<?php $y = $n = FALSE; if ($can_delete_orders == 'y') $y = TRUE; else $n = TRUE; ?>
				<?php echo form_radio('can_delete_orders', 'y', $y); ?> <?php echo lang('lang_yes'); ?>
				<?php echo form_radio('can_delete_orders', 'n', $n); ?> <?php echo lang('lang_no'); ?>
			</div>
			<div class="row1 inline">
				<?php echo form_label(lang('can_manage_listings'). ':', 'can_manage_listings'); ?>
				<?php $y = $n = FALSE; if ($can_manage_listings == 'y') $y = TRUE; else $n = TRUE; ?>
				<?php echo form_radio('can_manage_listings', 'y', $y); ?> <?php echo lang('lang_yes'); ?>
				<?php echo form_radio('can_manage_listings', 'n', $n); ?> <?php echo lang('lang_no'); ?>
			</div>
			<div class="row2 inline">
				<?php echo form_label(lang('can_delete_listings'). ':', 'can_delete_listings'); ?>
				<?php $y = $n = FALSE; if ($can_delete_listings == 'y') $y = TRUE; else $n = TRUE; ?>
				<?php echo form_radio('can_delete_listings', 'y', $y); ?> <?php echo lang('lang_yes'); ?>
				<?php echo form_radio('can_delete_listings', 'n', $n); ?> <?php echo lang('lang_no'); ?>
			</div>
			<div class="row1 inline">
				<?php echo form_label(lang('can_manage_products'). ':', 'can_manage_products'); ?>
				<?php $y = $n = FALSE; if ($can_manage_products == 'y') $y = TRUE; else $n = TRUE; ?>
				<?php echo form_radio('can_manage_products', 'y', $y); ?> <?php echo lang('lang_yes'); ?>
				<?php echo form_radio('can_manage_products', 'n', $n); ?> <?php echo lang('lang_no'); ?>
			</div>
			<div class="row2 inline">
				<?php echo form_label(lang('can_manage_coupons'). ':', 'can_manage_coupons'); ?>
				<?php $y = $n = FALSE; if ($can_manage_coupons == 'y') $y = TRUE; else $n = TRUE; ?>
				<?php echo form_radio('can_manage_coupons', 'y', $y); ?> <?php echo lang('lang_yes'); ?>
				<?php echo form_radio('can_manage_coupons', 'n', $n); ?> <?php echo lang('lang_no'); ?>
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
				<?php echo form_label(lang('can_manage_fields'). ':', 'can_manage_fields'); ?>
				<?php $y = $n = FALSE; if ($can_manage_fields == 'y') $y = TRUE; else $n = TRUE; ?>
				<?php echo form_radio('can_manage_fields', 'y', $y); ?> <?php echo lang('lang_yes'); ?>
				<?php echo form_radio('can_manage_fields', 'n', $n); ?> <?php echo lang('lang_no'); ?>
			</div>
			<div class="row1 inline">
				<?php echo form_label(lang('can_manage_settings'). ':', 'can_manage_settings'); ?>
				<?php $y = $n = FALSE; if ($can_manage_settings == 'y') $y = TRUE; else $n = TRUE; ?>
				<?php echo form_radio('can_manage_settings', 'y', $y); ?> <?php echo lang('lang_yes'); ?>
				<?php echo form_radio('can_manage_settings', 'n', $n); ?> <?php echo lang('lang_no'); ?>
			</div>
			<div class="row2 inline">
				<?php echo form_label(lang('can_manage_utilities'). ':', 'can_manage_utilities'); ?>
				<?php $y = $n = FALSE; if ($can_manage_utilities == 'y') $y = TRUE; else $n = TRUE; ?>
				<?php echo form_radio('can_manage_utilities', 'y', $y); ?> <?php echo lang('lang_yes'); ?>
				<?php echo form_radio('can_manage_utilities', 'n', $n); ?> <?php echo lang('lang_no'); ?>
			</div>
			<div class="row1 inline">
				<?php echo form_label(lang('can_manage_themes'). ':', 'can_manage_themes'); ?>
				<?php $y = $n = FALSE; if ($can_manage_themes == 'y') $y = TRUE; else $n = TRUE; ?>
				<?php echo form_radio('can_manage_themes', 'y', $y); ?> <?php echo lang('lang_yes'); ?>
				<?php echo form_radio('can_manage_themes', 'n', $n); ?> <?php echo lang('lang_no'); ?>
			</div>
			<div class="row2 inline">
				<?php echo form_label(lang('can_manage_content'). ':', 'can_manage_content'); ?>
				<?php $y = $n = FALSE; if ($can_manage_content == 'y') $y = TRUE; else $n = TRUE; ?>
				<?php echo form_radio('can_manage_content', 'y', $y); ?> <?php echo lang('lang_yes'); ?>
				<?php echo form_radio('can_manage_content', 'n', $n); ?> <?php echo lang('lang_no'); ?>
			</div>
			<div class="row2 inline">
				<?php echo form_label(lang('can_manage_modules'). ':', 'can_manage_modules'); ?>
				<?php $y = $n = FALSE; if ($can_manage_modules == 'y') $y = TRUE; else $n = TRUE; ?>
				<?php echo form_radio('can_manage_modules', 'y', $y); ?> <?php echo lang('lang_yes'); ?>
				<?php echo form_radio('can_manage_modules', 'n', $n); ?> <?php echo lang('lang_no'); ?>
			</div>
			<div class="row1 inline">
				<?php echo form_label(lang('can_use_modules'). ':', 'can_use_modules'); ?>
				<?php $y = $n = FALSE; if ($can_use_modules == 'y') $y = TRUE; else $n = TRUE; ?>
				<?php echo form_radio('can_use_modules', 'y', $y); ?> <?php echo lang('lang_yes'); ?>
				<?php echo form_radio('can_use_modules', 'n', $n); ?> <?php echo lang('lang_no'); ?>
			</div>
			</fieldset>
		</div>
		
		<?php } ?>
		<p class="submit">
			<?php echo form_submit('submit', lang('lang_save'), 'class="save"'); ?>
		</p>
		
	</div>
	<?php echo form_close(); ?>
</div>