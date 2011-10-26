<?php

// definitions for editing blocks



// Appended by Xoops Language Checker -GIJOE- in 2008-09-17 13:09:57
define('_MB_PICO_PROCESSBODY','Process body of the content dynamically');

// Appended by Xoops Language Checker -GIJOE- in 2008-04-23 04:51:13
define('_MB_PICO_TAGSNUM','Display');
define('_MB_PICO_TAGSLISTORDER','Order for displaying');
define('_MB_PICO_TAGSSQLORDER','Order for extracting');

define('_MB_PICO_PARENTCAT','Catégorie Parent');
define('_MB_PICO_PARENTCATDSC','Les sous-catégories de chaque catégorie Parent seront affichées. Vous pouvez indiquer de multiples catégories Parent avec leur nombres séparés par une virgule.');
define('_MB_PICO_DISPLAYBODY','Afficher également le corps du document (body)');
define("_MB_PICO_CATLIMIT","Indiquez la(es) catégorie(s)");
define("_MB_PICO_CATLIMITDSC","laisser en blanc pour toutes les catégories. 0 pour la catégorie TOP. Vous pouvez indiquer de multiples catégories avec leur nombres séparés par une virgule.");
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
