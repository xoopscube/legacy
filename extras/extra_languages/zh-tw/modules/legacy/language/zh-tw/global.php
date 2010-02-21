<?php
// $Id$

define('_TOKEN_ERROR', 'Alert ! This prevent you from instantiating a malformed request or post. Please, submit again to confirm!');
define('_SYSTEM_MODULE_ERROR', 'Following Modules are not installed.');
define('_INSTALL', '安裝');
define('_UNINSTALL', '移除');
define('_SYS_MODULE_UNINSTALLED', '必要（未安裝）');
define('_SYS_MODULE_DISABLED', '必要（停用）');
define('_SYS_RECOMMENDED_MODULES', '推薦的模組');
define('_SYS_OPTION_MODULES', '其他模組');
define('_UNINSTALL_CONFIRM', '您確定要移除系統模組？');

//%%%%%%	File Name mainfile.php 	%%%%%
define('_PLEASEWAIT', '請稍候');
define('_FETCHING', '載入中...');
define('_TAKINGBACK', '帶您回到先前的位置....');
define('_LOGOUT', '登出');
define('_SUBJECT', '主旨');
define('_MESSAGEICON', '訊息圖');
define('_COMMENTS', '評論');
define('_POSTANON', '匿名張貼');
define('_DISABLESMILEY', '取消表情圖示');
define('_DISABLEHTML', '取消 html 語法');
define('_PREVIEW', '預覽觀看');

define('_GO', '衝!');
define('_NESTED', '巢狀');
define('_NOCOMMENTS', '沒有評論');
define('_FLAT', '平面展開');
define('_THREADED', '討論串');
define('_OLDESTFIRST', '最舊的先');
define('_NEWESTFIRST', '最新的先');
define('_MORE', '詳情...');
define('_MULTIPAGE', '欲讓您的文章跨頁, 請在文章中插入 <font color=red>[pagebreak]</font> (包含中括號).');
define('_IFNOTRELOAD', '若這個頁面沒有自動重新載入, 請點按 <a href=\'%s\'>這裡</a>');
define('_WARNINSTALL2', '警告: 目錄 %s 仍存在於您的伺服器中. <br />.請立即移除它,以策安全.');
define('_WARNINWRITEABLE', '警告: 檔案 %s 在您的伺服器中可以被寫入. <br />請立即修改它的權限,以策安全.<br /> Unix 平台 (權限 444), Win32 平台 (唯讀)');
define('_WARNPHPENV', 'WARNING: php.ini parameter "%s" is set to "%s". %s');
define('_WARNSECURITY', '(It may cause a security problem)');

//%%%%%%	File Name themeuserpost.php 	%%%%%
define('_PROFILE', '設定資料');
define('_POSTEDBY', '發表人');
define('_VISITWEBSITE', '訪問站台');
define('_SENDPMTO', '傳送私人訊息給 %s');
define('_SENDEMAILTO', '傳送電子郵件給 %s');
define('_ADD', '新增');
define('_REPLY', '回覆');
define('_DATE', '日期');   // Posted date

//%%%%%%	File Name admin_functions.php 	%%%%%
define('_MAIN', '主要的');
define('_MANUAL', '手冊');
define('_INFO', '資訊');
define('_CPHOME', '控制設定頁面');
define('_YOURHOME', '首頁');

//%%%%%%	File Name misc.php (who's-online popup)	%%%%%
define('_WHOSONLINE', '誰在線上');
define('_GUESTS', '訪客');
define('_MEMBERS', '成員');
define('_ONLINEPHRASE', '<b>%s</b> 位使用者在線上');
define('_ONLINEPHRASEX', '<b>%s</b> 位使用者正在瀏覽 <b>%s</b>');
define('_CLOSE', '關閉視窗');  // Close window

//%%%%%%	File Name module.textsanitizer.php 	%%%%%
define('_QUOTEC', '引言:');

//%%%%%%	File Name admin.php 	%%%%%
define('_NOPERM', '抱歉, 您沒有取用這個區域的權限.');

//%%%%%		Common Phrases		%%%%%
define('_NO', '否');
define('_YES', '是');
define('_EDIT', '編輯');
define('_DELETE', '刪除');
define('_VIEW', 'View');
define('_SUBMIT', '送出');
define('_MODULENOEXIST', '選用的模組並不存在!');
define('_ALIGN', '排列對準');
define('_LEFT', '靠左');
define('_CENTER', '置中');
define('_RIGHT', '靠右');
define('_FORM_ENTER', '請進入 %s');
// %s represents file name
define('_MUSTWABLE', '檔案 %s 必須是伺服器可以寫入!');
// Module info
define('_PREFERENCES', '偏好設定');
define('_VERSION', '版本');
define('_DESCRIPTION', '說明');
define('_ERRORS', '錯誤');
define('_NONE', '無');
define('_ON', '於');
define('_READS', '人讀取');
define('_WELCOMETO', '歡迎來到 %s');
define('_SEARCH', '搜尋');
define('_ALL', '所有的');
define('_TITLE', '標題');
define('_OPTIONS', '選項');
define('_QUOTE', '引文');
define('_LIST', '列表');
define('_LOGIN', '使用者登入');
define('_USERNAME', '使用者名稱: ');
define('_PASSWORD', '密碼: ');
define('_SELECT', '選擇');
define('_IMAGE', '圖像');
define('_SEND', '送出');
define('_CANCEL', '取消');
define('_ASCENDING', '升冪排序');
define('_DESCENDING', '降冪排序');
define('_BACK', '返回');
define('_NOTITLE', '沒有標題');
define('_RETURN_TOP', 'returns to the top');

/* Image manager */
define('_IMGMANAGER', '圖像管理');
define('_NUMIMAGES', '%s 個圖像');
define('_ADDIMAGE', '新增圖像檔');
define('_IMAGENAME', '名稱:');
define('_IMGMAXSIZE', '最大可允許的檔案大小 (bytes):');
define('_IMGMAXWIDTH', '最大可允許的寬度 (pixels):');
define('_IMGMAXHEIGHT', '最大可允許的高度 (pixels):');
define('_IMAGECAT', '種類:');
define('_IMAGEFILE', '圖像檔:');
define('_IMGWEIGHT', '圖像管理中的顯示順序:');
define('_IMGDISPLAY', '顯示這個圖像嗎?');
define('_IMAGEMIME', 'MIME 形態:');
define('_FAILFETCHIMG', '無法取得上傳的檔案 %s');
define('_FAILSAVEIMG', '儲存圖像 %s 至資料庫中失敗');
define('_NOCACHE', '不使用快取');
define('_CLONE', '複本');

//%%%%%	File Name class/xoopsform/formmatchoption.php 	%%%%%
define('_STARTSWITH', '開始於');
define('_ENDSWITH', '結束於');
define('_MATCHES', '比對符合');
define('_CONTAINS', '內含');

//%%%%%%	File Name commentform.php 	%%%%%
define('_REGISTER', '註冊');

//%%%%%%	File Name xoopscodes.php 	%%%%%
define('_SIZE', '大小');  // font size
define('_FONT', '字型');  // font family
define('_COLOR', '顏色');  // font color
define('_EXAMPLE', '樣本');
define('_ENTERURL', '輸入您想要加入的連結位址:');
define('_ENTERWEBTITLE', '輸入網站的標題:');
define('_ENTERIMGURL', '輸入您想要加入的圖像URL位址.');
define('_ENTERIMGPOS', '現在, 輸入這個圖像的位置.');
define('_IMGPOSRORL', '\'R\' 或 \'r\' 代表右邊, \'L\' 或 \'l\' 代表右邊, 或者讓它空白.');
define('_ERRORIMGPOS', '錯誤! 輸入這個圖像的位置.');
define('_ENTEREMAIL', '輸入您想要加入的電子郵件位址.');
define('_ENTERCODE', '輸入您想要加入的程式碼.');
define('_ENTERQUOTE', '輸入您想要引用的文字.');
define('_ENTERTEXTBOX', '請在文字框中輸入文字.');
define('_ALLOWEDCHAR', '最大可允許的字元長度: ');
define('_CURRCHAR', '目前字元長度: ');
define('_PLZCOMPLETE', '請填完主旨及訊息欄位.');
define('_MESSAGETOOLONG', '您的訊息太長了.');

//%%%%%		TIME FORMAT SETTINGS   %%%%%
define('_SECOND', '1 秒');
define('_SECONDS', '%s 秒');
define('_MINUTE', '1 分鐘');
define('_MINUTES', '%s 分鐘');
define('_HOUR', '1 小時');
define('_HOURS', '%s 小時');
define('_DAY', '1 日');
define('_DAYS', '%s 日');
define('_WEEK', '1 周');
define('_MONTH', '1 個月');

define('_HELP', '說明');

?>