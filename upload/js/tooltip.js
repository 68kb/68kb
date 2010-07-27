/*
 * Tooltip script 
 * powered by jQuery (http://www.jquery.com)
 * 
 * written by Alen Grakalic (http://cssglobe.com)
 * 
 * for more info visit http://cssglobe.com/post/1695/easiest-tooltip-and-image-preview-using-jquery
 *
 */
this.tooltip = function(){	
	/* CONFIG */		
		xOffset = 10;
		yOffset = 20;		
		// these 2 variable determine popup's distance from the cursor
		// you might want to adjust to get the right result		
	/* END CONFIG */		
	$("a.tooltip").hover(function(e){											  
		this.t = this.title;
		this.title = "";									  
		$("body").append("<p id='tooltip'>"+ this.t +"</p>");
		$("#tooltip")
			.css("top",(e.pageY - xOffset) + "px")
			.css("left",(e.pageX + yOffset) + "px")
			.fadeIn("fast");		
    },
	function(){
		this.title = this.t;		
		$("#tooltip").remove();
    });	
	$("a.tooltip").mousemove(function(e){
		$("#tooltip")
			.css("top",(e.pageY - xOffset) + "px")
			.css("left",(e.pageX + yOffset) + "px");
	});			
};



// starting the script on page load
$(document).ready(function(){
	tooltip();
});

$(document).ready(function() {
	$("#checkbox").click(function()				
	{
		var checked_status = this.checked;
		$("form :checkbox").each(function()
		{
			this.checked = checked_status;
		});
	});
});