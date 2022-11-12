<?php
/**
 * Protector module for XCL - Administration panel.
 * @package    Protector
 * @version    XCL 2.3.1
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2022 Authors
 * @license    GPL v2.0
 */

require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
require_once dirname( __DIR__ ) . '/class/gtickets.php';

( method_exists( 'MyTextSanitizer', 'sGetInstance' ) and $myts = &MyTextSanitizer::sGetInstance() ) || $myts = &MyTextSanitizer::getInstance();

$db = &Database::getInstance();

// GET vars
$pos = empty( $_GET['pos'] ) ? 0 : (int) $_GET['pos'];
$num = empty( $_GET['num'] ) ? 20 : (int) $_GET['num'];

// Table Name
$log_table = $db->prefix( $mydirname . '_log' );

// Protector object
require_once dirname( __DIR__ ) . '/class/protector.php';
$db        = &Database::getInstance();
$protector = Protector::getInstance( $db->conn );
$conf      = $protector->getConf();

// transaction stage

if ( ! empty( $_POST['action'] ) ) {

    // Ticket check
    if ( ! $xoopsGTicket->check( true, 'protector_admin' ) ) {
        redirect_header( XOOPS_URL . '/', 3, $xoopsGTicket->getErrors() );
    }

    if ( 'update_ips' == $_POST['action'] ) {
        $error_msg = '';

        $lines   = empty( $_POST['bad_ips'] ) ? [] : explode( "\n", trim( $_POST['bad_ips'] ) );
        $bad_ips = [];
        foreach ( $lines as $line ) {
            @[ $bad_ip, $jailed_time ]  = explode( '-', $line, 2 );
            $bad_ips[ trim( $bad_ip ) ] = empty( $jailed_time ) ? 0x7fffffff : (int) $jailed_time;
        }
        if ( ! $protector->write_file_badips( $bad_ips ) ) {
            $error_msg .= _AM_MSG_BADIPSCANTOPEN;
        }

        $group1_ips = empty( $_POST['group1_ips'] ) ? [] : explode( "\n", trim( $_POST['group1_ips'] ) );
        foreach ( array_keys( $group1_ips ) as $i ) {
            $group1_ips[ $i ] = trim( $group1_ips[ $i ] );
        }
        $fp = @fopen( $protector->get_filepath4group1ips(), 'w' );
        if ( $fp ) {
            @flock( $fp, LOCK_EX );
            fwrite( $fp, serialize( array_unique( $group1_ips ) ) . "\n" );
            @flock( $fp, LOCK_UN );
            fclose( $fp );
        } else {
            $error_msg .= _AM_MSG_GROUP1IPSCANTOPEN;
        }

        $redirect_msg = $error_msg ?: _AM_MSG_IPFILESUPDATED;
        redirect_header( 'index.php', 2, $redirect_msg );
        exit;
    } elseif ( 'delete' == $_POST['action'] && isset( $_POST['ids'] ) && is_array( $_POST['ids'] ) ) {
        // remove selected records
        foreach ( $_POST['ids'] as $lid ) {
            $lid = (int) $lid;
            $db->query( "DELETE FROM $log_table WHERE lid='$lid'" );
        }
        redirect_header( 'index.php', 2, _AM_MSG_REMOVED );
        exit;
    } elseif ( 'deleteall' == $_POST['action'] ) {
        // remove all records
        $db->query( "DELETE FROM $log_table" );
        redirect_header( 'index.php', 2, _AM_MSG_REMOVED );
        exit;
    } elseif ( 'compactlog' == $_POST['action'] ) {
        // compactize records (removing duplicated records (ip,type)
        $result = $db->query( "SELECT `lid`,`ip`,`type` FROM $log_table ORDER BY lid DESC" );
        $buf    = [];
        $ids    = [];
        while ( list( $lid, $ip, $type ) = $db->fetchRow( $result ) ) {
            if ( isset( $buf[ $ip . $type ] ) ) {
                $ids[] = $lid;
            } else {
                $buf[ $ip . $type ] = true;
            }
        }
        $db->query( "DELETE FROM $log_table WHERE lid IN (" . implode( ',', $ids ) . ')' );
        redirect_header( 'index.php', 2, _AM_MSG_REMOVED );
        exit;
    }
}


// query for listing
$rs = $db->query( "SELECT count(lid) FROM $log_table" );
list( $numrows ) = $db->fetchRow( $rs );
$prs = $db->query( "SELECT l.lid, l.uid, l.ip, l.agent, l.type, l.description, UNIX_TIMESTAMP(l.timestamp), u.uname FROM $log_table l LEFT JOIN " . $db->prefix( 'users' ) . " u ON l.uid=u.uid ORDER BY timestamp DESC LIMIT $pos,$num" );

// Page Navigation
$nav      = new XoopsPageNav( $numrows, $num, $pos, 'pos', "num=$num" );
$nav_html = $nav->renderNav( 10 );

// Number selection
$num_options = '';
$num_array   = [ 20, 100, 500, 2000 ];
foreach ( $num_array as $n ) {
    if ( $n == $num ) {
        $num_options .= "<option value='$n' selected='selected'>$n</option>\n";
    } else {
        $num_options .= "<option value='$n'>$n</option>\n";
    }
}

// RENDER
xoops_cp_header();
include __DIR__ . '/mymenu.php';

// title
echo "<h2>" . $xoopsModule->name() . "</h2>\n";

////— ACTION-CONTROL —\\\\
echo '<section data-layout"row center-justify" class="action-control">
<div><!-- Filters --></div>
    <div class="control-view">
        <a class="button" href="'. XOOPS_URL .'/modules/legacy/admin/index.php?action=PreferenceEdit&confcat_id=1#ip_ban">'. _AM_TH_IP_BAN .'</a>
        <button class="help-admin button" type="button" data-module="protector" data-help-article="#help-blacklist" aria-label="'._HELP .'">
            <b>?</b>
        </button>
    </div>
</section>';
// -----/ CONTROL-ACTION

// configs writable check
if ( ! is_writable( dirname( __DIR__ ) . '/configs' ) ) {
    printf( "<p style='color:red;font-weight:bold;'>" . _AM_FMT_CONFIGSNOTWRITABLE . "</p>\n", dirname( __DIR__ ) . '/configs' );
}

// bad_ips
$bad_ips = $protector->get_bad_ips( true );
uksort( $bad_ips, 'protector_ip_cmp' );
$bad_ips4disp = '';
foreach ( $bad_ips as $bad_ip => $jailed_time ) {
    $line         = $jailed_time ? $bad_ip . '-' . $jailed_time : $bad_ip;
    $line         = str_replace( '-2147483647', '', $line ); // remove :0x7fffffff
    $bad_ips4disp .= htmlspecialchars( $line, ENT_QUOTES ) . "\n";
}

// group1_ips
$group1_ips = $protector->get_group1_ips();
usort( $group1_ips, 'protector_ip_cmp' );
$group1_ips4disp = htmlspecialchars( implode( "\n", $group1_ips ), ENT_QUOTES );

// edit configs about IP ban and IPs for group=1
echo "<form name='ConfigForm' action='' method='POST'>"
    . $xoopsGTicket->getTicketHtml( __LINE__, 1800, 'protector_admin' )
    . "<input type='hidden' name='action' value='update_ips'>
    <table class='outer'>
    <tbody>
    <tr>
        <td class='head'>" . _AM_TH_BADIPS . "</td>
        <td class='even'>
        <textarea name='bad_ips' id='bad_ips' rows='4' style='width:200px;'>$bad_ips4disp</textarea>
        <br>
        " . htmlspecialchars( $protector->get_filepath4badips() ) . "
        </td>
    </tr>
    <tr>
        <td class='head'>" . _AM_TH_GROUP1IPS . "</td>
        <td class='even'>
        <textarea name='group1_ips' id='group1_ips' rows='4' style='width:200px;'>$group1_ips4disp</textarea>
        <br>
        " . htmlspecialchars( $protector->get_filepath4group1ips() ) . "
        </td>
    </tr>
    </tbody>
    <tfoot>
    <tr>
        <td colspan='2'><input type='submit' value='" . _GO . "'></td>
    </tr>
    </tfoot>
</table>
</form>";

// LOG Pagination
echo "<form action='' method='GET'>
<div data-layout'row center-justify my-6'>
    <div>
    <select name='num' onchange='submit();'>$num_options</select>
    <input type='submit' value='" . _SUBMIT . "'>
    </div>
    <div data-self='right' class='pagenavi'>$nav_html</div>
</div>
</form>";

echo "<form name='MainForm' action='' method='POST'>
" . $xoopsGTicket->getTicketHtml( __LINE__, 1800, 'protector_admin' ) . "
<input type='hidden' name='action' value=''>
<table class='outer'>
<thead>
<tr>
    <th class='list_id'><input type='checkbox' name='dummy' onclick=\"with(document.MainForm){for(i=0;i<length;i++){if(elements[i].type=='checkbox'){elements[i].checked=this.checked;}}}\"></th>
    <th>" . _AM_TH_DATETIME . '</th>
    <th>' . _AM_TH_USER . '</th>
    <th>' . _AM_TH_IP . ' - ' . _AM_TH_AGENT . '</th>
    <th>' . _AM_TH_TYPE . '</th>
    <th>' . _AM_TH_DESC . '</th>
</tr>
</thead>';

// body of log listing
while ( list( $lid, $uid, $ip, $agent, $type, $description, $timestamp, $uname ) = $db->fetchRow( $prs ) ) {

    $ip          = htmlspecialchars( $ip, ENT_QUOTES );
    $type        = htmlspecialchars( $type, ENT_QUOTES );
    $description = htmlspecialchars( $description, ENT_QUOTES );
    $uname       = htmlspecialchars( ( $uid ? $uname : _GUESTS ), ENT_QUOTES );

    // make agents shorter
    if ( preg_match( '/MSIE\s+([0-9.]+)/', $agent, $regs ) ) {
        $agent_short = 'IE ' . $regs[1];
    } elseif ( false !== stripos( $agent, 'Gecko' ) ) {
        $agent_short = strrchr( $agent, ' ' );
    } else {
        $agent_short = substr( $agent, 0, strpos( $agent, ' ' ) );
    }
    $agent4disp = htmlspecialchars( $agent, ENT_QUOTES );
    $agent_desc = $agent == $agent_short ? $agent4disp : htmlspecialchars( $agent_short, ENT_QUOTES ) . "<img src='../images/dotdotdot.gif' alt='$agent4disp' aria-label='$agent4disp'>";

    echo "<tbody>
    <tr>
    <td><input type='checkbox' name='ids[]' value='$lid'></td>
    <td>" . formatTimestamp( $timestamp ) . "</td>
    <td>$uname</td>
    <td>$ip<br>$agent_desc</td>
    <td>$type</td>
    <td>$description</td>
    </tr>
    </tbody>\n";
}

// footer of log listing
echo "<tfoot>
    <tr>
    <td colspan='3'> 
    <input type='button' value='" . _AM_BUTTON_REMOVE . "' onclick='if(confirm(\"" . _AM_JS_REMOVECONFIRM . "\")){document.MainForm.action.value=\"delete\"; submit();}'>
    " . _AM_LABEL_REMOVE . "
    </td>
    <td colspan='5'> 
    <input type='button' value='" . _AM_BUTTON_COMPACTLOG . "' onclick='if(confirm(\"" . _AM_JS_COMPACTLOGCONFIRM . "\")){document.MainForm.action.value=\"compactlog\"; submit();}'>
    " . _AM_LABEL_COMPACTLOG . " 
    <input type='button' value='" . _AM_BUTTON_REMOVEALL . "' onclick='if(confirm(\"" . _AM_JS_REMOVEALLCONFIRM . "\")){document.MainForm.action.value=\"deleteall\"; submit();}'>
    " . _AM_LABEL_REMOVEALL . " 
    </td>
  </tr>
  </tfoot>
</table>
<div class='pagenavi'>$nav_html</div>
</form>\n";

xoops_cp_footer();

function protector_ip_cmp( $a, $b ) {
    $as   = explode( '.', $a );
    $aval = @$as[0] * 167777216 + @$as[1] * 65536 + @$as[2] * 256 + @$as[3];
    $bs   = explode( '.', $b );
    $bval = @$bs[0] * 167777216 + @$bs[1] * 65536 + @$bs[2] * 256 + @$bs[3];

    return $aval > $bval ? 1 : - 1;
}
