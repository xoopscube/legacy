<?php
// to replace main_theme legacy_default => pack2011_default
require_once dirname(dirname(__FILE__))."/wizards/install_insertData.inc.php";

$available_themes = array();
foreach ($_POST as $key=>$value) {
    if (preg_match('/^option_themes_\d+$/', $key) && preg_match('/^\w+$/', $value)) {
        $available_themes[] = $value;
    }
}
if (empty($available_themes)) {
    $available_themes = array('basic5','legacy_default', 'pack2011_default');
}

$default_theme = 'pack2011_default';
if (isset($_POST['default_theme']) && preg_match("/^\w+$/", $_POST['default_theme'])) {
    $default_theme = $_POST['default_theme'];
    if (!in_array($default_theme, $available_themes)) {
        $available_themes[] = $default_theme;
    }
}

$hd_query = array(
    sprintf('update %s set conf_value="%s" where conf_name="theme_set" limit 1',
            $dbm->db->prefix('config'), $default_theme),
    sprintf('update %s set conf_value=\'%s\' where conf_name="theme_set_allowed" limit 1',
            $dbm->db->prefix('config'), serialize($available_themes)),
    sprintf('update %s set theme="%s" where uid =1 limit 1',
//			$dbm->db->prefix('users'), $default_theme),
            $dbm->db->prefix('users'), ''), // fix A curse of the Theme (http://usadeki.jp/modules/pico/index.php?content_id=24)
    );

foreach ($hd_query as $hd_sql) {
    $result = $dbm->query($hd_sql);
}
