<?php
// $Id: user.php,v 1.3 2008/10/13 00:45:06 minahito Exp $
//%%%%%%		File Name user.php 		%%%%%
define('_US_NOTREGISTERED','尚未註冊嗎? 請按<a href=register.php>這裡</a>註冊.');
define('_US_LOSTPASSWORD','忘記密碼?');
define('_US_NOPROBLEM','沒關係.請填入您註冊的email來索取.');
define('_US_YOUREMAIL','您的Email: ');
define('_US_SENDPASSWORD','送出確認碼 / 密碼');
define('_US_LOGGEDOUT','您已經登出');
define('_US_THANKYOUFORVISIT','謝謝您光臨本站!');
define('_US_INCORRECTLOGIN','登入錯誤!');
define('_US_LOGGINGU','%s 登入程序完成...歡迎光臨本站.');

// 2001-11-17 ADD
define('_US_NOACTTPADM','這個帳號尚未啟動，或是已被撤銷.<br> 請詢問網站管理員.');
define('_US_ACTKEYNOT','啟動碼錯誤!');
define('_US_ACONTACT','您選擇的帳號已經啟動!');
define('_US_ACTLOGIN','您的帳號已經啟動，請使用您註冊時自訂的帳號與密碼登入本站.');
define('_US_NOPERMISS','抱歉，您的權限不足，不能執行此一動作!');
define('_US_SURETODEL','您確定要移除您的帳號?');
define('_US_REMOVEINFO','這個動作將會移除您在本站的所有個人資料.');
define('_US_BEENDELED','您的帳號已刪除，有緣再見.');
//

//%%%%%%		File Name register.php 		%%%%%
define('_US_USERREG','註冊表單');
define('_US_NICKNAME','帳號');
define('_US_EMAIL','Email');
define('_US_ALLOWVIEWEMAIL','公開您的Email');
define('_US_WEBSITE','網站');
define('_US_TIMEZONE','時區');
define('_US_AVATAR','大頭照');
define('_US_VERIFYPASS','確認密碼');
define('_US_SUBMIT','確定送出');
define('_US_USERNAME','帳號');
define('_US_FINISH','完成');
define('_US_REGISTERNG','無法註冊新帳號.');
define('_US_MAILOK','接受網站管理者及<br />有相關權限的管理員寄送Email通知最新消息嗎?');
define('_US_DISCLAIMER','註冊前請詳閱本站使用規定，<BR>如接受並願意註冊，請勾選接受規則才能註冊<BR>如不接受請勿註冊');
define('_US_IAGREE','我接受以上規則');
define('_US_UNEEDAGREE', '抱歉, 你必須接受此規定才能註冊.');
define('_US_NOREGISTER','抱歉, 目前我們不接受新會員');


// %s is username. This is a subject for email
define('_US_USERKEYFOR','%s 的啟動帳號');

define('_US_YOURREGISTERED','註冊完成，但是帳號尚未啟動，<br>系統將會寄送一封包含啟動碼的mail給您，<br>請依照該封Email的指示回站啟動您的帳號. ');
define('_US_YOURREGMAILNG','您好，雖然您已經完成註冊但是因為系統的mail發生故障，<br>所以無法寄啟動碼給您，請與本站管理員聯絡.');
define('_US_YOURREGISTERED2','您好，雖然您已經完成註冊但是因為必須等待管理人員啟動你的帳號，<br>當啟動後你將會收到Email確認，敬請耐心等候.');

// %s is your site name
define('_US_NEWUSERREGAT','新會員註冊於 %s');
// %s is a username
define('_US_HASJUSTREG','%s 剛剛註冊了!');

define('_US_INVALIDMAIL','ERROR: Email 錯誤');
define('_US_EMAILNOSPACES','ERROR: Email 位址不能有空白.');
define('_US_INVALIDNICKNAME','ERROR: 錯誤的帳號');
define('_US_NICKNAMETOOLONG','帳號太長，不能超過%s個字元.');
define('_US_NICKNAMETOOSHORT','帳號太短，不能短於%s個字元.');
define('_US_NAMERESERVED','ERROR: 不能用系統保留字當帳號.');
define('_US_NICKNAMENOSPACES','帳號請勿包含空白字元.');
define('_US_NICKNAMETAKEN','ERROR: 這個帳號已有人使用.');
define('_US_EMAILTAKEN','ERROR: 這個 Email 已經有人註冊過.');
define('_US_ENTERPWD','ERROR: 您必須提供密碼.');
define('_US_SORRYNOTFOUND','抱歉，找不到所填資料一致的使用者帳號.');




// %s is your site name
define('_US_NEWPWDREQ','有人於 %s 索取新密碼');
define('_US_YOURACCOUNT', '您有在 %s 申請帳號');

define('_US_MAILPWDNG','Email密碼 : 無法更新您的資料，請與網站管理員聯繫');

// %s is a username
define('_US_PWDMAILED','給 %s 的新密碼已經由 Email 送出.');
define('_US_CONFMAIL','給 %s 的確認函已經由 Email 送出.');
define('_US_ACTVMAILNG', '寄送給 %s email 失敗');
define('_US_ACTVMAILOK', '已寄送確認 email給 %s.');

//%%%%%%		File Name userinfo.php 		%%%%%
define('_US_SELECTNG','沒有選擇使用者，請回上頁再試.');
define('_US_PM','私人傳訊');
define('_US_ICQ','ICQ 號碼');
define('_US_AIM','AIM 帳號');
define('_US_YIM','雅虎 YIM 傳訊帳號');
define('_US_MSNM','Windows Live ID 帳號');
define('_US_LOCATION','來自');
define('_US_OCCUPATION','職業');
define('_US_INTEREST','興趣');
define('_US_SIGNATURE','簽名');
define('_US_EXTRAINFO','額外的資訊');
define('_US_EDITPROFILE','編輯個人資料');
define('_US_LOGOUT','登出');
define('_US_INBOX','私人傳訊收件匣');
define('_US_MEMBERSINCE','註冊日');
define('_US_RANK','等級');
define('_US_POSTS','回應/發表文章數');
define('_US_LASTLOGIN','最後登入時間');
define('_US_ALLABOUT','所有關於 %s');
define('_US_STATISTICS','統計資料');
define('_US_MYINFO','我的資訊');
define('_US_BASICINFO','基本資料');
define('_US_MOREABOUT','其他資料');
define('_US_SHOWALL','顯示全部');

//%%%%%%		File Name edituser.php 		%%%%%
define('_US_PROFILE','個人資料');
define('_US_REALNAME','真實姓名');
define('_US_SHOWSIG','發文時加入簽名');
define('_US_CDISPLAYMODE','回應評論的顯示模式');
define('_US_CSORTORDER','回應評論排列順序');
define('_US_PASSWORD','密碼');
define('_US_TYPEPASSTWICE','(輸入2次新密碼來變更)');
define('_US_SAVECHANGES','儲存變更');
define('_US_NOEDITRIGHT',"抱歉，您沒有權限可以編輯使用者資料.");
define('_US_PASSNOTSAME','密碼和確認密碼不相同，他們必須是一致的.');
define('_US_PWDTOOSHORT','抱歉，系統預設密碼至少必須是 <b>%s</b> 個字元.');
define('_US_PROFUPDATED','個人資料已更新!');
define('_US_USECOOKIE','在cookie中儲存您的帳號資料(不包含密碼)1年');
define('_US_NO','否');
define('_US_DELACCOUNT','刪除帳號');
define('_US_MYAVATAR', '我的個人頭像');
define('_US_UPLOADMYAVATAR', '上傳我的個人頭像');
define('_US_MAXPIXEL','最大寬度/高度(pixel)');
define('_US_MAXIMGSZ','最大檔案大小 (Bytes)');
define('_US_SELFILE','選擇檔案');
define('_US_OLDDELETED','舊的個人頭像已經刪除!');
define('_US_CHOOSEAVT', '選一個你要的個人頭像吧');

define('_US_PRESSLOGIN', '請按以下按鈕登入');

define('_US_ADMINNO', '不能刪除這個網站管理員群組的使用者');
define('_US_GROUPS', '使用者所屬群組');
?>