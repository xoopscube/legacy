<?php
/**
 * Protector Security Advisory
 * 
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster
 * @copyright  (c) 2024 The XOOPSCube Project
 * @license    GPL v2.0
 */


// RENDER
xoops_cp_header();
include __DIR__ . '/mymenu.php';

// Get module handlers
$module_handler = xoops_getHandler('module');
$config_handler = xoops_getHandler('config');

// Get protector module and its configs
$protector_module = $module_handler->getByDirname('protector');
$protector_configs = $config_handler->getConfigsByCat(0, $protector_module->getVar('mid'));

// Check if protection is disabled
$is_protection_active = empty($protector_configs['global_disabled']);

// Get database instance using the factory pattern
$db =& XoopsDatabaseFactory::getDatabaseConnection();

// calculate the relative path between XOOPS_ROOT_PATH and XOOPS_TRUST_PATH
$root_paths  = explode( '/', XOOPS_ROOT_PATH );
$trust_paths = explode( '/', XOOPS_TRUST_PATH );
foreach ( $root_paths as $i => $rpath ) {
	if ( $rpath != $trust_paths[ $i ] ) {
		break;
	}
}
$relative_path = str_repeat( '../', count( $root_paths ) - $i ) . implode( '/', array_slice( $trust_paths, $i ) );

// Helper - Accordion
$js_accordion = <<<EOD
<script>
    $( function() {
        $( ".accordion-advice" ).accordion({
            collapsible: true,
            active: 0, /* false to start collapsed by default */
            heightStyle: "content", /* "auto" height of the tallest panel. "fill" expand to parent height. "content": Each panel height as its content */
        });
    } );
</script>
EOD;

// ui-card-full
echo "<div>\n";

// Title
echo '<h2><svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" width="1em" height="1em" viewBox="0 0 32 32">
    <path d="M16 30l-6.176-3.293A10.982 10.982 0 0 1 4 17V4a2.002 2.002 0 0 1 2-2h20a2.002 2.002 0 0 1 2 2v13a10.982 10.982 0 0 1-5.824 9.707zM6 4v13a8.985 8.985 0 0 0 4.766 7.942L16 27.733l5.234-2.79A8.985 8.985 0 0 0 26 17V4z" fill="#626262"/><path d="M16 25.277V6h8v10.805a7 7 0 0 1-3.7 6.173z" fill="currentColor"/>
    </svg> ' ._AM_ADV_TITLE .'</h2>';

echo '<div class="tips">'. _AM_ADV_TITLE_TIP .'</div>';

// Check the type of server
// Perform access control Apache | Nginx
echo '<hr /><h2>Server Information</h2>';
echo '<p>Security configurations vary depending on the web server. Identifying your server type is essential for applying the appropriate security measures.</p>';

if ( false !== stripos( $_SERVER["SERVER_SOFTWARE"], 'nginx' ) ) {

	// header("X-Accel-Redirect: ../data/server_nginx.html");
	echo '<h3><svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" width="1em" height="1em" viewBox="0 0 24 24">
    <path d="M12 0L1.605 6v12L12 24l10.395-6V6L12 0zm6 16.59c0 .705-.646 1.29-1.529 1.29c-.631 0-1.351-.255-1.801-.81l-6-7.141v6.66c0 .721-.57 1.29-1.274 1.29H7.32c-.721 0-1.29-.6-1.29-1.29V7.41c0-.705.63-1.29 1.5-1.29c.646 0 1.38.255 1.83.81l5.97 7.141V7.41c0-.721.6-1.29 1.29-1.29h.075c.72 0 1.29.6 1.29 1.29v9.18H18z" fill="currentColor"/>
    </svg> NginX</h3>'
	.'<p>' ._AM_ADV_NGINX .'</p>';

	// toggle var dump
	echo "<h4><input class='switch' type='checkbox' name='server-nginx' onclick=\"toggle('.nginx', this)\" value='0'>
    <label for='server-nginx'> ". _AM_ADV_NGINX_VAR ."</label></h4>";

	// Nginx var dump
	echo '<div class="nginx" style="display:none">
    <pre class="tips" style="display:block; max-width:100%; margin:auto; height:400px;white-space: pre-wrap; overflow-y: auto">';
	echo var_export( $_SERVER );
	echo '</pre></div>';

} else if ( false !== stripos( $_SERVER["SERVER_SOFTWARE"], 'apache' ) ) {
	if ( ! function_exists( 'apache_get_version' ) ) {
		function apache_get_version() {
			if ( ! isset( $_SERVER['SERVER_SOFTWARE'] ) || '' === $_SERVER['SERVER_SOFTWARE'] ) {
				return false;
			}

			return $_SERVER["SERVER_SOFTWARE"];
		}
	}

    echo '<div data-layout="row sm-column">';
    echo '<div data-self="size-1of2 sm-full">';

    // Check if the function exists. If not, the custom function returns
    // whatever string is stored in the SERVER_SOFTWARE superglobal variable.
    echo "<div class='success'>Apache " . apache_get_version() . "</div>";

    echo '</div>';
    echo '<div data-self="size-1of2 sm-full">';

    echo '<div class="accordion-advice" id="mainfile" data-layout="column" data-self="size-x1 sm-full">
    <h3>' . _AM_TH_INFO . ' </h3>
    <div>Apache HTTP Server combines a robust feature set, reliability, and extensive module system.</div>
    <h3>' . _AM_TH_DESC . '</h3>
    <div>Apache key features include improved performance through the Multi-Processing Modules (MPMs) like Event and Worker, offering better concurrency and resource utilization. It brought enhanced security with features like mod_security 2.x integration and refined access control mechanisms. Furthermore, Apache 2.4 offered greater flexibility with features like expression-based configuration, allowing for more dynamic and sophisticated server setups, and improved support for asynchronous I/O. These advancements contributed to a more efficient, secure, and adaptable web serving experience.</div>
    <h3>'. _AM_TH_TIPS . '</h3>
    <div>Craft rewrite rules carefully in .htaccess to create user-friendly and SEO-optimized URLs without altering the actual file structure, enhancing both navigation and search engine visibility.</div>
    </div>';

    echo '</div>';
    echo '</div>';

    } else if ( isset( $_SERVER['SERVER_SOFTWARE'] ) ) {
        echo "<p><input class='switch' type='checkbox'
        name='server-software-info' onclick=\"toggle('.server-software', this)\" value='0'>
        <label for='server-software-info'> ". _AM_ADV_SERVER ."</label></p>
        <div class='server-software' style='display:none'>
        <pre>" . $_SERVER['SERVER_SOFTWARE'] . "</pre>
        </div>\n";
}

// server environment information
echo '<div class="tips">'. _AM_ADV_ENV .'</div>';
$protocol = stripos( $_SERVER['SERVER_PROTOCOL'], 'https' ) === 0 ? 'https://' : 'http://';
echo "<h4><input class='switch' type='checkbox' name='server-software-info' onclick=\"toggle('.server-software', this)\" value='0'>
    <label for='server-software-info'> ". _AM_ADV_ENV_LABEL ."</label></h4>";

echo '<div class="server-software" style="display:none">
    <div class="tips">'. _AM_ADV_APACHE .' <code>$_SERVER["SERVER_SOFTWARE"]</code></div>'
     .'<table class="outer">
    <tr><td style="width:25%">'. _AM_ADV_SERVER .'</td><td><strong>' . $_SERVER['SERVER_SOFTWARE'] . '</strong> <code title="php sapi name">' . php_sapi_name() . '</code>
    <code title="GATEWAY_INTERFACE">' . $_SERVER['GATEWAY_INTERFACE'] . '</code>
    <code title="SERVER_PROTOCOL">' . $_SERVER['SERVER_PROTOCOL'] . '</code>
    <strong>Protocol:</strong><code title="Protocol http or https">' . $protocol . '</code></td></tr>
    <tr><td>Server Address : <b>' . $_SERVER['SERVER_ADDR'] . '</b></td><td>Server Name : <b>' . $_SERVER['SERVER_NAME'] . '</b></td></tr>
    <tr><td>HTTP_ACCEPT</td><td><pre><code>' . $_SERVER['HTTP_ACCEPT'] . $_SERVER['HTTP_ACCEPT_LANGUAGE'] . '</code> <code title="HTTP_ACCEPT_ENCODING">' . $_SERVER['HTTP_ACCEPT_ENCODING'] . '</code></pre></td></tr>
    <tr><td>DOCUMENT_ROOT</td><td><pre>' . $_SERVER['DOCUMENT_ROOT'] . '</pre></td></tr>
    <tr><td>SCRIPT_FILENAME</td><td><pre>' . $_SERVER['SCRIPT_FILENAME'] . '</pre></td></tr>
    <tr><td>PHP SELF</td><td><pre>' . $_SERVER['PHP_SELF'] . '</pre></td></tr>
    <tr><td>REQUEST_URI</td><td><pre>' . $_SERVER['REQUEST_URI'] . '</pre></td></tr>
    </table></div>';

// TODO : Modal loading echo phpinfo();
// echo '<h4>PHP Modules</h4>';
// echo '<div style="border:4px solid #ccc; display:block; width:400px; height:height: calc(100vh - 400px); overflow-y: auto">'. phpinfo(INFO_MODULES).'</div>';


// CHECK mainfile.php
// TODO: check is writable !
echo '<hr /><h2><svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" width="1em" height="1em" viewBox="0 0 1024 1024"><path d="M644.7 669.2a7.92 7.92 0 0 0-6.5-3.3H594c-6.5 0-10.3 7.4-6.5 12.7l73.8 102.1c3.2 4.4 9.7 4.4 12.9 0l114.2-158c3.8-5.3 0-12.7-6.5-12.7h-44.3c-2.6 0-5 1.2-6.5 3.3l-63.5 87.8l-22.9-31.9zM688 306v-48c0-4.4-3.6-8-8-8H296c-4.4 0-8 3.6-8 8v48c0 4.4 3.6 8 8 8h384c4.4 0 8-3.6 8-8zm-392 88c-4.4 0-8 3.6-8 8v48c0 4.4 3.6 8 8 8h184c4.4 0 8-3.6 8-8v-48c0-4.4-3.6-8-8-8H296zm184 458H208V148h560v296c0 4.4 3.6 8 8 8h56c4.4 0 8-3.6 8-8V108c0-17.7-14.3-32-32-32H168c-17.7 0-32 14.3-32 32v784c0 17.7 14.3 32 32 32h312c4.4 0 8-3.6 8-8v-56c0-4.4-3.6-8-8-8zm402.6-320.8l-192-66.7c-.9-.3-1.7-.4-2.6-.4s-1.8.1-2.6.4l-192 66.7a7.96 7.96 0 0 0-5.4 7.5v251.1c0 2.5 1.1 4.8 3.1 6.3l192 150.2c1.4 1.1 3.2 1.7 4.9 1.7s3.5-.6 4.9-1.7l192-150.2c1.9-1.5 3.1-3.8 3.1-6.3V538.7c0-3.4-2.2-6.4-5.4-7.5zM826 763.7L688 871.6L550 763.7V577l138-48l138 48v186.7z" fill="currentColor"/>
    </svg>mainfile.php</h2>';
    
echo '<div data-layout="row sm-column">';
echo '<div data-self="size-1of2 sm-full">';

if ( ! defined( 'PROTECTOR_PRECHECK_INCLUDED' ) ) {
    echo '<div class="error">'. _AM_ADV_MAIN_PRECHECK .' &nbsp; <span style="color:red;font-weight:bold;">' . _AM_ADV_NOTSECURE . '</span></div>';
    echo '<p>' . _AM_ADV_MAINUNPATCHED . '</p>';
} elseif ( ! defined( 'PROTECTOR_POSTCHECK_INCLUDED' ) ) {
    echo '<div class="error">'. _AM_ADV_MAIN_POSTCHECK .' <span style="color:red;font-weight:bold;">' . _AM_ADV_NOTSECURE . '</span></div>';
    echo '<p>' . _AM_ADV_MAINUNPATCHED . '</p>';
} else {
    echo '<div class="success"><span style="color:green;font-weight:bold;">ok</span> The mainfile.php is properly patched</div>';
}

echo '</div>';
echo '<div data-self="size-1of2 sm-full">';

echo '<div class="accordion-advice" id="mainfile" data-layout="column" data-self="size-x1 sm-full">
    <h3>' . _AM_TH_INFO . ' </h3>
    <div>' ._AM_ADV_MAIN_INFO . '</div>
    <h3>' . _AM_TH_DESC . '</h3>
    <div>' . _AM_ADV_MAIN_DESC . '</div>
    <h3>'. _AM_TH_TIPS . '</h3>
    <div>' ._AM_ADV_MAIN_TIPS . '</div>
</div>';

echo '</div>';
echo '</div>';

// CHECK TRUST_PATH
echo '<hr /><h2><svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" width="1em" height="1em" viewBox="0 0 24 24">
    <path d="M22 10H12v7.382c0 1.409.632 2.734 1.705 3.618H3a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h7.414l2 2H21a1 1 0 0 1 1 1v4zm-8 2h8v5.382c0 .897-.446 1.734-1.187 2.23L18 21.499l-2.813-1.885A2.685 2.685 0 0 1 14 17.383V12z" fill="currentColor"/>
    </svg> TRUST PATH</h2>';

echo '<div data-layout="row sm-column">';
echo '<div data-self="size-1of2 sm-full">';

echo "<div class='confirm'>
    Public check [ image ]  
    <img class='message-warning' src='" . XOOPS_URL . '/' . htmlspecialchars( $relative_path ) . "/modules/protector/public_check.png' width='40' height='25' alt='NG' style='border:1px solid black;margin-left:2em;'>
    </div>";
    echo "<div class='confirm'>
    Public check [ link ] : <a href='" . XOOPS_URL . '/' . htmlspecialchars( $relative_path ) . "/modules/protector/public_check.php' target='_blank'>" . _AM_ADV_TRUSTPATH_PUBLIC_LINK . "</a>
    </div>";

echo '</div>';
echo '<div data-self="size-1of2 sm-full">';

echo '<div class="accordion-advice" id="trust_path" data-layout="column" data-self="size-x1 sm-full">
    <h3>' . _AM_TH_INFO . '</h3>
    <div>' . _AM_ADV_TRUSTPATH_PUBLIC . '</div>
    <h3>' . _AM_TH_DESC . '</h3>
    <div>' . _AM_ADV_TRUSTPATH_DESC . '</div>
    <h3>'. _AM_TH_TIPS . '</h3>
    <div>' . _AM_ADV_TRUSTPATH_TIPS . '</div>
</div>';

echo '</div>';
echo '</div>';

// CHECK allow_url_fopen
echo '<hr /><h2><svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" width="1em" height="1em" viewBox="0 0 24 24"><path d="M13 19h1a1 1 0 0 1 1 1h7v2h-7a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1H2v-2h7a1 1 0 0 1 1-1h1v-1.66C8.07 16.13 6 13 6 9.67v-4L12 3l6 2.67v4c0 3.33-2.07 6.46-5 7.67V19M12 5L8 6.69V10h4V5m0 5v6c1.91-.47 4-2.94 4-5v-1h-4z" fill="currentColor"/>
    </svg> '. _AM_ADV_FOPEN .'</h2>';

echo '<div data-layout="row sm-column">';
echo '<div data-self="size-1of2 sm-full">';

$safe = ! ini_get( 'allow_url_fopen' );
if ( $safe ) {
	echo '<div class="success">[ off ] &nbsp; <span style="color:green;font-weight:bold;">ok</span></div>';
} else {
	echo '<div class="error">[ on ] &nbsp; <span style="color:red;font-weight:bold;">' . _AM_ADV_NOTSECURE . '</span></div>';

echo '</div>';
echo '<div data-self="size-1of2 sm-full">';

echo '<div class="accordion-advice" id="fopen" data-layout="column" data-self="size-x1 sm-full">
    <h3>' . _AM_TH_INFO . '</h3>
    <div>' . _AM_ADV_FOPEN_ON . '</div>
    <h3>' . _AM_TH_DESC . '</h3>
    <div>' . _AM_ADV_FOPEN_DESC . '</div>
    <h3>' . _AM_TH_TIPS . '</h3>
    <div>' . _AM_ADV_FOPEN_TIPS . '</div>
</div>';
}

echo '</div>';
echo '</div>';

// CHECK session.use_trans_sid
echo '<hr /><h2><svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" width="1em" height="1em" viewBox="0 0 24 24">
    <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12c5.16-1.26 9-6.45 9-12V5l-9-4m0 4a3 3 0 0 1 3 3a3 3 0 0 1-3 3a3 3 0 0 1-3-3a3 3 0 0 1 3-3m5.13 12A9.69 9.69 0 0 1 12 20.92A9.69 9.69 0 0 1 6.87 17c-.34-.5-.63-1-.87-1.53c0-1.65 2.71-3 6-3s6 1.32 6 3c-.24.53-.53 1.03-.87 1.53z" fill="currentColor"/>
    </svg> session.use_trans_sid</h2>';

    echo '<div data-layout="row sm-column">';
echo '<div data-self="size-1of2 sm-full">';

$safe = ! ini_get( 'session.use_trans_sid' );
if ( $safe ) {
	echo '<div class="success">[ off ] &nbsp; <span style="color:green;font-weight:bold;">ok</span></div>';
} else {
	echo '<div class="error">[ on ] &nbsp; <span style="color:red;font-weight:bold;">' . _AM_ADV_NOTSECURE . '</span></div>';
}

echo '</div>';
echo '<div data-self="size-1of2 sm-full">';

echo '<div class="accordion-advice" id="sid" data-layout="column" data-self="size-x1 sm-full">
<h3>' . _AM_TH_INFO . '</h3>
<div>' . _AM_ADV_SESSION_ON . '</div>
<h3>' . _AM_TH_DESC . '</h3>
<div>' . _AM_ADV_SESSION_DESC . '</div>
<h3>' . _AM_TH_TIPS . '</h3>
<div>' . _AM_ADV_SESSION_TIPS . '</div>
</div>';

echo '</div>';
echo '</div>';

// Check atabase PREFIX
echo '<hr /><h2><svg xmlns="http://www.w3.org/2000/svg" focusable="false" width="1em" height="1em" viewBox="0 0 24 24"><path d="M3 1h16a1 1 0 0 1 1 1v4a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1m0 8h16a1 1 0 0 1 1 1v.67l-2.5-1.11l-6.5 2.88V15H3a1 1 0 0 1-1-1v-4a1 1 0 0 1 1-1m0 8h8c.06 2.25 1 4.4 2.46 6H3a1 1 0 0 1-1-1v-4a1 1 0 0 1 1-1M8 5h1V3H8v2m0 8h1v-2H8v2m0 8h1v-2H8v2M4 3v2h2V3H4m0 8v2h2v-2H4m0 8v2h2v-2H4m13.5-7l4.5 2v3c0 2.78-1.92 5.37-4.5 6c-2.58-.63-4.5-3.22-4.5-6v-3l4.5-2m0 1.94L15 15.06v2.66c0 1.54 1.07 2.98 2.5 3.34v-7.12z" fill="currentColor"/>
    </svg> Database Prefix</h2>';

echo '<div data-layout="row sm-column">';
echo '<div data-self="size-1of2 sm-full">';

$safe = 'xoops' != strtolower( XOOPS_DB_PREFIX );
if ( $safe ) {
	echo '<div class="success">[ ' . XOOPS_DB_PREFIX . ' ] &nbsp; <span style="color:green;font-weight:bold;">ok</span></div>';
} else {
	echo '<div class="error">[ ' . XOOPS_DB_PREFIX . ' ] &nbsp; <span style="color:red;font-weight:bold;">' . _AM_ADV_NOTSECURE . '</span></div>';
}

echo '</div>';
echo '<div data-self="size-1of2 sm-full">';

echo '<div class="accordion-advice" id="prefix" data-layout="column" data-self="size-x1 sm-full">
<h3>' . _AM_TH_INFO . '</h3>
<div>' . _AM_ADV_DBPREFIX_ON . '</div>
<h3>' . _AM_TH_DESC . '</h3>
<div>' . _AM_ADV_DBPREFIX_DESC . '</div>
<h3>' . _AM_TH_TIPS . '</h3>
<div>' . _AM_ADV_DBPREFIX_TIPS . '</div>
</div>';

echo '</div>';
echo '</div>';

// Check databasefactory.php
echo '<hr /><h2><svg xmlns="http://www.w3.org/2000/svg" focusable="false" width="1em" height="1em" viewBox="0 0 24 24">
    <path d="M3 1h16a1 1 0 0 1 1 1v4a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1m0 8h16a1 1 0 0 1 1 1v.67l-2.5-1.11l-6.5 2.88V15H3a1 1 0 0 1-1-1v-4a1 1 0 0 1 1-1m0 8h8c.06 2.25 1 4.4 2.46 6H3a1 1 0 0 1-1-1v-4a1 1 0 0 1 1-1M8 5h1V3H8v2m0 8h1v-2H8v2m0 8h1v-2H8v2M4 3v2h2V3H4m0 8v2h2v-2H4m0 8v2h2v-2H4m13.5-7l4.5 2v3c0 2.78-1.92 5.37-4.5 6c-2.58-.63-4.5-3.22-4.5-6v-3l4.5-2m0 1.94L15 15.06v2.66c0 1.54 1.07 2.98 2.5 3.34v-7.12z" fill="currentColor"/>
    </svg> Database Protection</h2>';

echo '<div data-layout="row sm-column">';
echo '<div data-self="size-1of2 sm-full">';
// For debugging
echo '<div class="' . ($is_protection_active ? 'success' : 'danger') . '">';
echo 'Protection Status: <strong>' . ($is_protection_active ? 'Active' : '<span style="color:orange;font-weight:bold;">Temporarily Disabled</span>') . '</strong>
<br /> Database class: ' . get_class($db) . '</div>';

/* if ($is_protection_active) {
    echo '<div class="error"><span style="color:orange;font-weight:bold;">Warning!</span> Protection is temporarily disabled in module preferences.</div>';
}  */
// Protection is enabled, now check the database class
if ($is_protection_active === false) {  // Explicitly check that protection is enabled
    // Check if the database class is correct
    if ('protectormysqldatabase' != strtolower(get_class($db))) {
        echo '<div class="error"><span style="color:red;font-weight:bold;">' . _AM_ADV_DBFACTORYUNPATCHED . '</span></div>';
    } 
    // Everything is good - protection is enabled and database class is correct
    else {
        echo '<div class="success"><span style="color:green;font-weight:bold;">ok</span> ' . _AM_ADV_DBFACTORYPATCHED . '</div>';
    }
}
echo '</div>';
echo '<div data-self="size-1of2 sm-full">';
if ($is_protection_active === false) {
    echo '<div class="accordion-advice" id="protection-disabled" data-layout="column" data-self="size-x1 sm-full">
    <h3>' . _AM_TH_INFO . '</h3>
    <div>The database protection is currently disabled because "Temporary disabled" is set to "Yes" in the Protector module preferences. Your site is not protected against SQL injection attacks.</div>
            <h3>' . _AM_TH_DESC . '</h3>
            <div>Note: When protection is disabled, the database class check is skipped as it is expected that the standard database class is used instead of the Protector-enhanced class.</div>
    <h3>' . _AM_TH_TIPS . '</h3>
    <div>To enable protection, go to <a href="index.php">Protector Admin</a> → Preferences, and set "Temporary disabled" to "No".</div>
    </div>';
}
    // Add a note about the database class when protection is disabled
    else if ($is_protection_active === true) { 
    // if ('protectormysqldatabase' != strtolower(get_class($db))) {
echo '<div class="accordion-advice" id="db" data-layout="column" data-self="size-x1 sm-full">
        <h3>' . _AM_TH_INFO . '</h3>
        <div>' . _AM_ADV_DBFACTORY_ON . '</div>
        <h3>' . _AM_TH_DESC . '</h3>
        <div>' . _AM_ADV_DBFACTORY_DESC . '</div>
        <h3>' . _AM_TH_TIPS . '</h3>
        <div>' . _AM_ADV_DBFACTORY_TIPS . '</div>
        </div>';
//    } 
} 
echo '</div>';
echo '</div>';

// Check Content Security Policy
echo '<hr /><h2><svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" width="1em" height="1em" viewBox="0 0 24 24">
    <path d="M12 12h7c-.53 4.11-3.28 7.78-7 8.92V12H5V6.3l7-3.11M12 1L3 5v6c0 5.55 3.84 10.73 9 12c5.16-1.27 9-6.45 9-12V5l-9-4z" fill="currentColor"/>
    </svg> Content Security Policy</h2>';

echo '<div data-layout="row sm-column">';
echo '<div data-self="size-1of2 sm-full">';

// Check if CSP is enabled
$csp_enabled = !empty($protector_configs['enable_csp']);

if ($csp_enabled) {
    echo '<div class="success">[ Enabled ] &nbsp; <span style="color:green;font-weight:bold;">ok</span></div>';
    
    // Check if CSP preload is working
    $preload_file = XOOPS_ROOT_PATH . '/preload/ContentSecurityPolicy.class.php';
    if (file_exists($preload_file)) {
        echo '<div class="success">CSP Preload: <span style="color:green;font-weight:bold;">Found</span></div>';
    } else {
        echo '<div class="error">CSP Preload: <span style="color:red;font-weight:bold;">Not Found</span> - The ContentSecurityPolicy.class.php preload file is missing.</div>';
    }
    
    // Check if report directory exists and is writable
    $log_dir = XOOPS_CACHE_PATH . '/protector/logs';
    if (is_dir($log_dir)) {
        echo '<div class="success">Log Directory: <span style="color:green;font-weight:bold;">Exists</span></div>';
        if (is_writable($log_dir)) {
            echo '<div class="success">Log Directory: <span style="color:green;font-weight:bold;">Writable</span></div>';
        } else {
            echo '<div class="error">Log Directory: <span style="color:red;font-weight:bold;">Not Writable</span> - Please check permissions.</div>';
        }
    } else {
        echo '<div class="error">Log Directory: <span style="color:red;font-weight:bold;">Missing</span> - The logs directory does not exist.</div>';
    }
    
    // Check if report-uri is configured
    if (!empty($protector_configs['csp_report_uri'])) {
        echo '<div class="success">Report URI: <span style="color:green;font-weight:bold;">Configured</span> - ' . htmlspecialchars($protector_configs['csp_report_uri']) . '</div>';
    } else {
        echo '<div class="warning">Report URI: <span style="color:orange;font-weight:bold;">Default</span> - Using default report URI.</div>';
    }
    
    // Check if CSP is in report-only mode
    if (!empty($protector_configs['csp_report_only'])) {
        echo '<div class="warning">Mode: <span style="color:orange;font-weight:bold;">Report-Only</span> - CSP violations will be reported but not enforced.</div>';
    } else {
        echo '<div class="success">Mode: <span style="color:green;font-weight:bold;">Enforcement</span> - CSP violations will be blocked.</div>';
    }
    
} else {
    echo '<div class="error">[ Disabled ] &nbsp; <span style="color:red;font-weight:bold;">' . _AM_ADV_NOTSECURE . '</span></div>';
    echo '<p>Content Security Policy is not enabled. Your site is vulnerable to XSS attacks.</p>';
}

echo '</div>';
echo '<div data-self="size-1of2 sm-full">';

echo '<div class="accordion-advice" id="csp" data-layout="column" data-self="size-x1 sm-full">
    <h3>' . _AM_TH_INFO . '</h3>
    <div>Content Security Policy (CSP) is an added layer of security that helps to detect and mitigate certain types of attacks, including Cross-Site Scripting (XSS) and data injection attacks.</div>
    <h3>' . _AM_TH_DESC . '</h3>
    <div>CSP works by restricting the sources from which your site can load resources such as scripts, styles, images, and more. This helps prevent attackers from injecting malicious content into your pages.</div>
    <h3>' . _AM_TH_TIPS . '</h3>
    <div>
        <p>To enable CSP protection:</p>
        <ol>
            <li>Go to Protector module preferences</li>
            <li>Set "Enable Content Security Policy" to "Yes"</li>
            <li>Configure the CSP directives according to your site\'s needs</li>
            <li>Start with Report-Only mode to identify potential issues</li>
            <li>Switch to Enforcement mode once you\'ve resolved any legitimate violations</li>
        </ol>
        <p>For optimal protection, configure these directives:</p>
        <ul>
            <li>default-src: Controls fallback for other resource types</li>
            <li>script-src: Restricts JavaScript sources</li>
            <li>style-src: Restricts CSS sources</li>
            <li>img-src: Restricts image sources</li>
            <li>connect-src: Restricts URLs for fetch, XHR, WebSocket</li>
        </ul>
    </div>
</div>';

echo '</div>';
echo '</div>';

if ($csp_enabled) {

    echo '<div data-layout="row sm-column">';
    echo '<div data-self="size-1of2 sm-full">';

    // Link to test CSP
    echo '<div class="confirm"><p>Test your CSP configuration:</p>';
    echo '<p><a href="' . XOOPS_URL . '/modules/protector/test-csp.php" target="_blank" class="button">Run CSP Test</a></p></div>';
    echo '</div>';
    echo '<div data-self="size-1of2 sm-full">';

    // Link to view violations
    echo '<div class="confirm"><p>View CSP violations:</p>';
    echo '<p><a href="index.php?page=csp_violations" target="_blank" class="button">CSP Violations Log</a></p></div>';
    echo '</div>';
    echo '</div>';
}

// PROTECTION CHECK TEST
echo '<hr /><h2>' . _AM_ADV_SUBTITLECHECK . '</h2>';

echo '<div data-layout="row sm-column">';
echo '<div data-self="size-1of2 sm-full">';

// Check contamination
$uri_contami = XOOPS_URL . '/index.php?xoopsConfig%5Bnocommon%5D=1';
echo "<div class='confirm'>
<p>" . _AM_ADV_CHECKCONTAMI . ":</p>
<p><a href='$uri_contami' target='_blank' class='button'>Test injection</a></p>
<p>This test attempts injection vulnerabilities. 
Protector should prevent the injection of malicious code or data into queries. 
<strong>This test will be logged in the security log.</strong></p>
</div>";

echo '</div>';
echo '<div data-self="size-1of2 sm-full">';

// Check isolated comments
// Create multiple test variations to better understand the behavior
$uri_isocom_simple = XOOPS_URL . '/index.php?cid=' . urlencode('1 /*');
// Development test only
// $uri_isocom_post = XOOPS_URL . '/modules/protector/isolated_test.php';
// $uri_isocom_direct = XOOPS_URL . '/index.php?cid=' . urlencode('1;/*') . '&test=isolated';

echo "<div class='confirm'>
<p>" . _AM_ADV_CHECKISOCOM . ":</p>
<p><a href='$uri_isocom_simple' target='_blank' class='button'>Basic Test</a></p>";
// Development test only
// echo "<p><a href='$uri_isocom_direct' target='_blank' class='button'>Advanced Test</a></p>";

echo '<p>This test attempts an SQL injection using isolated comments.
Protector should redirect to homepage. 
<strong>This test will NOT be logged in the security log.</strong></p>';

// Development test only
/* echo "Here's what to expect:</p>
<ul>
    <li><strong>If set to 'None (only logging)':</strong> Should log without redirecting (but may still redirect)</li>
    <li><strong>If set to 'Blank screen':</strong> Should show a blank page with no redirect</li>
    <li><strong>If set to 'Exit':</strong> Should terminate with a message</li>
    <li><strong>If set to 'Sanitizing':</strong> Should fix the input and continue</li>
</ul>
<p>For a more reliable test, try this POST form:</p>
<form action='$uri_isocom_post' method='post' target='_blank'>
    <input type='hidden' name='test_comment' value='1 /*'>
    <input type='submit' class='button' value='Test via POST'>
</form>"; */
echo '</div>';

echo '</div>';
echo '</div>'; // ui-card

echo $js_accordion;

xoops_cp_footer();
