<?php
/**
 * Protector module for XCL - Administration panel.
 * Protector Administration Security
 * @package    Protector
 * @version    XCL 2.3.1
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2022 Authors
 * @license    GPL v2.0
 */

$db =& Database::getInstance();

// RENDER
xoops_cp_header();
include __DIR__ . '/mymenu.php';


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
        $( ".accordion" ).accordion({
            collapsible: true,
            active: false, /* stat collapsed by default */
            heightStyle: "content", /* "auto" height of the tallest panel. "fill" expand to parent height. "content": Each panel height as its content */
        });
    } );
    // UI toggle view options
    function toggle(className, obj) {
    $(className).toggle(750,"easeOutQuint", obj.checked )
    }
</script>
EOD;

// ui-card-full
echo "<div class='ui-card-full'>\n";

// Title
echo '<h2><svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" width="1em" height="1em" viewBox="0 0 32 32">
    <path d="M16 30l-6.176-3.293A10.982 10.982 0 0 1 4 17V4a2.002 2.002 0 0 1 2-2h20a2.002 2.002 0 0 1 2 2v13a10.982 10.982 0 0 1-5.824 9.707zM6 4v13a8.985 8.985 0 0 0 4.766 7.942L16 27.733l5.234-2.79A8.985 8.985 0 0 0 26 17V4z" fill="#626262"/><path d="M16 25.277V6h8v10.805a7 7 0 0 1-3.7 6.173z" fill="currentColor"/>
    </svg> ' ._AM_ADV_TITLE .'</h2>';

echo '<div class="tips">'. _AM_ADV_TITLE_TIP .'</div>';

// Check the type of server
// Perform access control Apache | Nginx
if ( false !== stripos( $_SERVER["SERVER_SOFTWARE"], 'nginx' ) ) {

	// header("X-Accel-Redirect: ../data/server_nginx.html");
	echo '<hr>
    <h2><svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" width="1em" height="1em" viewBox="0 0 24 24">
    <path d="M12 0L1.605 6v12L12 24l10.395-6V6L12 0zm6 16.59c0 .705-.646 1.29-1.529 1.29c-.631 0-1.351-.255-1.801-.81l-6-7.141v6.66c0 .721-.57 1.29-1.274 1.29H7.32c-.721 0-1.29-.6-1.29-1.29V7.41c0-.705.63-1.29 1.5-1.29c.646 0 1.38.255 1.83.81l5.97 7.141V7.41c0-.721.6-1.29 1.29-1.29h.075c.72 0 1.29.6 1.29 1.29v9.18H18z" fill="currentColor"/>
    </svg> NginX</h2>'
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
    // Check if the function exists. If not, the custom function
    // returns whatever string is stored in the SERVER_SOFTWARE superglobal variable.
    echo '<h3>Apache</h3>'
         .'<p>' . apache_get_version() . '</p>';

    } else if ( isset( $_SERVER['SERVER_SOFTWARE'] ) ) {
        echo "<p>ELSE IF <input class='switch' type='checkbox'
        name='server-software-info' onclick=\"toggle('.server-software', this)\" value='0'>
        <label for='server-software-info'> ". _AM_ADV_SERVER ."</label></p>
        <div class='server-software' style='display:none'>
        <pre>" . $_SERVER['SERVER_SOFTWARE'] . "</pre>
        </div>\n";
}

// server environment information
echo '<p>'. _AM_ADV_ENV .'</p>';
$protocol = stripos( $_SERVER['SERVER_PROTOCOL'], 'https' ) === 0 ? 'https://' : 'http://';
echo "<h4><input class='switch' type='checkbox' name='server-software-info' onclick=\"toggle('.server-software', this)\" value='0'>
    <label for='server-software-info'> ". _AM_ADV_ENV_LABEL ."</label></h4>";

echo '<div class="server-software" style="display:none">
    <div class="tips">'. _AM_ADV_APACHE .' <code>$_SERVER["SERVER_SOFTWARE"]</code></div>'
     .'<table class="outer">
    <tr><td style="width:25%">'. _AM_ADV_SERVER .'</td><td><strong>' . $_SERVER['SERVER_SOFTWARE'] . '</strong> <code aria-label="php sapi name">' . php_sapi_name() . '</code>
    <code aria-label="GATEWAY_INTERFACE">' . $_SERVER['GATEWAY_INTERFACE'] . '</code>
    <code aria-label="SERVER_PROTOCOL">' . $_SERVER['SERVER_PROTOCOL'] . '</code>
    <strong>Protocol:</strong><code aria-label="Protocol http or https">' . $protocol . '</code></td></tr>
    <tr><td>Server Address : <b>' . $_SERVER['SERVER_ADDR'] . '</b></td><td>Server Name : <b>' . $_SERVER['SERVER_NAME'] . '</b></td></tr>
    <tr><td>HTTP_ACCEPT</td><td><pre><code>' . $_SERVER['HTTP_ACCEPT'] . $_SERVER['HTTP_ACCEPT_LANGUAGE'] . '</code> <code aria-label="HTTP_ACCEPT_ENCODING">' . $_SERVER['HTTP_ACCEPT_ENCODING'] . '</code></pre></td></tr>
    <tr><td>DOCUMENT_ROOT</td><td><pre>' . $_SERVER['DOCUMENT_ROOT'] . '</pre></td></tr>
    <tr><td>SCRIPT_FILENAME</td><td><pre>' . $_SERVER['SCRIPT_FILENAME'] . '</pre></td></tr>
    <tr><td>PHP SELF</td><td><pre>' . $_SERVER['PHP_SELF'] . '</pre></td></tr>
    <tr><td>REQUEST_URI</td><td><pre>' . $_SERVER['REQUEST_URI'] . '</pre></td></tr>
    </table></div>';

// TODO : Modal loading echo phpinfo();
// echo '<h4>PHP Modules</h4>';
// echo '<div style="border:4px solid #ccc; display:block; width:400px; height:height: calc(100vh - 400px); overflow-y: auto">'. phpinfo(INFO_MODULES).'</div>';



// CHECK mainfile.php
echo '<h2><svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" width="1em" height="1em" viewBox="0 0 1024 1024"><path d="M644.7 669.2a7.92 7.92 0 0 0-6.5-3.3H594c-6.5 0-10.3 7.4-6.5 12.7l73.8 102.1c3.2 4.4 9.7 4.4 12.9 0l114.2-158c3.8-5.3 0-12.7-6.5-12.7h-44.3c-2.6 0-5 1.2-6.5 3.3l-63.5 87.8l-22.9-31.9zM688 306v-48c0-4.4-3.6-8-8-8H296c-4.4 0-8 3.6-8 8v48c0 4.4 3.6 8 8 8h384c4.4 0 8-3.6 8-8zm-392 88c-4.4 0-8 3.6-8 8v48c0 4.4 3.6 8 8 8h184c4.4 0 8-3.6 8-8v-48c0-4.4-3.6-8-8-8H296zm184 458H208V148h560v296c0 4.4 3.6 8 8 8h56c4.4 0 8-3.6 8-8V108c0-17.7-14.3-32-32-32H168c-17.7 0-32 14.3-32 32v784c0 17.7 14.3 32 32 32h312c4.4 0 8-3.6 8-8v-56c0-4.4-3.6-8-8-8zm402.6-320.8l-192-66.7c-.9-.3-1.7-.4-2.6-.4s-1.8.1-2.6.4l-192 66.7a7.96 7.96 0 0 0-5.4 7.5v251.1c0 2.5 1.1 4.8 3.1 6.3l192 150.2c1.4 1.1 3.2 1.7 4.9 1.7s3.5-.6 4.9-1.7l192-150.2c1.9-1.5 3.1-3.8 3.1-6.3V538.7c0-3.4-2.2-6.4-5.4-7.5zM826 763.7L688 871.6L550 763.7V577l138-48l138 48v186.7z" fill="currentColor"/>
    </svg>mainfile.php</h2>';

if ( ! defined( 'PROTECTOR_PRECHECK_INCLUDED' ) ) {
    echo '<div class="error">'. _AM_ADV_MAIN_PRECHECK .' &nbsp; <span style="color:red;font-weight:bold;">' . _AM_ADV_NOTSECURE . '</span></div>';
    echo '<p>' . _AM_ADV_MAINUNPATCHED . '</p>';
} elseif ( ! defined( 'PROTECTOR_POSTCHECK_INCLUDED' ) ) {
    echo '<div class="error">'. _AM_ADV_MAIN_POSTCHECK .' <span style="color:red;font-weight:bold;">' . _AM_ADV_NOTSECURE . '</span></div>';
    echo '<p>' . _AM_ADV_MAINUNPATCHED . '</p>';
} else {
    echo '<div class="success"><span style="color:green;font-weight:bold;">ok</span></div>';
}


// CHECK TRUST_PATH
echo '<h2><svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" width="1em" height="1em" viewBox="0 0 24 24">
    <path d="M22 10H12v7.382c0 1.409.632 2.734 1.705 3.618H3a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h7.414l2 2H21a1 1 0 0 1 1 1v4zm-8 2h8v5.382c0 .897-.446 1.734-1.187 2.23L18 21.499l-2.813-1.885A2.685 2.685 0 0 1 14 17.383V12z" fill="currentColor"/>
    </svg> TRUST PATH</h2>';

echo "<div class='confirm'>
    Public check [ image ]  
    <img class='message-warning' src='" . XOOPS_URL . '/' . htmlspecialchars( $relative_path ) . "/modules/protector/public_check.png' width='40' height='25' alt='NG' style='border:1px solid black;margin-left:2em;'>
    </div>";

echo "<div class='confirm'>
    Public check [ link ] : <a href='" . XOOPS_URL . '/' . htmlspecialchars( $relative_path ) . "/modules/protector/public_check.php' target='_blank'>" . _AM_ADV_TRUSTPATH_PUBLIC_LINK . "</a>
    </div>";

echo '<div class="accordion" id="trust_path" data-layout"column" data-self="size-1of2 sm-full">
    <h3>' . _AM_TH_INFO . '</h3>
    <div>' . _AM_ADV_TRUSTPATH_PUBLIC . '</div>
    <h3>' . _AM_TH_DESC . '</h3>
    <div>' . _AM_ADV_TRUSTPATH_DESC . '</div>
    <h3>'. _AM_TH_TIPS . '</h3>
    <div>' . _AM_ADV_TRUSTPATH_TIPS . '</div>
</div>';


// CHECK allow_url_fopen
echo '<h2><svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" width="1em" height="1em" viewBox="0 0 24 24"><path d="M13 19h1a1 1 0 0 1 1 1h7v2h-7a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1H2v-2h7a1 1 0 0 1 1-1h1v-1.66C8.07 16.13 6 13 6 9.67v-4L12 3l6 2.67v4c0 3.33-2.07 6.46-5 7.67V19M12 5L8 6.69V10h4V5m0 5v6c1.91-.47 4-2.94 4-5v-1h-4z" fill="currentColor"/>
    </svg> '. _AM_ADV_FOPEN .'</h2>';

$safe = ! ini_get( 'allow_url_fopen' );
if ( $safe ) {
	echo '<div class="success">[ off ] &nbsp; <span style="color:green;font-weight:bold;">ok</span></div>';
} else {
	echo '<div class="error">[ on ] &nbsp; <span style="color:red;font-weight:bold;">' . _AM_ADV_NOTSECURE . '</span></div>';

echo '<div class="accordion" id="fopen" data-layout"column" data-self="size-1of2 sm-full">
    <h3>' . _AM_TH_INFO . '</h3>
    <div>' . _AM_ADV_FOPEN_ON . '</div>
    <h3>' . _AM_TH_DESC . '</h3>
    <div>' . _AM_ADV_FOPEN_DESC . '</div>
    <h3>' . _AM_TH_TIPS . '</h3>
    <div>' . _AM_ADV_FOPEN_TIPS . '</div>
</div>';
}


// CHECK session.use_trans_sid
echo '<h2><svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" width="1em" height="1em" viewBox="0 0 24 24">
    <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12c5.16-1.26 9-6.45 9-12V5l-9-4m0 4a3 3 0 0 1 3 3a3 3 0 0 1-3 3a3 3 0 0 1-3-3a3 3 0 0 1 3-3m5.13 12A9.69 9.69 0 0 1 12 20.92A9.69 9.69 0 0 1 6.87 17c-.34-.5-.63-1-.87-1.53c0-1.65 2.71-3 6-3s6 1.32 6 3c-.24.53-.53 1.03-.87 1.53z" fill="currentColor"/>
    </svg> session.use_trans_sid</h2>';

$safe = ! ini_get( 'session.use_trans_sid' );
if ( $safe ) {
	echo '<div class="success">[ off ] &nbsp; <span style="color:green;font-weight:bold;">ok</span></div>';
} else {
	echo '<div class="error">[ on ] &nbsp; <span style="color:red;font-weight:bold;">' . _AM_ADV_NOTSECURE . '</span></div>';

    echo '<div class="accordion" id="sid" data-layout"column" data-self="size-1of2 sm-full">
    <h3>' . _AM_TH_INFO . '</h3>
    <div>' . _AM_ADV_SESSION_ON . '</div>
    <h3>' . _AM_TH_DESC . '</h3>
    <div>' . _AM_ADV_SESSION_DESC . '</div>
    <h3>' . _AM_TH_TIPS . '</h3>
    <div>' . _AM_ADV_SESSION_TIPS . '</div>
    </div>';
}


// Database PREFIX
echo '<h2><svg xmlns="http://www.w3.org/2000/svg" focusable="false" width="1em" height="1em" viewBox="0 0 24 24"><path d="M3 1h16a1 1 0 0 1 1 1v4a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1m0 8h16a1 1 0 0 1 1 1v.67l-2.5-1.11l-6.5 2.88V15H3a1 1 0 0 1-1-1v-4a1 1 0 0 1 1-1m0 8h8c.06 2.25 1 4.4 2.46 6H3a1 1 0 0 1-1-1v-4a1 1 0 0 1 1-1M8 5h1V3H8v2m0 8h1v-2H8v2m0 8h1v-2H8v2M4 3v2h2V3H4m0 8v2h2v-2H4m0 8v2h2v-2H4m13.5-7l4.5 2v3c0 2.78-1.92 5.37-4.5 6c-2.58-.63-4.5-3.22-4.5-6v-3l4.5-2m0 1.94L15 15.06v2.66c0 1.54 1.07 2.98 2.5 3.34v-7.12z" fill="currentColor"/>
    </svg> Database Prefix</h2>';

$safe = 'xoops' != strtolower( XOOPS_DB_PREFIX );
if ( $safe ) {
	echo '<div class="success">[ ' . XOOPS_DB_PREFIX . ' ] &nbsp; <span style="color:green;font-weight:bold;">ok</span></div>';
} else {
	echo '<div class="error">[ ' . XOOPS_DB_PREFIX . ' ] &nbsp; <span style="color:red;font-weight:bold;">' . _AM_ADV_NOTSECURE . '</span></div>';

    echo '<div class="accordion" id="prefix" data-layout"column" data-self="size-1of2 sm-full">
    <h3>' . _AM_TH_INFO . '</h3>
    <div>' . _AM_ADV_DBPREFIX_ON . '</div>
    <h3>' . _AM_TH_DESC . '</h3>
    <div>' . _AM_ADV_DBPREFIX_DESC . '</div>
    <h3>' . _AM_TH_TIPS . '</h3>
    <div>' . _AM_ADV_DBPREFIX_TIPS . '</div>
    </div>';
}


// Check databasefactory.php
echo '<h2><svg xmlns="http://www.w3.org/2000/svg" focusable="false" width="1em" height="1em" viewBox="0 0 24 24">
    <path d="M3 1h16a1 1 0 0 1 1 1v4a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1m0 8h16a1 1 0 0 1 1 1v.67l-2.5-1.11l-6.5 2.88V15H3a1 1 0 0 1-1-1v-4a1 1 0 0 1 1-1m0 8h8c.06 2.25 1 4.4 2.46 6H3a1 1 0 0 1-1-1v-4a1 1 0 0 1 1-1M8 5h1V3H8v2m0 8h1v-2H8v2m0 8h1v-2H8v2M4 3v2h2V3H4m0 8v2h2v-2H4m0 8v2h2v-2H4m13.5-7l4.5 2v3c0 2.78-1.92 5.37-4.5 6c-2.58-.63-4.5-3.22-4.5-6v-3l4.5-2m0 1.94L15 15.06v2.66c0 1.54 1.07 2.98 2.5 3.34v-7.12z" fill="currentColor"/>
    </svg> databasefactory.php</h2>';

$db =& Database::getInstance();
if ( 'protectormysqldatabase' != strtolower( get_class( $db ) ) ) {
	echo '<div class="error"><span style="color:red;font-weight:bold;">' . _AM_ADV_DBFACTORYUNPATCHED . '</span></div>';

    echo '<div class="accordion" id="db" data-layout"column" data-self="size-1of2 sm-full">
    <h3>' . _AM_TH_INFO . '</h3>
    <div>' . _AM_ADV_DBFACTORY_ON . '</div>
    <h3>' . _AM_TH_DESC . '</h3>
    <div>' . _AM_ADV_DBFACTORY_DESC . '</div>
    <h3>' . _AM_TH_TIPS . '</h3>
    <div>' . _AM_ADV_DBFACTORY_TIPS . '</div>
    </div>';
} else {
	echo '<div class="success"><span style="color:green;font-weight:bold;">ok</span> ' . _AM_ADV_DBFACTORYPATCHED . '</div>';
}


// PROTECTION CHECK TEST
echo '<h2>' . _AM_ADV_SUBTITLECHECK . '</h2>';

// Check contamination
$uri_contami = XOOPS_URL . '/index.php?xoopsConfig%5Bnocommon%5D=1';
echo "<div class='tips'><p>" . _AM_ADV_CHECKCONTAMI . ":</p>";
echo "<p><a href='$uri_contami' target='_blank'>$uri_contami</a></p></div>";

// Check isolated comments
$uri_isocom = XOOPS_URL . '/index.php?cid=' . urlencode( ',password /*' );
echo "<div class='tips'>" . _AM_ADV_CHECKISOCOM . ":</p>\n";
echo "<p><a href='$uri_isocom' target='_blank'>$uri_isocom</a></div>";

echo '</div>'; // ui-card

echo $js_accordion;

xoops_cp_footer();

