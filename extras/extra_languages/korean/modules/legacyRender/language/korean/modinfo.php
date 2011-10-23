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

define('_MI_LEGACYRENDER_ADMENU_BANNER_MANAGE', "배너 관리");
define('_MI_LEGACYRENDER_ADMENU_TEMPLATE_MANAGE', "템플릿 관리");
define('_MI_LEGACYRENDER_ADMENU_THEME_SETTING', "테마 설정");
define('_MI_LEGACYRENDER_ADMENU_TPLFILE_MANAGE', "템플릿 파일 관리");
define('_MI_LEGACYRENDER_ADMENU_HTACCESS_VIEW', "htaccess setting");
define('_MI_LEGACYRENDER_CONF_BANNERS', "배너 광고를 유효화/활성화");
define('_MI_LEGACYRENDER_NAME', "호환 랜더 시스템");
define('_MI_LEGACYRENDER_NAME_DESC', "이 호환 랜더엔진은 XOOPS 2.0.9 과 XOOPS JP 2.0.x. 시리즈의 테마엔진과의 호환성이 있습니다. 즉 XOOPS2용 테마를 사용하실 수 있습니다.");
define('_MI_LR_FOOTER', 'Footer 설정');
define('_MI_LR_FOOTER_DESC', '링크를 작성하실 때에는 반드시 풀패스(http://~)로 입력해 주세요! 그렇지 않으면 각 모듈내 페이지에서 올바르게 표시되지 않을 수 있습니다.');
define('_MI_LR_META_AUTHOR', 'Meta태그(작성자[Author])');
define('_MI_LR_META_AUTHOR_DESC', '작성자 Meta태크는 사이트 문서의 작성자 정보를 정의합니다. 이름, 웹마스터 메일주소, 회사명, URL등을 기입하실 수 있습니다.');
define('_MI_LR_META_COPYRIGHT', 'Meta태그(저작권[Copyright])');
define('_MI_LR_META_COPYRIGHT_DESC', '저작권 Meta태그는 사이트 내의 정보에 대한 저작권정보를 정의합니다.');
define('_MI_LR_META_DESCRIPTION', 'Meta태그(내용설명[Description])');
define('_MI_LR_META_DESCRIPTION_DESC', '내용설명 Meta태그는 사이트의 내용을 설명하기위한 태그입니다.');
define('_MI_LR_META_KEYWORDS', 'Meta태그(키워드[Keywords])');
define('_MI_LR_META_KEYWORDS_DESC', '키워드[Keywords] Meta태그는 사이트의 내용을 표현합니다. 각 키워드는 쉼표(콤마)로 구분해 주세요!(Ex. XOOPS, PHP, mySQL, portal system)');
define('_MI_LR_META_RATING', 'Meta태그(등급[Rating])');
define('_MI_LR_META_RATING_DESC', '사이트 접속 대상 연령층을 지정합니다.');
define('_MI_LR_META_ROBOTS', 'Meta태그(검색로봇[Robots])');
define('_MI_LR_META_ROBOTS_DESC', '검색로봇에의 대응방식을 지정합니다.');
define('_MI_LR_ROBOT_INDEXFOLLOW', 'Index, Follow');
define('_MI_LR_ROBOT_INDEXNOFOLLOW', 'Index, No Follow');
define('_MI_LR_ROBOT_METAO14YRS', '14 years');
define('_MI_LR_ROBOT_METAOGEN', 'General');
define('_MI_LR_ROBOT_METAOMAT', 'Mature');
define('_MI_LR_ROBOT_METAOREST', 'Restricted');
define('_MI_LR_ROBOT_NOINDEXFOLLOW', 'No Index, Follow');
define('_MI_LR_ROBOT_NOINDEXNOFOLLOW', 'No Index, No Follow');
define('_MI_LR_PAGETITLE_FORMAT', "페이지타이틀 형식");
define('_MI_LR_PAGETITLE_FORMAT_DESC', "페이지타이틀 형식을 {modulename}, {pagetitle}, {action}을 이용해 지정해 주세요! 모듈명은 {modulename}, 페이지타이틀(예:'안녕하세요', '설치방법'등과 같은 제목)은  {pagetitle}, 액션(예: 편집, 리스트 등과 같은 작업)은 {action}의 위치에 표시되게 됩니다. [module]***[/module] 과 같이 지정하면 ***은 {module}이 있는 경우에만 표시되게 됩니다. [pagetitle]***[/pagetitle], [action]***[action] 도 마찬가지로 동일하게 적용됩니다.");
define('_MI_LR_CSS_FILE', "jQuery UI CSS 파일의 URL");
define('_MI_LR_CSS_FILE_DESC', "jQuery UI CSS 파일의 URL을 지정합니다.");
define('_MI_LR_FEED_URL', "RSS Feed URL");
define('_MI_LR_FEED_URL_DESC', "RSS feed URL을 지정합니다.");
define('_MI_LR_JQUERY_CORE', "jQuery 코어 라이브러리");
define('_MI_LR_JQUERY_CORE_DESC', "Google Libraries API를 사용하는 경우 jQuery의 버전을 입력해 주세요! 로컬의 jQuery 파일을 사용하는 경우엔 그 URL을 입력하시면 됩니다.");
define('_MI_LR_JQUERY_UI', "jQuery UI 라이브러리");
define('_MI_LR_JQUERY_UI_DESC', "Google Libraries API를 사용하는 경우 jQuery UI의 버전을 입력해 주세요! 로컬의 jQuery UI 파일을 사용하는 경우엔 그 URL을 입력하시면 됩니다. 어느쪽이든 반드시 jQuery 코어 라이브러리와 동일한 방식이어야 합니다.");

?>