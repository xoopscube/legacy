<?php 
// mymenu




// Appended by Xoops Language Checker -GIJOE- in 2009-01-14 11:10:52
define('_AM_ADV_DBFACTORYPATCHED','Your databasefactory is ready for DBLayer Trapping anti-SQL-Injection');
define('_AM_ADV_DBFACTORYUNPATCHED','Your databasefactory is not ready for DBLayer Trapping anti-SQL-Injection. Some patches are required.');

// Appended by Xoops Language Checker -GIJOE- in 2008-12-03 11:47:19
define('_AM_ADV_TRUSTPATHPUBLIC','If you can look an image -NG- or the link returns normal page, your XOOPS_TRUST_PATH is not placed properly. The best place for XOOPS_TRUST_PATH is outside of DocumentRoot. If you cannot do that, you have to put .htaccess (DENY FROM ALL) just under XOOPS_TRUST_PATH as the second best way.');
define('_AM_ADV_TRUSTPATHPUBLICLINK','Check php files inside TRUST_PATH are private (it must be 404,403 or 500 error');

// Appended by Xoops Language Checker -GIJOE- in 2007-10-18 05:36:24
define('_AM_LABEL_COMPACTLOG','Compact log');
define('_AM_BUTTON_COMPACTLOG','Compact it!');
define('_AM_JS_COMPACTLOGCONFIRM','Duplicated (IP,Type) records will be removed');
define('_AM_LABEL_REMOVEALL','Remove all records');
define('_AM_BUTTON_REMOVEALL','Remove all!');
define('_AM_JS_REMOVEALLCONFIRM','All logs are removed absolutely. Are you really OK?');

// Appended by Xoops Language Checker -GIJOE- in 2007-07-30 05:37:50
define('_AM_FMT_CONFIGSNOTWRITABLE','Turn the configs directory writable: %s');

define('_MD_A_MYMENU_MYTPLSADMIN','');
define('_MD_A_MYMENU_MYBLOCKSADMIN','Permissões');
define('_MD_A_MYMENU_MYPREFERENCES','Preferências');

// index.php
define("_AM_TH_DATETIME","Data");
define("_AM_TH_USER","Usuário");
define("_AM_TH_IP","IP");
define("_AM_TH_AGENT","AGENT");
define("_AM_TH_TYPE","Tipo");
define("_AM_TH_DESCRIPTION","Detalhes");

define("_AM_TH_BADIPS","Lista de IPs banidos");
define( "_AM_TH_GROUP1IPS" , 'Autorizar IPs para Grupo=1<br /><br /><span style="font-weight:normal;">Escreva cada IP em uma linha.<br />192.168. significa 192.168.*<br />em branco significa que todos os IPs estão autorizados</span>' ) ;

define("_AM_LABEL_REMOVE","Remover as listas gravadas");
define("_AM_BUTTON_REMOVE","Apagar");
define("_AM_JS_REMOVECONFIRM","Você tem certeza de que deseja apagar os itens selecionados?");
define( "_AM_MSG_IPFILESUPDATED" , "Arquivos para IPs foram atualizados" ) ;
define( "_AM_MSG_BADIPSCANTOPEN" , "O arquivo para IPs banidos não pode ser aberto" ) ;
define( "_AM_MSG_GROUP1IPSCANTOPEN" , "O arquivo para permissões do grupo=1 não pode ser aberto" ) ;
define("_AM_MSG_REMOVED","Os itens selecionados foram apagados com sucesso.");

// prefix_manager.php
define("_AM_H3_PREFIXMAN","Gerenciador de PREFIXO");
define("_AM_MSG_DBUPDATED","O banco de dados foi atualizado com sucesso.");
define("_AM_CONFIRM_DELETE","Tem certeza de que deseja apagar todas as tabelas?");
define("_AM_TXT_HOWTOCHANGEDB","Ao mudar o prefixo, você deve alterar o seguinte conteúdo em %s/mainfile.php.<br /><br />define('XOOPS_DB_PREFIX', '<b>%s</b>');");

// advisory.php
define("_AM_ADV_NOTSECURE","INSEGURO");

define("_AM_ADV_REGISTERGLOBALS","Esta configuração permite uma variedade de ataques por injeîåo.<br />Se seu servidor suportar .htaccess, crie ou edite-o no diretóriio em que o XOOPS estiver instalado.");
define("_AM_ADV_ALLOWURLFOPEN","Esta configuração permite que atacantes executem scripts remotamente à vontade.<br />Para alterar esta opção, é necessário ter permissão de administrador do servidor.<br />Se for um administrador do servidor, edite o php.ini e o httpd.conf.<br /><b>Exemplo de httpd.conf:<br /> &nbsp; php_admin_flag &nbsp; allow_url_fopen &nbsp; off</b><br />Caso contrário, contate o suporte de seu host.");
define("_AM_ADV_USETRANSSID","Suas configurções estão definidas para exibir o ID da sessão nos links.<br />Para proteger-se contra session hijack, crie ou edite um .htaccess no diretório em que o XOOPS estiver instalado.<br /><b>php_flag session.use_trans_sid off</b>");
define("_AM_ADV_DBPREFIX","O prefixo do seu banco de dados é o padrão (\"xoops\"), o que o faz vulnerá×el à SQL injection.<br />Não se esqueça de ativar \"Sanitização em caso de detecção de comentários isolados\" e as proteções contra SQL injection.");
define("_AM_ADV_LINK_TO_PREFIXMAN","Ir para o Gerenciador de PREFIX");
define("_AM_ADV_MAINUNPATCHED","Edite seu mainfile.php como indicado no README.");

define("_AM_ADV_SUBTITLECHECK","Teste de funcionamento do Protector");
define("_AM_ADV_CHECKCONTAMI","Contaminações por variável");
define("_AM_ADV_CHECKISOCOM","Comentários isolados");
?>
