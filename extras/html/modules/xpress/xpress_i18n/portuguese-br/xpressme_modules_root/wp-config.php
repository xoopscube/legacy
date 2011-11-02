<?php
/** Carregamento adicional do XPressME */
require_once dirname( __FILE__ ).'/include/add_xpress_config.php' ;

/**
 * mb_language() define o idioma. Se ele for omitido, ele retorna o idioma atual.
 * Configurações de idioma são utilizadas para codificar mensagens de e-mail. 
 * Exemplos de idiomas válidos são "Japanese", "ja","English", "en" e "uni" (UTF-8). 
 * mb_send_mail() utiliza estas configurações para codificar os e-mails.
 * Idioma e configurações para Japanese, uni e English são respectivamente ISO-2022-JP/Base64, UTF-8/Base64, ISO-8859-1/quoted printable. 
 */
// if (function_exists("mb_language")) mb_language('uni');


// ** Configurações do MySQL - Você pode pegar essas informações com o serviço de hospedagem ** //
// Não altere 'DB_NAME','DB_USER','DB_PASSWORD' & 'DB_HOST'
// pois são obtidos os valores definidos no XOOPS.

/** Não altere. O nome do banco de dados do WordPress */
define('DB_NAME', $xoops_config->xoops_db_name);

/** Não altere. Usuário do banco de dados MySQL */
define('DB_USER', $xoops_config->xoops_db_user);

/** Não altere. Senha do banco de dados MySQL */
define('DB_PASSWORD', $xoops_config->xoops_db_pass);

/** Não altere. Nome do host do MySQL */
define('DB_HOST', $xoops_config->xoops_db_host);
	
/** Conjunto de caracteres do banco de dados a ser usado na criação das tabelas. */
define('DB_CHARSET', 'utf8');

/** O tipo de collate do banco de dados. Não altere isso se tiver dúvidas. */
define('DB_COLLATE', '');

/**#@+
 * Chaves únicas de autenticação.
 *
 * Altere cada chave para um frase única!
 * Você pode gerá-las usando {@link http://api.wordpress.org/secret-key/1.1/ WordPress.org secret-key service}
 *
 * @since 2.6.0
 */
define('AUTH_KEY', 'coloque sua frase única aqui');
define('SECURE_AUTH_KEY', 'coloque sua frase única aqui');
define('LOGGED_IN_KEY', 'coloque sua frase única aqui');
define('NONCE_KEY', 'coloque sua frase única aqui');
define('AUTH_SALT',        'put your unique phrase here');
define('SECURE_AUTH_SALT', 'put your unique phrase here');
define('LOGGED_IN_SALT',   'put your unique phrase here');
define('NONCE_SALT',       'put your unique phrase here');
/**#@-*/

/**
 * Prefixo da tabela do banco de dados do WordPress.
 *
 * Você pode ter várias instalações em um único banco de dados se você der para cada um um único
 * prefixo. Somente números, letras e sublinhados!
 */
// Do not change. $table_prefix is generated from XOOPS DB Priefix and the module directory name. 
$table_prefix  = $xoops_config->module_db_prefix;

/**
 * O idioma localizado do WordPress é o inglês por padrão.
 *
 * Altere esta definição para localizar o WordPress. Um arquivo MO correspondente a
 * língua escolhida deve ser instalado em wp-content/languages. Por exemplo, instale
 * pt_BR.mo em wp-content/languages e altere WPLANG para 'pt_BR' para habilitar o suporte
 * ao português do Brasil.
 */
define ('WPLANG', 'pt_BR');

/* Isto é tudo, pode parar de editar! :) */

/** Caminho absoluto do WordPress para o diretório Wordpress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Processamento do XPressME finalizado */
require_once( ABSPATH .'/include/add_xpress_process.php');

require_once(ABSPATH.'wp-settings.php');
?>
