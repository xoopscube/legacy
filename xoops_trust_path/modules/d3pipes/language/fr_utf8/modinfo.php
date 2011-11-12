<?php

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'd3pipes' ;
$constpref = '_MI_' . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {




// Appended by Xoops Language Checker -GIJOE- in 2009-01-18 07:46:41
define($constpref.'_COM_ORDER','Order of comment-integration');

// Appended by Xoops Language Checker -GIJOE- in 2008-11-18 04:46:02
define($constpref.'_INDEXKEEPPIPE','Displays upper pipes as possible in the top of this module');

// Appended by Xoops Language Checker -GIJOE- in 2008-05-20 05:59:23
define($constpref.'_COM_VIEW','View of comment-integration');
define($constpref.'_COM_POSTSNUM','Max posts displayed in comment-integration');

define( $constpref.'_LOADED' , 1 ) ;

// The name of this module
define($constpref."_NAME","D3 PIPES");

// A brief description of this module
define($constpref."_DESC","Module de Syndication Flexible");

// admin menus
define($constpref.'_ADMENU_PIPE','Flux') ;
define($constpref.'_ADMENU_CACHE','Cache') ;
define($constpref.'_ADMENU_CLIPPING','Clips') ;
define($constpref.'_ADMENU_JOINT','Joint initiales') ;
define($constpref.'_ADMENU_JOINTCLASS','Classes initials') ;
define($constpref.'_ADMENU_MYLANGADMIN','Langages') ;
define($constpref.'_ADMENU_MYTPLSADMIN','Templates') ;
define($constpref.'_ADMENU_MYBLOCKSADMIN','Blocs/Permissions') ;
define($constpref.'_ADMENU_MYPREFERENCES','Préférences') ;

// blocks
define($constpref.'_BNAME_ASYNC','Liste d\'entrées (Async)') ;
define($constpref.'_BNAME_SYNC','Liste d\'entrées (Sync)') ;

// configs
define($constpref.'_INDEXTOTAL','Total d\'entrées en tête de ce module');
define($constpref.'_INDEXEACH','Max entrées depuis un flux en tête de module');
define($constpref.'_ENTRIESAPIPE','Max entrées a afficher/RSS pour chaque flux');
define($constpref.'_ENTRIESAPAGE','Entrées par page dans la liste des clips');
define($constpref.'_ENTRIESARSS','Entrées par RSS/Atom');
define($constpref.'_ENTRIESSMAP','Entriées du sitemap xml pour google etc');
define($constpref.'_ARCB_FETCHED','Auto expire par temps de prélèvement (jours)');
define($constpref.'_ARCB_FETCHEDDSC','Specifier nombre de jours que les clips doivent être retirés. 0 signifie désactiver auto-expire. Les Clips avec des commentaures/surlignés ne sont jamais restirés.');
define($constpref.'_INTERNALENC','Encodage Interne');
define($constpref.'_FETCHCACHELT','Temps de cache du prélèvement (sec)');
define($constpref.'_REDIRECTWARN','Alerter si le URI du rss/atom est redirigé');
define($constpref.'_SNP_MAXREDIRS','Max redirections pour Snoopy');
define($constpref.'_SNP_MAXREDIRSDSC','Après contruire ules flux avec succès, cchangez cette option en 0');
define($constpref.'_SNP_PROXYHOST','Hostname ou serveur proxy');
define($constpref.'_SNP_PROXYHOSTDSC','specifier ceci par FQDN. Normalement laisser ceci en blanc');
define($constpref.'_SNP_PROXYPORT','Port du serveur proxy');
define($constpref.'_SNP_PROXYUSER','Nom d\'utilisateur pour le serveur proxy');
define($constpref.'_SNP_PROXYPASS','Mot de passe pour le serveur proxy');
define($constpref.'_SNP_CURLPATH','Chemin de boucle (défaut: /usr/bin/curl)');
define($constpref.'_TIDY_PATH','Chemin de Tidy (défaut: /usr/bin/tidy)');
define($constpref.'_XSLTPROC_PATH','Chemin de xsltproc (défaut: /usr/bin/xsltproc)');
define($constpref.'_UPING_SERVERS','Mettre à jour les serveurs Ping');
define($constpref.'_UPING_SERVERSDSC','Écrire un point final RPC commençant la ligne par "http://".<br />Si vous voulez envoyer extendedPing, ajouter " E" après le URI.');
define($constpref.'_UPING_SERVERSDEF',"http://blogsearch.google.com/ping/RPC2 E\nhttp://rpc.weblogs.com/RPC2 E\nhttp://ping.blo.gs/ E");

define($constpref.'_CSS_URI','CSS URI');
define($constpref.'_CSS_URIDSC','un chemin relatif ou absolut peut être définit. Défaut: {mod_url}/index.css');
define($constpref.'_IMAGES_DIR','Repértoire pour les fichiers images');
define($constpref.'_IMAGES_DIRDSC','le chemin relatif devrait être configuré dans le repértoire du module. défaut: images');
define($constpref.'_COM_DIRNAME','Integration-Commentaires: dirname de d3forum');
define($constpref.'_COM_FORUM_ID','Intégration-Commentaires: ID forum');

}


?>