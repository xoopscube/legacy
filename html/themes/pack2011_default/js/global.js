/**
* MediaQuery MultiDevice Template ver 0.1��
* @Author: funnythingz
* @Url:    http://hiropo.co.uk
*
* global.js
*/

//============================================================
// GlobalObject
// ProjectName
//
if( typeof(ProjectName) == 'undefined' || !ProjectName ){
	var ProjectName = {}
}

(function($) {

//============================================================
// Module Object
//

/**
* Module Method
*/
ProjectName.MOD = {
	print_r: function(rtn){
		return document.write(rtn + "\n");
	}
}


/**
* UserAgent
*/
ProjectName.ua = {
	Android: navigator.userAgent.indexOf('Linux; U; Android ')!=-1,
	Honeycomb: navigator.userAgent.indexOf('HONEYCOMB')!=-1,
	GalaxyTab: navigator.userAgent.indexOf('SC-01C')!=-1,
	iPhone: navigator.userAgent.indexOf('iPhone')!=-1,
	iPad: navigator.userAgent.indexOf('iPad')!=-1,
	WP7: navigator.userAgent.indexOf('Windows Phone OS 7')!=-1
}

/**
* Viewport writing
*/
ProjectName.META = {
	iOS: function(){
		var rtn = '<meta name="apple-mobile-web-app-capable" content="yes">' + "\n"
				+ '<meta name="format-detection" content="telephone=no">' + "\n"
				+ '<meta name="viewport" content="width=device-width, user-scalable=yes, initial-scale=1.0">' + "\n"
				+ '<link rel="apple-touch-icon" href="../images/logo.png">' + "\n"
		;
		return rtn;
	}
}

/**
* PARTS
*/
ProjectName.PARTS = {
	scrollAnchor: function(){
		$('a[href^=#]').click(function() {
			var speed = 400,
				href = $(this).attr("href"),
				target = $(href == "#" || href == "" ? 'html' : href),
				position = target.offset().top
			;
			$($.browser.safari ? 'body' : 'html').animate({scrollTop:position}, speed, 'swing');
			return false;
		});
	},
	hideAdBar: function(){
		setTimeout("scrollTo(0,1)", 100);
	}
}
})(jQuery);
