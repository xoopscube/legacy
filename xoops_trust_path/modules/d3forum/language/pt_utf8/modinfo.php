<?php
if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'd3forum';
$constpref = '_MI_' . strtoupper( $mydirname );
if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {



// Appended by Xoops Language Checker -GIJOE- in 2007-09-28 15:55:33
define($constpref.'_DEFAULT_OPTIONS','Default checked in post form');
define($constpref.'_DEFAULT_OPTIONSDSC','List checked options separated by comma(,).<br />eg) smiley,xcode,br,number_entity<br />You can add these options: special_entity html attachsig u2t_marked');

// Appended by Xoops Language Checker -GIJOE- in 2007-09-27 16:50:42
define($constpref.'_BODY_EDITOR','Body Editor');
define($constpref.'_BODY_EDITORDSC','WYSIWYG editor will be enabled under only forums allowing HTML. With forums escaping HTML specialchars, xoopsdhtml will be displayed automatically.');

// Appended by Xoops Language Checker -GIJOE- in 2007-09-26 17:55:48
define($constpref.'_ADMENU_POSTHISTORIES','Histories');
define($constpref.'_SHOW_BREADCRUMBS','Display breadcrumbs');
define($constpref.'_ANTISPAM_GROUPS','Groups should be checked anti-SPAM');
define($constpref.'_ANTISPAM_GROUPSDSC','Usually set all blank.');
define($constpref.'_ANTISPAM_CLASS','Class name of anti-SPAM');
define($constpref.'_ANTISPAM_CLASSDSC','Default value is "default". If you disable anti-SPAM against guests even, set it blank');

define( $constpref.'_LOADED' , 1 );
// The name of this module
define($constpref."_NAME","Forum");
// A brief description of this module
define($constpref."_DESC","Forum module for XOOPS");
// Names of blocks for this module (Not all module has blocks)
define($constpref."_BNAME_LIST_TOPICS","Topicos"); //
define($constpref."_BDESC_LIST_TOPICS","Este bloco pode ser usado para varias finalidade. Naturalmente, você pode por multiplly.");
define($constpref."_BNAME_LIST_POSTS","Mensagens"); //
define($constpref."_BNAME_LIST_FORUMS","Forums"); //
define($constpref.'_ADMENU_ADVANCEDADMIN','Avançado'); //
define($constpref.'_ADMENU_CATEGORYACCESS','Permissões das Categorias'); //
define($constpref.'_ADMENU_FORUMACCESS','Permissões dos fórums'); //
define($constpref.'_ADMENU_MYBLOCKSADMIN','Blocos e permissões');
define($constpref.'_ADMENU_MYLANGADMIN','Idiomas');
define($constpref.'_ADMENU_MYPREFERENCES','Preferências');
define($constpref.'_ADMENU_MYTPLSADMIN','Modelos');

// configurations
define($constpref.'_TOP_MESSAGE','Mensagem no inicio do forum'); //
define($constpref.'_TOP_MESSAGEDEFAULT','<h1 class="d3f_title">Inicio do Forum</h1><p class="d3f_welcome">Para começar a visualizar as mensagens, selecionar uma categoria ou o forum de que você queira visitar na seleção abaixo.</p>'); //
define($constpref.'_ALLOW_HTML','Permitir HTML'); //
define($constpref.'_ALLOW_HTMLDSC','Não coloque SIM, ocasionalmente. Pois isto pode conter uma vunerabilidade e que um usuario coloque um script malicioso.'); //
define($constpref.'_ALLOW_TEXTIMG','Permitir visualzar as imagens externas na mensagem'); //
define($constpref.'_ALLOW_TEXTIMGDSC','Se alguem afixar uma imagem externa usando [img], ele pode saber quais o IPs ou os usuários visitaram seu site.'); //
define($constpref.'_ALLOW_SIG','Permitir a assinatura'); //
define($constpref.'_ALLOW_SIGDSC','');
define($constpref.'_ALLOW_SIGIMG','Permitir vizualizar imagens externas na assinatura'); //
define($constpref.'_ALLOW_SIGIMGDSC','Se alguem afixar uma imagem externa usando [img], ele pode saber quais o IPs ou os usuários visitaram seu site.'); //
define($constpref.'_USE_VOTE','usar a opção de VOTO');
define($constpref.'_USE_SOLVED','usar a opção de RESOLVIDO'); //
define($constpref.'_ALLOW_MARK','usar a opção de MARCAR TÓPICO'); //
define($constpref.'_ALLOW_HIDEUID','Permitir um usuário registrado pode postar sem seu nome'); //
define($constpref.'_POSTS_PER_TOPIC','Máximo de mensagens por tópico'); //
define($constpref.'_POSTS_PER_TOPICDSC','O tópico tem um limite de suas mensagens'); //
define($constpref.'_HOT_THRESHOLD','TOPICO QUENTE');//
define($constpref.'_HOT_THRESHOLDDSC','Mensagens nescessarias para se tornar um TOPICO QUENTE');
define($constpref.'_TOPICS_PER_PAGE','Máximo de tópicos por pagina, mostrada no forum.'); //
define($constpref.'_TOPICS_PER_PAGEDSC','');
define($constpref.'_VIEWALLBREAK','Tópicos por uma página nos forums do cruzamento da vista');
define($constpref.'_VIEWALLBREAKDSC','');
define($constpref.'_SELFEDITLIMIT','O limite de tempo para usuários editar as mensagens (em segundo)'); //
define($constpref.'_SELFEDITLIMITDSC','Não permitir de usuários normais de poder editar, determinando 0 (zero). Permitir usuários normais de pode editar, determinando o valor em segundos.');
define($constpref.'_SELFDELLIMIT','Limite de tempo para os usuários apagarem as mensagens (em segundo)');
define($constpref.'_SELFDELLIMITDSC','Não permitir de usuários normais de poder apagar, determinando 0 (zero). Permitir usuários normais de pode apagar, determinando o valor em segundos.');
define($constpref.'_CSS_URI','Usar URI do arquivo CSS para este módulo'); //
define($constpref.'_CSS_URIDSC','o trajeto relativo ou absoluto pode ser ajustado por padrão: index.css'); //
define($constpref.'_IMAGES_DIR','Diretório para arquivos de imagem'); //
define($constpref.'_IMAGES_DIRDSC','o trajeto relativo deve ser ajustado no diretório do módulo, por padrão: imagens'); //
define($constpref.'_ANONYMOUS_NAME','Nome para Convidados');
define($constpref.'_ANONYMOUS_NAMEDSC','');
define($constpref.'_ICON_MEANINGS','Significado dos ícones');
define($constpref.'_ICON_MEANINGSDSC','Definição de ALTs dos ícones. Cada alts deve ser separado pelo pipe(|). O primeiro alt corresponde "posticon0.gif", dentro do diretório /images.');
define($constpref.'_ICON_MEANINGSDEF','none|normal|triste|feliz|baixo|alto|relatar|perguntar');
define($constpref.'_GUESTVOTE_IVL','Voto dos visitantes');//
define($constpref.'_GUESTVOTE_IVLDSC','Colocando 0(zero), impossibilita dos visitantes votarem. Qualquer outro numero significa o tempo (em segundos) para poderem votar de novo na mensagem, com o mesmo IP.'); //
// Notify Categories
define($constpref.'_NOTCAT_TOPIC', 'Este tópico');//
define($constpref.'_NOTCAT_TOPICDSC', 'As notificações sobre o objetivo do tópico');
define($constpref.'_NOTCAT_FORUM', 'Este forum'); //
define($constpref.'_NOTCAT_FORUMDSC', 'As notificações sobre o objetivo do forum');
define($constpref.'_NOTCAT_CAT', 'Esta categoria'); //
define($constpref.'_NOTCAT_CATDSC', 'As notificações sobre o objetivo do categoria');
define($constpref.'_NOTCAT_GLOBAL', 'Este modulo'); //
define($constpref.'_NOTCAT_GLOBALDSC', 'As notificações sobre o objetivo do modulo');
// Each Notifications
define($constpref.'_NOTIFY_TOPIC_NEWPOST', 'Nova mensagem no tópico'); //
define($constpref.'_NOTIFY_TOPIC_NEWPOSTCAP', 'Notificar-me de novas mensagens neste tópico'); //
define($constpref.'_NOTIFY_TOPIC_NEWPOSTSBJ', '[{X_SITENAME}] {X_MODULE}:{TOPIC_TITLE} Nova mensagem no tópico'); //
define($constpref.'_NOTIFY_FORUM_NEWPOST', 'Nova mensagem no forum'); //
define($constpref.'_NOTIFY_FORUM_NEWPOSTCAP', 'Notificar-me de novas mensagens neste forum.'); //
define($constpref.'_NOTIFY_FORUM_NEWPOSTSBJ', '[{X_SITENAME}] {X_MODULE}:{FORUM_TITLE} Nova mensagem no forum');//
define($constpref.'_NOTIFY_FORUM_NEWTOPIC', 'Novo tópico no forum'); //
define($constpref.'_NOTIFY_FORUM_NEWTOPICCAP', 'Notificar-me de novos tópicos neste forum.'); //
define($constpref.'_NOTIFY_FORUM_NEWTOPICSBJ', '[{X_SITENAME}] {X_MODULE}:{FORUM_TITLE} Novo tópico no forum'); //
define($constpref.'_NOTIFY_CAT_NEWPOST', 'Nova mensagem na categoria'); //
define($constpref.'_NOTIFY_CAT_NEWPOSTCAP', 'Notificar-me de novas mensagens nesta categoria.'); //
define($constpref.'_NOTIFY_CAT_NEWPOSTSBJ', '[{X_SITENAME}] {X_MODULE}:{CAT_TITLE} Nova mensagem na categoria'); //
define($constpref.'_NOTIFY_CAT_NEWTOPIC', 'Novo tópico na categoria'); //
define($constpref.'_NOTIFY_CAT_NEWTOPICCAP', 'Notificar-me de novos tópicos nesta categoria.'); //
define($constpref.'_NOTIFY_CAT_NEWTOPICSBJ', '[{X_SITENAME}] {X_MODULE}:{CAT_TITLE} Nova mensagem na categoria'); //
define($constpref.'_NOTIFY_CAT_NEWFORUM', 'Novo forum na categoria'); //
define($constpref.'_NOTIFY_CAT_NEWFORUMCAP', 'Notificar-me de novos foruns nesta categoria.'); //
define($constpref.'_NOTIFY_CAT_NEWFORUMSBJ', '[{X_SITENAME}] {X_MODULE}:{CAT_TITLE} Novo forum na categoria'); //
define($constpref.'_NOTIFY_GLOBAL_NEWPOST', 'Nova mensagem no módulo'); //
define($constpref.'_NOTIFY_GLOBAL_NEWPOSTCAP', 'Notificar-me de novas mensagens no módulo.'); //
define($constpref.'_NOTIFY_GLOBAL_NEWPOSTSBJ', '[{X_SITENAME}] {X_MODULE}: Nova mensagem'); //
define($constpref.'_NOTIFY_GLOBAL_NEWTOPIC', 'Novo tópico no módulo'); //
define($constpref.'_NOTIFY_GLOBAL_NEWTOPICCAP', 'Notificar-me de novos tópicos no módulo.'); //
define($constpref.'_NOTIFY_GLOBAL_NEWTOPICSBJ', '[{X_SITENAME}] {X_MODULE}: Novo tópica'); //
define($constpref.'_NOTIFY_GLOBAL_NEWFORUM', 'Novo forum no módule'); //
define($constpref.'_NOTIFY_GLOBAL_NEWFORUMCAP', 'Notificar-me de novos forums no módulo.'); //
define($constpref.'_NOTIFY_GLOBAL_NEWFORUMSBJ', '[{X_SITENAME}] {X_MODULE}: Novo forum'); //
define($constpref.'_NOTIFY_GLOBAL_NEWPOSTFULL', 'Nova Mensagem (Texto Completo)'); //
define($constpref.'_NOTIFY_GLOBAL_NEWPOSTFULLCAP', 'Notificar-me de todas as novas mensagens (incluir o texto completo nas mensagem).'); //
define($constpref.'_NOTIFY_GLOBAL_NEWPOSTFULLSBJ', '[{X_SITENAME}] {POST_TITLE}'); //
define($constpref.'_NOTIFY_GLOBAL_WAITING', 'Requerendo aprovação');
define($constpref.'_NOTIFY_GLOBAL_WAITINGCAP', 'Notificar-me de novas mensagens que requerem a aprovação. Somente para administrador.'); //
define($constpref.'_NOTIFY_GLOBAL_WAITINGSBJ', '[{X_SITENAME}] {X_MODULE}: Requerendo aprovação');
}
?>