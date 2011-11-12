<?php /* Brazilian Portuguese Translation by Marcelo Yuji Himoro <http://yuji.ws> */
if( ! defined( 'XP2_BLOCK_LANG_INCLUDED' ) ) {
	define( 'XP2_BLOCK_LANG_INCLUDED' , 1 ) ;
// general	
	define("_MB_XP2_COUNT",'N° de leituras');
	define("_MB_XP2_COUNT_ZERO_ALL",'N° de leituras (0 = exibir tudo)');
	define("_MB_XP2_LENGTH","Tamanho");
	define("_MB_XP2_ALL","Tudo");
	define("_MB_XP2_BLOCK_CACHE_ERR","O cache ainda não existe.<br />Acesse o módulo %s primeiro.");
	define("_MB_XP2_SHOW_NUM_OF_POST","N° de posts a exibir");
	define("_MB_XP2_SHOW_DROP_DOWN","Exibir lista selecionável");
	define("_MB_XP2_HIDE_EMPTY","Excluir da lista categorias vazias");
	define("_MB_XP2_TITLE","Título");
	define("_MB_XP2_PUBLISH_DATE","Data de publicação");
	define("_MB_XP2_SORT_ORDER","Ordem de exibição");
	define("_MB_XP2_SORT_ASC","Crescente");
	define("_MB_XP2_SORT_DESC","Decrescente");
	define("_MB_XP2_SHOW_DATE_SELECT","Exibir data");
	define("_MB_XP2_SHOW_DATE_NONE","Não exibir");
	define("_MB_XP2_SHOW_POST_DATE","Exibir data de publicação");
	define("_MB_XP2_SHOW_MODIFY_DATE","Exibir data de atualização");
	define("_MB_XP2_SHOW_DATE","Exibir data");
	define("_MB_XP2_DATE_FORMAT","Formato da data (deixe em branco para utilizar configurações do WordPress)");
	define("_MB_XP2_TIME_FORMAT","Formato da hora (deixe em branco para utilizar configurações do WordPress)");
	define("_MB_XP2_FLAT","Plano");
	define("_MB_XP2_LIST","Lista");
	define("_MB_XP2_FILE_NAME","Nome do arquivo");
	define("_MB_XP2_THISTEMPLATE","Modelo");
	define("_MB_XP2_NO_JSCRIPT","Javascript deveria ser habilite por um browser.");
	define("_MB_XP2_CACHE_NOT_WRITABLE","Diretório de Cache não é nenhum writable.");
	
// recent comment block	
	define("_MB_XP2_COMM_DISP_AUTH","Exibir autor do comentário");
	define("_MB_XP2_COMM_DISP_TYPE","Exibir tipo de comentário");
	define("_MB_XP2_COM_TYPE","Selecione o tipo de comentário a ser exibido");
	define("_MB_XP2_COMMENT","Comentário");
	define("_MB_XP2_TRUCKBACK","Trackback");
	define("_MB_XP2_PINGBACK","Pingback");
	
// recent posts content
	define("_MB_XP2_P_EXCERPT","Exibir post pelo excerto.");
	define("_MB_XP2_P_EXCERPT_SIZE","N° máx. de caracteres do post");
	define("_MB_XP2_CATS_SELECT","Selecionar a(s) categoria(s)");
	define("_MB_XP2_TAGS_SELECT","Selecionar a(s) tag(s) (separadas por ,)");
	define("_MB_XP2_DAY_SELECT","Select Post Date");
	define("_MB_XP2_NONE","None");
	define("_MB_XP2_TODAY","Hoje");
	define("_MB_XP2_LATEST","Latest");
	define("_MB_XP2_DAY_BETWEEN","Entre");
	define("_MB_XP2_DAYS_AND","e");
	define("_MB_XP2_DAYS_AGO","dias atrás");
	define("_MB_XP2_CATS_DIRECT_SELECT","Contribuição direta de ID(Vírgula separou lista de categorie ID)");
	
// recent posts list	
	define("_MB_XP2_REDNEW_DAYS","N° de dias para exibir sinal vermelho de \"NOVO\"");
	define("_MB_XP2_GREENNEW_DAYS","N° de dias para exibir sinal verde de \"NOVO\"");	

// calender		
	define("_MB_XP2_SUN_COLOR","Cor do domingo");
	define("_MB_XP2_SAT_COLOR","Cor do sábado");
	
// popular		
	define("_MB_XP2_MONTH_RANGE","Exibir posts de um mês específico (0 = mostrar tudo)");
	
// archives
	define("_MB_XP2_ARC_TYPE","Tipos de arquivo");
	define("_MB_XP2_ARC_YEAR","Arquivo por ano");
	define("_MB_XP2_ARC_MONTH","Arquivo por mês");
	define("_MB_XP2_ARC_WEEK","Arquivo por semana");
	define("_MB_XP2_ARC_DAY","Arquivo por dia");
	define("_MB_XP2_ARC_POST","Arquivo por post");

// authors	
	define("_MB_XP2_EXCLUEDEADMIN","Excluir administrador da lista");
	define("_MB_XP2_SHOW_FULLNAME","Exibir nome completo do autor");

// page 	
	define("_MB_XP2_PAGE_ORDERBY","Ordenar páginas por");
	define("_MB_XP2_PAGE_TITLE","Título");
	define("_MB_XP2_PAGE_MENU_ORDER","Páginas");
	define("_MB_XP2_PAGE_POST_DATE","Data de criação");
	define("_MB_XP2_PAGE_POST_MODIFY","Data de atualização");
	define("_MB_XP2_PAGE_ID","ID da página");
	define("_MB_XP2_PAGE_AUTHOR","ID do autor");
	define("_MB_XP2_PAGE_SLUG","Slug da página");
	define("_MB_XP2_PAGE_EXCLUDE","Excluir páginas por ID (separados por ,)");
	define("_MB_XP2_PAGE_EXCLUDE_TREE","Excluir páginas por ID e todas as suas sub-páginas (separados por ,)");
	define("_MB_XP2_PAGE_INCLUDE","Exibir apenas as páginas indicadas por ID (separados por ,)");
	define("_MB_XP2_PAGE_DEPTH","Nível máx. de sub-páginas a serem exibidas na lista (0 = todos os níveis)");
	define("_MB_XP2_PAGE_CHILD_OF","Nível máx. de sub-páginas a serem exibidas para páginas indicadas por ID (0 = todos os níveis)");
	define("_MB_XP2_PAGE_HIERARCHICAL","Identar quando mostrar sub-páginas.");
	define("_MB_XP2_PAGE_META_KEY","Exibir apenas páginas contendo a seguinte chave de campo personalizado");
	define("_MB_XP2_PAGE_META_VALUE","Exibir apenas páginas contendo o seguinte valor de campo personalizad");
	
// Search
	define("_MB_XP2_SEARCH_LENGTH","Tamanho da caixa de pesquisa");
	
// tag cloud
	define("_MB_XP2_CLOUD_SMALLEST",'Tamanho mínimo da fonte utilizada para exibir as tags');
	define("_MB_XP2_CLOUD_LARGEST",'Tamanho máximo da fonte utilizada para exibir as tag');
	define("_MB_XP2_CLOUD_UNIT","Unidades: pt, px, em, %, etc.");
	define("_MB_XP2_CLOUD_NUMBER","N° máx. de tags a serem exibidas na nuvem. (0 = mostrar tudo)");
	define("_MB_XP2_CLOUD_FORMAT","Formato de exibição da nuvem");
	define("_MB_XP2_CLOUD_ORDERBY","Ordenar tags por");
	define("_MB_XP2_CLOUD_ORDER","Ordem de exibição (randômico apenas para WordPress 2.5 ou superior)");
	define("_MB_XP2_CLOUD_EXCLUDE","Excluir tags pelo term_id (separado por ,)");
	define("_MB_XP2_CLOUD_INCLUDE","Exibir apenas as tags indicadas por term_id (separados por ,; deixe em branco para todas)");
	define("_MB_XP2_RAND","randômico");
	define("_MB_XP2_TAG_NAME","nome");
	define("_MB_XP2_TAG_COUNT","n° de posts");
	
// Categorie
	define("_MB_XP2_CAT_ALL_STR","Texto do link para todas as categorias (deixe em branco para não exibir)");
	define("_MB_XP2_CAT_ORDERBY","Ordenar categorias por");
	define("_MB_XP2_CAT_NAME","nome");
	define("_MB_XP2_CAT_COUNT","n° de posts");
	define("_MB_XP2_CAT_ID","ID");
	define("_MB_XP2_SHOW_LAST_UPDATE","Exibir para cada categoria data de último post atuaizado");
	define("_MB_XP2_CAT_HIDE_EMPTY","Ocultar categorias vazias");
	define("_MB_XP2_DESC_FOR_TITLE","Exibir descrição da categoria no título");
	define("_MB_XP2_CAT_EXCLUDE","Excluir categorias por ID (seaparadas por ,)");
	define("_MB_XP2_CAT_INCLUDE","Exibir apenas as categorias indicdas por ID (separadas por ,)");
	define("_MB_XP2_CAT_HIERARCHICAL","Identar quando exibir sub-categorias.");
	define("_MB_XP2_CAT_DEPTH","Nível max. de sub-categorias a seerem exibidas na lista (0 = todos os níveis）");
	
// meta 
	define("_MB_XP2_META_WP_LINK","Exibir link para o site do WordPress");
	define("_MB_XP2_META_XOOPS_LINK","Exibir link para o site do XOOPS");
	define("_MB_XP2_META_POST_RSS","Exibir RSS dos posts");
	define("_MB_XP2_META_COMMENT_RSS","Exibir RSS dos comentários");
	define("_MB_XP2_META_POST_NEW","Exibir \"Novo post\"");
	define("_MB_XP2_META_ADMIN","Exibir \"Administração do site\"");
	define("_MB_XP2_META_README","Exibir leia-me");
	define("_MB_XP2_META_CH_STYLE","Exibir seleção de modo de exibição");

// widget 
	define("_MB_XP2_SELECT_WIDGET","Selecione os widgets a serem exibidos");
	define("_MB_XP2_NO_WIDGET","Nenhum widget selecionado no WordPress.");
	define("_MB_XP2_WIDGET_TITLE_SHOW","Caso apenas um widget seja selecionado, exibir título do widget");
	
	
// custom 
	define("_MB_XP2_ENHACED_FILE","Nome do rquivo a ser exibido no bloco personalizado.");
	define("_MB_XP2_MAKE_ENHACED_FILE","Crie o arquivo no diretório de blocos dentro do diretório do tema.");

// blog_list
	define("_MB_XP2_BLOG_ORDERBY","Ordenar blogs por");
	define("_MB_XP2_BLOG_NAME","nome");
	define("_MB_XP2_BLOG_COUNT","n° de posts");
	define("_MB_XP2_BLOG_ID","ID");
// global_blog_list
	define("_MB_XP2_SHOW_BLOGS_SELECT","Selecione Exibir Blogs");
	define("_MB_XP2_EXCLUSION_BLOGS_SELECT","Selecione Blogs Exclusão");
	define("_MB_XP2_BLOGS_DIRECT_SELECT","Entrada direta de ID (lista separada por vírgulas de ID blog)");
	define("_MB_XP2_SHOWN_FOR_EACH_BLOG","Mostrados para cada blog");

}
?>
