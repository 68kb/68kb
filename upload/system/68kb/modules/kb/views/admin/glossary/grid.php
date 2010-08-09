<div class="grid_16">
	<div id="content">
		
		<h2><?php echo lang('lang_manage_glossary'); ?> <a class="addnew" href="<?php echo site_url('admin/kb/glossary/add');?>"><?php echo lang('lang_add_term'); ?></a></h2>

		<div id="dynamic">
			<form id="gridform" action="<?php echo site_url('admin/kb/glossary/update'); ?>" method="post">
				<table cellpadding="0" cellspacing="0" border="0" id="grid">
					<thead>
						<tr>
							<th class="largewidth"><?php echo lang('lang_term'); ?></th>
							<th><?php echo lang('lang_definition'); ?></th>
							<th width="3%"><input type="checkbox" id="checkall" /></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td colspan="3" class="dataTables_empty"><img src="<?php echo $template; ?>/images/ajax-loader.gif" alt="Loading" /><?php echo lang('lang_js_processing'); ?></td>
						</tr>
					</tbody>
				</table>
				
				<div class="status">
					<select name="newstatus">
						<option value="" selected><?php echo lang('lang_change_status'); ?></option>
						<option value="delete"><?php echo lang('lang_delete'); ?></option>
					</select>
					<input type="submit" value="Update" />
				</div>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript" charset="utf-8"> 
$(document).ready(function() {
	oTable = $('#grid').dataTable( {
		
		"aoColumns": [null,null,
			{ "bSortable": false }
		],
		"bProcessing": true,
		"bServerSide": true,
		"sPaginationType": "full_numbers",
		"bStateSave": true,
		"bJQueryUI": true,
		"sAjaxSource": "<?php echo site_url('admin/kb/glossary/grid'); ?>",
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