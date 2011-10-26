<?php
function b_user_topusers_show($options)
{
    $block = array();
    $criteria = new CriteriaCompo(new Criteria('level', 0, '>'));
    $limit = (!empty($options[0])) ? $options[0] : 10;
    $size = count($options);
    for ( $i = 2; $i < $size; $i++) {
        $criteria->add(new Criteria('rank', $options[$i], '<>'));
    }
    $criteria->setOrder('DESC');
    $criteria->setSort('posts');
    $criteria->setLimit($limit);
    $member_handler =& xoops_gethandler('member');
    $topposters =& $member_handler->getUsers($criteria);
    $count = count($topposters);
    for ($i = 0; $i < $count; $i++) {
        $block['users'][$i]['rank'] = $i+1;
        if ( $options[1] == 1 ) {
            $block['users'][$i]['avatar'] = $topposters[$i]->getVar('user_avatar') != 'blank.gif' ? XOOPS_UPLOAD_URL.'/'.$topposters[$i]->getVar('user_avatar') : '';
        } else {
            $block['users'][$i]['avatar'] = '';
        }
        $block['users'][$i]['id'] = $topposters[$i]->getVar('uid');
        $block['users'][$i]['name'] = $topposters[$i]->getVar('uname');
        $block['users'][$i]['posts'] = $topposters[$i]->getVar('posts');
    }
    return $block;
}

function b_user_topusers_edit($options)
{
    include_once XOOPS_ROOT_PATH.'/class/xoopslists.php';
    $inputtag = '<input type="text" name="options[]" value="'.intval($options[0]).'" />';
    $form = sprintf(_MB_USER_DISPLAY,$inputtag);
    $form .= '<br />'._MB_USER_DISPLAYA.'&nbsp;<input type="radio" id="options[]" name="options[]" value="1"';
    if ( $options[1] == 1 ) {
        $form .= ' checked="checked"';
    }
    $form .= ' />&nbsp;'._YES.'<input type="radio" id="options[]" name="options[]" value="0"';
    if ( $options[1] == 0 ) {
        $form .= ' checked="checked"';
    }
    $form .= ' />&nbsp;'._NO;
    $form .= '<br />'._MB_USER_NODISPGR.'<br /><select id="options[]" name="options[]" multiple="multiple">';
    $ranks =& XoopsLists::getUserRankList();
    $size = count($options);
    foreach ($ranks as $k => $v) {
        $sel = "";
        for ( $i = 2; $i < $size; $i++ ) {
            if ($k == $options[$i]) {
                $sel = ' selected="selected"';
            }
        }
        $form .= '<option value="'.$k.'"'.$sel.'>'.$v.'</option>';
    }
    $form .= '</select>';
    return $form;
}
?>
