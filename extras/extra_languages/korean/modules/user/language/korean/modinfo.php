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

define('_MI_USER_ADMENU_AVATAR_MANAGE', "아바타 관리");
define('_MI_USER_ADMENU_GROUP_LIST', "그룹 관리");
define('_MI_USER_ADMENU_LIST', "사용자 관리");
define('_MI_USER_ADMENU_MAIL', "일괄 메일 발송");
define('_MI_USER_ADMENU_MAILJOB_MANAGE', "메일작업 관리");
define('_MI_USER_ADMENU_RANK_LIST', "사용자등급 관리");
define('_MI_USER_ADMENU_USER_DATA_CSVUPLOAD', '사용자 일괄등록');
define('_MI_USER_ADMENU_USER_DATA_DOWNLOAD', '사용자 데이타 다운로드');
define('_MI_USER_ADMENU_USER_SEARCH', "사용자 검색");
define('_MI_USER_BLOCK_LOGIN_DESC', "로그인 폼을 표시합니다.");
define('_MI_USER_BLOCK_LOGIN_NAME', "로그인");
define('_MI_USER_BLOCK_NEWUSERS_DESC', "신규 등록회원의 리스트를 표시합니다.");
define('_MI_USER_BLOCK_NEWUSERS_NAME', "신규 등록회원");
define('_MI_USER_BLOCK_ONLINE_DESC', "온라인 상황을 표시합니다.");
define('_MI_USER_BLOCK_ONLINE_NAME', "온라인 상황");
define('_MI_USER_BLOCK_TOPUSERS_DESC', "투고수 랭킹을 표시합니다.");
define('_MI_USER_BLOCK_TOPUSERS_NAME', "투고수 랭킹");
define('_MI_USER_CONF_ACTV_ADMIN', "관리자가 계정 활성화 여부를 결정");
define('_MI_USER_CONF_ACTV_AUTO', "자동적으로 계정을 활성화함");
define('_MI_USER_CONF_ACTV_GROUP', "계정 활성화 확인 의뢰 메일을 받을 그룹을 선택해 주세요!");
define('_MI_USER_CONF_ACTV_GROUP_DESC', "관리자가 계정 활성화 여부를 결정 을 선택하신 경우만 유효함");
define('_MI_USER_CONF_ACTV_TYPE', "신규 회원 계정의 활성화 방식을 선택해 주세요!");
define('_MI_USER_CONF_ACTV_USER', "계정 활성화를 위해 회원 자신의 확인이 필요(추천)");
define('_MI_USER_CONF_ALLOW_REGISTER', "신규 회원의 등록을 허용함");
define('_MI_USER_CONF_ALW_RG_DESC', "예 를 선택하시면 신규 회원 등록을 허용하게 됩니다.");
define('_MI_USER_CONF_AVATAR_HEIGHT', "아바타 그림파일의 최대허용 높이 (pixel)");
define('_MI_USER_CONF_AVATAR_MAXSIZE', "아바타 그림파일의 최대허용 파일사이즈 (byte)");
define('_MI_USER_CONF_AVATAR_MINPOSTS', "아바타 업로드권한을 위한 최저 요구 투고수");
define('_MI_USER_CONF_AVT_MIN_DESC', "등록회원이 자기의 아바타를 업로드할 권한을 얻기위해 필요한 최저 투고수를 입력해 주세요.");
define('_MI_USER_CONF_AVATAR_WIDTH', "아바타 그림파일의 최대허용 폭 (pixel)");
define('_MI_USER_CONF_AVTR_ALLOW_UP', "아바타 그림파일의 업로드를 허용함?");
define('_MI_USER_CONF_BAD_EMAILS', "회원의 메일주소로서 사용할 수 없는 문자열");
define('_MI_USER_CONF_BAD_EMAILS_DESC', "각각의 문자열은 | 로 구분, 대소문자는 구별하지 않음, 정규식 사용가능");
define('_MI_USER_CONF_BAD_UNAMES', "사용자명으로 사용할 수 없는 문자열");
define('_MI_USER_CONF_BAD_UNAMES_DESC', "각각의 문자열은 | 로 구분, 대소문자는 구별하지 않음, 정규식 사용가능");
define('_MI_USER_CONF_CHGMAIL', "등록회원의 자기 메일주소 변경을 허용함?");
define('_MI_USER_CONF_DISCLAIMER', "이용약관(Registration disclaimer)");
define('_MI_USER_CONF_DISCLAIMER_DESC', "신규등록 페이지에 표시할 이용약관을 입력해 주세요!");
define('_MI_USER_CONF_DISCLAIMER_DESC_DEFAULT', '
제1장 총칙

제1조 (목적)
이 약관은 당 사이트에서 제공하는 서비스의 이용조건 및 절차 기타 제반 권리의무 사항을 규정함을 목적으로 합니다.

제2조 (약관의 효력과 변경)
당 사이트는 귀하가 본 약관 내용에 동의하는 것을 조건으로 귀하에게 서비스를 제공하며, 귀하가 본 약관의 내용에 동의하는 경우, 당 사이트의 서비스 제공 행위 및 귀하의 서비스 사용 행위에는 본 약관이 우선적으로 적용됩니다.
(1) 당 사이트는 필요하다고 인정되는 경우 이 약관의 내용을 변경할 수 있으며, 변경된 약관은 서비스 화면에 공지하며, 공지후 거부의사를 표시하지 아니하고 서비스를 계속 사용할 경우 약관의 변경 사항에 동의한 것으로 간주됩니다.
(2) 이용자가 변경된 약관에 동의하지 않는 경우 서비스 이용을 중단하고 본인의 회원등록을 취소할 수 있으며, 계속 사용하시는 경우에는 약관 변경에 동의한 것으로 간주됩니다. 변경된 약관은 공지와 동시에 그 효력이 발생됩니다.

제3조 (약관외 준칙)
이 약관에 명시되지 않은 사항은 관련법령의 규정에 따릅니다.

제2장 서비스 이용계약

제4조 (이용계약의 성립)
이용계약은 이용자의 이용신청에 대한 당 사이트의 승낙과 이용자의 약관 내용에 대한 동의로 성립됩니다.
(1) 다음 각 호에 해당하는 경우에는 이용 승낙을 하지 않을 수 있습니다.
1. 타인의 명의를 사용하여 신청하였을 때
2. 이용신청의 내용을 허위로 기재한 경우
3. 사회의 안녕 질서 혹은 미풍양속을 저해할 목적으로 신청하였을 때
4. 다른 사람의 당 사이트 서비스 이용을 방해하거나 그 정보를 도용하는 등의 행위를 하였을 때
5. 당 사이트를 이용하여 법령과 본 약관이 금지하는 행위를 하는 경우
6. 기타 당 사이트가 정한 이용신청 요건에 미비 되었을 때

제5조 (계약사항의 변경)
회원은 이용신청시 기재한 사항이 변경되었을 경우에는 수정하여야 하며, 수정하지 아니하여 발생하는 문제의 책임은 회원에게 있습니다.

제3장 계약당사자의 의무

제6조 (당 사이트의 의무)
당 사이트는 서비스 제공과 관련해서 알고 있는 회원의 신상 정보를 본인의 승낙 없이 제3자에게 누설하거나 배포하지 않습니다.
단, 범죄에 대한 수사상의 목적이 있거나 또는 기타 관계법령에서 정한 절차에 의한 요청이 있을 경우에는 그러하지 아니합니다.

제7조 (회원의 의무)
(1) 회원은 서비스를 이용할 때 다음 각 호의 행위를 하지 않아야 합니다.
1. 다른 회원의 ID를 부정하게 사용하는 행위
2. 당 사이트의 저작권, 제3자의 저작권 등 기타 권리를 침해하는 행위
3. 공공질서 및 미풍양속에 위반되는 내용을 유포하는 행위
4. 범죄와 결부된다고 객관적으로 판단되는 행위
5. 관리자를 사칭, 혼동시키거나 이와 유사하다고 판단되는 행위
6. 기타 관계법령에 위반되는 행위

제4장 서비스 이용

제8조 (게시물의 저작권)
게시물의 저작권은 게시자 본인에게 있으며 당 사이트는 게시된 내용을 사전 통지 없이 편집, 이동 할 수 있는 권리를 보유하며, 당 사이트의 이용 요건에 맞지 않을 경우 사전 통지 없이 삭제할 수 있습니다.

제9조 (서비스 이용 책임)
서비스를 이용하여 불법행위를 하여서는 아니되며, 이를 위반하여 발생한 활동의 결과 및 손실, 관계기관에 의한 법적 조치 등에 관해서 당 사이트는 일체 책임을 지지 않습니다.

제5장 계약해지 및 이용제한

제10조 (계약해지 및 이용제한)
(1) 회원이 이용계약을 해지하고자 하는 때에는 서비스 이용을 중단하고 본인의 회원등록을 취소할 수 있습니다.
(2) 당 사이트는 회원이 본 약관에 위배된 행위를 하였을 경우, 임의로 서비스 사용을 제한 또는 중지할 수 있습니다. 

제6장 기 타

제11조 (면책 조항)
당 사이트는 회원에게 제공하는 서비스의 이용과 관련된 어떠한 직접 혹은 간접적 손해에 대해도 일체 책임을 지지 않습니다.
');
define('_MI_USER_CONF_DISPDSCLMR', "이용약관(disclaimer)을 표시");
define('_MI_USER_CONF_DISPDSCLMR_DESC', "예 를 선택하시면 신규등록페이지에 이용약관을 표시합니다.");
define('_MI_USER_CONF_MAXUNAME', "사용자아이디의 최대 문자수(byte)");
define('_MI_USER_CONF_MINPASS', "패스워드의 최저 문자수");
define('_MI_USER_CONF_MINUNAME', "사용자아이디의 최저 문자수");
define('_MI_USER_CONF_NEW_NTF_GROUP', "신규 회원 등록 통지/통보 메일을 받을 그룹을 설정해 주세요!");
define('_MI_USER_CONF_NEW_USER_NOTIFY', "신규 회원의 등록이 있는 경우 메일로 통지/통보 받음?");
define('_MI_USER_CONF_SELF_DELETE', "등록회원의 자기 계정삭제를 허용(탈퇴허용)?");
define('_MI_USER_CONF_SELF_DELETE_CONF', "계정삭제전 확인메세지");
define('_MI_USER_CONF_SELF_DELETE_CONFIRM_DEFAULT', "계정을 정말로 삭제하시겠습니까?\n사용자 계정이 삭제되면 관련 사용자정보도 모두 삭제처리되어집니다.");
define('_MI_USER_CONF_SSLLOGINLINK', "SSL 로그인 페이지의 URL");
define('_MI_USER_CONF_SSLPOST_NAME', "SSL 로그인시에 사용될 Post 변수명");
define('_MI_USER_CONF_UNAME_TEST_LEVEL', "사용자아이디로 사용가능한 문자들을 설정! 문자 제한의 레벨을 선택해주세요!");
define('_MI_USER_CONF_UNAME_TEST_LEVEL_NORMAL', "중간");
define('_MI_USER_CONF_UNAME_TEST_LEVEL_STRONG', "엄격 (알파벳과 숫자만)");
define('_MI_USER_CONF_UNAME_TEST_LEVEL_WEAK', "관대 (한글,한자등 사용허가)");
define('_MI_USER_CONF_USE_SSL', "로그인에 SSL을 사용");
define('_MI_USER_CONF_USERCOOKIE', "등록회원에게 할당할 쿠키의 이름");
define('_MI_USER_CONF_USERCOOKIE_DESC', "이 쿠키엔 등록회원의 아이디가 저장되어 1년간 회원의 컴퓨터에 존재하게됩니다.(회원이 허용한 경우) ");
define('_MI_USER_KEYWORD_AVATAR_MANAGE', "아바타 커스텀 아바타 시스템아바타 리스트 편집 변경 삭제");
define('_MI_USER_KEYWORD_CREATE_AVATAR', "아바타 커스텀 아바타 시스템아바타 신규작성 업로드");
define('_MI_USER_KEYWORD_CREATE_GROUP', "사용자그룹 그룹");
define('_MI_USER_KEYWORD_CREATE_RANK', "사용자등급 등급");
define('_MI_USER_KEYWORD_CREATE_USER', "사용자 신규등록");
define('_MI_USER_KEYWORD_GROUP_LIST', "그룹 리스트 편집 변경 삭제 사용자 사용자그룹 권한 퍼미션 추가 멤버");
define('_MI_USER_KEYWORD_MAILJOB_LINK_LIST', "MailJob 링크 리스트");
define('_MI_USER_KEYWORD_MAILJOB_MANAGE', "Mailjob manage");
define('_MI_USER_KEYWORD_USER_LIST', "사용자 리스트 편집 변경 삭제");
define('_MI_USER_KEYWORD_USER_SEARCH', "사용자 검색");
define('_MI_USER_LANG_MAILJOB_LINK_LIST', "MailJob 링크 리스트");
define('_MI_USER_MENU_CREATE_AVATAR', "아바타 신규작성");
define('_MI_USER_MENU_CREATE_GROUP', "그룹 신규작성");
define('_MI_USER_MENU_CREATE_RANK', "사용자등급 신규작성");
define('_MI_USER_MENU_CREATE_USER', "사용자 신규작성");
define('_MI_USER_NAME', "사용자 모듈");
define('_MI_USER_NAME_DESC', "사용자계정에 관련된 작업을 처리하기 위한 모듈입니다.");

?>