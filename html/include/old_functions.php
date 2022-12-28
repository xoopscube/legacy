<?php
/**
 * *
 *  * Old theme functions
 *  *
 *  * @package    Legacy
 *  * @subpackage core
 *  * @author     Original Authors: Minahito
 *  * @author     Other Authors : Kazumi Ono (aka onokazu)
 *  * @copyright  2005-2020 The XOOPSCube Project
 *  * @license    Legacy : GPL 2.0
 *  * @license    Cube : https://github.com/xoopscube/xcl/blob/master/BSD_license.txt
 *  * @version    v 1.1 2007/05/15 02:34:18 minahito, Release: @package_230@
 *  * @link       https://github.com/xoopscube/xcl
 * *
 */

// #################### Block functions from here ##################

/*
 * Purpose : Builds the blocks on both sides
 * Input   : $side = On wich side should the block are displayed?
 *             0, l, left : On the left side
 *             1, r, right: On the right side
 *             other:   Only on one side (
 *                          Call from theme.php makes all blocks on the left side
 *                          and from theme.php for the right site)
 */
function make_sidebar($side)
{
    global $xoopsUser;
    $xoopsblock = new XoopsBlock();
    if ('left' == $side) {
        $side = XOOPS_SIDEBLOCK_LEFT;
    } elseif ('right' == $side) {
        $side = XOOPS_SIDEBLOCK_RIGHT;
    } else {
        $side = XOOPS_SIDEBLOCK_BOTH;
    }
    if (is_object($xoopsUser)) {
        $block_arr =& $xoopsblock->getAllBlocksByGroup($xoopsUser->getGroups(), true, $side, XOOPS_BLOCK_VISIBLE);
    } else {
        $block_arr =& $xoopsblock->getAllBlocksByGroup(XOOPS_GROUP_ANONYMOUS, true, $side, XOOPS_BLOCK_VISIBLE);
    }

    if (!isset($GLOBALS['xoopsTpl']) || !is_object($GLOBALS['xoopsTpl'])) {
        include_once XOOPS_ROOT_PATH.'/class/template.php';
        $xoopsTpl = new XoopsTpl();
    } else {
        $xoopsTpl =& $GLOBALS['xoopsTpl'];
    }
    $xoopsLogger =& XoopsLogger::instance();
    foreach ($block_arr as $iValue) {
        $bcachetime = (int)$iValue->getVar('bcachetime');
        if (empty($bcachetime)) {
            $xoopsTpl->xoops_setCaching(0);
        } else {
            $xoopsTpl->xoops_setCaching(2);
            $xoopsTpl->xoops_setCacheTime($bcachetime);
        }
        $btpl = $iValue->getVar('template');
        if ('' !== $btpl) {
            if (empty($bcachetime) || !$xoopsTpl->is_cached('db:'.$btpl)) {
                $xoopsLogger->addBlock($iValue->getVar('name'));
                $bresult =& $iValue->buildBlock();
                if (!$bresult) {
                    continue;
                }
                $xoopsTpl->assign_by_ref('block', $bresult);
                $bcontent =& $xoopsTpl->fetch('db:'.$btpl);
                $xoopsTpl->clear_assign('block');
            } else {
                $xoopsLogger->addBlock($iValue->getVar('name'), true, $bcachetime);
                $bcontent =& $xoopsTpl->fetch('db:'.$btpl);
            }
        } else {
            $bid = $iValue->getVar('bid');
            if (empty($bcachetime) || !$xoopsTpl->is_cached('db:system_dummy.html', 'blk_'.$bid)) {
                $xoopsLogger->addBlock($iValue->getVar('name'));
                $bresult =& $iValue->buildBlock();
                if (!$bresult) {
                    continue;
                }
                $xoopsTpl->assign_by_ref('dummy_content', $bresult['content']);
                $bcontent =& $xoopsTpl->fetch('db:system_dummy.html', 'blk_'.$bid);
                $xoopsTpl->clear_assign('block');
            } else {
                $xoopsLogger->addBlock($iValue->getVar('name'), true, $bcachetime);
                $bcontent =& $xoopsTpl->fetch('db:system_dummy.html', 'blk_'.$bid);
            }
        }
        switch ($iValue->getVar('side')) {
        case XOOPS_SIDEBLOCK_LEFT:
            themesidebox($iValue->getVar('title'), $bcontent);
            break;
        case XOOPS_SIDEBLOCK_RIGHT:
            if (function_exists('themesidebox_right')) {
                themesidebox_right($iValue->getVar('title'), $bcontent);
            } else {
                themesidebox($iValue->getVar('title'), $bcontent);
            }
            break;
        }
        unset($bcontent);
    }
}

/*
 * Function to display center block
 */
function make_cblock()
{
    global $xoopsUser, $xoopsOption;
    $xoopsblock = new XoopsBlock();
    $cc_block = $cl_block = $cr_block = '';
    $arr = [];
    if (0 == $xoopsOption['theme_use_smarty']) {
        if (!isset($GLOBALS['xoopsTpl']) || !is_object($GLOBALS['xoopsTpl'])) {
            include_once XOOPS_ROOT_PATH.'/class/template.php';
            $xoopsTpl = new XoopsTpl();
        } else {
            $xoopsTpl =& $GLOBALS['xoopsTpl'];
        }
        if (is_object($xoopsUser)) {
            $block_arr =& $xoopsblock->getAllBlocksByGroup($xoopsUser->getGroups(), true, XOOPS_CENTERBLOCK_ALL, XOOPS_BLOCK_VISIBLE);
        } else {
            $block_arr =& $xoopsblock->getAllBlocksByGroup(XOOPS_GROUP_ANONYMOUS, true, XOOPS_CENTERBLOCK_ALL, XOOPS_BLOCK_VISIBLE);
        }
        $xoopsLogger =& XoopsLogger::instance();
        foreach ($block_arr as $iValue) {
            $bcachetime = (int)$iValue->getVar('bcachetime');
            if (empty($bcachetime)) {
                $xoopsTpl->xoops_setCaching(0);
            } else {
                $xoopsTpl->xoops_setCaching(2);
                $xoopsTpl->xoops_setCacheTime($bcachetime);
            }
            $btpl = $iValue->getVar('template');
            if ('' !== $btpl) {
                if (empty($bcachetime) || !$xoopsTpl->is_cached('db:'.$btpl)) {
                    $xoopsLogger->addBlock($iValue->getVar('name'));
                    $bresult =& $iValue->buildBlock();
                    if (!$bresult) {
                        continue;
                    }
                    $xoopsTpl->assign_by_ref('block', $bresult);
                    $bcontent =& $xoopsTpl->fetch('db:'.$btpl);
                    $xoopsTpl->clear_assign('block');
                } else {
                    $xoopsLogger->addBlock($iValue->getVar('name'), true, $bcachetime);
                    $bcontent =& $xoopsTpl->fetch('db:'.$btpl);
                }
            } else {
                $bid = $iValue->getVar('bid');
                if (empty($bcachetime) || !$xoopsTpl->is_cached('db:system_dummy.html', 'blk_'.$bid)) {
                    $xoopsLogger->addBlock($iValue->getVar('name'));
                    $bresult =& $iValue->buildBlock();
                    if (!$bresult) {
                        continue;
                    }
                    $xoopsTpl->assign_by_ref('dummy_content', $bresult['content']);
                    $bcontent =& $xoopsTpl->fetch('db:system_dummy.html', 'blk_'.$bid);
                    $xoopsTpl->clear_assign('block');
                } else {
                    $xoopsLogger->addBlock($iValue->getVar('name'), true, $bcachetime);
                    $bcontent =& $xoopsTpl->fetch('db:system_dummy.html', 'blk_'.$bid);
                }
            }
            $title = $iValue->getVar('title');
            switch ($iValue->getVar('side')) {
            case XOOPS_CENTERBLOCK_CENTER:
                if ('' !== $title) {
                    $cc_block .= '<tr valign="top"><td colspan="2"><b>'.$title.'</b><hr />'.$bcontent.'<br><br></td></tr>'."\n";
                } else {
                    $cc_block .= '<tr><td colspan="2">'.$bcontent.'<br><br></td></tr>'."\n";
                }
                break;
            case XOOPS_CENTERBLOCK_LEFT:
                if ('' !== $title) {
                    $cl_block .= '<p><b>'.$title.'</b><hr />'.$bcontent.'</p>'."\n";
                } else {
                    $cl_block .= '<p>'.$bcontent.'</p>'."\n";
                }
                break;
            case XOOPS_CENTERBLOCK_RIGHT:
                if ('' !== $title) {
                    $cr_block .= '<p><b>'.$title.'</b><hr />'.$bcontent.'</p>'."\n";
                } else {
                    $cr_block .= '<p>'.$bcontent.'</p>'."\n";
                }
                break;
            default:
                break;
            }
            unset($bcontent, $title);
        }
        echo '<table width="100%">'.$cc_block.'<tr valign="top"><td width="50%">'.$cl_block.'</td><td width="50%">'.$cr_block.'</td></tr></table>'."\n";
    }
}

function openThread($width= '100%')
{
    echo "<table border='0' cellpadding='0' cellspacing='0' align='center' width='$width'><tr><td class='bg2'><table border='0' cellpadding='4' cellspacing='1' width='100%'><tr class='bg3' align='left'><td class='bg3' width='20%'>". _CM_POSTER ."</td><td class='bg3'>". _CM_THREAD . '</td></tr>';
}

function showThread($color_number, $subject_image, $subject, $text, $post_date, $ip_image, $reply_image, $edit_image, $delete_image, $username= '', $rank_title= '', $rank_image= '', $avatar_image= '', $reg_date= '', $posts= '', $user_from= '', $online_image= '', $profile_image= '', $pm_image= '', $email_image= '', $www_image= '', $icq_image= '', $aim_image= '', $yim_image= '', $msnm_image= ''
)
{
    if (1 == $color_number) {
        $bg = 'bg1';
    } else {
        $bg = 'bg3';
    }
    echo "<tr align='left'><td valign='top' class='$bg' nowrap='nowrap'><b>$username</b><br>$rank_title<br>$rank_image<br>$avatar_image<br><br>$reg_date<br>$posts<br>$user_from<br><br>$online_image</td>";
    echo "<td valign='top' class='$bg'><table width='100%' border='0'><tr><td valign='top'>$subject_image&nbsp;<b>$subject</b></td><td align='right'>".$ip_image . '' . $reply_image . '' . $edit_image . '' . $delete_image . '</td></tr>';
    echo "<tr><td colspan='2'><p>$text</p></td></tr></table></td></tr>";
    echo "<tr align='left'><td class='$bg' valign='middle'>$post_date</td><td class='$bg' valign='middle'>".$profile_image . '' . $pm_image . '' . $email_image . '' . $www_image . '' . $icq_image . '' . $aim_image . '' . $yim_image . '' . $msnm_image . '</td></tr>';
}

function closeThread()
{
    echo '</table></td></tr></table>';
}
