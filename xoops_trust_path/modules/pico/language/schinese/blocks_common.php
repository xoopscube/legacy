<?php

// definitions for editing blocks



// Appended by Xoops Language Checker -GIJOE- in 2008-09-17 13:09:56
define('_MB_PICO_PROCESSBODY','Process body of the content dynamically');

// Appended by Xoops Language Checker -GIJOE- in 2008-04-23 04:51:12
define('_MB_PICO_TAGSNUM','Display');
define('_MB_PICO_TAGSLISTORDER','Order for displaying');
define('_MB_PICO_TAGSSQLORDER','Order for extracting');

// Appended by Xoops Language Checker -GIJOE- in 2007-06-15 05:03:02
define('_MB_PICO_PARENTCAT','Parent category');
define('_MB_PICO_PARENTCATDSC','Subcategories directly belonging to this parent category will be displayed. you can specify parent categories multiply by numbers separated with comma.');

define("_MB_PICO_CATLIMIT","指定类别");
define("_MB_PICO_CATLIMITDSC","空为全部类别，0为顶层。您可以指定多个类别，以逗号 (,) 作为分隔。");
define("_MB_PICO_SELECTORDER","排序");
define("_MB_PICO_CONTENTSNUM","显示的条目数");
define("_MB_PICO_THISTEMPLATE","区块模板 (源文件)");
define("_MB_PICO_DISPLAYBODY","同时显示正文内容");
define("_MB_PICO_CONTENT_ID","文章ID");


// LTR or RTL
if( defined( '_ADM_USE_RTL' ) ) {
	@define( '_ALIGN_START' , _ADM_USE_RTL ? 'right' : 'left' ) ;
	@define( '_ALIGN_END' , _ADM_USE_RTL ? 'left' : 'right' ) ;
} else {
	@define( '_ALIGN_START' , 'left' ) ; // change it right for RTL
	@define( '_ALIGN_END' , 'right' ) ;  // change it left for RTL
}


?>
