<?php
// $Id$
define('_TOKEN_ERROR', '警示讯息 ! 您所变更的内容与原始内容相同，请确认!');
define('_SYSTEM_MODULE_ERROR', '以下模组未安装.');
define('_INSTALL','安装');
define('_UNINSTALL','卸载');
define('_SYS_MODULE_UNINSTALLED','必须安装(未安装)');
define('_SYS_MODULE_DISABLED','必须安装(未启用)');
define('_SYS_RECOMMENDED_MODULES','建议安装的模组');
define('_SYS_OPTION_MODULES','附加模组');
define('_UNINSTALL_CONFIRM','您确定要反安装系统模组?');

//%%%%%%	File Name mainfile.php 	%%%%%
define("_PLEASEWAIT","请稍候");
define("_FETCHING","资料载入中...");
define("_TAKINGBACK","系统将带您回到原来的页面....");
define("_LOGOUT","登出");
define("_SUBJECT","主题");
define("_MESSAGEICON","文章图示");
define("_COMMENTS","回应评论");
define("_POSTANON","匿名发表");
define("_DISABLESMILEY","关闭表情图");
define("_DISABLEHTML","关闭html语法");
define("_PREVIEW","预览");

define("_GO","确定");
define("_NESTED","巢状");
define("_NOCOMMENTS","无回应评注");
define("_FLAT","全部展开");
define("_THREADED","树状显示");
define("_OLDESTFIRST","旧的在前");
define("_NEWESTFIRST","新的在前");
define("_MORE","尚有...");
define("_MULTIPAGE","文章如果要分页，请在分页处加入 <font color=red>[pagebreak]</font> (包含括号).");
define("_IFNOTRELOAD","假如系统没有自动前往，请按<a href=%s>这里</a>继续");
define("_WARNINSTALL2","警示讯息 ! %s 还存在于您的主机上. <br />为了安全起见请把这文件及目录删除.");
define("_WARNINWRITEABLE","警示讯息 ! %s 属性为可读写. <br />为了安全起见请将它的可写属性改为不可写入。.<br /> Unix (444), Win32 (唯读)");
define('_WARNPHPENV','警示讯息 ! php.ini 参数 "%s" 设置为"%s". %s');
define('_WARNSECURITY','(这可能会导致安全性问题)');

//%%%%%%	File Name themeuserpost.php 	%%%%%
define("_PROFILE","个人资料");
define("_POSTEDBY","发表者");
define("_VISITWEBSITE","拜访网站");
define("_SENDPMTO","传送私人讯息给 %s");
define("_SENDEMAILTO","传送 Email 给 %s");
define("_ADD","加入");
define("_REPLY","回应");
define("_DATE","发表日");   // Posted date

//%%%%%%	File Name admin_functions.php 	%%%%%
define("_MAIN","主页");
define("_MANUAL","使用手册");
define("_INFO","信息");
define("_CPHOME","管理控制台首页");
define("_YOURHOME","网站首页");

//%%%%%%	File Name misc.php (who's-online popup)	%%%%%
define("_WHOSONLINE","线上人数");
define('_GUESTS', '访客');
define('_MEMBERS', '会员');
define("_ONLINEPHRASE","线上目前共<b>%s</b>人<br>");
define("_ONLINEPHRASEX","<b>%s</b>人在浏览<b>%s</b>");
define("_CLOSE","关闭视窗");  // Close window

//%%%%%%	File Name module.textsanitizer.php 	%%%%%
define("_QUOTEC","引文:");

//%%%%%%	File Name admin.php 	%%%%%
define("_NOPERM","抱歉!您的权限不够无法进入本区.");

//%%%%%		Common Phrases		%%%%%
define("_NO","否");
define("_YES","是");
define("_EDIT","编辑");
define("_DELETE","删除");
define("_VIEW","View");
define("_SUBMIT","确定送出");
define("_MODULENOEXIST","选择的模组不存在!");
define("_ALIGN","位置");
define("_LEFT","靠左");
define("_CENTER","置中");
define("_RIGHT","靠右");
define("_FORM_ENTER", "请输入 %s");
// %s represents file name
define("_MUSTWABLE","文件 %s 必须设为可读写!");
// Module info
define('_PREFERENCES', '设定');
define("_VERSION", "版本");
define("_DESCRIPTION", "描述");
define("_ERRORS", "错误");
define("_NONE", "无");
define('_ON','开');
define('_POSTON','于');
define('_READS','人气');
define('_WELCOMETO','欢迎来到 %s');
define('_SEARCH','搜寻');
define('_ALL', '全部');
define('_TITLE', '主题');
define('_OPTIONS', '附加项目');
define('_QUOTE', '引文');
define('_LIST', '列出');
define('_LOGIN','使用者登入');
define('_USERNAME','帐号: ');
define('_PASSWORD','密码: ');
define("_SELECT","选择");
define("_IMAGE","图片");
define("_SEND","传送");
define("_CANCEL","取消");
define("_ASCENDING","升幂排列");
define("_DESCENDING","降幂排列");
define('_BACK', '回上页');
define('_NOTITLE', '无标题');
define('_RETURN_TOP', '回页面顶端');

/* Image manager */
define('_IMGMANAGER','图片管理员');
define('_NUMIMAGES', '%s 图片');
define('_ADDIMAGE','新增图片');
define('_IMAGENAME','名称:');
define('_IMGMAXSIZE','最大文件大小 (kb):');
define('_IMGMAXWIDTH','最大宽度 (pixels):');
define('_IMGMAXHEIGHT','最大高度(pixels):');
define('_IMAGECAT','目录:');
define('_IMAGEFILE','图片:');
define('_IMGWEIGHT','图片排序:');
define('_IMGDISPLAY','显示此图片?');
define('_IMAGEMIME','MIME格式:');
define('_FAILFETCHIMG', '%s 无法上传');
define('_FAILSAVEIMG', '%s 无法储存');
define('_NOCACHE', '不使用 Cache');
define('_CLONE', '复制');

//%%%%%	File Name class/xoopsform/formmatchoption.php 	%%%%%
define("_STARTSWITH", "开始于");
define("_ENDSWITH", "结束于");
define("_MATCHES", "符合");
define("_CONTAINS", "相容");

//%%%%%%	File Name commentform.php 	%%%%%
define("_REGISTER","会员");

//%%%%%%	File Name xoopscodes.php 	%%%%%
define("_SIZE","大小");  // font size
define("_FONT","字型");  // font family
define("_COLOR","颜色");  // font color
define("_EXAMPLE","范例");
define("_ENTERURL","输入网址:");
define("_ENTERWEBTITLE","输入网站名称:");
define("_ENTERIMGURL","输入图片网址.");
define("_ENTERIMGPOS","输入图片放置位置");
define("_IMGPOSRORL","'R' 或 'r' 表示右边 'L' 或 'l' 表示左边，或留空白。");
define("_ERRORIMGPOS","错误！请输入图片放置位置");
define("_ENTEREMAIL","请输入Email。");
define("_ENTERCODE","请输入你要增加的代码");
define("_ENTERQUOTE","请输入内文");
define("_ENTERTEXTBOX","请在文字框里输入文字");
define("_ALLOWEDCHAR","字元长度限制:");
define("_CURRCHAR","文章的字元长度: ");
define("_PLZCOMPLETE","请确定主旨及内容是否填写");
define("_MESSAGETOOLONG","您的文章太长，请缩小长度。");

//%%%%%		TIME FORMAT SETTINGS   %%%%%
define('_SECOND', '1 秒');
define('_SECONDS', '%s 秒');
define('_MINUTE', '1 分');
define('_MINUTES', '%s 分');
define('_HOUR', '1 小时');
define('_HOURS', '%s 小时');
define('_DAY', '1 天');
define('_DAYS', '%s 天');
define('_WEEK', '1 周');
define('_MONTH', '1 月');

define('_HELP', "帮助说明");

?>