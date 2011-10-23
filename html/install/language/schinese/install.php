<?php
// $Id$
define("_INSTALL_L0","欢迎来到XOOPS Cube V2 安装向导，请依照指示安装XOOPS Cube。");
define("_INSTALL_L70","请先确定mainfile.php 这个文件的属性是可读写的，以便让程序把必要的信息写入(例如！在 UNIX/LINUX 主机上 chmod 666 mainfile.php ，于Windows 系统上则把只读属性去除). 当您做好这个动作时请按重设按钮。");
//define("_INSTALL_L71","按下按钮并开始安装。");
define("_INSTALL_L1","开启mainfile.php 并使用您的文字编辑器软体寻找下列的第31行程序码：");
define("_INSTALL_L2","现在，改变这一行成为：");
define("_INSTALL_L3","下一步，第35行把 %s 改为 %s");
define("_INSTALL_L4","最后，按下储存按钮，并尝试一次。");
define("_INSTALL_L5","警告！");
define("_INSTALL_L6","程序侦测到您的XOOPS_ROOT_PATH 与mainfile.php的设定并不相同，请修正。");
define("_INSTALL_L7","您的设定为：");
define("_INSTALL_L8","程序侦测到为：");
define("_INSTALL_L9","( 使用微软MS平台，将有可能会出现这个错误讯息，有时即使您设定正确也还是会出现，请忽略它并按下一步继续安装。)");
define("_INSTALL_L10","请确定以下讯息都正确并请按下一步继续安装。");
define("_INSTALL_L11","您要安装XOOPS Cube 的绝对路径：");
define("_INSTALL_L12","您要安装XOOPS Cube 的网址：");
define("_INSTALL_L13","如果以上资料都正确，请按下面按钮继续。<br />（此步骤尚未连接数据库及建立数据库表）");
define("_INSTALL_L14","下一步");
define("_INSTALL_L15","请开启 mainfile.php 并输入您的数据库相关资料");
define("_INSTALL_L16","%s 是您的SQL server的主机位置");
define("_INSTALL_L17","%s 是您SQL Server的使用者名称");
define("_INSTALL_L18","%s 是您SQL Server的使用者密码");
define("_INSTALL_L19","%s 是您要安装XOOPS Cube所建立的新数据库名称");
define("_INSTALL_L20","%s prefix是在数据库表前会加上一个识别名称，尤其是您在同一数据库安装多个程序时，请注意不要跟其他程序，如 *xoopscube等重复，以免发生错误");
define("_INSTALL_L21","以下的数据库并未在您的MySQL Server上建立");
define("_INSTALL_L22","您确定要建立吗？");
define("_INSTALL_L23","是");
define("_INSTALL_L24","否");
define("_INSTALL_L25","安装程序检查您的mainfile.php里的设定如下，如果不正确请作修改。");
define("_INSTALL_L26","数据库设定");
define("_INSTALL_L51","数据库");
define("_INSTALL_L66","选择要使用的数据库");
define("_INSTALL_L27","数据库主机位置");
define("_INSTALL_L67","数据库主机位置名称如果不确定，请使用'localhost'试试看。");
define("_INSTALL_L28","数据库使用者名称");
define("_INSTALL_L65","数据库使用者名称可以让您进入及建立数据库名称");
define("_INSTALL_L29","数据库名称");
define("_INSTALL_L64","如果发现没有这个数据库名称将自行建立一个新的数据库名称");
define("_INSTALL_L52","数据库使用者密码");
define("_INSTALL_L68","数据库使用者密码是针对您的名称所需要的进入密码。");
define("_INSTALL_L30","Prefix=数据库表前置名称");
define("_INSTALL_L63","Prefix=数据库表前置名称可以让不同程序使用的数据库表不会互相冲突。可自由决定英文或数字，如果您不确定要取什么就用预设值");
define("_INSTALL_L54","使用MySQL pconnect的连接方式吗?");
define("_INSTALL_L69","预设值 '否'。假如您无法确定的话，请选择 '否' .");
define("_INSTALL_L55","XOOPS Cube 的绝对路径");
define("_INSTALL_L59","绝对路径最后不要加斜线");
define("_INSTALL_L56","XOOPS Cube 的网址(URL)");
define("_INSTALL_L58","网址最后不要加斜线");

define("_INSTALL_L31","无法建立数据库，请通知系统管理员询问详细系统信息。");
define("_INSTALL_L32","数据库建立完成！");
define("_INSTALL_L33","请按<a href='../index.php'>这里</font></a>到网站首页");
define("_INSTALL_L35","如果有任何错误，请联系 <a href='http://www.xoopscube.org/'>XOOPS Cube开发站</a>或<a href=\"http://www.xoopscube.tw/\">XOOPS Cube中文支援站</a>");
define("_INSTALL_L36","请填入以下的网站管理员资料以便建立数据库表");
define("_INSTALL_L37","网站管理员帐号：");
define("_INSTALL_L38","网站管理员的Email：");
define("_INSTALL_L39","网站管理员密码：");
define("_INSTALL_L74","再确认一次网站管理员密码：");
define("_INSTALL_L40","建立数据库表");
define("_INSTALL_L41","请回上页再确认一下资料没有错误后再试");
define("_INSTALL_L42","回上页");
define("_INSTALL_L57","请输入 %s");

// %s is database name
define("_INSTALL_L43","数据库 %s 建立完成！");

// %s is table name
define("_INSTALL_L44","无法建立 %s");
define("_INSTALL_L45","数据库表 %s 建立完成");

define("_INSTALL_L46","为确定预设的模组能运作顺畅，请修改以下文件为可读写属性：");
define("_INSTALL_L47","下一步");

define("_INSTALL_L53","请确认以下资料：");

define("_INSTALL_L60","无法开启 mainfile.php文件，请确定此档为可读写属性，并重试一次。");
define("_INSTALL_L61","无法写入 mainfile.php文件，请确定此档为可读写属性。并询问系统管理员。");
define("_INSTALL_L62","设定项目储存完成，按下按钮并继续安装。");
define("_INSTALL_L72","以下目录必须是可写入状态(WIN系统请移除只读属性)(UNIX/LINUX 请 Chmod 666 或是 777 )");
define("_INSTALL_L73","无效的 Email");

// add by haruki
define("_INSTALL_L80","XOOPS Cube 介绍");
define("_INSTALL_L81","确认文件权限范围");
define("_INSTALL_L82","确认文件及目录权限范围..");
define("_INSTALL_L83","%s 档属性为只读");
define("_INSTALL_L84","%s 档属性为可读写");
define("_INSTALL_L85","%s 目录属性为只读");
define("_INSTALL_L86","%s 目录属性为可读写");
define("_INSTALL_L87","无错误报告");
define("_INSTALL_L89","一般设定");
define("_INSTALL_L90","一般设定");
define("_INSTALL_L91","确认");
define("_INSTALL_L92","储存设定");
define("_INSTALL_L93","编辑设定");
define("_INSTALL_L88","储存设定资料..");
define("_INSTALL_L94","确认 & URL");
define("_INSTALL_L127","确认文件路径及URL设定..");
define("_INSTALL_L95","无法侦测到XOOPS Cube目录实体路径");
define("_INSTALL_L96","程序侦测到您的实体路径(%s)与您的的设定并不相同，请修正。");
define("_INSTALL_L97","<b>实体路径</b>正确.");

define("_INSTALL_L99","<b>实体路径</b>必须是个目录");
define("_INSTALL_L100","<b>网址</b>必须是个有效URL.");
define("_INSTALL_L101","<b>网址</b>侦测为无效URL.");
define("_INSTALL_L102","确定所有设定");
define("_INSTALL_L103","回到设定最初画面");
define("_INSTALL_L104","确认数据库");
define("_INSTALL_L105","建立数据库");
define("_INSTALL_L106","无法联结到数据库");
define("_INSTALL_L107","请确认数据库的设定是否正确.");
define("_INSTALL_L108","已联结到数据库");
define("_INSTALL_L109","数据库 %s 目前不存在.");
define("_INSTALL_L110","已跟数据库 %s 连结上.");
define("_INSTALL_L111","数据库连结完成.<br />按下按键建立数据库表");
define("_INSTALL_L112","管理者设定");
define("_INSTALL_L113","数据库表 %s 删除.");
define("_INSTALL_L114","数据库表建立失败.");
define("_INSTALL_L115","数据库表已建立.");
define("_INSTALL_L116","输入资料");
define("_INSTALL_L117","完成");

define("_INSTALL_L118","数据库表 %s 数据库表建立失败.");
define("_INSTALL_L119","%d 资料输入到 %s.");
define("_INSTALL_L120","%d 资料输入到 %s 失败.");

define("_INSTALL_L121","%s 内容输入到 %s.");
define("_INSTALL_L122","资料输入到 %s 失败.");

define("_INSTALL_L123","%s 档储存在/cache/ 目录.");
define("_INSTALL_L124","%s 档储存在/cache/ 目录失败.");

define("_INSTALL_L125","%s 档被 %s 覆写.");
define("_INSTALL_L126","无法覆写 %s.");

define("_INSTALL_L130","安装精灵侦测到您有XOOPS 1.3.x 或 XOOPS2 的相关数据库表，<br />现在让XOOPS Cube安装升级精灵让您升级到XOOPS Cube.");
define("_INSTALL_L131","XOOPS Cube相关数据库表已存在。");
define("_INSTALL_L132","升级数据库表");
define("_INSTALL_L133","%s 数据库表升级.");
define("_INSTALL_L134","%s 数据库表升级失败.");
define("_INSTALL_L135","数据库表升级失败.");
define("_INSTALL_L136","数据库表升级.");
define("_INSTALL_L137","升级模组");
define("_INSTALL_L138","升级评论");
define("_INSTALL_L139","升级大头照");
define("_INSTALL_L140","升级表情图");
define("_INSTALL_L141","安装升级精灵将把您所有模组升级到XOOPS Cube，<br />请确定您上传 XOOPS Cube所有文件到网站中.<br />剩下的就交给安装升级精灵.");
define("_INSTALL_L142","更新模组中..");
define("_INSTALL_L143","安装升级精灵将您旧有的 XOOPS 1.3.x 或 XOOPS2 转换成XOOPS Cube。");
define("_INSTALL_L144","升级设定");
define("_INSTALL_L145","评论 (ID: %s) 输入到数据库中.");
define("_INSTALL_L146","无法将评论 (ID: %s) 输入到数据库中。");
define("_INSTALL_L147","升级评论...");
define("_INSTALL_L148","升级完成。");
define("_INSTALL_L149","安装升级精灵将您XOOPS 1.3.x新闻评论升级到XOOPS Cube.<br />请耐心等候。");
define("_INSTALL_L150","安装升级精灵将您XOOPS 1.3.x表情图及评分系统图案升级到XOOPS Cube，<br />请耐心等候。");
define("_INSTALL_L151","安装升级精灵将您XOOPS 1.3.x大头照图片升级到XOOPS Cube，<br />请耐心等候。");
define("_INSTALL_L155","升级表情图及评分系统图案...");
define("_INSTALL_L156","升级大头照图片...");
define("_INSTALL_L157","为各类群组选择预设值");
define("_INSTALL_L158","1.3.x 或 XOOPS2 旧有群组");
define("_INSTALL_L159","网站管理员");
define("_INSTALL_L160","注册会员");
define("_INSTALL_L161","访客");
define("_INSTALL_L162","您必须为各群组设定权限.");
define("_INSTALL_L163","%s数据库表删除.");
define("_INSTALL_L164","%s数据库表删除失败.");
define("_INSTALL_L165","站务维修中，请稍后再访问.");

// %s is filename
define("_INSTALL_L152","无法开启%s.");
define("_INSTALL_L153","无法更新 %s.");
define("_INSTALL_L154","%s 更新完成.");

define('_INSTALL_L128', '选择安装过程所使用的语言，简体中文GB2312码请选择chinese，如果您使用简体中文UTF-8编码，请从extras/extra_languages/cn_utf-8/htm里复制所有文件后选择cn_utf-8。');
define('_INSTALL_L200', '重新载入');
define('_INSTALL_L210', '进入第二阶段安装设置');

define('_INSTALL_CHARSET','GB2312');

define('_INSTALL_LANG_XOOPS_SALT', "编码");
define('_INSTALL_LANG_XOOPS_SALT_DESC', "这个是为了补充作用来生成安全编码和标记使用， 您不需改变预设值。");

define('_INSTALL_HEADER_MESSAGE','请依照荧幕指示进行安装程序。');
?>
