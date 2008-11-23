<?php
// $Id: notification.php,v 1.1 2007/05/25 06:05:05 minahito Exp $
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

// RMV-NOTIFY

// Text for various templates...

define ('_NOT_NOTIFICATIONOPTIONS', '이벤트 선택');
define ('_NOT_UPDATENOW', '지금 바로 갱신');
define ('_NOT_UPDATEOPTIONS', '이벤트 갱신');

define ('_NOT_CANCEL', '취소');
define ('_NOT_CLEAR', 'Clear');
define ('_NOT_DELETE', '삭제');
define ('_NOT_CHECKALL', '모두 선택');
define ('_NOT_MODULE', '모듈');
define ('_NOT_CATEGORY', '카테고리');
define ('_NOT_ITEMID', 'ID');
define ('_NOT_ITEMNAME', '명칭');
define ('_NOT_EVENT', '이벤트');
define ('_NOT_EVENTS', '이벤트');
define ('_NOT_ACTIVENOTIFICATIONS', '선택가능한 이벤트');
define ('_NOT_NAMENOTAVAILABLE', '제목없음');
// RMV-NEW : TODO: remove NAMENOTAVAILBLE above
define ('_NOT_ITEMNAMENOTAVAILABLE', '항목명이 없습니다.');
define ('_NOT_ITEMTYPENOTAVAILABLE', '항목타입이 없습니다.');
define ('_NOT_ITEMURLNOTAVAILABLE', '항목URL이 없습니다.');
define ('_NOT_DELETINGNOTIFICATIONS', '선택이벤트 삭제');
define ('_NOT_DELETESUCCESS', '선택하신 이벤트를 성공적으로 삭제하였습니다.');
define ('_NOT_UPDATEOK', '이벤트를 갱신하였습니다.');
define ('_NOT_NOTIFICATIONMETHODIS', '통지 방식: ');
define ('_NOT_EMAIL', '메일');
define ('_NOT_PM', 'PM쪽지');
define ('_NOT_DISABLE', '사용않음');
define ('_NOT_CHANGE', '갱신');
define ('_NOT_RUSUREDEL', '선택하신 이벤트를 정말로 삭제하시겠습니까?');
define ('_NOT_NOACCESS', '이 페이지에 대한 액세스 권한이 없습니다.');

// Text for module config options

define ('_NOT_ENABLE', '사용함');
define ('_NOT_NOTIFICATION', '이벤트 통지방식');

define ('_NOT_CONFIG_ENABLED', '이벤트 통지기능 설정');
define ('_NOT_CONFIG_ENABLEDDSC', '이 모듈에선 특정 이벤트가 발생한 경우, 해당 이벤트에 대한 통지를 희망한 이들에게 통지메세지를 보내도록 설정하실 수 있습니다. 이 기능을 사용하시려면 예 를 선택해 주세요!');

define ('_NOT_CONFIG_EVENTS', '특정 이벤트를 사용함');
define ('_NOT_CONFIG_EVENTSDSC', '사용자가 선택가능한 이벤트를 설정해 주세요');

define ('_NOT_CONFIG_ENABLE', '이벤트 통지기능 설정');
define ('_NOT_CONFIG_ENABLEDSC', '이 모듈에선 특정 이벤트가 발생한 경우, 해당 이벤트에 대한 통지를 희망한 이들에게 통지메세지를 보내도록 설정하실 수 있습니다. 이 기능을 사용하기 위한 방식을 지정해 주세요.');
define ('_NOT_CONFIG_DISABLE', '이벤트 통지 사용않음');
define ('_NOT_CONFIG_ENABLEBLOCK', '이벤트 선택 옵션을 블록에 표시');
define ('_NOT_CONFIG_ENABLEINLINE', '이벤트 선택 옵션을 메인콘텐츠의 하단에 표시');
define ('_NOT_CONFIG_ENABLEBOTH', '이벤트 선택 옵션을 블록 그리고 메인콘텐츠 하단에 표시');

// For notification about comment events

define ('_NOT_COMMENT_NOTIFY', '코멘트가 첨가되었습니다.');
define ('_NOT_COMMENT_NOTIFYCAP', '이 페이지에 코멘트가 첨가되었을 경우 통지함');
define ('_NOT_COMMENT_NOTIFYDSC', '');
define ('_NOT_COMMENT_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} {X_ITEM_TYPE} 에 코멘트가 첨가되었습니다.');

define ('_NOT_COMMENTSUBMIT_NOTIFY', '신규 코멘트 투고가 있었습니다.');
define ('_NOT_COMMENTSUBMIT_NOTIFYCAP', '이 페이지에 승인이 필요한 코멘트가 투고되었을 경우 통지함');
define ('_NOT_COMMENTSUBMIT_NOTIFYDSC', '');
define ('_NOT_COMMENTSUBMIT_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} {X_ITEM_TYPE} 에 신규 코멘트의 투고가 있었습니다.(자동통지)');

// For notification bookmark feature
// (Not really notification, but easy to do with this module)

define ('_NOT_BOOKMARK_NOTIFY', '북마크');
define ('_NOT_BOOKMARK_NOTIFYCAP', '이 페이지를 북마크함.');
define ('_NOT_BOOKMARK_NOTIFYDSC', '이 페이지를 북마크합니다. 통지메세지는 발송되지 않습니다.');

// For user profile
// FIXME: These should be reworded a little...

define ('_NOT_NOTIFYMETHOD', '이벤트갱신 통지 메세지 수신방식');
define ('_NOT_METHOD_EMAIL', '메일');
define ('_NOT_METHOD_PM', 'PM쪽지');
define ('_NOT_METHOD_DISABLE', '일시적으로 중단');

define ('_NOT_NOTIFYMODE', '이벤트 통지의 타이밍');
define ('_NOT_MODE_SENDALWAYS', '이벤트 갱신시엔 반드시 통지함');
define ('_NOT_MODE_SENDONCE', '한번만 통지함');
define ('_NOT_MODE_SENDONCEPERLOGIN', '한번 통지한 후엔 재로그인시까지 통지하지 않음');

?>