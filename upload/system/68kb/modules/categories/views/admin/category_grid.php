<div class="grid_16">
	<div id="content">
	
		<h2><?php echo lang('lang_categories'); ?> <a class="addnew" href="<?php echo site_url('admin/categories/add');?>"><?php echo lang('lang_add_category'); ?></a></h2>

		<?php
			$this->table->set_template($this->table_template);
			$this->table->set_heading(
				lang('lang_title'),
				lang('lang_allow_ads'),
				lang('lang_display'),
				lang('lang_duplicate'),
				lang('lang_delete')
			);
			if (count($categories) > 0)
			{
				foreach ($categories as $category)
				{
					$spcr = '&nbsp;&nbsp; &raquo; ';
					$indent = ($category['cat_parent'] != 0) ? repeater($spcr, $category['depth']) : '';
					$allow_ads = ($category['cat_allowads'] == 'yes') ? lang('lang_yes') : lang('lang_no');
					$cat_display = ($category['cat_display'] == 'yes') ? lang('lang_yes') : lang('lang_no');
					$this->table->add_row(
					 	$indent.anchor('admin/categories/edit/'.$category['cat_id'], $category['cat_name']),
					 	$allow_ads,
					 	$cat_display,
						anchor('admin/categories/duplicate/'.$category['cat_id'], lang('lang_duplicate'), array('class' => 'duplicate')),
						anchor('admin/categories/delete/'.$category['cat_id'], lang('lang_delete'), array('class' => 'delete'))
					 	
					 );
				}
			}
			else
			{
				$this->table->add_row(array('data' => lang('lang_js_zero'), 'colspan' => 5));
			}
			
			echo $this->table->generate();
		?>
	</div>
</div>