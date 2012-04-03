var XOOPS_URL;
var XELFINDER_URL;

(function (){
	var scripts = document.getElementsByTagName("head")[0].getElementsByTagName("script");
	var i = scripts.length;
	while (i--) {
		var match = scripts[i].src.match(/^((.+)\/[^\/]+\/[^\/]+)\/include\/js\/openWithSelfMain_iframe\.js$/);
		if (match) {
			XELFINDER_URL = match[1];
			XOOPS_URL = match[2];
			break;
		}
	}
	if (typeof jQuery == 'undefined') {
		document.write (
			'<script src="'+XOOPS_URL+'/common/elfinder/jquery/jquery-1.7.1.min.js" type="text/javascript" charset="utf-8"></script>'
				+
			'<script src="'+XOOPS_URL+'/common/elfinder/jquery/jquery-ui-1.8.16.custom.min.js" type="text/javascript" charset="utf-8"></script>'
				+
			'<link rel="stylesheet" href="'+XOOPS_URL+'/common/elfinder/jquery/ui-themes/smoothness/jquery-ui-1.8.16.custom.css" type="text/css" media="screen" charset="utf-8">'
		);
	}
	document.write (
		'<link rel="stylesheet" href="'+XOOPS_URL+'/common/js/simplemodal/css/basic.css" type="text/css" media="screen" />'
		+'<script defer="defer" type="text/javascript" src="'+XOOPS_URL+'/common/js/simplemodal/js/jquery.simplemodal.js"></script>'
		+'<script defer="defer" type="text/javascript" src="'+XOOPS_URL+'/common/js/simplemodal/js/basic.js"></script>'
		+'<script defer="defer" type="text/javascript">jQuery.noConflict();</script>'
	);
})();

function openWithSelfMain(url, name, w, h, returnwindow) {
	var $ = jQuery;
	
	w = $(window).width() - 40;
	h = $(window).height() - 40;
	$.modal('<iframe name="'+name+'" id="xelf_window" src="' + url + '" height="'+h+'" width="'+w+'" style="border:0;overflow:hidden;" allowtransparency="true" scrolling="no" frameborder="0">', {
		containerCss:{
			backgroundColor:	"transparent",
			borderColor:		"transparent",
			border:				"none",
			backgroundImage:	"url('"+XELFINDER_URL+"/images/manager_loading.gif')",
			backgroundRepeat:	"no-repeat",
			backgroundPosition: "center center",
			padding:			0,
			height:				h,
			width:				w
		},
		dataCss:{
			padding:			0
		},
		overlayClose:		 true
	});

	$('#xelf_window').load(
		function(e){
			setTimeout(function(){ e.target.contentWindow.focus(); }, 100);
		}
	);

	if (returnwindow != null){
		return $('#xelf_window');
	}
}
