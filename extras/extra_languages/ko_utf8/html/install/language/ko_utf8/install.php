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


define("_INSTALL_L0","어서오세요. XOOPS Cube 설치마법사 입니다.");
define("_INSTALL_L168","XOOPS Cube Legacy requires PHP5 or later");
define("_INSTALL_L70","mainfile.php파일을 쓰기가능으로 설정해 주세요!<br />(예: UNIX/LINUX 서버의 경우는 웹서버에 mainfile.php 쓰기권한을 부여, Windows서버의 경우는 혹시 읽기전용으로 되어있는지 확인후 체크를 해제해주세요! ). <br />권한설정 변경후엔 이 페이지를 새로고침(리로드)해 주세요!");
//define("_INSTALL_L71","Click on the button below to begin the installation.");
define("_INSTALL_L1","mainfile.php 파일을 여신 후 31번째 줄에 이하의 코드가 존재하는지 확인해주세요!");
define("_INSTALL_L2","이 줄을 다음과 같이 변경해 주세요!");
define("_INSTALL_L3","다음으론 35번째 줄의 %s 을 %s 로 변경해 주세요!");
define("_INSTALL_L4","설치(인스톨)을 계속함");
define("_INSTALL_L5","주의!");
define("_INSTALL_L6","mainfile.php에 설정되어 있는 XOOPS_ROOT_PATH 와  설치 마법사가 탐지한 루트패스(root path)가 일치하지 않습니다.");
define("_INSTALL_L7","mainfile.php의 설정: ");
define("_INSTALL_L8","설치마법사가 탐지한 설정: ");
define("_INSTALL_L9","( Windows환경하에서는 설정이 올바른 경우에도 주의메세지가 표시되는 경우가 발생할수도 있습니다. 설정이 정확하다면 아래의 버튼을 클릭해서 설치를 계속해주시기 바랍니다.)");
define("_INSTALL_L10","설정에 틀린 점이 없다면 아래의 버튼을 클릭해서 설치를 계속해주시기 바랍니다.");
define("_INSTALL_L11","XOOPS Cube 디렉토리의 경로: ");
define("_INSTALL_L12","XOOPS Cube의 URL: ");
define("_INSTALL_L13","이상의 설정이 정확하다면 설치를 계속해 주시기 바랍니다.<br />잘못된 경우엔 처음부터 다시 시도해 주시기 바랍니다.<br />혹은 mainfile.php를 직접 수정한후 이 페이지를 새로고침(리로딩)해주시기 바랍니다.");
define("_INSTALL_L14","다음");
define("_INSTALL_L15","mainfile.php 파일을 연 후 필요한 설정을 모두 기입해 주시기 바랍니다.");
define("_INSTALL_L16","%s 는 데이타베이스 서버의 호스트명입니다.");
define("_INSTALL_L17","%s 는 데이타베이스 서버의 사용자계정명 입니다.");
define("_INSTALL_L18","%s 는 데이타베이스에 액세스하기 위해 필요한 패스워드입니다.");
define("_INSTALL_L19","%s 는 XOOPS Cube가 사용할 데이타베이스의 이름입니다.");
define("_INSTALL_L20","%s 는 XOOPS Cube가 사용할 각 데이타베이스 테이블의 접두어(prefix)입니다. 접두어를 붙임으로써 기존의 테이블과 테이블명이 중복되는 것을 방지하실 수 있습니다.");
define("_INSTALL_L21","이하의 데이타베이스를 발견하지 못했습니다: ");
define("_INSTALL_L22","이 데이타베이스를 신규작성하시려면 설치를 계속하시기 바랍니다.<br />데이타베이스명이 잘못 지정된 경우엔 처음부터 다시 시도해주시기 바랍니다.");
define("_INSTALL_L23","예");
define("_INSTALL_L24","아니요");
define("_INSTALL_L25","mainfile.php 파일에 기록되어 있는 다음의 설정들이 틀림이 없는지 확인해 주시기 바랍니다.");
define("_INSTALL_L26","데이타베이스 설정");
define("_INSTALL_L51","데이타베이스 서버");
define("_INSTALL_L66","사용하는 데이타베이스 서버의 종류를 선택해 주세요");
define("_INSTALL_L27","데이타베이스 서버 호스트명");
define("_INSTALL_L67","사용하는 데이타베이스 서버의 호스트명을 입력해 주세요. <br />잘 모르실 경우는 localhost 로 설정하시면 대부분 문제없이 작동합니다.");
define("_INSTALL_L28","데이타베이스 사용자 계정명");
define("_INSTALL_L65","사용하는 데이타베이스 서버에서의 사용자계정명을 입력해 주세요");
define("_INSTALL_L29","데이타베이스명");
define("_INSTALL_L64","사용할 데이타베이스명을 입력해 주세요!<br />존재하지 않을 경우엔 이 데이타베이스명으로 신규작성을 시도하게 됩니다.");
define("_INSTALL_L52","데이타베이스 패스워드");
define("_INSTALL_L68","위에서 입력한 데이타베이스 사용자 계정명의 패스워드를 입력해 주세요");
define("_INSTALL_L30","테이블 접두어");
define("_INSTALL_L63","각 테이블명에 이 접두어를 붙임으로써 기존의 테이블과 이름이 중복되는 것을 방지하게 됩니다. <br />잘 모르실 경우는 기본값을 사용해 주세요.");
define("_INSTALL_L54","데이타베이스에의 영구접속(persistent connection)");
define("_INSTALL_L69","기본값은  아니요(NO) 입니다. 잘 모르실 경우는 아니요(NO) 를 선택해 주세요");
define("_INSTALL_L55","XOOPS Cube의 경로(Physical Path)");
define("_INSTALL_L59","XOOPS Cube가 설치되어있는 디렉토리로의 전체경로(Full Path)를 입력해 주세요! 끝에 [ / ]를 붙이지 마시기 바랍니다.");
define("_INSTALL_L75","XOOPS_TRUST_PATH Physical Path");
define("_INSTALL_L76","Physical path to your main XOOPS_TRUST_PATH directory WITHOUT trailing slash<br />You should set XOOPS_TRUST_PATH outside DocumentRoot.");
define("_INSTALL_L56","XOOPS Cube의 URL(Virtual Path)");
define("_INSTALL_L58","XOOPS Cube에 접속하기 위한 URL을 입력해 주세요! 끝에 [ / ] 를 붙이지 마시기 바랍니다.");

define("_INSTALL_L31","데이타베이스의 작성에 실패하였습니다. 설정값이 올바른지 확인한 후 다시 처음부터 다시시도하시거나 관리자에게 해당사항을 문의해 보시기 바랍니다.");
define("_INSTALL_L32","설치 제1단계작업 완료");
define("_INSTALL_L33","설치가 끝난 사이트를 보시려면<a href='../index.php'>여기</a> 를 클릭해 주세요!");
define("_INSTALL_L35","설치중에 에러가 발생한 경우엔 <a href='http://xoopscube.sourceforge.net/' rel='external'>XOOPS Cube Project</a> 또는 <a href='http://www.wanisys.net/' rel='external'>XOOPS Cube 비공식 한국어지원사이트</a>에 문의해 주시기 바랍니다.");
define("_INSTALL_L36","사이트 관리자의 아이디, 패스워드, 메일주소를 입력해 주시기 바랍니다.");
define("_INSTALL_L37","관리자 아이디");
define("_INSTALL_L38","관리자 이메일");
define("_INSTALL_L39","관리자 패스워드");
define("_INSTALL_L74","관리자 패스워드(재입력)");
define("_INSTALL_L77","Set Default Timezone");
define("_INSTALL_L40","데이타베이스테이블 작성");
define("_INSTALL_L41","필요한 정보를 모두 입력해 주시기 바랍니다.");
define("_INSTALL_L42","뒤로");
define("_INSTALL_L57","%s 를 입력해 주세요!");

// %s is database name
define("_INSTALL_L43","데이타베이스 %s 을 작성하였습니다.");

// %s is table name
define("_INSTALL_L44","%s 테이블의 작성에 실패하였습니다.");
define("_INSTALL_L45","%s 테이블을 작성하였습니다.");

define("_INSTALL_L46","XOOPS Cube의 모듈들이 정상적으로 작동하기 위해선 이하의 파일들이 서버에 의해 쓰기가능으로 설정되어 있어야만 합니다.");
define("_INSTALL_L47","다음");

define("_INSTALL_L53","설정내용확인:");

define("_INSTALL_L60","mainfile.php 파일에의 쓰기에 실패하였습니다. 파일권한설정이 올바른지 다시 확인해 주시기 바랍니다.");
define("_INSTALL_L61","mainfile.php 파일에의 쓰기에 실패하였습니다. 서버 관리자에게 관련사항에 대해 문의해 보시기 바랍니다.");
define("_INSTALL_L62","설정내용을 mainfile.php 파일에 성공적으로 기록하였습니다.");
define("_INSTALL_L72","다음의 디렉토리들을 서버에 의해 쓰기가능으로 설정해 주시기 바랍니다.");
define("_INSTALL_L73","올바르지 않은 메일주소입니다.");

// add by haruki
define("_INSTALL_L80","소개글");
define("_INSTALL_L81","파일의 권한(퍼미션) 체크");
define("_INSTALL_L82","파일과 디렉토리의 권한(퍼미션) 체크");
define("_INSTALL_L83","파일 %s 은(는) 쓰기불가 상태입니다. 쓰기가능으로 설정해주세요!");
define("_INSTALL_L84","파일 %s 은(는) 쓰기가능 상태입니다.");
define("_INSTALL_L85","디렉토리 %s 은(는) 쓰기불가 상태입니다. 쓰기가능으로 설정해주세요!");
define("_INSTALL_L86","디렉토리 %s 은 쓰기가능 상태입니다.");
define("_INSTALL_L87","파일의 권한(퍼미션) 체크 이상무!");
define("_INSTALL_L89","기본 설정");
define("_INSTALL_L90","일반설정: 데이타베이스,PATH,URL 설정");
define("_INSTALL_L91","확인");
define("_INSTALL_L92","설정을 저장");
define("_INSTALL_L93","설정을 재입력");
define("_INSTALL_L88","파일,디렉토리의 액세스권한을 확인해 주세요.");
define("_INSTALL_L166","Check file permissions in XOOPS_TRUST_PATH");
define("_INSTALL_L167","Checking file and directory permissions..");
define("_INSTALL_L94","PATH, URL 체크");
define("_INSTALL_L127","파일의 PATH & URL을 체크하고 있습니다...");
define("_INSTALL_L95","XOOPS Cube 경로(PATH) 탐지에 실패하였습니다.");
define("_INSTALL_L96","탐지된 XOOPS Cube 경로가 설정되어진 값(XOOPS_ROOT_PATH)과 일치하지 않습니다.");
define("_INSTALL_L97","탐지된 XOOPS Cube 경로가 설정되어진 값(XOOPS_ROOT_PATH)과 일치합니다.");

define("_INSTALL_L99","<b>설정된 경로(Physical path)</b>는 디렉토리가 아닙니다.");
define("_INSTALL_L100","설정된 <b>URL(Virtual path)</b>은 올바른 형식의 URL입니다.");
define("_INSTALL_L101","설정된 <b>URL(Virtual path)</b>은 올바른 형식의 URL이 아닙니다.");
define("_INSTALL_L102","데이타베이스 설정의 확인");
define("_INSTALL_L103","처음부터 다시 시도함");
define("_INSTALL_L104","데이타베이스 체크");
define("_INSTALL_L105","데이타베이스 작성을 시도");
define("_INSTALL_L106","데이타베이스 서버에 접속하는데 실패하였습니다.");
define("_INSTALL_L107","데이타베이스 관련설정이 올바른지 혹은 데이타베이스 서버가 정상적으로 동작중인지 확인해 보시기 바랍니다.");
define("_INSTALL_L108","데이타베이스 서버에 성공적으로 접속하였습니다.");
define("_INSTALL_L109","데이타베이스 %s 는 존재하지 않습니다.");
define("_INSTALL_L110","데이타베이스 %s 는 존재하고 접속가능한 상태입니다.");
define("_INSTALL_L111","데이타베이스 서버에 성공적으로 접속하였습니다.<br />아래의 버튼을 클릭하셔서 테이타베이스테이블을 작성하시기 바랍니다.");
define("_INSTALL_L112","사이트 관리자 관련 설정");
define("_INSTALL_L113","테이블 %s 가 삭제되었습니다.");
define("_INSTALL_L114","데이타베이스테이블의 작성에 실패하였습니다.");
define("_INSTALL_L115","데이타베이스테이블이 성공적으로 작성되었습니다.");
define("_INSTALL_L116","데이타 작성(삽입)");
define("_INSTALL_L117","완료");

define("_INSTALL_L118","데이타베이스 %s의 작성에 실패하였습니다.");
define("_INSTALL_L119","%d 개의 데이타가 데이타베이스 %s에 삽입되었습니다.");
define("_INSTALL_L120","%d 개의 데이타가 데이타베이스 %s에 삽입되는데 실패하였습니다.");

define("_INSTALL_L121","상수 %s 는 %s 로 설정되었습니다.");
define("_INSTALL_L122","상수 %s 의 기록에 실패하였습니다.");

define("_INSTALL_L123","파일 %s 가  cache/ 디렉토리에 기록되어졌습니다.");
define("_INSTALL_L124","파일 %s 를 cache/ 디렉토리에 기록하는데 실패하였습니다.");

define("_INSTALL_L125","파일 %s 는 파일 %s 로 덮어쓰여졌습니다.");
define("_INSTALL_L126","파일 %s의 덮어쓰기에 실패하였습니다.");

define("_INSTALL_L130","설치마법사가 데이타베이스에서 XOOPS 1.3.x 의 테이블을 발견하였습니다.<br />설치마법사는 지금부터 XOOPS2 로의 업그레이드를 시도하게 됩니다.");
define("_INSTALL_L131","XOOPS Cube Legacy의 테이블이 데이타베이스에 이미 존재합니다.");
define("_INSTALL_L132","테이블 업그레이드");
define("_INSTALL_L133","테이블 %s 를 업그레이드했습니다.");
define("_INSTALL_L134","테이블 %s 의 업그레이드에 실패하였습니다.");
define("_INSTALL_L135","데이타베이스테이블의 업그레이드에 실패하였습니다.");
define("_INSTALL_L136","데이타베이스테이블을 업그레이드하였습니다..");
define("_INSTALL_L137","모듈 업그레이드");
define("_INSTALL_L138","코멘트 업그레이드");
define("_INSTALL_L139","아바타 업그레이드");
define("_INSTALL_L140","얼굴아이콘 업그레이드");
define("_INSTALL_L141","설치마법사는 지금부터 XOOPS Cube 에서 동작가능하도록 각 모듈을 업그레이드합니다.<br />XOOPS Cube 패키지에 포함된 모든 파일들이 서버에 업로드되었는지 확인해 주세요!<br />이 작업엔 약간 시간이 걸릴수도 있습니다.");
define("_INSTALL_L142","모듈 업그레이드 중..");
define("_INSTALL_L143","The installer will now update configuration data of XOOPS 1.3.x to be used with XOOPS Cube.");
define("_INSTALL_L144","설정(Configuration) 업그레이드");
define("_INSTALL_L145","코멘트 (ID: %s) 를 데이타베이스에 삽입처리하였습니다.");
define("_INSTALL_L146","코멘트 (ID: %s) 를 데이타베이스에 삽입하는데 실패하였습니다.");
define("_INSTALL_L147","코멘트 업그레이드 중..");
define("_INSTALL_L148","업그레이드가 완료되었습니다..");
define("_INSTALL_L149","지금부터 설치마법사는 XOOPS Cube에서 사용가능하도록 XOOPS 1.3.x의 코멘트들을 업그레이드합니다. <br />이 작업은 약간 시간이 걸릴 수도 있습니다.");
define("_INSTALL_L150","지금부터 설치마법사는 XOOPS Cube에서 사용가능하도록 얼굴아이콘,회원등급 관련 이미지파일을 업그레이드합니다.<br />이 작업은 약간 시간이 걸릴 수도 있습니다.");
define("_INSTALL_L151","지금부터 설치마법사는 XOOPS Cube에서 사용가능하도록 회원아바타관련 이미지파일을 업그레이드합니다.<br />이 작업은 약간 시간이 걸릴 수도 있습니다.");
define("_INSTALL_L155","얼굴아이콘,회원등급 관련 이미지파일 업그레이드 중..");
define("_INSTALL_L156","회원아바타 관련 이미지파일 업그레이드 중..");
define("_INSTALL_L157","각 그룹타입에 대해 기본 그룹을 선택해 주세요");
define("_INSTALL_L158","버전 1.3.x");
define("_INSTALL_L159","관리자그룹");
define("_INSTALL_L160","등록회원그룹");
define("_INSTALL_L161","손님그룹");
define("_INSTALL_L162","각 그룹 타입에 대해 기본 그룹을 설정해 주시기 바랍니다.");
define("_INSTALL_L163","테이블 %s 를 삭제하였습니다.");
define("_INSTALL_L164","테이블 %s 의 삭제에 실패하였습니다.");
define("_INSTALL_L165","이 사이트는 현재 서버점검중입니다. 잠시후에 다시 접속해 주시기 바랍니다.");

// %s is filename
define("_INSTALL_L152","파일 %s 를 열지 못했습니다.");
define("_INSTALL_L153","파일 %s 의 업그레이드에 실패하였습니다.");
define("_INSTALL_L154","파일 %s 를 업그레이드 하였습니다.");

define('_INSTALL_L128', '설치(인스톨)과정에서 사용하실 언어를 선택해 주세요!');
define('_INSTALL_L200', '새로고침(Reload)');
define("_INSTALL_L210","설치 제2단계 작업");


define('_INSTALL_CHARSET','UTF-8');

define('_INSTALL_LANG_XOOPS_SALT', "SALT");
define('_INSTALL_LANG_XOOPS_SALT_DESC', "암호 및 토큰을 생성할 때 보조적 역할을 하는 정보입니다. 기본값을 변경없이 사용하셔도 무방합니다.");

define('_INSTALL_HEADER_MESSAGE','설치 단계별 설명을 잘 읽어보신 후 지시에 따라 설치작업을 진행해 주세요!');

?>