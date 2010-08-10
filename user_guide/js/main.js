/*-----------------------
* jQuery Plugin: Scroll to Top
* by Craig Wilson, Ph.Creative (http://www.ph-creative.com)
* 
* Copyright (c) 2009 Ph.Creative Ltd.
* Description: Adds an unobtrusive "Scroll to Top" link to your page with smooth scrolling.
* For usage instructions and version updates to go http://blog.ph-creative.com/post/jquery-plugin-scroll-to-top.aspx
* 
* Version: 1.0, 12/03/2009
-----------------------*/
$(function(){$.fn.scrollToTop=function(){$(this).hide().removeAttr("href");if($(window).scrollTop()!="0"){$(this).fadeIn("slow")}var scrollDiv=$(this);$(window).scroll(function(){if($(window).scrollTop()=="0"){$(scrollDiv).fadeOut("slow")}else{$(scrollDiv).fadeIn("slow")}});$(this).click(function(){$("html, body").animate({scrollTop:0},"slow")})}});


/*-----------------------
* Adapted from "Build An Incredible Login Form With jQuery".
* http://net.tutsplus.com/javascript-ajax/build-a-top-panel-with-jquery/
-----------------------*/
$(document).ready(function() {
	$("div.panel_button").click(function(){
		$("div#panel").animate({
			height: "375px"
		})
		.animate({
			height: "275px"
		}, "fast");
		$("div.panel_button").toggle();
		return false;
	});	
	
	$("div#hide_button").click(function(){
		$("div#panel").animate({
			height: "0px"
		}, "fast");
		return false;
	});	
	
	/*-----------------------
	* Scroll to to top
	-----------------------*/
	$(function() {
		$(".top a").scrollToTop();
	});
	
	/*-----------------------
	* Input value replacement
	-----------------------*/
	function textReplacement(input){
		var originalvalue = input.val();
		input.focus( function(){
			if( $.trim(input.val()) == originalvalue ){ input.val(''); }
		});
		input.blur( function(){
			if( $.trim(input.val()) == '' ){ input.val(originalvalue); }
		});
	}
	textReplacement($('#search'));
});