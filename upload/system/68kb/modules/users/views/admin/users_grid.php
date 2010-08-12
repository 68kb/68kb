<div class="grid_16">
	<div id="content">
		
		<h2><?php echo lang('lang_manage_users'); ?> <a class="addnew" href="<?php echo site_url('admin/users/add');?>"><?php echo lang('lang_add_user'); ?></a></h2>

		<div id="dynamic">
			<form id="gridform" action="<?php echo site_url('admin/users/update'); ?>" method="post">
				<table cellpadding="0" cellspacing="0" border="0" id="grid">
					<thead>
						<tr>
							<th width="5%" class="largewidth"><?php echo lang('lang_username'); ?></th>
							<th><?php echo lang('lang_join_date'); ?></th>
							<th><?php echo lang('lang_last_login'); ?></th>
							<th class="largewidth"><?php echo lang('lang_user_group'); ?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td colspan="4" class="dataTables_empty"><img src="<?php echo $template; ?>/images/ajax-loader.gif" alt="Loading" /><?php echo lang('lang_js_processing'); ?></td>
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

		"aoColumns": [null,null,null,null],
		"bProcessing": true,
		"bServerSide": true,
		"sPaginationType": "full_numbers",
		"bStateSave": true,
		"bJQueryUI": true,
		"sAjaxSource": "<?php echo site_url('admin/users/grid'); ?>",
		"fnServerData": function ( sSource, aoData, fnCallback ) {
			aoData.push( { "name": "<?php echo $this->security->csrf_token_name; ?>", "value": "<?php echo $this->security->csrf_hash; ?>" } );
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