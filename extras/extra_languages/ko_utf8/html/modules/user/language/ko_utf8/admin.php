<?php
//  ------------------------------------------------------------------------ //
//                XOOPS Cube - PHP Content Management System                 //
//                    Copyright (c) 2006 XOOPSCube.org                       //
//                       <http://www.xoopscube.org/>                         //
//  ------------------------------------------------------------------------ //
//  ------------------------------------------------------------------------ //
//        XOOPS Cube Korean (translated by wanikoo[ wani@wanisys.net ])      //
//                 <http://www.xoops.ne.kr/xoopscube/>                       //
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

// DATA
define('_AD_USER_DATA_DOWNLOAD_DO', 'CSV방식 다운로드');
define('_AD_USER_DATA_NUM', '%d 명의 사용자가 등록되어졌습니다.'); 
define('_AD_USER_DATA_UPLOAD_BACK', 'CSV 파일을 다시 선택해 주세요!');
define('_AD_USER_DATA_UPLOAD_CHECK_USER_CSVFILE', '등록 내용을 다시 한번 확인해 보시기 바랍니다.');
define('_AD_USER_DATA_UPLOAD_CONF', '등록 내용 확인');
define('_AD_USER_DATA_UPLOAD_DO', '등록');
define('_AD_USER_DATA_UPLOAD_DONE', 'CSV 데이타에 따라 사용자 데이타가 갱신되어 졌습니다.');
define('_AD_USER_DATA_UPLOAD_SELECT_USER_CSVFILE', '등록 CSV 파일을 선택해 주세요!');

// ERROR
define('_AD_USER_ERROR_CONTENT_IS_NOT_FOUND', "데이타를 찾을 수가 없었습니다.");define('_AD_USER_ERROR_COULD_NOT_SAVE_AVATAR_FILE', "아바타 파일을 저장 실패 : '{0}'");
define('_AD_USER_ERROR_DBUPDATE_FAILED', "데이타베이스 업그레이드에 실패하였습니다.");
define('_AD_USER_ERROR_EMAIL', "입력하신 {0} 은 올바른 Email형식이 아닙니다.");
define('_AD_USER_ERROR_EXTENSION_IS_WRONG', "업로드용 파일의 확장자가 올바르지 않습니다.");
define('_AD_USER_ERROR_GROUP_VALUE', "지정하신 그룹의 값이 올바르지 않습니다.");
define('_AD_USER_ERROR_IMAGE_REQUIRED', "그림파일을 반드시 지정해 주세요!");
define('_AD_USER_ERROR_INJURY_MIN_MAX', "최소값과 최대값의 관계가 올바르지 않습니다.");
define('_AD_USER_ERROR_INTRANGE', "{0} 의 입력값은 올바르지 않습니다.");
define('_AD_USER_ERROR_MAILJOB_SEND_FAIL', "메일/PM쪽지의 송신에 실패하였습니다.");
define('_AD_USER_ERROR_MAILJOB_SEND_MEANS', "최저 하나이상의 송신방법을 지정해 주세요!");
define('_AD_USER_ERROR_MIN', "{0}은 {1}이상의 값을 입력해 주세요!");
define('_AD_USER_ERROR_OBJECTEXIST', "{0} 의 입력값이 올바르지 않습니다.");
define('_AD_USER_ERROR_REQUIRED', "{0} 은 필수입니다.");
define('_AD_USER_ERROR_REQUEST_IS_WRONG', "올바르지 않은 요청 입니다.");
define('_AD_USER_ERROR_UMODE', "코멘트 정렬순에 지정한 값이 올바르지 않습니다.");
define('_AD_USER_ERROR_UNAME_NO_UNIQUE', "사용자명이 이미 사용중입니다. 다른 것으로 변경해 주세요!");

// LANG
define('_AD_USER_LANG_ALL_OF_USERS', "모든 사용자");
define('_AD_USER_LANG_APPROVE_USERS_ONLY', "승인완료(활성화) 사용자만");
define('_AD_USER_LANG_AVATAR_CREATED', "작성일시");
define('_AD_USER_LANG_AVATAR_DELETE', "아바타 삭제");
define('_AD_USER_LANG_AVATAR_DISPLAY', "표시");
define('_AD_USER_LANG_AVATAR_FILE', "그림파일");
define('_AD_USER_LANG_AVATAR_MIMETYPE', "Mime 타입");
define('_AD_USER_LANG_AVATAR_NAME', "이름");
define('_AD_USER_LANG_AVATAR_NEW', "아바타 신규추가");
define('_AD_USER_LANG_AVATAR_TOTAL', "아바타 총계");
define('_AD_USER_LANG_AVATAR_TYPE', "종류");
define('_AD_USER_LANG_AVATAR_TYPE_C', "커스텀");
define('_AD_USER_LANG_AVATAR_TYPE_S', "시스템");
define('_AD_USER_LANG_AVATAR_UPDATECONF', "아바타 업데이트 확인");
define('_AD_USER_LANG_AVATAR_UPLOAD', "아바타 일괄 업로드");
define('_AD_USER_LANG_AVATAR_UPLOAD_FILE', "아바타 아카이브(tar.gz 혹은 zip파일만)");
define('_AD_USER_LANG_AVATAR_UPLOAD_RESULT', "아바타 일괄 업로드 결과");
define('_AD_USER_LANG_AVATAR_USING_COUNT', "사용수");
define('_AD_USER_LANG_AVATAR_WEIGHT', "우선순위");
define('_AD_USER_LANG_BODY', "본문");
define('_AD_USER_LANG_COMPLETED', "완료");
define('_AD_USER_LANG_CONTROL', "조작");
define('_AD_USER_LANG_CREATE_NEW', "신규작성");
define('_AD_USER_LANG_CREATE_UNIXTIME', "작성일");
define('_AD_USER_LANG_DELETE_RANK', "사용자 등급 삭제");
define('_AD_USER_LANG_DISPLAY_USER_LEVEL', "표시할 사용자의 종류");
define('_AD_USER_LANG_DISPLAY_USER_MAIL_CONDITION', "표시할 사용자의 메일관련 설정");
define('_AD_USER_LANG_FOUNDUSERS', "발견된 사용자 총수");
define('_AD_USER_LANG_FROM_EMAIL', "송신자 메일주소 ");
define('_AD_USER_LANG_FROM_NAME', "송신자명");
define('_AD_USER_LANG_GROUP', "그룹");
define('_AD_USER_LANG_GROUP_AMMO', "회원 수");
define('_AD_USER_LANG_GROUP_ASSIGN', "회원 등록");
define('_AD_USER_LANG_GROUP_ASSIN_MEMBERS', "이 그룹에 등록되어 있는 사용자 리스트");
define('_AD_USER_LANG_GROUP_DELETE', "사용자 그룹 삭제");
define('_AD_USER_LANG_GROUP_DELETE_ADVICE', "이 사용자 그룹을 삭제합니다. 계속하시겠습니까? ");
define('_AD_USER_LANG_GROUP_DELETE_ADVICE2', "그룹을 삭제하여도 소속 사용자계정이 삭제되지는 않습니다.");
define('_AD_USER_LANG_GROUP_DESC', "설명");
define('_AD_USER_LANG_GROUP_EDIT', "사용자그룹 편집");
define('_AD_USER_LANG_GROUP_GID', "GID");
define('_AD_USER_LANG_GROUP_LIST', "사용자그룹 관리");
define('_AD_USER_LANG_GROUP_NAME', "이름");
define('_AD_USER_LANG_GROUP_NEW', "사용자그룹 신규 추가");
define('_AD_USER_LANG_GROUP_NOASSIN_MEMBERS', "이 그룹에 등록되어 있지 않은 사용자 리스트");
define('_AD_USER_LANG_GROUP_PERMISSION', "퍼미션");
define('_AD_USER_LANG_GROUP_PROPERTY', "속성");
define('_AD_USER_LANG_GROUP_TYPE', "타입");
define('_AD_USER_LANG_IS_MAIL', "메일 송신");
define('_AD_USER_LANG_IS_PM', "PM쪽지 송신");
define('_AD_USER_LANG_LASTLOG_LESS', "X 일 이내에 로그인한적이 있음");
define('_AD_USER_LANG_LASTLOG_MORE', "X 일 이상 로그인하지 않음");
define('_AD_USER_LANG_LASTLOGIN', "최종 로그인");
define('_AD_USER_LANG_LEFT_TARGET_USER', "남은 송신수");
define('_AD_USER_LANG_LEVEL_ACTIVE', "승인 완료 사용자");
define('_AD_USER_LANG_LEVEL_PENDING', "승인 미완료 사용자");
define('_AD_USER_LANG_LEVEL_ROOT', "Root 사용자");
define('_AD_USER_LANG_MAIL_NG_USERS_ONLY', "메일수신 OK인 사용자만");
define('_AD_USER_LANG_MAIL_OK_USERS_ONLY', "메일수신 NO인 사용자만");
define('_AD_USER_LANG_MAILJOB_DELETE', "메일작업 삭제");
define('_AD_USER_LANG_MAILJOB_EDIT', "메일작업 편집");
define('_AD_USER_LANG_MAILJOB_ID', "메일작업 ID");
define('_AD_USER_LANG_MAILJOB_LINK_LIST', "메일작업 link list");
define('_AD_USER_LANG_MAILJOB_LIST', "메일작업 리스트");
define('_AD_USER_LANG_MAILJOB_NEW', "메일작업 신규작성");
define('_AD_USER_LANG_MAILJOB_SEND', "일괄 메일 송신");
define('_AD_USER_LANG_MAILJOB_VIEW', "메일작업 열람");
define('_AD_USER_LANG_MESSAGE', "메세지");
define('_AD_USER_LANG_NO_SPECIAL_RANK', "--------------");
define('_AD_USER_LANG_OVER_POSTS', "투고수 X 건 이상");
define('_AD_USER_LANG_PENDING_USERS_ONLY', "승인이 완료되지 않은(비활성)사용자만");
define('_AD_USER_LANG_PERM_ACCESS', "액세스");
define('_AD_USER_LANG_PERM_ACCESS_ADMIN', "액세스 관리 권한");
define('_AD_USER_LANG_PERM_ADMIN', "관리");
define('_AD_USER_LANG_PERM_BLOCK_ACCESS', "블록 액세스 권한");
define('_AD_USER_LANG_PERM_GROUP_PERM_BLOCK', "블록 관리");
define('_AD_USER_LANG_PERM_GROUP_PERM_MODULE', "모듈 관리");
define('_AD_USER_LANG_PERM_MODULE_ACCESS', "시스템/모듈 관리권한");
define('_AD_USER_LANG_PERM_SYSTEM_PERM_MODULE', "시스템 관리");
define('_AD_USER_LANG_RANK', "등급");
define('_AD_USER_LANG_RANK_EDIT', "사용자등급 편집");
define('_AD_USER_LANG_RANK_IMAGE', "그림파일");
define('_AD_USER_LANG_RANK_LIST', "사용자등급 관리");
define('_AD_USER_LANG_RANK_MAX', "최고 투고수");
define('_AD_USER_LANG_RANK_MIN', "최저 투고수");
define('_AD_USER_LANG_RANK_NEW', "사용자등급 신규등록");
define('_AD_USER_LANG_RANK_SPECIAL', "특별등급");
define('_AD_USER_LANG_RANK_TITLE', "등급명");
define('_AD_USER_LANG_RANK_TOTAL', "등급 총계");
define('_AD_USER_LANG_RANK_UPDATECONF', "등급 업데이트 확인");
define('_AD_USER_LANG_RECOUNT', "재집계");
define('_AD_USER_LANG_REGDATE', "등록일");
define('_AD_USER_LANG_REGDATE_LESS', "사용자 등록일시가 X 일 이내");
define('_AD_USER_LANG_REGDATE_MORE', "사용자 등록일시가 X 일 이전");
define('_AD_USER_LANG_RESET', "리셋");
define('_AD_USER_LANG_RETRY', "재시도");
define('_AD_USER_LANG_SEARCH_AGAIN', "조건 수정후 재검색");
define('_AD_USER_LANG_SEND_MAIL_BY_THIS_CONDITION', "이 조건으로 메일 발송");
define('_AD_USER_LANG_TARGET_RETRY_NUMBER', "처리할 재시도 번호(Retry number)");
define('_AD_USER_LANG_TITLE', "타이틀");
define('_AD_USER_LANG_TOTAL', "총계");
define('_AD_USER_LANG_UID', "uid");
define('_AD_USER_LANG_UNDER_POSTS', "투고수가 X 건 이하");
define('_AD_USER_LANG_UPLOAD', "업로드");
define('_AD_USER_LANG_USER', "사용자");
define('_AD_USER_LANG_USER_DELETE', "사용자 삭제");
define('_AD_USER_LANG_USER_DELETE_ADVICE', "이 사용자를 삭제합니다. 계속하시겠습니까?");
define('_AD_USER_LANG_USER_EDIT', "사용자 편집");
define('_AD_USER_LANG_USER_LIST', "사용자 관리");
define('_AD_USER_LANG_USER_NEW', "사용자 신규등록");
define('_AD_USER_LANG_USER_NEW_FIELD', "신규 필드 추가");
define('_AD_USER_LANG_USER_SEARCH_LIST', "사용자 검색 결과");
define('_AD_USER_LANG_USER_TOTAL', "사용자 총계");
define('_AD_USER_LANG_USER_UPDATECONF', "사용자 업데이트 확인");
define('_AD_USER_LANG_USER_VIEW', "사용자 열람");
define('_AD_USER_LANG_VPASS', "패스워드 확인");

// Message
define('_AD_USER_MESSAGE_CONFIRM_DELETE', "정말로 삭제하시겠습니까?");
define('_AD_USER_MESSAGE_CONFIRM_DELETE_RANK', "이 사용자등급을 정말로 삭제하시겠습니까?");
define('_AD_USER_MESSAGE_CONFIRM_UPDATE_AVATAR', "아바타를 정말로 업데이트하시겠습니까?");
define('_AD_USER_MESSAGE_CONFIRM_UPDATE_RANK', "등급을 정말로 업데이트하시겠습니까?");
define('_AD_USER_MESSAGE_CONFIRM_UPDATE_USER', "사용자를 정말로 업데이트하시겠습니까?");
define('_AD_USER_MESSAGE_RECOUNT_SUCCESS', "재집계에 성공하였습니다.");

// Tips
define('_AD_USER_TIPS_AVATAR', "등록회원들은 자신의 아바타를 가질 수 있습니다.<br /> 관련 설정은 사용자 모듈의 일반설정에서 하시면 됩니다. ");
define('_AD_USER_TIPS_AVATAR_UPLOAD', "아카이브 파일의 업로드를 통해 다량의 아바타를 일괄 등록처리하실 수 있습니다.<br />미리 각 아바타의 사이즈와 용량을 규격에 맞게 체크한 후에 아카이브 파일을 작성하시기 바랍니다.<br />일괄 등록작업시엔 별도로 체크하지 않으니 꼭 미리 규격에 맞추시기 바랍니다.<br />(tar.gz 혹은 zip파일만)");
define('_AD_USER_TIPS_DATA_DOWNLOAD', 'You are only able to get CSV User data order by user_id.');
define('_AD_USER_TIPS_DELETE_AVATAR', "이 아바타를 사용중인 유저의 아바타는 blank.gif 으로 변경되어질 것입니다.");
define('_AD_USER_TIPS_MAILJOB_SEND', "송신대상 유저가 너무 많을 경우엔 서버로부터의 반응이 오지 않는 현상이 발생할 수 있습니다.(공백화면표시) 그럴 경우엔 리로드(새로고침)를 반복하셔서 모두 발송처리하실 수 있습니다.");
define('_AD_USER_TIPS_RANK', "A social user ranking system is helpfull to identify contributors. You can define your own policy by editing default ranks.");
define('_AD_USER_TIPS_RECOUNT_POSTS', "만약 투고수가 잘못되었다고 생각되어지시면 재집계를 하실 수 있습니다.");
define('_AD_USER_TIPS_USER_ADMIN', "Access to your XOOPS Cube web site is controlled through a group-based system by which users are assigned to groups that authorize their access to Modules and blocks content. Some recent modules can extend default User Management providing a role-based system which allows you to assign permissions by User.<br />
You can also extend default User Profile by adding new fields. To learn more, click the following link :");
define('_AD_USER_TIPS_USER_EDIT', "패스워드를 변경하고자 하실 경우에만 패스워드, 확인용 패스워드를 입력해 주세요!");
define('_AD_USER_TIPS_USER_NEW', "사용자명, 메일주소, 패스워드, 확인용 패스워드는 반드시 입력하셔야 합니다.");
define('_AD_USER_TIPS_USER_SEARCH', "You can search and sort your users data to find the vital information you are looking for.<br />The accuracy of the end result will be directly related to the number of fields you fill.");
define('_AD_USER_TIPS1_DATA_UPLOAD', 'The user batch registration with CSV file is possible.');
define('_AD_USER_TIPS2_DATA_UPLOAD', 'Use CSV file downloaded from <a href="?action=UserDataDownload" style="color:#941d55;font-weight:bold;">'._MI_USER_ADMENU_USER_DATA_DOWNLOAD.'</a> Do not increase and decrease columns.');
define('_AD_USER_TIPS3_DATA_UPLOAD', 'Please describe only the user who wants to update and wants to register information newly in CSV file.');
define('_AD_USER_TIPS4_DATA_UPLOAD', 'When the row of leftmost UID is emptied(or 0), it registers as a new user.');
define('_AD_USER_TIPS5_DATA_UPLOAD', 'The user information is updated when there is a value of the row of leftmost(UID).<br>If you set password,set it within 30bytes.');

?>