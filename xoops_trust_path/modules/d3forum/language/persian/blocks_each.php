<?php
/**
* Translation of d3forum for Persian users
*
* @copyright	      http://www.impresscms.ir/ The Persian ImpressCMS Project 
* @copyright	http://www.irxoops.org/ The Persian XOOPS support site
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @package	      Translations
* @since		 0.44
* @author		Sina Asghari (aka stranger) <pesian_stranger@users.sourceforge.net>
* @author		voltan <djvoltan@gmail.com>
* @version		$Id$
*/

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'd3forum' ;
$constpref = '_MB_' . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {

define( $constpref.'_LOADED' , 1 ) ;

// definitions for displaying blocks 
define($constpref."_FORUM","انجمن");
define($constpref."_TOPIC","گفتگو");
define($constpref."_REPLIES","پاسخ‌ها");
define($constpref."_VIEWS","بازدید");
define($constpref."_VOTESCOUNT","آراء");
define($constpref."_VOTESSUM","امتیازات");
define($constpref."_LASTPOST","آخرین پست");
define($constpref."_LASTUPDATED","آخرین به‌روز رسانی");
define($constpref."_LINKTOSEARCH","جستجو در این انجمن");
define($constpref."_LINKTOLISTCATEGORIES","صفحه‌ی اصلی انجمن‌ها");
define($constpref."_LINKTOLISTFORUMS","صفحه اصلی انجمن");
define($constpref."_LINKTOLISTTOPICS","فهرست تمام موضوع‌ها");
define($constpref.'_ALT_UNSOLVED','گفتگوی حل نشده');
define($constpref.'_ALT_MARKED','گفتگوی نشانه دار');

}

?>