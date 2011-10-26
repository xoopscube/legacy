<?php
function b_user_newusers_show($options)
{
    $block = array();
    $criteria = new CriteriaCompo(new Criteria('level', 0, '>'));
    $limit = (!empty($options[0])) ? $options[0] : 10;
    $criteria->setOrder('DESC');
    $criteria->setSort('user_regdate');
    $criteria->setLimit($limit);
    $member_handler =& xoops_gethandler('member');
    $newmembers =& $member_handler->getUsers($criteria);
    $count = count($newmembers);
    for ($i = 0; $i < $count; $i++) {
        if ( $options[1] == 1 ) {
            $block['users'][$i]['avatar'] = $newmembers[$i]->getVar('user_avatar') != 'blank.gif' ? XOOPS_UPLOAD_URL.'/'.$newmembers[$i]->getVar('user_avatar') : '';
        } else {
            $block['users'][$i]['avatar'] = '';
        }
        $block['users'][$i]['id'] = $newmembers[$i]->getVar('uid');
        $block['users'][$i]['name'] = $newmembers[$i]->getVar('uname');
        $block['users'][$i]['joindate'] = $newmembers[$i]->getVar('user_regdate');
    }
    return $block;
}

function b_user_newusers_edit($options)
{
    $inputtag = '<input type="text" name="options[]" value="'.$options[0].'" />';
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
    return $form;
}
?>
