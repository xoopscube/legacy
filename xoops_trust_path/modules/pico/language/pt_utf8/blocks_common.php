<?php
// Translation Info
// *************************************************************** //
// ############################################################### //
// ## XOOPS Cube Legacy 2.1 - Tradução para o Português
// ############################################################### //
// ## Por............: Mikhail Miguel
// ## E-mail.........: mikhail@underpop.com
// ## Website........: http://xoopscube.com.br
// ############################################################### //
// *************************************************************** //


// Appended by Xoops Language Checker -GIJOE- in 2008-09-17 13:09:57
define('_MB_PICO_PROCESSBODY','Process body of the content dynamically');

// Appended by Xoops Language Checker -GIJOE- in 2008-04-23 04:51:13
define('_MB_PICO_TAGSNUM','Display');
define('_MB_PICO_TAGSLISTORDER','Order for displaying');
define('_MB_PICO_TAGSSQLORDER','Order for extracting');

define("_MB_PICO_CATLIMIT","Especifique as categorias");
define("_MB_PICO_CATLIMITDSC","Em branco significa todas as categorias, e 0 (zero) significa a categoria principal. Você pode especificar categorias com números separados por vírgulas.");
define("_MB_PICO_CONTENTSNUM","Número de itens a serem mostrados");
define("_MB_PICO_CONTENT_ID","Número do artigo");
define("_MB_PICO_DISPLAYBODY","Também mostrar o corpo do artigo");
define("_MB_PICO_PARENTCAT","Categoria anterior");
define("_MB_PICO_PARENTCATDSC","Serão mostradas todas as subcategorias desta categoria principal. Você pode especificar as categorias principais escrevendo os seus respectivos números separados com vírgula.");
define("_MB_PICO_SELECTORDER","Ordenar por");
define("_MB_PICO_THISTEMPLATE","Modelo do bloco");

// LTR or RTL
if( defined( '_ADM_USE_RTL' ) ) {
	@define( '_ALIGN_START' , _ADM_USE_RTL ? 'right' : 'left' ) ;
	@define( '_ALIGN_END' , _ADM_USE_RTL ? 'left' : 'right' ) ;
} else {
	@define( '_ALIGN_START' , 'left' ) ; // change it right for RTL
	@define( '_ALIGN_END' , 'right' ) ;  // change it left for RTL
}



?>
