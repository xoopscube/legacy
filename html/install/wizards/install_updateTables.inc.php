<?php
/**
 * @package    XCL
 * @subpackage Installation Wizard
 * @version    XCL 2.5.0
 * @author     kilica, 2008/09/25
 * @copyright  (c) 2005-2024 The XOOPSCube Project
 * @license    GPL 2.0
 */

include_once '../mainfile.php';
include_once './class/dbmanager.php';

$db = new db_manager;
$sql = 'SELECT * FROM '.$db->prefix('groups');
$result = $db->query($sql);


##### Extracted from Test
if ( ! $db -> isConnectable() ) {
    echo "";
    echo '<div class="confirmError"><h2>Test DB</h2><p>Error Establishing a Database Connection : <code style="color:darkorange">' . $db->isConnectable() . '</code></p></div>';
    } else {
    echo '<div class="confirmInfo"><h1>Test Group Type</h1><p>Connect to Database MySQL : <code style="color:limegreen">' . $db -> isConnectable() .'</code></p>';
    echo '<h3>mysqli_fetch_array User Groups :</h3>';
        while($row = mysqli_fetch_array($result)){
            echo '<p><code style="color:limegreen">'. $row['group_type'] .'</code>';
        }
    echo "</div>";
    // exit();
}
##### Extracted from Test


$content = '<h5>'._INSTALL_L157.'</h5>';

$content .= '<table><tr><td>'._INSTALL_L158.'</td><td>'._INSTALL_L159.'</td><td>'._INSTALL_L160.'</td><td>'._INSTALL_L161.'</td></tr>';

/*
 * Deprecated versions used row = ['type']
 * SQL column : groupid | name | description | group_type
 */
while ($myrow = $db->fetchArray($result)) {
//while($myrow = mysqli_fetch_array($result)){
    if ($myrow['group_type'] == 'Admin') {
        $content .= '<tr><td>'.$myrow['name'].'</td><td><input type="radio" name="g_webmasters" value="'.$myrow['groupid'].'"></td><td>&nbsp;</td><td>&nbsp;</td></tr>';
    } elseif ($myrow['group_type'] == 'User') {
        $content .= '<tr><td>'.$myrow['name'].'</td><td>&nbsp;</td><td><input type="radio" name="g_users" value="'.$myrow['groupid'].'"></td><td>&nbsp;</td></tr>';
    } else {
        $content .= '<tr><td>'.$myrow['name'].'</td><td>&nbsp;</td><td>&nbsp;</td><td><input type="radio" name="g_anonymous" value="'.$myrow['groupid'].'"></td></tr>';
    }
}
$content .= '</table>';

$b_back = [];
$b_next = ['updateTables_go', _INSTALL_L132];

include './install_tpl.php';
