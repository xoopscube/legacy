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

//%%%%%%	Admin Module Name  AdminGroup 	%%%%%
// dont change
define("_AM_DBUPDATED",_MD_AM_DBUPDATED);

define("_MD_AM_SITEPREF","사이트 일반설정");
define("_MD_AM_SITENAME","사이트명");
define("_MD_AM_SLOGAN","사이트 슬로건");
define("_MD_AM_ADMINML","관리자 메일주소");
define("_MD_AM_LANGUAGE","기본사용언어");
define("_MD_AM_STARTPAGE","시작 모듈");
define("_MD_AM_NONE","없음");
define("_MD_AM_SERVERTZ","서버 시간대");
define("_MD_AM_DEFAULTTZ","기본 시간대");
define("_MD_AM_DTHEME","기본 사이트 테마");
define("_MD_AM_THEMESET","테마 세트");
define("_MD_AM_ANONNAME","미등록방문객(손님)의 표시명");
define("_MD_AM_MINPASS","패스워드의 최저 문자수");
define("_MD_AM_NEWUNOTIFY","신규 회원의 등록이 있는 경우 메일로 통지/통보 받음");
define("_MD_AM_SELFDELETE","등록회원의 자기 계정삭제를 허용(탈퇴허용)");
define("_MD_AM_LOADINGIMG","잠시만 기다려주세요...화면(그림파일)을 표시");
define("_MD_AM_USEGZIP","gzip 압축을 사용");
define("_MD_AM_UNAMELVL","아이디로 사용가능한 문자들을 설정! 문자 제한의 레벨을 선택해주세요!");
define("_MD_AM_STRICT","엄격 (알파벳과 숫자만)");
define("_MD_AM_MEDIUM","중간");
define("_MD_AM_LIGHT","관대 (한글,한자등 사용허가)");
define("_MD_AM_USERCOOKIE","등록회원에게 할당할 쿠키의 이름");
define("_MD_AM_USERCOOKIEDSC","이 쿠키엔 등록회원의 아이디가 저장되어 1년간 회원의 컴퓨터에 존재하게됩니다.(회원이 허용한 경우) ");
define("_MD_AM_USEMYSESS","세션의 설정을 카스트마이즈함(자체설정함)");
define("_MD_AM_USEMYSESSDSC","세션을 카스트마이즈(자체설정)");
define("_MD_AM_SESSNAME","세션ID의 저장에 사용할 쿠키의 이름");
define("_MD_AM_SESSNAMEDSC","이 쿠키에 저장된 세션ID는 세션이 타임아웃되거나 회원이 로그아웃할 때까지 유효하게됩니다.(세션 카스트마이즈(자체설정)를 선택한 경우만)");
define("_MD_AM_SESSEXPIRE","세션이 타임아웃될 때까지의 시간(단위:분)");
define("_MD_AM_SESSEXPIREDSC","세션이 타임아웃될 때까지의 시간을 분단위로 지정해 주세요! ( 세션 카스트마이즈(자체설정)를 선택한 경우만)");
define("_MD_AM_BANNERS","배너광고를 활성화");
define("_MD_AM_MYIP","님의 IP 주소를 입력해주세요!");
define("_MD_AM_MYIPDSC","이 IP 주소는 배너 임프레션 수,그외 기타 사이트 통계시 제외됩니다.");
define("_MD_AM_ALWDHTML","투고문내에서의 사용을 허락할 HTML태그");
define("_MD_AM_INVLDMINPASS","패스워드의 최저문자수가 올바르지 않습니다.");
define("_MD_AM_INVLDUCOOK","회원 쿠키의 이름이 올바르지 않습니다.");
define("_MD_AM_INVLDSCOOK","세션ID용 쿠키의 이름이 올바르지 않습니다.");
define("_MD_AM_INVLDSEXP","세션이 타임아웃될 때까지의 시간이 올바르게 입력되지 않았습니다.");
define("_MD_AM_ADMNOTSET","관리자 메일이 설정되지 않았습니다.");
define("_MD_AM_YES","예");
define("_MD_AM_NO","아니요");
define("_MD_AM_DONTCHNG","이하는 절대 변경하지 마시기 바랍니다.");
define("_MD_AM_REMEMBER","이 파일을 웹상의 관리자메뉴에서 편집가능하게 하려면 반드시 액세스권한을 666(chmod 666)으로 설정하셔야 합니다.");
define("_MD_AM_IFUCANT","만약 이 파일의 액세스권한을 변경하실 수 없는 경우엔 직접 파일을 편집/수정하셔야만 합니다.");


define("_MD_AM_COMMODE","기본 코멘트 표시방식");
define("_MD_AM_COMORDER","기본 코멘트 표시순");
define("_MD_AM_ALLOWHTML","코멘트 문에의 서명 첨가를 허용함 ");
define("_MD_AM_DEBUGMODE","디버그 모드");
define("_MD_AM_DEBUGMODEDSC","서버 테스트/디버그 시에 사용하시기 바랍니다. 공개서버에서는 디버그 모드를 오프(껌)로 설정하시기 바랍니다.");
define("_MD_AM_AVATARALLOW","아바타 그림파일의 업로드를 허용함");
define('_MD_AM_AVATARMP','아바타 업로드권한을 위한 최저 요구 투고 수');
define('_MD_AM_AVATARMPDSC','등록회원이 자기의 아바타를 업로드할 권한을 얻기위해 필요한 최저 투고 수를 입력해 주세요');
define("_MD_AM_AVATARW","아바타 그림파일의 최대허용 폭 (pixel)");
define("_MD_AM_AVATARH","아바타 그림파일의 최대허용 높이 (pixel)");
define("_MD_AM_AVATARMAX","아바타 그림파일의 최대허용 파일사이즈 (byte)");
define("_MD_AM_AVATARCONF","등록회원 자체 아바타 그림파일에 관한 설정");
define("_MD_AM_CHNGUTHEME","모든 등록회원의 테마를 변경함");
define("_MD_AM_NOTIFYTO","신규 회원 등록 통지/통보 메일을 받을 그룹을 설정해 주세요!");
define("_MD_AM_ALLOWTHEME","사이트 테마의 선택을 허가함");
define("_MD_AM_ALLOWIMAGE","투고문에의 그림파일 표시를 허가함");

define("_MD_AM_USERACTV","계정 활성화를 위해 회원 자신의 확인이 필요(추천)");
define("_MD_AM_AUTOACTV","자동적으로 계정을 활성화함");
define("_MD_AM_ADMINACTV","관리자가 계정 활성화 여부를 결정");
define("_MD_AM_ACTVTYPE","신규 회원 계정의 활성화 방식을 선택해 주세요!");
define("_MD_AM_ACTVGROUP","계정 활성화 확인 의뢰 메일을 받을 그룹을 선택해 주세요!");
define("_MD_AM_ACTVGROUPDSC","관리자가 계정 활성화 여부를 결정 을 선택하신 경우만 유효함");
define('_MD_AM_USESSL', '로그인에 SSL을 사용');
define('_MD_AM_SSLPOST', 'SSL로그인시에 사용할 POST변수의 이름');
define('_MD_AM_SSLPOSTDSC', 'The name of variable used to transfer session value via POST. If you are unsure, set any name that is hard to guess.');
define('_MD_AM_DEBUGMODE0','오프(껌)');
define('_MD_AM_DEBUGMODE1','PHP 디버그');
define('_MD_AM_DEBUGMODE2','MySQL/Blocks 디버그');
define('_MD_AM_DEBUGMODE3','Smarty 템플렛 디버그');
define('_MD_AM_MINUNAME', '아이디의 최저 문자수(byte)');
define('_MD_AM_MAXUNAME', '아이디의 최대 문자수(byte)');
define('_MD_AM_GENERAL', '일반설정');
define('_MD_AM_USERSETTINGS', '등록회원 정보설정');
define('_MD_AM_ALLWCHGMAIL', '등록회원의 자기 메일주소 변경을 허용함');
define('_MD_AM_ALLWCHGMAILDSC', '');
define('_MD_AM_IPBAN', 'IP 차단(IP Banning)');
define('_MD_AM_BADEMAILS', '회원의 메일주소로서 사용할 수 없는 문자열');
define('_MD_AM_BADEMAILSDSC', '각각의 문자열은 <b>|</b> 로 구분, 대소문자는 구별하지 않음, 정규식 사용가능');
define('_MD_AM_BADUNAMES', '아이디로 사용할 수 없는 문자열');
define('_MD_AM_BADUNAMESDSC', '각각의 문자열은 <b>|</b> 로 구분, 대소문자는 구별하지 않음, 정규식 사용가능');
define('_MD_AM_DOBADIPS', 'IP 차단(IP bans) 사용');
define('_MD_AM_DOBADIPSDSC', '해당 IP 주소로부터 이 사이트로의 접속은 차단됩니다.');
define('_MD_AM_BADIPS', '차단 처리할 IP 주소를 입력해 주세요!<br />각 IP주소는 <b>|</b> 로 구분, 대소문자 구별하지 않음, 정규식 사용가능');
define('_MD_AM_BADIPSDSC', '^aaa.bbb.ccc 는 aaa.bbb.ccc 로 시작하는 IP주소를 차단,<br />aaa.bbb.ccc$ 는 aaa.bbb.ccc로 끝나는 IP주소를 차단,<br />aaa.bbb.ccc 는 aaa.bbb.ccc 를 포함한 IP주소를 차단합니다.');
define('_MD_AM_PREFMAIN', '시스템 설정 메인');
define('_MD_AM_METAKEY', 'Meta태그(키워드[Keywords])');
define('_MD_AM_METAKEYDSC', '키워드[Keywords] Meta태그는 사이트의 내용을 표현합니다. 각 키워드는 쉼표(콤마)로 구분해 주세요!(Ex. XOOPS, PHP, mySQL, portal system)');
define('_MD_AM_METARATING', 'Meta태그(등급[Rating])');
define('_MD_AM_METARATINGDSC', '사이트 접속 대상 연령층을 지정합니다.');
define('_MD_AM_METAOGEN', 'General');
define('_MD_AM_METAO14YRS', '14 years');
define('_MD_AM_METAOREST', 'Restricted');
define('_MD_AM_METAOMAT', 'Mature');
define('_MD_AM_METAROBOTS', 'Meta태그(검색로봇[Robots])');
define('_MD_AM_METAROBOTSDSC', '검색로봇에의 대응방식을 지정합니다.');
define('_MD_AM_INDEXFOLLOW', 'Index, Follow');
define('_MD_AM_NOINDEXFOLLOW', 'No Index, Follow');
define('_MD_AM_INDEXNOFOLLOW', 'Index, No Follow');
define('_MD_AM_NOINDEXNOFOLLOW', 'No Index, No Follow');
define('_MD_AM_METAAUTHOR', 'Meta태그(작성자[Author])');
define('_MD_AM_METAAUTHORDSC', '작성자 Meta태크는 사이트 문서의 작성자 정보를 정의합니다. 이름, 웹마스터 메일주소, 회사명, URL등을 기입하실 수 있습니다. ');
define('_MD_AM_METACOPYR', 'Meta태그(저작권[Copyright])');
define('_MD_AM_METACOPYRDSC', '저작권 Meta태그는 사이트 내의 정보에 대한 저작권정보를 정의합니다.');
define('_MD_AM_METADESC', 'Meta태그(내용설명[Description])');
define('_MD_AM_METADESCDSC', '내용설명 Meta태그는 사이트의 내용을 설명하기위한 태그입니다.');
define('_MD_AM_METAFOOTER', 'Meta태그/Footer 설정');
define('_MD_AM_FOOTER', 'Footer');
define('_MD_AM_FOOTERDSC', '링크를 기술하실때에는 반드시 전체 패스(http://~)를 입력하시기 바랍니다. 그렇게 하지 않으면 모듈내의 페이지에서 올바르게 표시되지 않을 수 있습니다.');
define('_MD_AM_CENSOR', '금지용어 설정');
define('_MD_AM_DOCENSOR', '금지용어 설정을 사용함');
define('_MD_AM_DOCENSORDSC', '이 기능을 ON(사용)할 경우엔 금지용어를 체크하게 됩니다.(사이트 처리스피드를 중시할 경우엔 OFF로 설정하세요.)');
define('_MD_AM_CENSORWRD', '금지용어');
define('_MD_AM_CENSORWRDDSC', '사용자가 투고시 사용을 금지할 문자열을 입력해 주세요!<br />각 문자열은 <b>|</b>로 구분, 대소문자 구별하지 않음.');
define('_MD_AM_CENSORRPLC', '금지용어 대신 표시할 문자열:');
define('_MD_AM_CENSORRPLCDSC', '금지용어가 있을 경우 이곳에 기입하신 문자열로 대치되게 됩니다.');

define('_MD_AM_SEARCH', '검색 옵션');
define('_MD_AM_DOSEARCH', '글로벌 검색기능을 사용함');
define('_MD_AM_DOSEARCHDSC', '사이트내의 투고글/기사등에 대한 전체검색을 실시합니다.');
define('_MD_AM_MINSEARCH', '키워드 최저문자수');
define('_MD_AM_MINSEARCHDSC', '검색을 할 때 필요한 키워드의 최저문자수를 지정합니다.');
define('_MD_AM_MODCONFIG', '모듈 설정 옵션');
define('_MD_AM_DSPDSCLMR', '이용약관(disclaimer)을 표시');
define('_MD_AM_DSPDSCLMRDSC', '예 를 선택하시면 신규등록페이지에 이용약관을 표시합니다.');
define('_MD_AM_REGDSCLMR', '이용약관(Registration disclaimer)');
define('_MD_AM_REGDSCLMRDSC', '신규등록 페이지에 표시할 이용약관을 입력해 주세요!');
define('_MD_AM_ALLOWREG', '신규 회원의 등록을 허용함');
define('_MD_AM_ALLOWREGDSC', '예 를 선택하시면 신규 회원 등록을 허용하게 됩니다.');
define('_MD_AM_THEMEFILE', 'themes/ 디렉토리로부터의 자동 업그레이드기능을 사용');
define('_MD_AM_THEMEFILEDSC', '현재 사용중인 테마보다 갱신일이 더 최근인 파일이 themes/ 디렉토리내에 존재할 경우 자동적으로 DB의 내용을 갱신하게 됩니다. 공개사이트에서는 OFF(껌)로 설정할 것을 추천합니다.');
define('_MD_AM_CLOSESITE', '사이트 공개중지');
define('_MD_AM_CLOSESITEDSC', '특정 그룹 이외에는 사이트에 접속하지 못하게 합니다.');
define('_MD_AM_CLOSESITEOK', '사이트 공개중지시에도 접속을 허용할 그룹');
define('_MD_AM_CLOSESITEOKDSC', '기본 관리자그룹은 자동적으로 접속이 허용됩니다.');
define('_MD_AM_CLOSESITETXT', '사이트 공개중지의 이유');
define('_MD_AM_CLOSESITETXTDSC', '입력하신 글은 사이트 공개중지시에 표시되게 됩니다.');
define('_MD_AM_SITECACHE', '사이트 캐쉬');
define('_MD_AM_SITECACHEDSC', '사이트내의 콘텐츠를 모듈별로 캐쉬합니다. 사이트 캐쉬기능은 모듈의 독자적인 캐쉬기능(있는 경우)보다 우선시 됩니다.');
define('_MD_AM_MODCACHE', '모듈 캐쉬');
define('_MD_AM_MODCACHEDSC', '각 모듈의 콘텐츠를 캐쉬해 둘 시간의 길이를 지정해 주세요. 모듈에 독자적 캐쉬기능이 있는 경우에는 캐쉬않음 을 선택하실 것을 추천합니다.(블록캐쉬는 포함되지 않습니다.) ');
define('_MD_AM_NOMODULE', '캐쉬가능한 모듈이 존재하지 않습니다.');
define('_MD_AM_DTPLSET', '기본 템플렛 세트');
define('_MD_AM_SSLLINK', 'SSL로그인 페이지 URL');

// added for mailer
define("_MD_AM_MAILER","메일 설정");
define("_MD_AM_MAILER_MAIL","");
define("_MD_AM_MAILER_SENDMAIL","");
define("_MD_AM_MAILER_","");
define("_MD_AM_MAILFROM","송신자 메일주소");
define("_MD_AM_MAILFROMDESC","");
define("_MD_AM_MAILFROMNAME","송신자");
define("_MD_AM_MAILFROMNAMEDESC","");
// RMV-NOTIFY
define("_MD_AM_MAILFROMUID","PM쪽지 송신자");
define("_MD_AM_MAILFROMUIDDESC","PM쪽지를 보낼 때 송신자로서 기본적으로 표시될 사람을 선택해 주세요.");
define("_MD_AM_MAILERMETHOD","메일 전송 방식");
define("_MD_AM_MAILERMETHODDESC","메일 전송 방식을 선택해 주세요! 기본설정에서는 PHP의 mail()함수가 사용됩니다.");
define("_MD_AM_SMTPHOST","SMTP 서버 주소");
define("_MD_AM_SMTPHOSTDESC","SMTP 서버의 주소 목록을 기입해 주세요!");
define("_MD_AM_SMTPUSER","SMTPAuth 유저명");
define("_MD_AM_SMTPUSERDESC","SMTPAuth를 사용해 SMTP서버에 접속시 사용될 유저명");
define("_MD_AM_SMTPPASS","SMTPAuth 패스워드");
define("_MD_AM_SMTPPASSDESC","SMTPAuth를 사용해 SMTP서버에 접속시 사용될 패스워드");
define("_MD_AM_SENDMAILPATH","sendmail 경로");
define("_MD_AM_SENDMAILPATHDESC","sendmail program에의 전체 경로를 기입해 주세요");
define("_MD_AM_THEMEOK","선택가능한 테마");
define("_MD_AM_THEMEOKDSC","사용자가 기본테마로 선택할 수 있게 할 테마를 선택해 주세요.");

?>