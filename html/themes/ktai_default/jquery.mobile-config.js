(function($){

$.noConflict();

$(document).bind("mobileinit", function(){
//	$.mobile.ajaxEnabled = false;
	$.mobile.hashListeningEnabled = false;
//	$.mobile.touchOverflowEnabled = true;
	$.mobile.fixedToolbars.setTouchToggleEnabled(false);
});

}(jQuery));
