<?php
//  ------------------------------------------------------------------------ //
//                XOOPS Cube - PHP Content Management System                      //
//                    Copyright (c) 2006 XOOPSCube.org                           //
//                       <http://www.xoopscube.org/>                             //
//  ------------------------------------------------------------------------ //
//  ------------------------------------------------------------------------ //
//                XOOPS Cube Korean (translated by wanikoo[ wani@wanisys.net ])       //
//                       < http://www.wanisys.net/ >                             //
//                       < http://www.xoops.ne.kr/xoopscube/ >                             //
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

//%%%%%%	File Name  modulesadmin.php 	%%%%%
define("_MD_AM_MODADMIN","모듈 관리");
define("_MD_AM_MODULE","모듈");
define("_MD_AM_VERSION","버전");
define("_MD_AM_LASTUP","최종 갱신일");
define("_MD_AM_DEACTIVATED","비활성화");
define("_MD_AM_ACTION","처리");
define("_MD_AM_DEACTIVATE","비활성화처리");
define("_MD_AM_ACTIVATE","활성화처리");
define("_MD_AM_UPDATE","업그레이드");
define("_MD_AM_DUPEN","모듈이 데이타베이스내에 이중등록되어 있습니다!");
define("_MD_AM_DEACTED","선택하신 모듈을 비활성화처리하였습니다. 이제 이 모듈을 안전하게 제거(언인스톨)하실 수 있습니다.");
define("_MD_AM_ACTED","선택하신 모듈을 활성화처리하였습니다.");
define("_MD_AM_UPDTED","선택하신 모듈을 업그레이드처리하였습니다.");
define("_MD_AM_SYSNO","시스템 모듈은 비활성화처리하실 수 없습니다.");
define("_MD_AM_STRTNO","이 모듈은 사이트의 시작모듈로서 등록되어있습니다. 이 모듈을 비활성화하시려면 먼저 일반설정 메뉴에서 시작모듈을 변경해 주시기 바랍니다.");

// added in RC2
define("_MD_AM_PCMFM","이하의 내용을 변경처리합니다.");

// added in RC3
define("_MD_AM_ORDER","표시순위");
define("_MD_AM_ORDER0","(0 = 표시않음)");
define("_MD_AM_ACTIVE","활성화");
define("_MD_AM_INACTIVE","비활성화");
define("_MD_AM_NOTINSTALLED","미설치");
define("_MD_AM_NOCHANGE","변경없음");
define("_MD_AM_INSTALL","설치(인스톨)");
define("_MD_AM_UNINSTALL","제거(언인스톨)");
define("_MD_AM_SUBMIT","보내기");
define("_MD_AM_CANCEL","취소");
define("_MD_AM_DBUPDATE","데이타베이스를 갱신하였습니다.");
define("_MD_AM_BTOMADMIN","모듈관리 메뉴로 돌아감");

// %s represents module name
define("_MD_AM_FAILINS","%s 모듈의 설치에 실패하였습니다.");
define("_MD_AM_FAILACT","%s 모듈의 활성화처리에 실패하였습니다.");
define("_MD_AM_FAILDEACT","%s 모듈의 비활성화처리에 실패하였습니다.");
define("_MD_AM_FAILUPD","%s 모듈의 업그레이드처리에 실패하였습니다.");
define("_MD_AM_FAILUNINS","%s 모듈의 제거에 실패하였습니다.");
define("_MD_AM_FAILORDER","%s 모듈의 표시순위변경에 실패하였습니다.");
define("_MD_AM_FAILWRITE","메인 메뉴 파일에의 쓰기에 실패하였습니다.");
define("_MD_AM_ALEXISTS","%s 모듈은 이미 존재합니다.");
define("_MD_AM_ERRORSC", "에러:");
define("_MD_AM_OKINS","%s 모듈의 설치가 성공적으로 완료되었습니다.");
define("_MD_AM_OKACT","%s 모듈의 활성화처리가 성공적으로 완료되었습니다.");
define("_MD_AM_OKDEACT","%s 모듈의 비활성화처리가 성공적으로 완료되었습니다.");
define("_MD_AM_OKUPD","%s 모듈의 업그레이드처리가 성공적으로 완료되었습니다.");
define("_MD_AM_OKUNINS","%s 모듈이 성공적으로 제거(언인스톨)되었습니다.");
define("_MD_AM_OKORDER","%s 모듈의 표시순위변경이 성공적으로 이루어졌습니다.");

define('_MD_AM_RUSUREINS', '이 모듈을 설치하시려면 아래의 버튼을 클릭해 주세요');
define('_MD_AM_RUSUREUPD', '이 모듈을 업그레이드하시려면 아래의 버튼을 클릭해 주세요');
define('_MD_AM_RUSUREUNINS', '정말로 이 모듈을 제거(언인스톨)하실 건가요?');
define('_MD_AM_LISTUPBLKS', '모듈을 업그레이드처리합니다.<br />선택하신 블록의 내용(템플릿과 옵션)은 덮어쓰기 처리됩니다.<br />');
define('_MD_AM_NEWBLKS', '신규 블록');
define('_MD_AM_DEPREBLKS', 'Deprecated Blocks');

?>