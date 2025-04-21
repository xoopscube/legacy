<?php
/**
 * Pico content management D3 module for XCL
 *
 * @package    Pico
 * @version    XCL 2.5.0
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2025 Authors
 * @license    GPL v2.0
 */

if ( ! defined( 'XOOPS_ROOT_PATH' ) ) {
	exit;
}

if ( ! preg_match( '/^[0-9a-zA-Z_-]+$/', $mydirname ) ) {
	exit;
}

if ( ! class_exists( 'PicoPreloadBase' ) ) {

	class PicoPreloadBase extends XCube_ActionFilter {
		public $mydirname = 'pico';

		public function postFilter() {
			$this->mRoot->mDelegateManager->add( 'Legacy_BackendAction.GetRSSItems', [ &$this, 'getRSSItems' ] );

			$this->mRoot->mDelegateManager->add( 'Ckeditor4.Utils.PreBuild_ckconfig', [ $this, 'ckeditor4PreBuild' ] );
		}

		public function getRSSItems( &$items ) {
			/*		$mydirname = $this->mydirname ;
			$module_handler =& xoops_gethandler( 'module' ) ;
			$xoopsModule =& $module_handler->getByDirname( $this->mydirname ) ;
			$xoopsDB = XoopsDatabaseFactory::getDatabaseConnection() ;
			$_GET['page'] = 'rss' ;
			include dirname(__FILE__).'/main/index.php' ;

			$items[] = array(
				'pubdate' => time() ,
				'title' => $this->mydirname ,
				'link' => 'link' ,
				'description' => 'desc' ,
				'guid' => 'guid' ,
			) ;*/
		}

		public function ckeditor4PreBuild( &$params ) {
			$mObj = $this->mRoot->mContext->mXoopsModule;

			if ( is_a( $mObj, 'XoopsModule' ) && 'pico' === $mObj->get( 'trust_dirname' ) ) {

				$params['allowhtml'] = true;

				if ( !isset( $params['switcher'] ) ) {
					$id                 = $params['id'] ?? null;
					$params['switcher'] = <<<EOD
(function(){
var f = $("#{$id}").closest("form");
// checkbox
var bbcode_c = $("#filter_enabled_xcode");
var br_c = $("#filter_enabled_nl2br");
var smiley_c = $("#filter_enabled_smiley");
var xoopsts_c = $("#filter_enabled_xoopsts");
var eval_c = $("#filter_enabled_eval");
var textwiki_c = $("#filter_enabled_textwiki");
var xoopstpl_c = $("#filter_enabled_xoopstpl");
var htmlspecialchars_c = $("#filter_enabled_htmlspecialchars");
// local func
var set = function(name, check, disable) {
	var elm = eval(name+"_c");
	if (elm) {
		(check !== null) && elm.prop("checked", check);
		(disable !== null) && elm.prop("disabled", disable);
	}
};
var enable = function() {
	return ((!eval_c || !eval_c.is(":checked")) && (!textwiki_c || !textwiki_c.is(":checked")) && (!xoopstpl_c || !xoopstpl_c.is(":checked")) && (!htmlspecialchars_c || !htmlspecialchars_c.is(":checked") || $("#{$id}").data("editor") != "html"));
}
// xcode checkbox
if (bbcode_c) {
	bbcode_c.change(function(){
		if (!$(this).is(":focus")) return;
		var change = null;
		if (enable()) {
			var obj = CKEDITOR.instances.{$id},
				conf = ckconfig_{$id};
			if ($(this).is(":checked")) {
				if ($("#{$id}").data("editor") != "bbcode") {
					change = 'bbcode';
					conf = $.extend(conf, ckconfig_bbcode_{$id});
				}
			} else if (!htmlspecialchars_c.is(":checked")) {
				change = 'html';
				conf = $.extend(conf, ckconfig_html_{$id});
			} else {
				change = 'none';
			}
			if (change) {
				$("#{$id}").data("editor", change);
				obj && obj.destroy();
				(change != "none") && CKEDITOR.replace("{$id}", conf);
			} else {
				change = "bbcode";
			}
		}
		set("br",((change && change != "none")? (change == 'bbcode') : null),(change == "bbcode" || change == "html"));
		set("htmlspecialchars",((change && change != "none")? (change == 'bbcode' && htmlspecialchars_c.is(":checked")) : null),false);
		set("smiley",((change && change != "none")? (change == 'bbcode') : null),false);
		set("xoopsts",false);
	});
}
// xoopsts checkbox
if (xoopsts_c && (!eval_c || !eval_c.is(":checked"))) {
	xoopsts_c.change(function(){
		if (!$(this).is(":focus")) return;
		var change = null;
		if (enable()) {
			var obj = CKEDITOR.instances.{$id},
			conf = ckconfig_{$id};
			if ($(this).is(":checked")) {
				if ($("#{$id}").data("editor") != "bbcode") {
					change = 'bbcode';
					conf = $.extend(conf, ckconfig_bbcode_{$id});
				}
			} else {
				change = 'html';
				conf = $.extend(conf, ckconfig_html_{$id});
			}
			if (change) {
				$("#{$id}").data("editor", change);
				obj && obj.destroy();
				CKEDITOR.replace("{$id}", conf);
			} else {
				change = "bbcode";
			}
		}
		set("br",false,!!change);
		set("htmlspecialchars",false,(change == "bbcode"));
		set("smiley",false,(change == 'bbcode'));
		set("bbcode",false);
	});
}
// htmlspecialchars checkbox
if (htmlspecialchars_c) {
	htmlspecialchars_c.change(function(e){
		if (!$(this).is(":focus")) return;
		var obj = CKEDITOR.instances.{$id},
			conf = ckconfig_{$id};
		if ($(this).is(":checked")) {
			if ($("#{$id}").data("editor") == "html") {
				$("#{$id}").data("editor", "none");
				obj && obj.destroy();
			}
		} else if (!obj) {
			$("#{$id}").data("editor", "html");
			conf = $.extend(conf, ckconfig_html_{$id});
			CKEDITOR.replace("{$id}", conf);
		}
	});
}
// eval, textwiki, xoopstpl checkboxes
eval_c.add(textwiki_c).add(xoopstpl_c).change(function(){
	if (!$(this).is(":focus")) return;
	var obj = CKEDITOR.instances.{$id};
	if ($(this).is(":checked")) {
		obj && obj.destroy();
		$("#{$id}").data("editor", "none");
		set("smiley",null,false);
		set("br",null,false);
		set("htmlspecialchars",null,false);
	} else {
		if (enable()) {
			var conf = ckconfig_{$id};
			if (bbcode_c  && bbcode_c.is(":checked")){
				change = 'bbcode';
				conf = $.extend(conf, ckconfig_bbcode_{$id});
			} else {
				set("htmlspecialchars", false);
				if (xoopsts_c && xoopsts_c.is(":checked")) {
					change = 'bbcode';
					conf = $.extend(conf, ckconfig_bbcode_{$id});
				} else {
					change = 'html';
					conf = $.extend(conf, ckconfig_html_{$id});
				}
			}
			$("#{$id}").data("editor", change);
			CKEDITOR.replace("{$id}", conf);
		}
	}
});
// form submit
f.bind("submit", function(){
	if ($("#{$id}").data("editor") == "bbcode") {
		set("br",(!xoopsts_c || !xoopsts_c.is(":checked")),false);
		set("smiley",(!xoopsts_c || !xoopsts_c.is(":checked")));
	} else if ($("#{$id}").data("editor") == "html") {
		set("br",false,false);
	}
	set("smiley",null,false);
	set("htmlspecialchars",null,false);
});
// init
if (CKEDITOR.instances.{$id}) {
	if (!enable()) {
		CKEDITOR.instances.{$id}.destroy();
		$("#{$id}").data("editor", "none");
	}
}
})();
EOD;
				}
			}
		}
	}
}

if ( ! is_numeric( $mydirname[0] ) ) {
	// If you want to name the directory from 0-9, make a site preload.
	eval( 'class ' . ucfirst( $mydirname ) . '_PicoPreload extends PicoPreloadBase { var $mydirname = \'' . $mydirname . '\' ; }' );
}
