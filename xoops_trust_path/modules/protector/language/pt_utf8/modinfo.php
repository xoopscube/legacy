<?php

if (defined('FOR_XOOPS_LANG_CHECKER')) {
    $mydirname = 'protector';
}
$constpref = '_MI_'.strtoupper($mydirname);

if (defined('FOR_XOOPS_LANG_CHECKER') || !defined($constpref.'_LOADED')) {

// Appended by Xoops Language Checker -GIJOE- in 2017-02-27 14:47:37
    define($constpref.'_BANIP_IPV6PREFIX', 'IPv6 deny list registration prefix');
    define($constpref.'_BANIP_IPV6PREFIXDSC', 'Number of prefix bit at IPv6 address registration (128 bit to all bits)');
    define($constpref.'_HIJACK_TOPBITV6', 'Protected IP bits for the session(IPv6)');
    define($constpref.'_HIJACK_TOPBITV6DSC', 'Anti Session Hi-Jacking:<br />Default 128(bit). (All bits are protected)<br />When your IP is not stable, set the IP range by number of the bits.');

// Appended by Xoops Language Checker -GIJOE- in 2009-11-17 18:12:56
    define($constpref.'_FILTERS', 'filters enabled in this site');
    define($constpref.'_FILTERSDSC', 'specify file names inside of filters_byconfig/ separated with LF');
    define($constpref.'_MANIPUCHECK', 'enable manipulation checking');
    define($constpref.'_MANIPUCHECKDSC', 'notify to admin if your root folder or index.php is modified.');
    define($constpref.'_MANIPUVALUE', 'value for manipulation checking');
    define($constpref.'_MANIPUVALUEDSC', 'do not edit this field');

// Appended by Xoops Language Checker -GIJOE- in 2009-07-06 05:46:52
    define($constpref.'_DBTRAPWOSRV', 'Never checking _SERVER for anti-SQL-Injection');
    define($constpref.'_DBTRAPWOSRVDSC', 'Some servers always enable DB Layer trapping. It causes wrong detections as SQL Injection attack. If you got such errors, turn this option on. You should know this option weakens the security of DB Layer trapping anti-SQL-Injection.');

// Appended by Xoops Language Checker -GIJOE- in 2009-01-14 11:10:52
    define($constpref.'_DBLAYERTRAP', 'Enable DB Layer trapping anti-SQL-Injection');
    define($constpref.'_DBLAYERTRAPDSC', 'Almost SQL Injection attacks will be canceled by this feature. This feature is required a support from databasefactory. You can check it on Security Advisor page.');

// Appended by Xoops Language Checker -GIJOE- in 2008-11-21 04:44:30
    define($constpref.'_DEFAULT_LANG', 'Default language');
    define($constpref.'_DEFAULT_LANGDSC', 'Specify the language set to display messages before processing common.php');
    define($constpref.'_BWLIMIT_COUNT', 'Bandwidth limitation');
    define($constpref.'_BWLIMIT_COUNTDSC', 'Specify the max access to mainfile.php during watching time. This value should be 0 for normal environments which have enough CPU bandwidth. The number fewer than 10 will be ignored.');

// Appended by Xoops Language Checker -GIJOE- in 2007-07-30 16:31:32
    define($constpref.'_BANIP_TIME0', 'Banned IP suspension time (sec)');
    define($constpref.'_OPT_BIPTIME0', 'Ban the IP (moratorium)');
    define($constpref.'_DOSOPT_BIPTIME0', 'Ban the IP (moratorium)');

// Appended by Xoops Language Checker -GIJOE- in 2007-03-29 03:36:14
    define($constpref.'_ADMENU_MYBLOCKSADMIN', 'Permissions');

    define($constpref.'_LOADED', 1);

// The name of this module
    define($constpref.'_NAME', 'Protector');

// A brief description of this module
    define($constpref.'_DESC', 'M�dulo para prote��o do XOOPS contra ataques mal-intencionados, em especial: ataques DoS, SQL Injection e contamina��es por vari�vel.');

// Menu
    define($constpref.'_ADMININDEX', 'Central de prote��o');
    define($constpref.'_ADVISORY', 'Guia de seguran�a');
    define($constpref.'_PREFIXMANAGER', 'Gerenciador de PREFIXO');

// Configs
    define($constpref.'_GLOBAL_DISBL', 'Interrup��o tempor�ria de funcionamento');
    define($constpref.'_GLOBAL_DISBLDSC', 'Suspende temporariamente o funcionamento de todas as prote��es.<br />Ap�s resolver os problemas, n�o se esque�a de desativ�-la.');
    define($constpref.'_RELIABLE_IPS', 'IPs confi�veis');
    define($constpref.'_RELIABLE_IPSDSC', 'Indique os endere�os IP que n�o passar�o por examina��o para ataques DoS, separados por |. ^ para o inv�lido, e  $ para o final do string.');
    define($constpref.'_LOG_LEVEL', 'N�vel de logging');
    define($constpref.'_LOG_LEVELDSC', '');

    define($constpref.'_LOGLEVEL0', 'N�o gerar log');
    define($constpref.'_LOGLEVEL15', 'Gerar log apenas de elementos de alto risco');
    define($constpref.'_LOGLEVEL63', 'N�o gerar log de elementos de baixo risco');
    define($constpref.'_LOGLEVEL255', 'Gerar log de todos os elementos');

    define($constpref.'_HIJACK_TOPBIT', 'Prote��o de IP bits contra renova��o de sess�o');
    define($constpref.'_HIJACK_TOPBITDSC', 'Preven��o � session hijack:<br />O padr�o � 32(bit) e protege de todos os bits.<br />Caso use Proxy ou seu endere�o IP mude a cada acesso, defina o intervalo de bits mais longo poss�vel � invaria��o.<br />Ex.: Se houver possibilidade de varia��o dentro de 192.168.0.0~192.168.0.255, defina esta op��o como 24(bit).');
    define($constpref.'_HIJACK_DENYGP', 'Grupos proibidos de mudan�a de IP');
    define($constpref.'_HIJACK_DENYGPDSC', 'Preven��o � session hijack:<br />Escolha os grupos cujos usu�rios proibidos de altera��o de endere�o IP durante uma sess�o.<br />(Recomendado: "Administradores")');
    define($constpref.'_SAN_NULLBYTE', 'Substitui��o de caracteres nulos por espa�os');
    define($constpref.'_SAN_NULLBYTEDSC', 'O caracter "\\0" fatal � usado freq�entemente em ataques maliciosos.<br />Sempre que detectado, ele ser� substitui��o por um espa�o.<br />(Recomendado)');
    define($constpref.'_DIE_NULLBYTE', 'Encerramento for�ado de sess�o em caso de detec��o de caracteres nulos');
    define($constpref.'_DIE_NULLBYTEDSC', 'O caracter "\\0" fatal � usado frequentemente em ataques maliciosos.<br />(Recomendado)');
    define($constpref.'_DIE_BADEXT', 'Encerramento for�ado de sess�o em caso de uploads com extens�es proibidas');
    define($constpref.'_DIE_BADEXTDSC', 'Caso houver uploads de arquivos com extens�es como .php ou outros arquivos execut�veis no servidor, a sess�o ser� apagada.<br />(N�o recomendado se voc� for usu�rio de B-Wiki ou PukiWikiMod e anexar c�digos-fonte em PHP.)');
    define($constpref.'_CONTAMI_ACTION', 'Solu��o em caso de detec��o de contamina��es por vari�vel');
    define($constpref.'_CONTAMI_ACTIONDS', 'Escolha o tipo de solu��o quando uma tentativa de altera��o das globais de sistema do XOOPS for detectada.<br />(Padr�o: "Encerramento for�ado de sess�o")');
    define($constpref.'_ISOCOM_ACTION', 'Solu��o em caso de detec��o de coment�rios isolados');
    define($constpref.'_ISOCOM_ACTIONDSC', 'Preven��o � SQL injection:<br />Escolha o tipo de solu��o quando um coment�rio isolado /* for detectado sem seu par */.<br />Processo de sanitiza��o: */ � inserido no final.<br />(Recomendado: "Sanitiza��o")');
    define($constpref.'_UNION_ACTION', 'Solu��o em caso de detec��o de UNION');
    define($constpref.'_UNION_ACTIONDSC', 'Preven��o � SQL injection:<br />Escolha o tipo de solu��o quando uma sintaxe UNION do SQL for detectada.<br />Processo de sanitiza��o: UNION � alterado para uni-on.<br />(Recomendado: "Sanitiza��o")');
    define($constpref.'_ID_INTVAL', 'Convers�o for�ada de vari�vel ID');
    define($constpref.'_ID_INTVALDSC', 'For�a valores num�ricos e vari�veis com nomes terminados em "id". � eficaz, principalmente, com m�dulos derivados do myLinks. Protege tamb�m de alguns XSS e SQL injection. Entretanto, pode entrar em conflito com alguns m�dulos.');
    define($constpref.'_FILE_DOTDOT', 'Proibido de DirectoryTraversal');
    define($constpref.'_FILE_DOTDOTDSC', 'Numa tentativa de DirectoryTraversal, o pedido � analisado, e a pattern ".." � removida.');

    define($constpref.'_BF_COUNT', 'Preven��o � Brute Force');
    define($constpref.'_BF_COUNTDSC', 'Contra round-robin. Se, dentro de 10 minutos, o n� de tentativas de login incorreto definido nesta op��o for excedido, o IP ser� banido.');

    define($constpref.'_DOS_SKIPMODS', 'M�dulos exclu�dos de observa��o de alvo de DoS');
    define($constpref.'_DOS_SKIPMODSDSC', 'Defina os m�dulos que quiser excluir, separados por |. Ative para m�dulos de chat e similares.');

    define($constpref.'_DOS_EXPIRE', 'Tempo de observa��o para ataques DoS (em segundos)');
    define($constpref.'_DOS_EXPIREDSC', 'Tempo de observa��o para acompanhar a frequ�ncia dos acessos de DoS e crawlers maliciosos.');

    define($constpref.'_DOS_F5COUNT', 'N� de vezes para ser reconhecido como ataque F5');
    define($constpref.'_DOS_F5COUNTDSC', 'Defesa contra ataques DoS:<br />Se houver muitos acessos � uma mesma URL dentro do tempo de observa��o definido acima e do n� de vezes definidas nesta op��o, ser� reconhecido como um ataque.');
    define($constpref.'_DOS_F5ACTION', 'Medidas contra ataques F5');

    define($constpref.'_DOS_CRCOUNT', 'N� de vezes para ser reconhecido como um crawler malicioso');
    define($constpref.'_DOS_CRCOUNTDSC', 'Preven��o � crawlers maliciosos (como bots catadores de e-mails):<br />Se forem realizadas buscas dentro do site dentro do tempo de observa��o definido acima e do n� de vezes definidas nesta op��o, ser� reconhecido como um crawler malicioso.');
    define($constpref.'_DOS_CRACTION', 'Solu��o para crawlers maliciosos');

    define($constpref.'_DOS_CRSAFE', 'User-Agent permitidos');
    define($constpref.'_DOS_CRSAFEDSC', 'Descreva incondicionalmente o nome dos prov�veis crawlers com uma perl regex pattern.<br />Ex.: /(msnbot|Googlebot|Yahoo! Slurp)/i');

    define($constpref.'_OPT_NONE', 'Nenhuma (apenas gerar log)');
    define($constpref.'_OPT_SAN', 'Sanitiza��o');
    define($constpref.'_OPT_EXIT', 'Encerramento for�ado de sess�o');
    define($constpref.'_OPT_BIP', 'Banimento por IP');

    define($constpref.'_DOSOPT_NONE', 'Nenhuma (apenas gerar log)');
    define($constpref.'_DOSOPT_SLEEP', 'Sleep');
    define($constpref.'_DOSOPT_EXIT', 'exit');
    define($constpref.'_DOSOPT_BIP', 'Adicionar � lista de IPs banidos');
    define($constpref.'_DOSOPT_HTA', 'Registrar DENY atrav�s de .htaccess (experimental)');

    define($constpref.'_BIP_EXCEPT', 'Grupos livres de banimento por IP');
    define($constpref.'_BIP_EXCEPTDSC', 'Mesmo quando a condi��o for satisfeita, os usu�rios dos grupos indicados nesta op��o n�p ser�o adicionados � lista de IPs banidos. Entretanto, se estes usu�rios n�o fizerem login, o efeito desta op��o ser� anulado. TENHA CUIDADO!<br />(Recomendado: "Administradores")');

    define($constpref.'_DISABLES', 'Desativar op��es inseguras');

    define($constpref.'_BIGUMBRELLA', 'ativar anti-XSS (BigUmbrella)');
    define($constpref.'_BIGUMBRELLADSC', 'Isto protege seu site contra ataques por vulnerabilidades via XSS. Mas n�o � 100% garantido');

    define($constpref.'_SPAMURI4U', 'anti-SPAM: URLs for normal users');
    define($constpref.'_SPAMURI4UDSC', 'If this number of URLs are found in POST data from users other than admin, the POST is considered as SPAM. 0 means disabling this feature.');
    define($constpref.'_SPAMURI4G', 'anti-SPAM: URLs for guests');
    define($constpref.'_SPAMURI4GDSC', 'If this number of URLs are found in POST data from guests, the POST is considered as SPAM. 0 means disabling this feature.');
}
