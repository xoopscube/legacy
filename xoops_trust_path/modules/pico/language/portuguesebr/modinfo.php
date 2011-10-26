<?php

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'pico' ;
$constpref = '_MI_' . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {


// Appended by Xoops Language Checker -GIJOE- in 2009-01-18 18:29:26
define($constpref.'_COM_ORDER','Order of comment-integration');
define($constpref.'_COM_POSTSNUM','Max posts displayed in comment-integration');

define( $constpref.'_LOADED' , 1 ) ;

// The name of this module
define($constpref."_NAME","pico");

// A brief description of this module
define($constpref."_DESC","um módulo para conteúdos estáticos");

// admin menus
define( $constpref.'_ADMENU_CONTENTSADMIN' , 'Lista de conteúdos' ) ;
define( $constpref.'_ADMENU_CATEGORYACCESS' , 'Permissões das categorias' ) ;
define( $constpref.'_ADMENU_IMPORT' , 'Importar/Sincronizar' ) ;
define( $constpref.'_ADMENU_TAGS' , 'Palavras-chave' ) ;
define( $constpref.'_ADMENU_EXTRAS' , 'Extra' ) ;
define( $constpref.'_ADMENU_MYLANGADMIN' , 'Linguagens' ) ;
define( $constpref.'_ADMENU_MYTPLSADMIN' , 'Modelos' ) ;
define( $constpref.'_ADMENU_MYBLOCKSADMIN' , 'Blocos/Permissões' ) ;
define( $constpref.'_ADMENU_MYPREFERENCES' , 'Preferências' ) ;

// configurations
define($constpref.'_USE_WRAPSMODE','habilitar modo wraps');
define($constpref.'_USE_REWRITE','habilitar modo mod_rewrite');
define($constpref.'_USE_REWRITEDSC','Depende de seu ambiente. Se você ligar, renomeie .htaccess.rewrite_wraps(com wraps) ou htaccess.rewrite_normal(sem wraps) para .htaccess sob XOOPS_ROOT_PATH/modules/(dirname)/');
define($constpref.'_WRAPSAUTOREGIST','habilitar arquivos auto-registering HTML wrapped no banco de dados de conteúdos');
define($constpref.'_AUTOREGISTCLASS','nome da classe para registrar ou não registrar arquivos HTML wrapped');
define($constpref.'_TOP_MESSAGE','Descrição da categoria TOP');
define($constpref.'_TOP_MESSAGEDEFAULT','');
define($constpref.'_MENUINMODULETOP','Mostrar o menu(index) no topo deste módulo');
define($constpref.'_LISTASINDEX',"Mostrar o index dos conteúdos no topo da categoria");
define($constpref.'_LISTASINDEXDSC','SIM significa que uma lista feita automaticamente é mostrada no topo da categoria. NÃO significa um conteúdo com prioridade mais alta é mostrado ao invés da lista feita automaticamente');
define($constpref.'_SHOW_BREADCRUMBS','Mostrar breadcrumbs');
define($constpref.'_SHOW_PAGENAVI','Mostrar página de navegação');
define($constpref.'_SHOW_PRINTICON','Mostrar icone de impressão amigável');
define($constpref.'_SHOW_TELLAFRIEND','Mostrar o icone do módulo Recomende a um amigo');
define($constpref.'_SEARCHBYUID','Habilitar conceito de que postou o artigo');
define($constpref.'_SEARCHBYUIDDSC','Os conteúdos serão listado no perfil do usuário quem postar. Se você este módulo com conteúdo estático, desligue isso.');
define($constpref.'_USE_TAFMODULE','Utitilizar o módulo Recomende um amigo');
define($constpref.'_FILTERS','Configuração padrão do filtro');
define($constpref.'_FILTERSDSC','informe os nomes dos filtros separados por | (pipe)');
define($constpref.'_FILTERSDEFAULT','xcode|smiley|nl2br');
define($constpref.'_FILTERSF','Forçar filtros');
define($constpref.'_FILTERSFDSC','informe o nome dos filtros separados com ,(vírgula). filtro: ÚLTIMO significa que o filtro é passado na última frase. Outros filtros são passados na primeira frase.');
define($constpref.'_FILTERSP','Filtros proibidos');
define($constpref.'_FILTERSPDSC','informe os nomes separados com ,(vírgula).');
define($constpref.'_SUBMENU_SC','Mostrar conteúdos no submenu');
define($constpref.'_SUBMENU_SCDSC','Somente as categorias são mostradas como padrão. Se você ativar isso, os conteúdos marcados no menu serão mostrados também');
define($constpref.'_SITEMAP_SC','Mostrar conteúdos no módulo Mapa do Site');
define($constpref.'_USE_VOTE','usar a característica de votação');
define($constpref.'_GUESTVOTE_IVL','Voto de convidados');
define($constpref.'_GUESTVOTE_IVLDSC','Configure como 0, para desabilitar votação de convidados. Outro número significa tempo em segundos para permitir um segundo voto de um mesmo IP.');
define($constpref.'_HTMLHEADER','cabeçalho html comum');
define($constpref.'_ALLOWEACHHEAD','specificar cabeçalhos comum em HTML para cada um dos conteúdos');
define($constpref.'_CSS_URI','URI do arquivo CSS para este módulo');
define($constpref.'_CSS_URIDSC','o percurso absoluto ou relativo pode ser configurado. Padrão: {mod_url}/index.php?page=main_css');
define($constpref.'_IMAGES_DIR','Diretório para arquivos de imagem');
define($constpref.'_IMAGES_DIRDSC','o percurso relativo deve ser configurado no diretório do módulo. Padrão: images');
define($constpref.'_BODY_EDITOR','Editor para o corpo');
define($constpref.'_HTMLPR_EXCEPT','Grupos que podem evitar purificação por HTMLPurifier');
define($constpref.'_HTMLPR_EXCEPTDSC','Posts de usuários que não pertencem a esses serão forçados a purificação com o sanitized HTML pelo HTMLPurifier no Protector>=3.14. Esta purificação não trabalha com PHP4');
define($constpref.'_HISTORY_P_C','Número de revisões que são armazenadas no banco de dados');
define($constpref.'_MLT_HISTORY','Tempo  mínimo de vida de cada revisão, em segundos');
define($constpref.'_BRCACHE','Tempo de vida em Cache para os arquivos de imagem (somente no modo wraps)');
define($constpref.'_BRCACHEDSC','Outros arquivos than HTML serão armazenados pelo navegador neste segundo (0 significa desabilitado)');
define($constpref.'_EF_CLASS' , 'classe para extra_fields');
define($constpref.'_EF_CLASSDSC' , 'Mude isso se você precisar sobrescrever o tratamento para extra_fields. O valor padrão é PicoExtraFields');
define($constpref.'_URIM_CLASS' , 'classe de mapeamento da URI');
define($constpref.'_URIM_CLASSDSC' , 'Mude isso se você precisar sobrescrever o mapeamento da URI. O Vvalor padrão é PicoUriMapper');
define($constpref.'_EFIMAGES_DIR' , 'diretório para extra_fields');
define($constpref.'_EFIMAGES_DIRDSC' , 'configurar percurso relativo do XOOPS_ROOT_PATH. Primerio, crie e dê chmod 777 ao diretório. Padrão: uploads/(module dirname)');
define($constpref.'_EFIMAGES_SIZE' , 'pixels para imagens extras');
define($constpref.'_EFIMAGES_SIZEDSC' , '(largura_principal)x(altura_principal) (largura_miniatura)x(altura_miniatura) Padrão: 480x480 150x150');
define($constpref.'_IMAGICK_PATH' , 'Percurso para ImageMagick binaries');
define($constpref.'_IMAGICK_PATHDSC' , 'Normal, deixe em branco ou configure como /usr/X11R6/bin/');
define($constpref.'_COM_DIRNAME','Integração de comentário: nome do diretório do módulo d3forum');
define($constpref.'_COM_FORUM_ID','Integração de comentário: ID do fórum');
define($constpref.'_COM_VIEW','vizualização do Integração de comentário');

// blocks
define($constpref.'_BNAME_MENU','Menu');
define($constpref.'_BNAME_CONTENT','Conteúdo');
define($constpref.'_BNAME_LIST','Lista');
define($constpref.'_BNAME_SUBCATEGORIES','Subcategorias');
define($constpref.'_BNAME_MYWAITINGS','Meus posts aguardando aprovação');
define($constpref.'_BNAME_TAGS','Palavras-chave');

// Notify Categories
define($constpref.'_NOTCAT_GLOBAL', 'global');
define($constpref.'_NOTCAT_GLOBALDSC', 'Notificações sobre este módulo');
define($constpref.'_NOTCAT_CATEGORY', 'categoria');
define($constpref.'_NOTCAT_CATEGORYDSC', 'notificações sob esta categoria');
define($constpref.'_NOTCAT_CONTENT', 'conteúdo');
define($constpref.'_NOTCAT_CONTENTDSC', 'notificações sobre este conteúdo');

// Each Notifications
define($constpref.'_NOTIFY_GLOBAL_WAITINGCONTENT', 'aguardando aprovação');
define($constpref.'_NOTIFY_GLOBAL_WAITINGCONTENTCAP', 'Notifique-me de novos posts ou modificações no aguardo de aprovação (notificar apenas os administradores e moderadores)');
define($constpref.'_NOTIFY_GLOBAL_WAITINGCONTENTSBJ', '[{X_SITENAME}] {X_MODULE}: aguardando');
define($constpref.'_NOTIFY_GLOBAL_NEWCONTENT', 'novo conteúdo');
define($constpref.'_NOTIFY_GLOBAL_NEWCONTENTCAP', 'Notifique-me quando um novo conteúdo for registrado. (somente conteúdos aprovados)');
define($constpref.'_NOTIFY_GLOBAL_NEWCONTENTSBJ', '[{X_SITENAME}] {X_MODULE} : um novo conteúdo {CONTENT_SUBJECT}');
define($constpref.'_NOTIFY_CATEGORY_NEWCONTENT', 'novo conteúdo');
define($constpref.'_NOTIFY_CATEGORY_NEWCONTENTCAP', 'Notifique-me quando um novo conteúdo for registrado. (somente conteúdos aprovados)');
define($constpref.'_NOTIFY_CATEGORY_NEWCONTENTSBJ', '[{X_SITENAME}] {X_MODULE}:{CAT_TITLE} um novo conteúdo {CONTENT_SUBJECT}');
define($constpref.'_NOTIFY_CONTENT_COMMENT', 'novo comentário');
define($constpref.'_NOTIFY_CONTENT_COMMENTCAP', 'Notifique-me quando um novo comentário for postado. (somente comentários aprovados)');
define($constpref.'_NOTIFY_CONTENT_COMMENTSBJ', '[{X_SITENAME}] {X_MODULE} : um novo comentário');

}


?>
