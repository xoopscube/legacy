<?php /* Brazilian Portuguese Translation by Marcelo Yuji Himoro <http://yuji.ws> */
if( ! defined( 'XP2_MODINFO_LANG_INCLUDED' ) ) {
	define( 'XP2_MODINFO_LANG_INCLUDED' , 1 ) ;

	// The name of this module admin menu
	define("_MI_XP2_MENU_SYS_INFO","Informações de sistema");
	define("_MI_XP2_MENU_BLOCK_ADMIN","Blocos/permissões");
	define("_MI_XP2_MENU_BLOCK_CHECK","Checar blocos");
	define("_MI_XP2_MENU_WP_ADMIN","Administração do WordPress");
	define("_MI_XP2_MOD_ADMIN","Administração do módulo");

	// The name of this module
	define("_MI_XP2_NAME","Blog");

	// A brief description of this module
	define("_MI_XP2_DESC","Port do WordPressME para o XOOPS.");

	// Sub menu titles
	define("_MI_XP2_MENU_POST_NEW","Novo post");
	define("_MI_XP2_MENU_EDIT","Editar post");
	define("_MI_XP2_MENU_ADMIN","Administração do WordPress");
	define("_MI_XP2_MENU_XPRESS","Configurações do XPressME");
	define("_MI_XP2_MENU_TO_MODULE","Ir para o módulo");
	define("_MI_XP2_TO_UPDATE","atualização");

	// Module Config
	define("_MI_LIBXML_PATCH","Force um remendo para o bicho de libxml2 em um bloco");
	define("_MI_LIBXML_PATCH_DESC","libxml2 Ver 2.70-2.72 têm o bicho que' < ' e' > ' é afastado. 
XPressME adquire uma versão de libxml2 automaticamente, e é adaptado um remendo se for necessário. 
Quando XPressME não puder adquirir uma versão de libxml2, o senhor pode deixar um remendo isto ajustar violentamente com esta opção.");
	
	define("_MI_MEMORY_LIMIT","Tamanho de memória (MB) pelo menos necessário para módulo");
	define("_MI_MEMORY_LIMIT_DESC","php.iniのmemory_limit値がこの値より小さいとき、可能であればini_set('memory_limit', Value);を実行しmemory_limitを再設定する");

	// Block Name
	define("_MI_XP2_BLOCK_COMMENTS","Comentários recentes");
	define("_MI_XP2_BLOCK_CONTENT","Posts recentes com conteúdo");
	define("_MI_XP2_BLOCK_POSTS","Posts recentes");
	define("_MI_XP2_BLOCK_CALENDER","Calendário");
	define("_MI_XP2_BLOCK_POPULAR","Posts mais lidos");
	define("_MI_XP2_BLOCK_ARCHIVE","Arquivo");
	define("_MI_XP2_BLOCK_AUTHORS","Autores");
	define("_MI_XP2_BLOCK_PAGE","Páginas");
	define("_MI_XP2_BLOCK_SEARCH","Pesquisa");
	define("_MI_XP2_BLOCK_TAG","Nuvem de tags");
	define("_MI_XP2_BLOCK_CATEGORY","Categorias");
	define("_MI_XP2_BLOCK_META","Meta");
	define("_MI_XP2_BLOCK_SIDEBAR","Barra lateral");
	define("_MI_XP2_BLOCK_WIDGET","Widgets");
	define("_MI_XP2_BLOCK_ENHANCED","Bloco avançado");
	define("_MI_XP2_BLOCK_BLOG_LIST","Blogs Lista");
	define("_MI_XP2_BLOCK_GLOBAL_POSTS","Posts recentes(Todo o blog)");
	define("_MI_XP2_BLOCK_GLOBAL_COMM","Comentários recentes(Todo o blog)");
	define("_MI_XP2_BLOCK_GLOBAL_POPU","Posts mais lidos(Todo o blog)");

	// Notify Categories
	define('_MI_XP2_NOTCAT_GLOBAL', 'TUDO');
	define('_MI_XP2_NOTCAT_GLOBALDSC', 'Opções de aviso para o blog inteiro.');
	define('_MI_XP2_NOTCAT_CAT', 'Categoria selecionada');
	define('_MI_XP2_NOTCAT_CATDSC', 'Opções de aviso para a categoria selecionada.');
	define('_MI_XP2_NOTCAT_AUTHOR', 'Autor selecionado'); 
	define('_MI_XP2_NOTCAT_AUTHORDSC', 'Opções de aviso para o autor selecionado.');
	define('_MI_XP2_NOTCAT_POST', 'Post atual'); 
	define('_MI_XP2_NOTCAT_POSTDSC', 'Opções de aviso para o post atual.');

	// Each Notifications
	define('_MI_XP2_NOTIFY_GLOBAL_WAITING', 'Aguardando moderação');
	define('_MI_XP2_NOTIFY_GLOBAL_WAITINGCAP', 'Avisos sobre posts e edições que necessitam moderação (para administradores).');
	define('_MI_XP2_NOTIFY_GLOBAL_WAITINGSBJ', '[{X_SITENAME}] {X_MODULE}: aguardando moderação');

	define('_MI_XP2_NOTIFY_GLOBAL_NEWPOST', 'Novo post');
	define('_MI_XP2_NOTIFY_GLOBAL_NEWPOSTCAP', 'Aviso sobre novo post publicados no blog.');
	define('_MI_XP2_NOTIFY_GLOBAL_NEWPOSTSBJ', '[Post em {XPRESS_BLOG_NAME}]: "{XPRESS_POST_TITLE}"');

	define('_MI_XP2_NOTIFY_GLOBAL_NEWCOMMENT', 'Novo comentário');
	define('_MI_XP2_NOTIFY_GLOBAL_NEWCOMMENTCAP', 'Aviso sobre novo comentário postado no blog.');
	define('_MI_XP2_NOTIFY_GLOBAL_NEWCOMMENTSBJ', '[{XPRESS_BLOG_NAME}]コメント: "{XPRESS_POST_TITLE}"');

	define('_MI_XP2_NOTIFY_CAT_NEWPOST', 'Novo post em categoria');
	define('_MI_XP2_NOTIFY_CAT_NEWPOSTCAP', 'Aviso sobre novo post publicados nesta categoria.');
	define('_MI_XP2_NOTIFY_CAT_NEWPOSTSBJ', '[Post em {XPRESS_BLOG_NAME}]: "{XPRESS_POST_TITLE}" (CATEGORIA="{XPRESS_CAT_TITLE}")');

	define('_MI_XP2_NOTIFY_CAT_NEWCOMMENT', 'Novo comentário em categoria');
	define('_MI_XP2_NOTIFY_CAT_NEWCOMMENTCAP', 'Aviso sobre novo comentário nesta categoria.');
	define('_MI_XP2_NOTIFY_CAT_NEWCOMMENTSBJ', '[Comentário em {XPRESS_BLOG_NAME}]: "{XPRESS_POST_TITLE}" (CATEGORIA="{XPRESS_CAT_TITLE}")');

	define('_MI_XP2_NOTIFY_AUT_NEWPOST', 'Novo post de autor');
	define('_MI_XP2_NOTIFY_AUT_NEWPOSTCAP', 'Aviso sobre novo post deste autor.');
	define('_MI_XP2_NOTIFY_AUT_NEWPOSTSBJ', '[Post em {XPRESS_BLOG_NAME}]: "{XPRESS_POST_TITLE}" (AUTOR="{XPRESS_AUTH_NAME}")');

	define('_MI_XP2_NOTIFY_AUT_NEWCOMMENT', 'Novo comentário para autor');
	define('_MI_XP2_NOTIFY_AUT_NEWCOMMENTCAP', 'Aviso sobre novo comentário em post deste autor.');
	define('_MI_XP2_NOTIFY_AUT_NEWCOMMENTSBJ', '[Comentário em {XPRESS_BLOG_NAME}]: "{XPRESS_POST_TITLE}" (AUTOR="{XPRESS_AUTH_NAME}")');

	define('_MI_XP2_NOTIFY_POST_EDITPOST', 'Atualização de post');
	define('_MI_XP2_NOTIFY_POST_EDITPOSTCAP', 'Aviso sobre atualização no post atual.');
	define('_MI_XP2_NOTIFY_POST_EDITPOSTSBJ', '[Post em {XPRESS_BLOG_NAME}]: Atualização de "{XPRESS_POST_TITLE}" (ACOMPANHANDO)');

	define('_MI_XP2_NOTIFY_POST_NEWCOMMENT', 'Comentário em post');
	define('_MI_XP2_NOTIFY_POST_NEWCOMMENTCAP', 'Aviso sobre comentário no post atual.');
	define('_MI_XP2_NOTIFY_POST_NEWCOMMENTSBJ', '[Comentário em {XPRESS_BLOG_NAME}]: "{XPRESS_POST_TITLE}" (ACOMPANHANDO)');

}
?>
