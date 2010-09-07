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
			<?php echo form_textarea('article_short_desc', set_value('article_short_desc', @$row['article_short_desc']), 'id="article_short_desc" class="shortdesc"'); ?>
		</div>
		
		<div class="row2">
			<?php echo form_label(lang('lang_description'). ':', 'article_description'); ?>
			<div class="toolbar"><script type="text/javascript">if(typeof edToolbar=='function') edToolbar('cat_description');</script></div>
			<?php echo form_textarea('article_description', set_value('article_description', @$row['article_description']), 'id="article_description" class="inputtext"'); ?>
		</div>
		
		<?php $this->events->trigger('article/fields', @$row); ?>
		
		<input type="hidden" name="article_id" value="<?php echo @$row['article_id']; ?>" />
		
	</div>
	<div class="grid_7 inline">
		<?php if ($action == 'edit'): ?>
		<div class="row2">
			<label for="article_author"><?php echo lang('lang_author'); ?>:</label>
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
		
		<a name="attachments"></a>
		<?php if(isset($attach) && is_array($attach)): ?>
			<fieldset>
				<legend><?php echo lang('lang_attachments'); ?></legend>
					<table width="100%" class="main" id="attach">
						<tr>
							<th><?php echo lang('lang_title'); ?></th>
							<th>File</th>
							<th>Type</th>
							<th>Size</th>
							<th>Delete</th>
						</tr>
						<?php  foreach($attach as $item): ?>
							<tr>
								<td><?php echo $item['attach_title']; ?></td>
								<td><?php echo $item['attach_file']; ?></td>
								<td><?php echo $item['attach_type']; ?></td>
								<td><?php echo $item['attach_size']; ?></td>
								
								<td><?php echo '<a href="'.site_url('admin/kb/articles/upload_delete/'.$item['attach_id']).'">'.lang('lang_delete').'</a>'; ?></td>
							</tr>
				<?php endforeach; ?>
					</table>
			</fieldset>
		<?php endif; ?>
		
		<div class="row2">
			<p>
				<?php echo form_label(lang('lang_attachment_title'). ':', 'attach_title'); ?>
				<?php echo form_input('attach_title', set_value('attach_title'), 'size="25"'); ?>
			</p>
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