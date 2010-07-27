$(document).ready(function(){
	if($('#checkbox').length) {
		$("#checkbox").click(function()	{
			var checked_status = this.checked;
			$("input[@type='checkbox']").each(function() {
				this.checked = checked_status;
			});
		});
	}
	$("a.delete").click(function(){
		removemsg = "<?php echo lang('lang_are_you_sure'); ?>"
		var answer = confirm(removemsg);
		if (answer) {
			return true;
		}
		return false;
	});
	$(".ed_img").fancybox({
		'type' : 'iframe'
	})
});

function deleteSomething(url){
	removemsg = "<?php echo lang('lang_are_you_sure'); ?>"
	if (confirm(removemsg)) { document.location = url; }
}
