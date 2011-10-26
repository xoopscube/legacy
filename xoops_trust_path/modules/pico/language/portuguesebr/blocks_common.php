<?php

// definitions for editing blocks

define("_MB_PICO_CATLIMIT","Especificar a categoria (s)");
define("_MB_PICO_CATLIMITDSC","Em branco significa todas as categorias. 0 significa a categoria TOP. Você pode especificar múltiplas categorias através de números seperados com vírgula.");
define("_MB_PICO_PARENTCAT","Categoria pai");
define("_MB_PICO_PARENTCATDSC","Serão mostradas subcategorias diretamente pertencentes a esta categoria pai. Você pode especificar múltiplas categorias pai através de números separados com vírgula.");
define("_MB_PICO_SELECTORDER","Ordenar por");
define("_MB_PICO_CONTENTSNUM","Número de itens que é mostrado");
define("_MB_PICO_THISTEMPLATE","Modelo (recurso)do bloco");
define("_MB_PICO_DISPLAYBODY","Mostrar o corpo do conteúdo também");
define("_MB_PICO_CONTENT_ID","Conteúdo ID");
define("_MB_PICO_PROCESSBODY","Processar o corpo do conteúdo dinamicamente");
define("_MB_PICO_TAGSNUM","Mostrar");
define("_MB_PICO_TAGSLISTORDER","Ordem de exibição");
define("_MB_PICO_TAGSSQLORDER","Ordem de extração");

// LTR or RTL
if( defined( '_ADM_USE_RTL' ) ) {
	@define( '_ALIGN_START' , _ADM_USE_RTL ? 'right' : 'left' ) ;
	@define( '_ALIGN_END' , _ADM_USE_RTL ? 'left' : 'right' ) ;
} else {
	@define( '_ALIGN_START' , 'left' ) ; // change it right for RTL
	@define( '_ALIGN_END' , 'right' ) ;  // change it left for RTL
}



?>
