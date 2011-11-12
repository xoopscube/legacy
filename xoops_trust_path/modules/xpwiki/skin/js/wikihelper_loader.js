/*
 * easy wikihelper loader for any form.
 */
(function () {
	if (typeof(XpWiki) == 'undefined') {
		var lang;
		if(document.all) {
			lang = (navigator.userLanguage || navigator.browserLanguage);
		} else {
			lang = navigator.language;
		}
		lang = lang.substr(0,2);
		if (! lang.match(/(en|de|ja)/)) {
			lang = 'en';
		}
		var charset = 'charset="ISO-8859-1" ';
		if (lang == 'ja') {
			charset = 'charset="EUC-JP" ';
		}
		// load default.*.js
		document.write ('<script type="text/javascript" ' + charset + 'src="$wikihelper_root_url/skin/loader.php?src=default.'+lang+'.js"></script>');
	}
})();
