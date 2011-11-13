<?php
/**
 * @file jQuery_Pretty.class.php
 * @package For legacy Cube Legacy 2.2
 * @version $Id: jQuery_Pretty.class.php ver0.01 2011/07/27  00:40:00 domifara  $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

class jQuery_Pretty extends XCube_ActionFilter
{
	public function preBlockFilter()
	{
		$this->mRoot->mDelegateManager->add('Site.JQuery.AddFunction',array(&$this, 'addScript'));
	}

	public function addScript(&$jQuery)
	{
		$jQuery->addLibrary('/common/prettyphoto/js/jquery.prettyPhoto.js', true);
		$jQuery->addStylesheet('/common/prettyphoto/css/prettyPhoto.css', true);
		$jQuery->addScript("
jQuery(document).ready(function($){
	//The img element has a child element
	$(\"img\").closest(\"a[rel^='external']\").each(function(i, elem){
		if (typeof elem != 'undefined'){
			if (typeof $(elem).attr('rel') != 'undefined'){
				if (typeof $(elem).attr('href') != 'undefined'){
					var itemSrc = $(elem).attr('href');
					if (typeof itemSrc != 'undefined' && itemSrc != ''){
						if (pretty_getFileType(itemSrc) != 'link'){
							//add a class
							$(elem).addClass('pretty');
							$(elem).attr({rel:'pretty[gallery]'});
						}
					}
				}
			}
		}
	});

	//pettyPhoto init
	$(\"a.pretty\").prettyPhoto({
		deeplinking: false,
		overlay_gallery: false,
		theme: 'facebook'
	});
	function pretty_getFileType(itemSrc){
		if (itemSrc.match(/youtube\.com\/watch/i)) {
			return 'youtube';
		}else if (itemSrc.match(/vimeo\.com/i)) {
			return 'vimeo';
		}else if(itemSrc.match(/\b.mov\b/i)){
			return 'quicktime';
		}else if(itemSrc.match(/\b.swf\b/i)){
			return 'flash';
		}else if(itemSrc.match(/\biframe=true\b/i)){
			return 'iframe';
//		}else if(itemSrc.match(/\bajax=true\b/i)){
//			return 'ajax';
//		}else if(itemSrc.match(/\bcustom=true\b/i)){
//			return 'custom';
//		}else if(itemSrc.substr(0,1) == '#'){
//			return 'inline';
		}else if(itemSrc.match(/\b.jpeg\b/i)){
			return 'image';
		}else if(itemSrc.match(/\b.jpg\b/i)){
			return 'image';
		}else if(itemSrc.match(/\b.png\b/i)){
			return 'image';
		}else if(itemSrc.match(/\b.gif\b/i)){
			return 'image';
		}else{
			return 'link';
		};
	};
});
");

	}
//class END
}
?>