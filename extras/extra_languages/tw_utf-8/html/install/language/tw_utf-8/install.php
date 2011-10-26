<?php
// $Id: install.php,v 1.1 2008/03/09 02:26:08 minahito Exp $
define("_INSTALL_L0","歡迎來到XOOPS Cube V2 安裝導引，請依照指示安裝XOOPS Cube。");
define("_INSTALL_L70","請先確定mainfile.php 這個檔案的屬性是可讀寫的，以便讓程式把必要的資訊寫入(例如！在 UNIX/LINUX 主機上 chmod 666 mainfile.php ，於Windows 系統上則把唯讀屬性去除). 當您做好這個動作時請按重新整理按鈕。");
//define("_INSTALL_L71","按下按鈕並開始安裝。");
define("_INSTALL_L1","開啟mainfile.php 並使用您的文字編輯器軟體尋找下列的第31行程式碼：");
define("_INSTALL_L2","現在，改變這一行成為：");
define("_INSTALL_L3","下一步，第35行把 %s 改為 %s");
define("_INSTALL_L4","最後，按下儲存按鈕，並嘗試一次。");
define("_INSTALL_L5","警告！");
define("_INSTALL_L6","程式偵測到您的XOOPS_ROOT_PATH 與mainfile.php的設定並不相同，請修正。");
define("_INSTALL_L7","您的設定為：");
define("_INSTALL_L8","程式偵測到為：");
define("_INSTALL_L9","( 使用微軟MS平台，將有可能會出現這個錯誤訊息，有時即使您設定正確也還是會出現，請忽略它並按下一步繼續安裝。)");
define("_INSTALL_L10","請確定以下訊息都正確並請按下一步繼續安裝。");
define("_INSTALL_L11","您要安裝XOOPS Cube 的絕對路徑：");
define("_INSTALL_L12","您要安裝XOOPS Cube 的網址：");
define("_INSTALL_L13","如果以上資料都正確，請按下面按鈕繼續。<br />（此步驟尚未連接資料庫及建立資料表）");
define("_INSTALL_L14","下一步");
define("_INSTALL_L15","請開啟 mainfile.php 並輸入您的資料庫相關資料");
define("_INSTALL_L16","%s 是您的SQL server的主機位置");
define("_INSTALL_L17","%s 是您SQL Server的使用者名稱");
define("_INSTALL_L18","%s 是您SQL Server的使用者密碼");
define("_INSTALL_L19","%s 是您要安裝XOOPS Cube所建立的新資料庫名稱");
define("_INSTALL_L20","%s prefix是在資料表前會加上一個識別名稱，尤其是您在同一資料庫安裝多個程式時，請注意不要跟其他程式，如 *xoopscube等重複，以免發生錯誤");
define("_INSTALL_L21","以下的資料庫並未在您的MySQL Server上建立");
define("_INSTALL_L22","您確定要建立嗎？");
define("_INSTALL_L23","是");
define("_INSTALL_L24","否");
define("_INSTALL_L25","安裝程式檢查您的mainfile.php裡的設定如下，如果不正確請作修改。");
define("_INSTALL_L26","資料庫設定");
define("_INSTALL_L51","資料庫");
define("_INSTALL_L66","選擇要使用的資料庫");
define("_INSTALL_L27","資料庫主機位置");
define("_INSTALL_L67","資料庫主機位置名稱如果不確定，請使用'localhost'試試看。");
define("_INSTALL_L28","資料庫使用者名稱");
define("_INSTALL_L65","資料庫使用者名稱可以讓您進入及建立資料庫名稱");
define("_INSTALL_L29","資料庫名稱");
define("_INSTALL_L64","如果發現沒有這個資料庫名稱將自行建立一個新的資料庫名稱");
define("_INSTALL_L52","資料庫使用者密碼");
define("_INSTALL_L68","資料庫使用者密碼是針對您的名稱所需要的進入密碼。");
define("_INSTALL_L30","Prefix=資料表前置名稱");
define("_INSTALL_L63","Prefix=資料表前置名稱可以讓不同程式使用的資料表不會互相衝突。可自由決定英文或數字，如果您不確定要取什麼就用預設值");
define("_INSTALL_L54","使用MySQL pconnect的連接方式嗎?");
define("_INSTALL_L69","預設值 '否'。假如您無法確定的話，請選擇 '否' .");
define("_INSTALL_L55","XOOPS Cube 的絕對路徑");
define("_INSTALL_L59","絕對路徑最後不要加斜線");
define("_INSTALL_L56","XOOPS Cube 的網址(URL)");
define("_INSTALL_L58","網址最後不要加斜線");

define("_INSTALL_L31","無法建立資料庫，請通知系統管理員詢問詳細系統資訊。");
define("_INSTALL_L32","資料庫建立完成！");
define("_INSTALL_L33","請按<a href='../index.php'>這裡</font></a>到網站首頁");
define("_INSTALL_L35","如果有任何錯誤，請聯繫 <a href='http://www.xoopscube.org/'>XOOPS Cube開發站</a>或<a href=\"http://www.xoopscube.tw/\">XOOPS Cube中文支援站</a>");
define("_INSTALL_L36","請填入以下的網站管理員資料以便建立資料表");
define("_INSTALL_L37","網站管理員帳號：");
define("_INSTALL_L38","網站管理員的Email：");
define("_INSTALL_L39","網站管理員密碼：");
define("_INSTALL_L74","再確認一次網站管理員密碼：");
define("_INSTALL_L40","建立資料表");
define("_INSTALL_L41","請回上頁再確認一下資料沒有錯誤後再試");
define("_INSTALL_L42","回上頁");
define("_INSTALL_L57","請輸入 %s");

// %s is database name
define("_INSTALL_L43","資料庫 %s 建立完成！");

// %s is table name
define("_INSTALL_L44","無法建立 %s");
define("_INSTALL_L45","資料表 %s 建立完成");

define("_INSTALL_L46","為確定預設的模組能運作順暢，請修改以下檔案為可讀寫屬性：");
define("_INSTALL_L47","下一步");

define("_INSTALL_L53","請確認以下資料：");

define("_INSTALL_L60","無法開啟 mainfile.php檔案，請確定此檔為可讀寫屬性，並重試一次。");
define("_INSTALL_L61","無法寫入 mainfile.php檔案，請確定此檔為可讀寫屬性。並詢問系統管理員。");
define("_INSTALL_L62","設定項目儲存完成，按下按鈕並繼續安裝。");
define("_INSTALL_L72","以下目錄必須是可寫入狀態(WIN系統請移除唯讀屬性)(UNIX/LINUX 請 Chmod 666 或是 777 )");
define("_INSTALL_L73","無效的 Email");

// add by haruki
define("_INSTALL_L80","XOOPS Cube 介紹");
define("_INSTALL_L81","確認檔案權限範圍");
define("_INSTALL_L82","確認檔案及目錄權限範圍..");
define("_INSTALL_L83","%s 檔屬性為唯讀");
define("_INSTALL_L84","%s 檔屬性為可讀寫");
define("_INSTALL_L85","%s 目錄屬性為唯讀");
define("_INSTALL_L86","%s 目錄屬性為可讀寫");
define("_INSTALL_L87","無錯誤報告");
define("_INSTALL_L89","一般設定");
define("_INSTALL_L90","一般設定");
define("_INSTALL_L91","確認");
define("_INSTALL_L92","儲存設定");
define("_INSTALL_L93","編輯設定");
define("_INSTALL_L88","儲存設定資料..");
define("_INSTALL_L94","確認 & URL");
define("_INSTALL_L127","確認檔案路徑及URL設定..");
define("_INSTALL_L95","無法偵測到XOOPS Cube目錄實體路徑");
define("_INSTALL_L96","程式偵測到您的實體路徑(%s)與您的的設定並不相同，請修正。");
define("_INSTALL_L97","<b>實體路徑</b>正確.");

define("_INSTALL_L99","<b>實體路徑</b>必須是個目錄");
define("_INSTALL_L100","<b>網址</b>必須是個有效URL.");
define("_INSTALL_L101","<b>網址</b>偵測為無效URL.");
define("_INSTALL_L102","確定所有設定");
define("_INSTALL_L103","回到設定最初畫面");
define("_INSTALL_L104","確認資料庫");
define("_INSTALL_L105","建立資料庫");
define("_INSTALL_L106","無法聯結到資料庫");
define("_INSTALL_L107","請確認資料庫的設定是否正確.");
define("_INSTALL_L108","已聯結到資料庫");
define("_INSTALL_L109","資料庫 %s 目前不存在.");
define("_INSTALL_L110","已跟資料庫 %s 連結上.");
define("_INSTALL_L111","資料庫連結完成.<br />按下按鍵建立資料表");
define("_INSTALL_L112","管理者設定");
define("_INSTALL_L113","資料表 %s 刪除.");
define("_INSTALL_L114","資料表建立失敗.");
define("_INSTALL_L115","資料表已建立.");
define("_INSTALL_L116","輸入資料");
define("_INSTALL_L117","完成");

define("_INSTALL_L118","資料表 %s 資料表建立失敗.");
define("_INSTALL_L119","%d 資料輸入到 %s.");
define("_INSTALL_L120","%d 資料輸入到 %s 失敗.");

define("_INSTALL_L121","%s 內容輸入到 %s.");
define("_INSTALL_L122","資料輸入到 %s 失敗.");

define("_INSTALL_L123","%s 檔儲存在/cache/ 目錄.");
define("_INSTALL_L124","%s 檔儲存在/cache/ 目錄失敗.");

define("_INSTALL_L125","%s 檔被 %s 覆寫.");
define("_INSTALL_L126","無法覆寫 %s.");

define("_INSTALL_L130","安裝精靈偵測到您有XOOPS 1.3.x 或 XOOPS2 的相關資料表，<br />現在讓XOOPS Cube安裝升級精靈讓您升級到XOOPS Cube.");
define("_INSTALL_L131","XOOPS Cube相關資料表已存在。");
define("_INSTALL_L132","升級資料表");
define("_INSTALL_L133","%s 資料表升級.");
define("_INSTALL_L134","%s 資料表升級失敗.");
define("_INSTALL_L135","資料表升級失敗.");
define("_INSTALL_L136","資料表升級.");
define("_INSTALL_L137","升級模組");
define("_INSTALL_L138","升級評論");
define("_INSTALL_L139","升級大頭照");
define("_INSTALL_L140","升級表情圖");
define("_INSTALL_L141","安裝升級精靈將把您所有模組升級到XOOPS Cube，<br />請確定您上傳 XOOPS Cube所有檔案到網站中.<br />剩下的就交給安裝升級精靈.");
define("_INSTALL_L142","更新模組中..");
define("_INSTALL_L143","安裝升級精靈將您舊有的 XOOPS 1.3.x 或 XOOPS2 轉換成XOOPS Cube。");
define("_INSTALL_L144","升級設定");
define("_INSTALL_L145","評論 (ID: %s) 輸入到資料庫中.");
define("_INSTALL_L146","無法將評論 (ID: %s) 輸入到資料庫中。");
define("_INSTALL_L147","升級評論...");
define("_INSTALL_L148","升級完成。");
define("_INSTALL_L149","安裝升級精靈將您XOOPS 1.3.x新聞評論升級到XOOPS Cube.<br />請耐心等候。");
define("_INSTALL_L150","安裝升級精靈將您XOOPS 1.3.x表情圖及評分系統圖案升級到XOOPS Cube，<br />請耐心等候。");
define("_INSTALL_L151","安裝升級精靈將您XOOPS 1.3.x大頭照圖片升級到XOOPS Cube，<br />請耐心等候。");
define("_INSTALL_L155","升級表情圖及評分系統圖案...");
define("_INSTALL_L156","升級大頭照圖片...");
define("_INSTALL_L157","為各類群組選擇預設值");
define("_INSTALL_L158","1.3.x 或 XOOPS2 舊有群組");
define("_INSTALL_L159","網站管理員");
define("_INSTALL_L160","註冊會員");
define("_INSTALL_L161","訪客");
define("_INSTALL_L162","您必須為各群組設定權限.");
define("_INSTALL_L163","%s資料表刪除.");
define("_INSTALL_L164","%s資料表刪除失敗.");
define("_INSTALL_L165","站務維修中，請稍後再訪問.");

// %s is filename
define("_INSTALL_L152","無法開啟%s.");
define("_INSTALL_L153","無法更新 %s.");
define("_INSTALL_L154","%s 更新完成.");

define('_INSTALL_L128', '選擇安裝過程所使用的語言，繁中BIG5碼請選擇tw_big5，如果您使用繁中UTF-8編碼，請從extras/extra_languages/tw_utf-8/htm裡複製所有檔案後選擇tw_utf-8。');
define('_INSTALL_L200', '重新載入');
define('_INSTALL_L210', '進入第二階段安裝設置');

define('_INSTALL_CHARSET','big5');

define('_INSTALL_LANG_XOOPS_SALT', "編碼");
define('_INSTALL_LANG_XOOPS_SALT_DESC', "這個是為了補充作用來生成安全編碼和標記使用， 您不需改變預設值。");

define('_INSTALL_HEADER_MESSAGE','請依照螢幕指示進行安裝程序。');
?>
