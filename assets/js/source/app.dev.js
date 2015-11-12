(function ($) {
	"use strict";
	
	$(function() {
    
    // fix for z-index issue for iframe IE9
    $('iframe').each(function(){
        var url = $(this).attr("src");
        $(this).attr("src",url+"&wmode=transparent");
    });
   

	});
}(jQuery));
