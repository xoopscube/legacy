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

//%%%%%%	Admin Module Name  MailUsers	%%%%%
define("_AM_DBUPDATED",_MD_AM_DBUPDATED);

//%%%%%%	mailusers.php 	%%%%%
define("_AM_SENDTOUSERS","메세지 수신자 선택:");
define("_AM_SENDTOUSERS2","수신자:");
define("_AM_GROUPIS","그룹 (생략가능)");
define("_AM_TIMEFORMAT", "(yyyy-mm-dd형식으로 기입, 생략가능)");
define("_AM_LASTLOGMIN","최종로그인 일시가 이하의 일시보다 뒤");
define("_AM_LASTLOGMAX","최종로그인 일시가 이하의 일시보다 앞");
define("_AM_REGDMIN","등록 일시가 이하의 일시보다 뒤");
define("_AM_REGDMAX","등록 일시가 이하의 일시보다 앞");
define("_AM_IDLEMORE","최종로그인 일시가 X 일 이상 전(생략가능)");
define("_AM_IDLELESS","최종로그인 일시가  X 일 이내 (생략가능)");
define("_AM_MAILOK","사이트로부터의 메일수신을 희망하는 등록회원에게만 (생략가능)");
define("_AM_INACTIVE","비활성화상태의 회원에게만(생략가능)");
define("_AMIFCHECKD", "체크할 경우 위의 설정은 모두 무시됩니다. 또한 PM쪽지도 발송되지 않습니다.");
define("_AM_MAILFNAME","송신자 (메일사용시)");
define("_AM_MAILFMAIL","송신자 메일주소 (메일사용시)");
define("_AM_MAILSUBJECT","제목");
define("_AM_MAILBODY","본문");
define("_AM_MAILTAGS","사용가능 태그:");
define("_AM_MAILTAGS1","{X_UID} 는 등록회원 아이디를 표시합니다.");
define("_AM_MAILTAGS2","{X_UNAME} 은 등록회원 이름을 표시합니다.");
define("_AM_MAILTAGS3","{X_UEMAIL} 은 등록회원 메일주소를 표시합니다.");
define("_AM_MAILTAGS4","{X_UACTLINK} 는 등록활성화 페이지로의 링크를 표시합니다.");
define("_AM_SENDTO","송신방법");
define("_AM_EMAIL","메일");
define("_AM_PM","PM쪽지");
define("_AM_SENDMTOUSERS", "메세지 송신");
define("_AM_SENT", "송신이 끝난 등록회원");
define("_AM_SENTNUM", "%s - %s (합계: %s 명)");
define("_AM_SENDNEXT", "계속");
define("_AM_NOUSERMATCH", "조건을 만족하는 등록회원을 찾을수가 없습니다.");
define("_AM_SENDCOMP", "메세지 송신이 완료되었습니다.");

?>