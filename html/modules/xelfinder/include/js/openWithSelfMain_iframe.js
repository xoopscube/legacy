// Open Modal iframe
var XELFINDER_URL = document.location + '';
var XOOPS_URL = XELFINDER_URL.split( '/' ).slice( 0, -3 ).join( '/' );
// alert(XOOPS_URL);
// console.log("XELFINDER_URL: " + XELFINDER_URL);
// console.log("XOOPS_URL: " + XOOPS_URL);

// Requires xScriptLoader util
var ScriptLoader = new xScriptLoader([
	// XOOPS_URL+"/common/elfinder/js/elfinder.min.js",
	XOOPS_URL+"/common/js/simplemodal/css/basic.css",
	XOOPS_URL+"/common/js/simplemodal/js/jquery.simplemodal.js",
	XOOPS_URL+"/common/js/simplemodal/js/basic.js",
]);

ScriptLoader.loadFiles();

function openWithSelfMain(url, name, w, h, returnwindow) {

	var $ = jQuery;
	var margin = $.mobile? 0 : 60;

	w = $(window).width() - margin;
	h = $(window).height() - margin;

	$.modal(
		'<iframe name="'+name+'" id="xelf_window" src="' + url +
		'" height="100%" width="100%" style="border:0;overflow:hidden;" allowtransparency="true" allowfullscreen="allowfullscreen">', {

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
			overflow:			"hidden",
			padding:			0,
			height:				"100%",
			width:				"100%"
		},
		overlayClose:			true,
		zIndex:					100000
	});

	$('#xelf_window').on( function(e){
			$(this).css({overflow: 'auto'});
			$.mobile && $('#simplemodal-container a.modalCloseImg').css({
				position: relative,
				top:0,
				right:0});
			setTimeout(function(){ e.target.contentWindow.focus(); }, 100);
		}
	);

	var resizeTimer = null;

	$(window).resize(function() {
		resizeTimer && clearTimeout(resizeTimer);
		resizeTimer = setTimeout(function() {
			$("#simplemodal-container").css({
				height: $(window).height() - margin,
				width: $(window).width() - margin,
				top: margin/2,
				left: margin/2});
		}, 200);
	});

	if (returnwindow != null){
		return $('#xelf_window');
	}
}
