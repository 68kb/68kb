<div class="grid_16">
	<div id="content">
		
		<h2><?php echo lang('lang_manage_articles'); ?> <a class="addnew" href="<?php echo site_url('admin/kb/articles/add');?>"><?php echo lang('lang_add_article'); ?></a></h2>

		<div id="dynamic">
			<form id="gridform" action="<?php echo site_url('admin/kb/articles/update'); ?>" method="post">
				<table cellpadding="0" cellspacing="0" border="0" id="grid">
					<thead>
						<tr>
							<th class="largewidth"><?php echo lang('lang_title'); ?></th>
							<th><?php echo lang('lang_categories'); ?></th>
							<th><?php echo lang('lang_date_added'); ?></th>
							<th><?php echo lang('lang_date_edited'); ?></th>
							<th><?php echo lang('lang_display'); ?></th>
							<th width="3%"><input type="checkbox" id="checkall" /></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td colspan="6" class="dataTables_empty"><img src="<?php echo $template; ?>/images/ajax-loader.gif" alt="Loading" /><?php echo lang('lang_js_processing'); ?></td>
						</tr>
					</tbody>
				</table>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript" charset="utf-8"> 
$(document).ready(function() {
	oTable = $('#grid').dataTable( {
		
		"aoColumns": [null,null,null,null,null,
			{ "bSortable": false }
		],
		"bProcessing": true,
		"bServerSide": true,
		"sPaginationType": "full_numbers",
		"bStateSave": true,
		"bJQueryUI": true,
		"sAjaxSource": "<?php echo site_url('admin/kb/articles/grid'); ?>",
		"fnServerData": function ( sSource, aoData, fnCallback ) {
			$.ajax({ "dataType": 'json', "type": "POST", "url": sSource, "data": aoData, "success": fnCallback });
		}
	} );

	$('#checkall').click( function() {
		var checked_status = $('input', oTable.fnGetNodes()).attr('checked')?0:1; //this.checked;
		$('input', oTable.fnGetNodes()).attr('checked',checked_status);
		return false; // to avoid refreshing the page
	});
});
</script>