if (typeof jQuery == 'undefind') {
	document.write ('<script type="text/javascript" src="//www.google.com/jsapi"></script>');
	google.load('jquery', '1');
}
document.write (
	'<link rel="stylesheet" href="css/popupwindow.css" type="text/css" media="all" />'
	+'<script type="text/javascript" src="popupwindow-1.8.1.js"></script>'
);