<?php
/**
 *
 * @package Legacy
 * @version $Id: install_updateModules_go.inc.php,v 1.3 2008/09/25 15:12:35 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */
    unset($xoopsOption['nocommon']);
    include_once "../mainfile.php";
    error_reporting(E_ERROR);
    ob_start();
    $module_handler =& xoops_gethandler('module');
    $modules =& $module_handler->getObjects(null, true);
    foreach (array_keys($modules) as $mid) {
        echo '<h5>'.$modules[$mid]->getVar('name').'</h5>';
        $dirname = $modules[$mid]->getVar('dirname');
        if (is_dir(XOOPS_ROOT_PATH.'/modules/'.$dirname)) {
            $modules[$mid]->loadInfoAsVar($dirname, false);
            if (!$module_handler->insert($modules[$mid])) {
                echo '<p>Could not update '.$modules[$mid]->getVar('name').'</p>';
            } else {
                $newmid = $modules[$mid]->getVar('mid');
                $msgs = array();
                $msgs[] = 'Module data updated.';
                $tplfile_handler =& xoops_gethandler('tplfile');
                $templates = $modules[$mid]->getInfo('templates');
                if ($templates != false) {
                    $msgs[] = 'Generating templates...';
                    foreach ($templates as $tpl) {
                        $tpl['file'] = trim($tpl['file']);
                        $tpldata =& xoops_module_gettemplate($dirname, $tpl['file']);
                        $tplfile =& $tplfile_handler->create();
                        $tplfile->setVar('tpl_refid', $newmid);
                        $tplfile->setVar('tpl_lastimported', 0);
                        $tplfile->setVar('tpl_lastmodified', time());
                        if (preg_match("/\.css$/i", $tpl['file'])) {
                            $tplfile->setVar('tpl_type', 'css');
                        } else {
                            $tplfile->setVar('tpl_type', 'module');
                            //if ($xoopsConfig['default_theme'] == 'default') {
                            //  include_once XOOPS_ROOT_PATH.'/class/template.php';
                            //  xoops_template_touch($tplfile->getVar('tpl_id'));
                            //}
                        }
                        $tplfile->setVar('tpl_source', $tpldata, true);
                        $tplfile->setVar('tpl_module', $dirname);
                        $tplfile->setVar('tpl_tplset', 'default');
                        $tplfile->setVar('tpl_file', $tpl['file'], true);
                        $tplfile->setVar('tpl_desc', $tpl['description'], true);
                        if (!$tplfile_handler->insert($tplfile)) {
                            $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">ERROR: Could not insert template <b>'.$tpl['file'].'</b> to the database.</span>';
                        } else {
                            $msgs[] = '&nbsp;&nbsp;Template <b>'.$tpl['file'].'</b> inserted to the database.';
                        }
                        unset($tpldata);
                    }
                }
                $blocks = $modules[$mid]->getInfo('blocks');
                $msgs[] = 'Rebuilding blocks...';
                $showfuncs = array();
                $funcfiles = array();
                if ($blocks != false) {
                    $count = count($blocks);
                    include_once(XOOPS_ROOT_PATH.'/class/xoopsblock.php');
                    for ($i = 1; $i <= $count; $i++) {
                        if (isset($blocks[$i]['show_func']) && $blocks[$i]['show_func'] != '' && isset($blocks[$i]['file']) && $blocks[$i]['file'] != '') {
                            $editfunc = isset($blocks[$i]['edit_func']) ? $blocks[$i]['edit_func'] : '';
                            $showfuncs[] = $blocks[$i]['show_func'];
                            $funcfiles[] = $blocks[$i]['file'];
                            $template = '';
                            if ((isset($blocks[$i]['template']) && trim($blocks[$i]['template']) != '')) {
                                $content =& xoops_module_gettemplate($dirname, $blocks[$i]['template'], true);
                                $template = $blocks[$i]['template'];
                            }
                            if (!$content) {
                                $content = '';
                            }
                            $options = '';
                            if (isset($blocks[$i]['options']) && $blocks[$i]['options'] != '') {
                                $options = $blocks[$i]['options'];
                            }
                            $sql = "SELECT bid, name FROM ".$xoopsDB->prefix('newblocks')." WHERE mid=".$mid." AND func_num=".$i;
                            $fresult = $xoopsDB->query($sql);
                            $fcount = 0;
                            while ($fblock = $xoopsDB->fetchArray($fresult)) {
                                $fcount++;
                                $sql = "UPDATE ".$xoopsDB->prefix("newblocks")." SET name='".addslashes($blocks[$i]['name'])."', title='".addslashes($blocks[$i]['name'])."', dirname='".addslashes($dirname)."',  func_file='".addslashes($blocks[$i]['file'])."', show_func='".addslashes($blocks[$i]['show_func'])."', template='".addslashes($template)."', edit_func='".addslashes($editfunc)."', options='".addslashes($options)."', content='', template='".$template."', last_modified=".time()." WHERE bid=".$fblock['bid'];
                                $result = $xoopsDB->query($sql);
                                if (!$result) {
                                    $msgs[] = '&nbsp;&nbsp;ERROR: Could not update '.$fblock['name'];
                                } else {
                                    $msgs[] = '&nbsp;&nbsp;Block <b>'.$fblock['name'].'</b> updated. Block ID: <b>'.$fblock['bid'].'</b>';
                                    if ($template != '') {
                                        $tplfile =& $tplfile_handler->create();
                                        $tplfile->setVar('tpl_refid', $fblock['bid']);
                                        $tplfile->setVar('tpl_source', $content, true);
                                        $tplfile->setVar('tpl_tplset', 'default');
                                        $tplfile->setVar('tpl_file', $blocks[$i]['template']);
                                        $tplfile->setVar('tpl_module', $dirname);
                                        $tplfile->setVar('tpl_type', 'block');
                                        $tplfile->setVar('tpl_desc', $blocks[$i]['description'], true);
                                        $tplfile->setVar('tpl_lastimported', 0);
                                        $tplfile->setVar('tpl_lastmodified', time());
                                        if (!$tplfile_handler->insert($tplfile)) {
                                            $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">ERROR: Could not insert template <b>'.$blocks[$i]['template'].'</b> to the database.</span>';
                                        } else {
                                            $msgs[] = '&nbsp;&nbsp;Template <b>'.$blocks[$i]['template'].'</b> inserted to the database.';
                                            //if ($xoopsConfig['default_theme'] == 'default') {
                                            //  if (!xoops_template_touch($tplfile[0]->getVar('tpl_id'))) {
                                            //      $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">ERROR: Could not recompile template <b>'.$blocks[$i]['template'].'</b>.</span>';
                                            //  } else {
                                            //      $msgs[] = '&nbsp;&nbsp;Template <b>'.$blocks[$i]['template'].'</b> recompiled.';
                                            //  }
                                            //}
                                        }
                                    }
                                }
                            }
                            if ($fcount == 0) {
                                $newbid = $xoopsDB->genId($xoopsDB->prefix('newblocks').'_bid_seq');
                                $block_name = addslashes($blocks[$i]['name']);
                                $sql = "INSERT INTO ".$xoopsDB->prefix("newblocks")." (bid, mid, func_num, options, name, title, content, side, weight, visible, block_type, isactive, dirname, func_file, show_func, edit_func, template, last_modified) VALUES (".$newbid.", ".$mid.", ".$i.",'".addslashes($options)."','".$block_name."', '".$block_name."', '', 0, 0, 0, 'M', 1, '".addslashes($dirname)."', '".addslashes($blocks[$i]['file'])."', '".addslashes($blocks[$i]['show_func'])."', '".addslashes($editfunc)."', '".$template."', ".time().")";
                                $result = $xoopsDB->query($sql);
                                if (!$result) {
                                    $msgs[] = '&nbsp;&nbsp;ERROR: Could not create '.$blocks[$i]['name'];
                                } else {
                                    if (empty($newbid)) {
                                        $newbid = $xoopsDB->getInsertId();
                                    }
                                    if ($template != '') {
                                        $tplfile =& $tplfile_handler->create();
                                        $tplfile->setVar('tpl_module', $dirname);
                                        $tplfile->setVar('tpl_refid', $newbid);
                                        $tplfile->setVar('tpl_source', $content, true);
                                        $tplfile->setVar('tpl_tplset', 'default');
                                        $tplfile->setVar('tpl_file', $blocks[$i]['template'], true);
                                        $tplfile->setVar('tpl_type', 'block');
                                        $tplfile->setVar('tpl_lastimported', 0);
                                        $tplfile->setVar('tpl_lastmodified', time());
                                        $tplfile->setVar('tpl_desc', $blocks[$i]['description'], true);
                                        if (!$tplfile_handler->insert($tplfile)) {
                                            $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">ERROR: Could not insert template <b>'.$blocks[$i]['template'].'</b> to the database.</span>';
                                        } else {
                                            $msgs[] = '&nbsp;&nbsp;Template <b>'.$blocks[$i]['template'].'</b> inserted to the database.';
                                        }
                                    }
                                    $msgs[] = '&nbsp;&nbsp;Block <b>'.$blocks[$i]['name'].'</b> created. Block ID: <b>'.$newbid.'</b>';
                                }
                            }
                        }
                    }
                }
                $block_arr = XoopsBlock::sGetByModule($mid);
                foreach ($block_arr as $block) {
                    if (!in_array($block->getVar('show_func'), $showfuncs) || !in_array($block->getVar('func_file'), $funcfiles)) {
                        $sql = sprintf("DELETE FROM %s WHERE bid = %u", $xoopsDB->prefix('newblocks'), $block->getVar('bid'));
                        if (!$xoopsDB->query($sql)) {
                            $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">ERROR: Could not delete block <b>'.$block->getVar('name').'</b>. Block ID: <b>'.$block->getVar('bid').'</b></span>';
                        } else {
                            $msgs[] = '&nbsp;&nbsp;Block <b>'.$block->getVar('name').' deleted. Block ID: <b>'.$block->getVar('bid').'</b>';
                        }
                    }
                }

                $configs = $modules[$mid]->getInfo('config');
                if ($configs != false) {
                    if ($modules[$mid]->getVar('hascomments') != 0) {
                        include_once(XOOPS_ROOT_PATH.'/include/comment_constants.php');
                        array_push($configs, array('name' => 'com_rule', 'title' => '_CM_COMRULES', 'description' => '', 'formtype' => 'select', 'valuetype' => 'int', 'default' => 1, 'options' => array('_CM_COMAPPROVEALL' => XOOPS_COMMENT_APPROVEALL, '_CM_COMAPPROVEUSER' => XOOPS_COMMENT_APPROVEUSER, '_CM_COMAPPROVEADMIN' => XOOPS_COMMENT_APPROVEADMIN)));
                        array_push($configs, array('name' => 'com_anonpost', 'title' => '_CM_COMANONPOST', 'description' => '', 'formtype' => 'yesno', 'valuetype' => 'int', 'default' => 0));
                    }
                } else {
                    if ($modules[$mid]->getVar('hascomments') != 0) {
                        $configs = array();
                        include_once(XOOPS_ROOT_PATH.'/include/comment_constants.php');
                        $configs[] = array('name' => 'com_rule', 'title' => '_CM_COMRULES', 'description' => '', 'formtype' => 'select', 'valuetype' => 'int', 'default' => 1, 'options' => array('_CM_COMAPPROVEALL' => XOOPS_COMMENT_APPROVEALL, '_CM_COMAPPROVEUSER' => XOOPS_COMMENT_APPROVEUSER, '_CM_COMAPPROVEADMIN' => XOOPS_COMMENT_APPROVEADMIN));
                        array_push($configs, array('name' => 'com_anonpost', 'title' => '_CM_COMANONPOST', 'description' => '', 'formtype' => 'yesno', 'valuetype' => 'int', 'default' => 0));
                    }
                }
                // RMV-NOTIFY
                if ($modules[$mid]->getVar('hasnotification') != 0) {
                    if (empty($configs)) {
                        $configs = array();
                    }
                    include_once(XOOPS_ROOT_PATH.'/include/notification_constants.php');
                    $configs[] = array('name' => 'notification_enabled', 'title' => '_NOT_CONFIG_ENABLED', 'description' => '_NOT_CONFIG_ENABLEDDSC', 'formtype' => 'select', 'valuetype' => 'int', 'default' => XOOPS_NOTIFICATION_ENABLEBOTH, 'options' => $options);
                }

                if ($configs != false) {
                    $msgs[] = 'Adding module config data...';
                    $config_handler =& xoops_gethandler('config');
                    $order = 0;
                    foreach ($configs as $config) {
                        $confobj =& $config_handler->createConfig();
                        $confobj->setVar('conf_modid', $newmid);
                        $confobj->setVar('conf_catid', 0);
                        $confobj->setVar('conf_name', $config['name']);
                        $confobj->setVar('conf_title', $config['title'], true);
                        $confobj->setVar('conf_desc', $config['description'], true);
                        $confobj->setVar('conf_formtype', $config['formtype']);
                        $confobj->setVar('conf_valuetype', $config['valuetype']);
                        $confobj->setVar('conf_value', $config['default'], true);
                        $confobj->setVar('conf_order', $order);
                        $confop_msgs = '';
                        if (isset($config['options']) && is_array($config['options'])) {
                            foreach ($config['options'] as $key => $value) {
                                $confop =& $config_handler->createConfigOption();
                                $confop->setVar('confop_name', $key, true);
                                $confop->setVar('confop_value', $value, true);
                                $confobj->setConfOptions($confop);
                                $confop_msgs .= '<br />&nbsp;&nbsp;&nbsp;&nbsp;Config option added. Name: <b>'.$key.'</b> Value: <b>'.$value.'</b>';
                                unset($confop);
                            }
                        }
                        $order++;
                        if ($config_handler->insertConfig($confobj) != false) {
                            $msgs[] = '&nbsp;&nbsp;Config <b>'.$config['name'].'</b> added to the database.'.$confop_msgs;
                        } else {
                            $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">ERROR: Could not insert config <b>'.$config['name'].'</b> to the database.</span>';
                        }
                        unset($confobj);
                    }
                    unset($configs);
                }
                foreach ($msgs as $msg) {
                    echo '<code>'.$msg.'</code><br />';
                }
            }
            // data for table 'block_module_link'
            include_once './class/dbmanager.php';
            $dbm = new db_manager;
            $sql = 'SELECT bid, side FROM '.$dbm->prefix('newblocks');
            $result = $dbm->query($sql);

            while ($myrow = $dbm->fetchArray($result)) {
                if ($myrow['side'] == 0) {
                    $dbm->insert("block_module_link", " VALUES (".$myrow['bid'].", 0)");
                } else {
                    $dbm->insert("block_module_link", " VALUES (".$myrow['bid'].", -1)");
                }
            }
        }
        echo '<br />';
        flush();
        sleep(1);
    }
    $title = _INSTALL_L142;
    $content = "<table width='80%' align='center'><tr><td align='left'>\n";
    $content .= ob_get_contents();
    $content .= "</td></tr></table>\n";
    ob_end_clean();
    $b_next = array('updateComments', _INSTALL_L14);
    include './install_tpl.php';
