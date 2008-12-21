<?php
// $Id: comment.php,v 1.1 2007/05/24 06:49:40 minahito Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  ------------------------------------------------------------------------ //
//                XOOPS Korean (translated by wanikoo[ wani@wanisys.net ])	   //
//                       <http://www.wanisys.net/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //

if (!defined('_CM_TITLE')) {

define('_CM_TITLE','제목');
define('_CM_MESSAGE','내용');
define('_CM_DOSMILEY','얼굴아이콘을 사용');
define('_CM_DOHTML','HTML태그를 사용');
define('_CM_DOAUTOWRAP','줄바꾸기(개행)를 자동처리');
define('_CM_DOXCODE','XOOPS 코드를 사용');
define('_CM_REFRESH','새로고침');
define('_CM_PENDING','승인대기');
define('_CM_HIDDEN','표시않음');
define('_CM_ACTIVE','활성화');
define('_CM_STATUS','상태');
define('_CM_POSTCOMMENT','코멘트 투고');
define('_CM_REPLIES','답신');
define('_CM_PARENT','부모 코멘트');
define('_CM_TOP','위로');
define('_CM_BOTTOM','아래로');
define('_CM_ONLINE','온라인');
define('_CM_POSTED','투고일시'); // Posted date
define('_CM_UPDATED', '갱신일시');
define('_CM_THREAD','쓰레드');
define('_CM_POSTER','투고자');
define('_CM_JOINED','등록일');
define('_CM_POSTS','투고 수');
define('_CM_FROM','주소');
define('_CM_COMDELETED', '코멘트를 삭제하였습니다.');
define('_CM_COMDELETENG', '코멘트 삭제에 실패하였습니다.');
define('_CM_DELETESELECT' , '코멘트 삭제방식을 선택해 주세요!');
define('_CM_DELETEONE' , '이 코멘트만을 삭제');
define('_CM_DELETEALL', '이 코멘트에 대한 답글들도 모두 삭제');
define('_CM_THANKSPOST', '투고해주셔서 감사합니다.');
define('_CM_NOTICE', "투고되어진 코멘트에 대한 저작권은 코멘트 작성자에게 귀속됩니다.");
define('_CM_COMRULES','코멘트 투고에 관한 룰');
define('_CM_COMAPPROVEALL','코멘트는 자동승인됨. 별도의 승인 불필요!');
define('_CM_COMAPPROVEUSER','등록회원이외의 코멘트는 승인 필요!');
define('_CM_COMAPPROVEADMIN','모든 코멘트는 반드시 승인 필요!');
define('_CM_COMANONPOST','익명 코멘트 투고를 허용하실건가요?');
define('_CM_COMNOCOM','코멘트 기능을 사용하지 않음');

}

?>