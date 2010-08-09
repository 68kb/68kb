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
	<h2><?php echo lang('lang_manage_articles'); ?></h2>
</div>

<?php if(isset($error)) {
	echo '<div class="error">'.$error,'</div>';
} ?>
<?php if(validation_errors()) {
	echo '<div class="error"><h3>'.lang('lang_errors_occured').'</h3> '.validation_errors().'</div>';
}
?>
<?php echo form_open_multipart($this->uri->uri_string(), 'class="crud"'); ?>
<div id="form">
	<div class="grid_9">
		
		<div class="row1">
			<?php echo form_label(lang('lang_title'). ': <em>('.lang('lang_required').')</em>', 'article_title'); ?>
			<?php echo form_input('article_title', set_value('article_title', @$row['article_title']), 'class="inputtext"'); ?>
		</div>
		<div class="row2">
			<?php echo form_label(lang('lang_uri') . ': '. tooltip(lang('lang_uri_desc')), 'article_uri'); ?>
			<?php echo form_input('article_uri', set_value('article_uri', @$row['article_uri']), 'class="inputtext"'); ?>
		</div>
		
		<div class="row1">
			<?php echo form_label(lang('lang_short_description'). ':', 'article_short_desc'); ?>
			<div class="toolbar"><script type="text/javascript">if(typeof edToolbar=='function') edToolbar('cat_description');</script></div>
			<?php echo form_textarea('article_short_desc', set_value('article_short_desc', @$row['article_short_desc']), 'id="article_short_desc" class="inputtext"'); ?>
		</div>
		
		<div class="row2">
			<?php echo form_label(lang('lang_description'). ':', 'article_description'); ?>
			<div class="toolbar"><script type="text/javascript">if(typeof edToolbar=='function') edToolbar('cat_description');</script></div>
			<?php echo form_textarea('article_description', set_value('article_description', @$row['article_description']), 'id="article_description" class="inputtext"'); ?>
		</div>

		<input type="hidden" name="article_id" value="<?php echo @$row['article_id']; ?>" />
		
	</div>
	<div class="grid_7 inline">
		<?php if ($action == 'edit'): ?>
		<fieldset>
			<legend>General Details</legend>
			<div class="user_box">
				<a href="<?php echo site_url('admin/users/edit/'.$row['article_author']); ?>"><img width="48" height="48" src="<?php echo gravatar($row['user_email'], 'PG', 48); ?>'" class="user_gravatar" /></a>
				<h3><a href="<?php echo site_url('admin/users/edit/'.$row['user_id']); ?>"><?php echo $row['user_username']; ?></a></h3>
				<a href="mailto:<?php echo $row['user_email']; ?>"><?php echo $row['user_email']; ?></a><br />
				<span class="date"><?php echo lang('lang_join_date'); ?>: <?php echo date($this->config->item('short_date_format'), $row['user_join_date']); ?></span>
				<span class="date"><?php echo lang('lang_last_login'); ?>: <?php echo date($this->config->item('short_date_format'), $row['user_last_login']); ?></span><br />
			</div>
		</fieldset>
		<div class="row2">
			<label for="article_author"><?php echo lang('lang_username'); ?>:</label>
			<input type="text" name="article_author" class="search" id="article_author" value="<?php echo set_value('article_author', $username); ?>" /> <span id="loader"><img src="<?php echo $template; ?>images/ajax-loader.gif" /></span>
		</div>
		<div id="display">
		</div>
		<?php endif; ?>
		
		<div class="row1">
			<?php echo form_label(lang('lang_display'). ':', 'article_display'); ?>
			<?php $options = array('yes' => lang('lang_yes'), 'no' => lang('lang_no')); ?>
			<?php echo form_dropdown('article_display', $options, set_value('article_display', @$row['article_display'])); ?>
		</div>
		<div class="row2">
			<?php echo form_label(lang('lang_site_meta_keywords'). ':', 'article_keywords'); ?>
			<?php echo form_input('article_keywords', set_value('article_keywords', @$row['article_keywords']), 'size="25"'); ?>
			<?php echo tooltip(lang('lang_keywords_desc')); ?>
		</div>
		
		<div class="row1">
			<?php echo form_label(lang('lang_weight'). ':', 'article_order'); ?>
			<?php echo form_input('article_order', set_value('article_order', @$row['article_order']), 'size="25"'); ?>
			<?php echo tooltip(lang('lang_weight_desc')); ?>
		</div>
		
		<fieldset id="categories">
			<legend><?php echo lang('lang_categories'); ?></legend>
			<div class="multiple">
				<ul><li><label><input type="checkbox" id="checkbox" /> <em><?php echo lang('lang_select_all'); ?></em></label></li></ul>
				<?php echo $tree; ?>
			</div>
		</fieldset>
		
		<?php if(isset($cat['cat_image']) && $cat['cat_image'] <> '') { ?>
		<div class="row1">
			<?php
				echo img($this->config->item('cat_image_path') . $cat['cat_image']) .'<br />';
				echo '<a href="'.site_url('admin/categories/delete_image/'.$cat['cat_id']).'">'.lang('lang_delete').'</a>';
			?>
		</div>
		<?php } ?>
		
		<div class="row2">
			<?php echo form_label(lang('lang_attachment'). ':', 'userfile'); ?>
			<?php echo form_upload('userfile', '', 'id="userfile"'); ?>
		</div>
		
		<div class="submit">
			<?php echo form_submit('submit', lang('lang_save'), 'class="save"'); ?>
		</div>
		
	</div>
</div>
</form>

<script type="text/javascript" charset="utf-8">
$(document).ready(function() {
	$(".search").keyup(function() {
				var searchbox = $(this).val();
				var dataString = 'searchword='+ searchbox;
				if(searchbox != '') {
					$.ajax({
					type: "POST",
						url: "<?php echo site_url('admin/users/search'); ?>",
						data: dataString,
						cache: false,
						success: function(html) {
						$("#display").html(html).show();
					}
				});
				$('#loader').ajaxStart(function () {
					$(this).fadeIn();
				});
				$('#loader').ajaxStop(function () {
					$(this).fadeOut();
				});
			}return false;    
	});
});	
</script>