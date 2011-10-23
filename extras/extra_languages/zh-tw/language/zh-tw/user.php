<?php
// $Id$
//%%%%%%		File Name user.php 		%%%%%
define('_US_NOTREGISTERED', '還沒註冊嗎? 請點按 <a href=register.php>這裡</a>.');
define('_US_LOSTPASSWORD', '忘記您的密碼了嗎？');
define('_US_NOPROBLEM', '沒問題，只要輸入您以前在本站註冊時登錄的電子郵件即可.');
define('_US_YOUREMAIL', '您的電子郵件: ');
define('_US_SENDPASSWORD', '傳送密碼給您');
define('_US_LOGGEDOUT', '目前您已登出');
define('_US_THANKYOUFORVISIT', '感謝您參觀我們的網站!');
define('_US_INCORRECTLOGIN', '登入錯誤!');
define('_US_LOGGINGU', '感謝您登入, %s.');

// 2001-11-17 ADD
define('_US_NOACTTPADM', '選用的使用者已被取消或尚未自行啟用.<br />相關細節請連繫管理員.');
define('_US_ACTKEYNOT', '啟動碼錯誤!');
define('_US_ACONTACT', '選用的帳號已經啟用過了!');
define('_US_ACTLOGIN', '您的帳號已經啟用. 請用您註冊的密碼來登入.');
define('_US_NOPERMISS', '抱歉, 您沒有權限可以執行這個操作!');
define('_US_SURETODEL', '您確定想要刪除您的帳號嗎?');
define('_US_REMOVEINFO', '此舉將會由我們的資料庫中移除您的所有資料.');
define('_US_BEENDELED', '您的帳號已經刪除.');
//

//%%%%%%		File Name register.php 		%%%%%
define('_US_USERREG', '註冊表單');
define('_US_NICKNAME', '帳號');
define('_US_EMAIL', '電子郵件');
define('_US_ALLOWVIEWEMAIL', '公開您的電子郵件');
define('_US_WEBSITE', '網站');
define('_US_TIMEZONE', '時區');
define('_US_AVATAR', '大頭貼');
define('_US_VERIFYPASS', '確認密碼');
define('_US_SUBMIT', '確定送出');
define('_US_USERNAME', '帳號');
define('_US_FINISH', '完成');
define('_US_REGISTERNG', '無法註冊新的帳號.');
define('_US_MAILOK', '接受網站管理者及<br />有相關權限的管理員寄送Email通知最新消息嗎?');
define('_US_DISCLAIMER', '註冊前請詳閱本站使用規定，<BR>如接受並願意註冊，請勾選接受規則才能註冊<BR>如不接受請勿註冊');
define('_US_IAGREE', '我接受以上規則');
define('_US_UNEEDAGREE', '抱歉, 你必須接受此規定才能註冊.');
define('_US_NOREGISTER', '抱歉, 目前我們不接受新會員');


// %s is username. This is a subject for email
define('_US_USERKEYFOR', '%s 的啟動帳號');

define('_US_YOURREGISTERED', '註冊完成，但是帳號尚未啟動，<br>系統將會寄送一封包含啟動碼的mail給您，<br>請依照該封Email的指示回站啟動您的帳號. ');
define('_US_YOURREGMAILNG', '您好，雖然您已經完成註冊但是因為系統的mail發生故障，<br>所以無法寄啟動碼給您，請與本站管理員聯絡.');
define('_US_YOURREGISTERED2', '您好，雖然您已經完成註冊但是因為必須等待管理人員啟動你的帳號，<br>當啟動後你將會收到Email確認，敬請耐心等候.');

// %s is your site name
define('_US_NEWUSERREGAT', '新會員註冊於 %s');
// %s is a username
define('_US_HASJUSTREG', '%s 剛剛註冊了!');

define('_US_INVALIDMAIL', '錯誤：Email 錯誤');
define('_US_EMAILNOSPACES', '錯誤：Email 位址不能有空白.');
define('_US_INVALIDNICKNAME', '錯誤：錯誤的帳號');
define('_US_NICKNAMETOOLONG', '帳號太長，不能超過%s個字元.');
define('_US_NICKNAMETOOSHORT', '帳號太短，不能短於%s個字元.');
define('_US_NAMERESERVED', '錯誤：不能用系統保留字當帳號。');
define('_US_NICKNAMENOSPACES', '帳號請勿包含空白字元.');
define('_US_NICKNAMETAKEN', 'ERROR: 這個帳號已有人使用.');
define('_US_EMAILTAKEN', 'ERROR: 這個 Email 已經有人註冊過.');
define('_US_ENTERPWD', 'ERROR: 您必須提供密碼.');
define('_US_SORRYNOTFOUND', '抱歉，找不到所填資料一致的使用者帳號.');




// %s is your site name
define('_US_NEWPWDREQ', '有人於 %s 索取新密碼');
define('_US_YOURACCOUNT', '您有在 %s 申請帳號');

define('_US_MAILPWDNG', 'Email密碼 : 無法更新您的資料，請與系統管理員聯繫');

// %s is a username
define('_US_PWDMAILED', '給 %s 的新密碼已經由 Email 送出.');
define('_US_CONFMAIL', '給 %s 的確認函已經由 Email 送出.');
define('_US_ACTVMAILNG', '寄送給 %s email 失敗');
define('_US_ACTVMAILOK', '已寄送確認 email給 %s.');

//%%%%%%		File Name userinfo.php 		%%%%%
define('_US_SELECTNG', '沒有選擇使用者，請回上頁再試.');
define('_US_PM', '私人傳訊');
define('_US_ICQ', 'ICQ 號碼');
define('_US_AIM', 'SKYPE 帳號');
define('_US_YIM', 'YAHOO 即時通帳號');
define('_US_MSNM', 'Windows Live ID 帳號');
define('_US_LOCATION', '來自');
define('_US_OCCUPATION', '職業');
define('_US_INTEREST', '興趣');
define('_US_SIGNATURE', '簽名');
define('_US_EXTRAINFO', '額外的資訊');
define('_US_EDITPROFILE', '編輯個人資料');
define('_US_LOGOUT', '登出');
define('_US_INBOX', '私人傳訊收件匣');
define('_US_MEMBERSINCE', '註冊日');
define('_US_RANK', '等級');
define('_US_POSTS', '回應/發表文章數');
define('_US_LASTLOGIN', '最後登入時間');
define('_US_ALLABOUT', '所有關於 %s');
define('_US_STATISTICS', '統計資料');
define('_US_MYINFO', '我的資訊');
define('_US_BASICINFO', '基本資料');
define('_US_MOREABOUT', '其他資料');
define('_US_SHOWALL', '顯示全部');

//%%%%%%		File Name edituser.php 		%%%%%
define('_US_PROFILE', '個人資料');
define('_US_REALNAME', '真實姓名');
define('_US_SHOWSIG', '發文時加入簽名');
define('_US_CDISPLAYMODE', '回應評註的顯示模式');
define('_US_CSORTORDER', '回應評註排列順序');
define('_US_PASSWORD', '密碼');
define('_US_TYPEPASSTWICE', '(輸入2次新密碼來變更)');
define('_US_SAVECHANGES', '儲存變更');
define('_US_NOEDITRIGHT', '抱歉，您沒有權限可以編輯使用者資料.');
define('_US_PASSNOTSAME', '密碼和確認密碼不相同，他們必須是一致的.');
define('_US_PWDTOOSHORT', '抱歉，系統預設密碼至少必須是 <b>%s</b> 個字元.');
define('_US_PROFUPDATED', '個人資料已更新!');
define('_US_USECOOKIE', '在cookie中儲存您的帳號資料(不包含密碼)1年');
define('_US_NO', '否');
define('_US_DELACCOUNT', '刪除帳號');
define('_US_MYAVATAR', '我的大頭貼');
define('_US_UPLOADMYAVATAR', '上傳我的大頭貼');
define('_US_MAXPIXEL', '最大寬度/高度(Pixel)');
define('_US_MAXIMGSZ', '最大檔案大小 (Bytes)');
define('_US_SELFILE', '選擇檔案');
define('_US_OLDDELETED', '舊的大頭貼已經刪除!');
define('_US_CHOOSEAVT', '選一個你要的大頭貼吧');

define('_US_PRESSLOGIN', '請按以下按鈕登入');

define('_US_ADMINNO', '不能刪除這個站長群組的使用者');
define('_US_GROUPS', '使用者所屬群組');
?>