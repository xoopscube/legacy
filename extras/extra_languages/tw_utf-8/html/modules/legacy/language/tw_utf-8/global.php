<?php
// $Id: global.php,v 1.1 2008/03/09 02:26:03 minahito Exp $
define('_TOKEN_ERROR', '警示訊息 ! 您所變更的內容與原始內容相同，請確認!');
define('_SYSTEM_MODULE_ERROR', '以下模組未安裝.');
define('_INSTALL','安裝');
define('_UNINSTALL','反安裝');
define('_SYS_MODULE_UNINSTALLED','必須安裝(未安裝)');
define('_SYS_MODULE_DISABLED','必須安裝(未啟用)');
define('_SYS_RECOMMENDED_MODULES','建議安裝的模組');
define('_SYS_OPTION_MODULES','附加模組');
define('_UNINSTALL_CONFIRM','您確定要反安裝系統模組?');

//%%%%%%	File Name mainfile.php 	%%%%%
define("_PLEASEWAIT","請稍候");
define("_FETCHING","資料載入中...");
define("_TAKINGBACK","系統將帶您回到原來的頁面....");
define("_LOGOUT","登出");
define("_SUBJECT","主題");
define("_MESSAGEICON","文章圖示");
define("_COMMENTS","回應評論");
define("_POSTANON","匿名發表");
define("_DISABLESMILEY","關閉表情圖");
define("_DISABLEHTML","關閉html語法");
define("_PREVIEW","預覽");

define("_GO","確定");
define("_NESTED","巢狀");
define("_NOCOMMENTS","無回應評註");
define("_FLAT","全部展開");
define("_THREADED","樹狀顯示");
define("_OLDESTFIRST","舊的在前");
define("_NEWESTFIRST","新的在前");
define("_MORE","尚有...");
define("_MULTIPAGE","文章如果要分頁，請在分頁處加入 <font color=red>[pagebreak]</font> (包含括號).");
define("_IFNOTRELOAD","假如系統沒有自動前往，請按<a href=%s>這裡</a>繼續");
define("_WARNINSTALL2","警示訊息 ! %s 還存在於您的主機上. <br />為了安全起見請把這檔案及目錄刪除.");
define("_WARNINWRITEABLE","警示訊息 ! %s 屬性為可讀寫. <br />為了安全起見請將它的可寫屬性改為不可寫入。.<br /> Unix (444), Win32 (唯讀)");
define('_WARNPHPENV','警示訊息 ! php.ini 參數 "%s" 設置為"%s". %s');
define('_WARNSECURITY','(這可能會導致安全性問題)');

//%%%%%%	File Name themeuserpost.php 	%%%%%
define("_PROFILE","個人資料");
define("_POSTEDBY","發表者");
define("_VISITWEBSITE","拜訪網站");
define("_SENDPMTO","傳送私人訊息給 %s");
define("_SENDEMAILTO","傳送 Email 給 %s");
define("_ADD","加入");
define("_REPLY","回應");
define("_DATE","發表日");   // Posted date

//%%%%%%	File Name admin_functions.php 	%%%%%
define("_MAIN","主頁");
define("_MANUAL","使用手冊");
define("_INFO","資訊");
define("_CPHOME","管理控制台首頁");
define("_YOURHOME","網站首頁");

//%%%%%%	File Name misc.php (who's-online popup)	%%%%%
define("_WHOSONLINE","線上人數");
define('_GUESTS', '訪客');
define('_MEMBERS', '會員');
define("_ONLINEPHRASE","線上目前共<b>%s</b>人<br>");
define("_ONLINEPHRASEX","<b>%s</b>人在瀏覽<b>%s</b>");
define("_CLOSE","關閉視窗");  // Close window

//%%%%%%	File Name module.textsanitizer.php 	%%%%%
define("_QUOTEC","引文:");

//%%%%%%	File Name admin.php 	%%%%%
define("_NOPERM","抱歉!您的權限不夠無法進入本區.");

//%%%%%		Common Phrases		%%%%%
define("_NO","否");
define("_YES","是");
define("_EDIT","編輯");
define("_DELETE","刪除");
define("_VIEW","View");
define("_SUBMIT","確定送出");
define("_MODULENOEXIST","選擇的模組不存在!");
define("_ALIGN","位置");
define("_LEFT","靠左");
define("_CENTER","置中");
define("_RIGHT","靠右");
define("_FORM_ENTER", "請輸入 %s");
// %s represents file name
define("_MUSTWABLE","檔案 %s 必須設為可讀寫!");
// Module info
define('_PREFERENCES', '設定');
define("_VERSION", "版本");
define("_DESCRIPTION", "描述");
define("_ERRORS", "錯誤");
define("_NONE", "無");
define('_ON','開');
define('_POSTON','於');
define('_READS','人氣');
define('_WELCOMETO','歡迎來到 %s');
define('_SEARCH','搜尋');
define('_ALL', '全部');
define('_TITLE', '主題');
define('_OPTIONS', '附加項目');
define('_QUOTE', '引文');
define('_LIST', '列出');
define('_LOGIN','使用者登入');
define('_USERNAME','帳號: ');
define('_PASSWORD','密碼: ');
define("_SELECT","選擇");
define("_IMAGE","圖片");
define("_SEND","傳送");
define("_CANCEL","取消");
define("_ASCENDING","升冪排列");
define("_DESCENDING","降冪排列");
define('_BACK', '回上頁');
define('_NOTITLE', '無標題');
define('_RETURN_TOP', '回頁面頂端');

/* Image manager */
define('_IMGMANAGER','圖檔管理員');
define('_NUMIMAGES', '%s 圖檔');
define('_ADDIMAGE','新增圖檔');
define('_IMAGENAME','名稱:');
define('_IMGMAXSIZE','最大檔案大小 (kb):');
define('_IMGMAXWIDTH','最大寬度 (pixels):');
define('_IMGMAXHEIGHT','最大高度(pixels):');
define('_IMAGECAT','目錄:');
define('_IMAGEFILE','圖檔:');
define('_IMGWEIGHT','圖檔排序:');
define('_IMGDISPLAY','顯示此圖檔?');
define('_IMAGEMIME','MIME格式:');
define('_FAILFETCHIMG', '%s 無法上載');
define('_FAILSAVEIMG', '%s 無法儲存');
define('_NOCACHE', '不使用 Cache');
define('_CLONE', '複製');

//%%%%%	File Name class/xoopsform/formmatchoption.php 	%%%%%
define("_STARTSWITH", "開始於");
define("_ENDSWITH", "結束於");
define("_MATCHES", "符合");
define("_CONTAINS", "相容");

//%%%%%%	File Name commentform.php 	%%%%%
define("_REGISTER","會員");

//%%%%%%	File Name xoopscodes.php 	%%%%%
define("_SIZE","大小");  // font size
define("_FONT","字型");  // font family
define("_COLOR","顏色");  // font color
define("_EXAMPLE","範例");
define("_ENTERURL","輸入網址:");
define("_ENTERWEBTITLE","輸入網站名稱:");
define("_ENTERIMGURL","輸入圖檔網址.");
define("_ENTERIMGPOS","輸入圖檔放置位置");
define("_IMGPOSRORL","'R' 或 'r' 表示右邊 'L' 或 'l' 表示左邊，或留空白。");
define("_ERRORIMGPOS","錯誤！請輸入圖檔放置位置");
define("_ENTEREMAIL","請輸入Email。");
define("_ENTERCODE","請輸入你要增加的代碼");
define("_ENTERQUOTE","請輸入內文");
define("_ENTERTEXTBOX","請在文字框裡輸入文字");
define("_ALLOWEDCHAR","字元長度限制:");
define("_CURRCHAR","文章的字元長度: ");
define("_PLZCOMPLETE","請確定主旨及內容是否填寫");
define("_MESSAGETOOLONG","您的文章太長，請縮小長度。");

//%%%%%		TIME FORMAT SETTINGS   %%%%%
define('_SECOND', '1 秒');
define('_SECONDS', '%s 秒');
define('_MINUTE', '1 分');
define('_MINUTES', '%s 分');
define('_HOUR', '1 小時');
define('_HOURS', '%s 小時');
define('_DAY', '1 天');
define('_DAYS', '%s 天');
define('_WEEK', '1 週');
define('_MONTH', '1 月');

define('_HELP', "幫助說明");

?>