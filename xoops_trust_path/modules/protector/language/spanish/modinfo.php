<?php
// Traducci�n al espa�ol por Colossus (19/1/2008), www.zonadepruebas.com

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'protector' ;
$constpref = '_MI_' . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {




// Appended by Xoops Language Checker -GIJOE- in 2009-11-17 18:12:56
define($constpref.'_FILTERS','filters enabled in this site');
define($constpref.'_FILTERSDSC','specify file names inside of filters_byconfig/ separated with LF');
define($constpref.'_MANIPUCHECK','enable manipulation checking');
define($constpref.'_MANIPUCHECKDSC','notify to admin if your root folder or index.php is modified.');
define($constpref.'_MANIPUVALUE','value for manipulation checking');
define($constpref.'_MANIPUVALUEDSC','do not edit this field');

// Appended by Xoops Language Checker -GIJOE- in 2009-07-06 05:46:52
define($constpref.'_DBTRAPWOSRV','No comprobar nunca _SERVER en la protecci�n contra inyecci�n SQL');
define($constpref.'_DBTRAPWOSRVDSC','Algunos servidores siempre activan la intercepci�n en la capa de base de datos. Esto provoca detecciones err�neas de ataques de inyecci�n SQL. Si observa demasiados errores, active esta opci�n. Es necesario destacar que esta opci�n debilita la seguridad de la protecci�n contra inyecci�n de SQL por intercepci�n en la capa de base de datos.');

// Appended by Xoops Language Checker -GIJOE- in 2009-01-14 11:10:52
define($constpref.'_DBLAYERTRAP','Activar protecci�n contra inyecci�n de SQL por interceptaci�n en la capa de base de datos');
define($constpref.'_DBLAYERTRAPDSC','Casi todos los ataques de inyecci�n SQL ser�n neutralizados activando esta opci�n. Es necesario que la base de datos lo soporte. Puede comprobar si es as� en el Asesor de Seguridad.');

// Appended by Xoops Language Checker -GIJOE- in 2008-11-21 04:44:30
define($constpref.'_DEFAULT_LANG','Idioma por defecto');
define($constpref.'_DEFAULT_LANGDSC','Especifique el c�digo de idioma para mostrar mensajes antes de procesar common.php');
define($constpref.'_BWLIMIT_COUNT','Limitaci�n de carga');
define($constpref.'_BWLIMIT_COUNTDSC','Especifique el m�ximo n�mero de accesos a mainfile.php durante el periodo de vigilancia. Este valor deber�a de ser 0 para entornos normales con suficiente potencia de CPU. Los n�meros menores de 10 ser�n ignorados.');

// Appended by Xoops Language Checker -GIJOE- in 2007-07-30 16:31:32
define($constpref.'_BANIP_TIME0','Tiempo de suspensi�n de IP (seg)');
define($constpref.'_OPT_BIPTIME0','Bloquear la IP (moratorium)');
define($constpref.'_DOSOPT_BIPTIME0','Bloquear la IP (moratorium)');

// Appended by Xoops Language Checker -GIJOE- in 2007-03-29 03:36:14
define($constpref.'_ADMENU_MYBLOCKSADMIN','Permisos');

define( $constpref.'_LOADED' , 1 ) ;

// The name of this module
define($constpref."_NAME","Protector");

// A brief description of this module
define($constpref."_DESC","Este m�dulo proteje su sitio Xoops de varios tipos de ataques, como DoS , Inyecciones de SQL y contaminaci�n de variables.");

// Menu
define($constpref."_ADMININDEX","Centro de Protecci�n");
define($constpref."_ADVISORY","Asesor de Seguridad");
define($constpref."_PREFIXMANAGER","Administrador de Prefijos");

// Configs
define($constpref.'_GLOBAL_DISBL','Deshabilitado temporalmente');
define($constpref.'_GLOBAL_DISBLDSC','Todas las protecciones fueron deshabilitadas temporaralmente.<br />No olvide apagar esta opci�n luego de resolver el problema.');

define($constpref.'_RELIABLE_IPS','IPs confiables');
define($constpref.'_RELIABLE_IPSDSC','Fijar IPs confiables separadas con | . ^ iguala el inicio de la serie; $ iguala el final de la serie.');

define($constpref.'_LOG_LEVEL','Nivel de registro');
define($constpref.'_LOG_LEVELDSC','');

define($constpref.'_LOGLEVEL0','Ninguno');
define($constpref.'_LOGLEVEL15','Callado');
define($constpref.'_LOGLEVEL63','callado');
define($constpref.'_LOGLEVEL255','Completo');

define($constpref.'_HIJACK_TOPBIT','Bits de IP protegidos para la sesi�n');
define($constpref.'_HIJACK_TOPBITDSC','Contra Secuestro de Sesi�n:<br />Por defecto 32(bit). (Todos los bits son protegidos)<br />Cuando su IP no es estable, fije el rango de IP por n�mero de bits.<br />Por ejemplo, si su IP Puede moverse en el rango de 192.168.0.0 - 192.168.0.255, fije 24 (bits).');
define($constpref.'_HIJACK_DENYGP','Grupos cuya IP no puede modificarse durante la sesi�n.');
define($constpref.'_HIJACK_DENYGPDSC','Contra Secuestro de Sesi�n:<br />Seleccione grupos cuyo IP no puede modificarse durante la sesi�n.<br />(Recomiendo encender Administradores.)');
define($constpref.'_SAN_NULLBYTE','Limpiar bytes nulos');
define($constpref.'_SAN_NULLBYTEDSC','El caracter de terminaci�n "\\0" con frecuencia es empleado en ataques maliciosos.<br />Los bytes nulos ser�n cambiados por un espacio.<br />(Altamente recomendado: Encender)');
define($constpref.'_DIE_NULLBYTE','Salir si se detectan bytes nulos');
define($constpref.'_DIE_NULLBYTEDSC','El caracter de terminaci�n "\\0" con frecuencia es empleado en ataques maliciosos.<br />(Altamente recomendado: Encender)');
define($constpref.'_DIE_BADEXT','Salir si se suben archivos malignos');
define($constpref.'_DIE_BADEXTDSC','Si alguien trata de subir archivos con extensiones prohibidas como .php , este m�dulo lo saca de su sitio XOOPS.<br />Si con frecuencia agrega archivos php en m�dulos como B-Wiki o PukiWikiMod, apague esta opci�n.');
define($constpref.'_CONTAMI_ACTION','Acci�n al detectar una contaminaci�n.');
define($constpref.'_CONTAMI_ACTIONDS','Seleccione la acci�n frente a una contaminaci�n de variables globales del sistema en su sitio XOOPS.<br />(Opci�n recomendada: pantalla en blanco)');
define($constpref.'_ISOCOM_ACTION','Acci�n al detectar un comentario aislado');
define($constpref.'_ISOCOM_ACTIONDSC','Contra Inyecci�n de SQL:<br />Seleccione la acci�n cuando se detecte una "/*" aislada.<br />"Limpieza" significa agregar otra "*/" al final.<br />(Opci�n recomendada: Limpieza)');
define($constpref.'_UNION_ACTION','Acci�n al detectar una UNION');
define($constpref.'_UNION_ACTIONDSC','Contra Inyecci�n de SQL:<br />Seleccione la acci�n al detectar alguna sintaxis como UNION de SQL.<br />"Limpieza" significa cambiar "union" a "uni-on".<br />(Opci�n recomendada: Limpieza)');
define($constpref.'_ID_INTVAL','Forzar intervalo a variables como id');
define($constpref.'_ID_INTVALDSC','Todas las peticiones llamadas "*id" ser�n tratadas como n�mero entero.<br />Esta opci�n le protege contra algunos ataques XSS e Inyecciones de SQL.<br />Aunque recomiendo activar esta opci�n, puede causar problemas con algunos m�dulos.');
define($constpref.'_FILE_DOTDOT','Protecci�n contra Traves�as de Directorio');
define($constpref.'_FILE_DOTDOTDSC','Elimina ".." de todas las peticiones que parezcan Traves�a de Directorio.');

define($constpref.'_BF_COUNT','Contra Fuerza Bruta');
define($constpref.'_BF_COUNTDSC','Fija la cantidad de veces que un an�nimo intenta darse de alta en 10 minutos. Si alguien no puede darse de alta en esta cantidad de ocasiones, su IP ser� bloqueada.');

define($constpref.'_DOS_SKIPMODS','M�dulos exentos de revisi�n DoS/Crawler');
define($constpref.'_DOS_SKIPMODSDSC','Fija los dirnames de los m�dulos separados con |. Esta opci�n es �til con m�dulos de chat, etc.');

define($constpref.'_DOS_EXPIRE','Tiempo de vigilancia para cargas frecuentes (segundos)');
define($constpref.'_DOS_EXPIREDSC','Este valor especifica el tiempo de vigilancia para cargas frecuentes (Ataque F5) y crawlers de subidas frecuentes.');

define($constpref.'_DOS_F5COUNT','Conteo l�mite para Ataque F5');
define($constpref.'_DOS_F5COUNTDSC','Prevenci�n de ataques DoS.<br />Este valor especifica el conteo de recargas para ser considerado como un ataque malicioso.');
define($constpref.'_DOS_F5ACTION','Acci�n contra Ataque F5');

define($constpref.'_DOS_CRCOUNT','Conteo l�mite para Crawlers');
define($constpref.'_DOS_CRCOUNTDSC','Prevenci�n contra crawlers de carga frecuente.<br />Este valor especifica el conteo de accesos para ser considerados como un crawler malicioso.');
define($constpref.'_DOS_CRACTION','Acci�n contra Crawlers de carga frecuente');

define($constpref.'_DOS_CRSAFE','Agente-Usuario bienvenido');
define($constpref.'_DOS_CRSAFEDSC','Un patr�n de perl regex para Agente-Usuario.<br />Si concuerda, el crawler nunca es considerado como de carga frecuente.<br />Por ejemplo: /(msnbot|Googlebot|Yahoo! Slurp)/i');

define($constpref.'_OPT_NONE','Ninguna (s�lo registro)');
define($constpref.'_OPT_SAN','Limpieza');
define($constpref.'_OPT_EXIT','Pantalla en Blanco');
define($constpref.'_OPT_BIP','Bloquear IP');

define($constpref.'_DOSOPT_NONE','Ninguna (s�lo registro)');
define($constpref.'_DOSOPT_SLEEP','Dormir');
define($constpref.'_DOSOPT_EXIT','Pantalla en Blanco');
define($constpref.'_DOSOPT_BIP','Bloquear IP');
define($constpref.'_DOSOPT_HTA','NEGAR por .htaccess (experimental)');

define($constpref.'_BIP_EXCEPT','Grupos nunca registrados como IP Malicioso');
define($constpref.'_BIP_EXCEPTDSC','Un usuario que pertenece al grupo especificado aqu� jam�s ser� bloqueado.<br />(Recomiendo activar al Administrador.)');

define($constpref.'_DISABLES','Deshabilita caracter�sticas peligrosas de XOOPS');

define($constpref.'_BIGUMBRELLA','Habilitar anti-XSS (BigUmbrella)');
define($constpref.'_BIGUMBRELLADSC','Esto protege de casi cualquier ataque v�a vulnerabilidades XSS. Pero no al 100%');

define($constpref.'_SPAMURI4U','Contra SPAM: URLs para usuarios normales');
define($constpref.'_SPAMURI4UDSC','Si esta cantidad de URLs es hallada en datos ENVIADOS por usuarios diferentes al Administrador, el ENV�O es considerado como SPAM. Cero (0) significa deshabilitar esta caracter�stica.');
define($constpref.'_SPAMURI4G','Contra SPAM: URLs para an�nimos');
define($constpref.'_SPAMURI4GDSC','Si esta cantidad de URLs es hallada en datos ENVIADOS por an�nimos, el ENV�O es considerado como SPAM. Cero (0) significa deshabilitar esta caracter�stica.');

}

?>
