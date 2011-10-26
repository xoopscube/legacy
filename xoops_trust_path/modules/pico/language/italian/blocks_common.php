<?php
//traduzione italiana di evoc cadelsanto@gmail.com www.cadelsanto.org
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

// Appended by Xoops Language Checker -GIJOE- in 2007-05-14 04:45:30
define('_MB_PICO_DISPLAYBODY','Display content body also');

define("_MB_PICO_CATLIMIT","Specifica categoria/e");
define("_MB_PICO_CATLIMITDSC","blank significa tutte le categorie. 0 significa la categoria TOP. Tu puoi specificare categorie multiple con numeri separati con una virgola.");
define("_MB_PICO_SELECTORDER","Ordina per");
define("_MB_PICO_CONTENTSNUM","Numeri di articoli da mostrare");
define("_MB_PICO_THISTEMPLATE","Template (risorsa) del blocco");
define("_MB_PICO_CONTENT_ID","Content ID");


// LTR or RTL
if( defined( '_ADM_USE_RTL' ) ) {
	@define( '_ALIGN_START' , _ADM_USE_RTL ? 'right' : 'left' ) ;
	@define( '_ALIGN_END' , _ADM_USE_RTL ? 'left' : 'right' ) ;
} else {
	@define( '_ALIGN_START' , 'left' ) ; // change it right for RTL
	@define( '_ALIGN_END' , 'right' ) ;  // change it left for RTL
}


?>
