<?php
/**
 * Admin Menu
 *
 * @version $Rev$
 * @link $URL$
 */
$adminmenu = array(
    array('title' => _MI_OPENID_ADMENU,
          'link' => 'admin/index.php'
    ),
    array('title' => _MI_OPENID_ADMENU_FILTER_0,
          'link' => 'admin/index.php?controller=filter&auth=0'
    ),
    array('title' => _MI_OPENID_ADMENU_FILTER_1,
          'link' => 'admin/index.php?controller=filter&auth=1'
    ),
    array('title' => _MI_OPENID_ADMENU_ASSOC,
          'link' => 'admin/index.php?controller=assoc'
    ),
    array('title' => _MI_OPENID_ADMENU_EXTENSION,
          'link' => 'admin/index.php?controller=extension'
    ),
    array('title' => _MI_OPENID_ADMENU_BUTTONS,
          'link' => 'admin/index.php?controller=buttons'
    )
);
$openid_allowed_controller = array('identifier', 'filter', 'assoc', 'extension', 'buttons');
?>