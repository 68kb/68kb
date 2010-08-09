<h2><?php echo lang('lang_search'); ?></h2>

<?php echo form_open('search/do_search/'); ?>
	
	<table width="100%">
		<tr>
			<td><?php echo lang('lang_keywords'); ?>:</td><td><input type="text" name="keywords" value="" /></td>
		</tr>
		<tr>
			<td><?php echo lang('lang_category'); ?>:</td>
			<td>
				<?php 
				$cat_options['0'] = lang('lang_search_all');
				foreach($cats as $row)
				{
					$indent = ($row['cat_parent'] != 0) ? repeater('&nbsp;&raquo;&nbsp;', $row['depth']) : '';
					$cat_options[$row['cat_id']] = $indent.$row['cat_name']; 
				}	
				echo form_dropdown('category', $cat_options, 'id="category"');
				?>
			</td>
		</tr>
	</table>
	
	<p class="continue"><input type="submit" name="submit" value="<?php echo lang('lang_search'); ?>" /></p>
</form>