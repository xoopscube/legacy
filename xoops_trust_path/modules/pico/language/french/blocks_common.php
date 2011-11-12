<?php

// definitions for editing blocks




// Appended by Xoops Language Checker -GIJOE- in 2008-09-17 13:09:56
define('_MB_PICO_PROCESSBODY','Process body of the content dynamically');

// Appended by Xoops Language Checker -GIJOE- in 2008-04-23 04:51:12
define('_MB_PICO_TAGSNUM','Display');
define('_MB_PICO_TAGSLISTORDER','Order for displaying');
define('_MB_PICO_TAGSSQLORDER','Order for extracting');

// Appended by Xoops Language Checker -GIJOE- in 2007-06-15 05:03:01
define('_MB_PICO_PARENTCAT','Parent category');
define('_MB_PICO_PARENTCATDSC','Subcategories directly belonging to this parent category will be displayed. you can specify parent categories multiply by numbers separated with comma.');

// Appended by Xoops Language Checker -GIJOE- in 2007-05-14 04:45:29
define('_MB_PICO_DISPLAYBODY','Display content body also');

define("_MB_PICO_CATLIMIT","Indiquez the catégorie(s)");
define("_MB_PICO_CATLIMITDSC","laisser en blanc pour toutes les catégories. 0 pourla catégorie TOP. vous pouvez indiquer de multiplies nombres de catégories, séparés par une virgule.");
define("_MB_PICO_SELECTORDER","Classer par");
define("_MB_PICO_CONTENTSNUM","Nombre d'articles à montrer");
define("_MB_PICO_THISTEMPLATE","Template(resource) du block");
define("_MB_PICO_CONTENT_ID","ID Contenu");


// LTR or RTL
if( defined( '_ADM_USE_RTL' ) ) {
	@define( '_ALIGN_START' , _ADM_USE_RTL ? 'right' : 'left' ) ;
	@define( '_ALIGN_END' , _ADM_USE_RTL ? 'left' : 'right' ) ;
} else {
	@define( '_ALIGN_START' , 'left' ) ; // change it right for RTL
	@define( '_ALIGN_END' , 'right' ) ;  // change it left for RTL
}


?>
