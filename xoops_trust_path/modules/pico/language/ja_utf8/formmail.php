<?php

define('_MD_PICO_FORMMAIL_MSG_ERRORANONYMOUS','入力項目に不備があります') ;
define('_MD_PICO_FORMMAIL_MSG_SENTSUCCESSFULLY','送信完了しました') ;

define('_MD_PICO_FORMMAIL_MAILSUBJECT','%s への問い合わせ') ;
define('_MD_PICO_FORMMAIL_CCMAILSUBJECT','%s への問い合わせ（確認メール）') ;
define('_MD_PICO_FORMMAIL_MAILFLDSEP',"\n---------------------------------------------------------------\n") ;
define('_MD_PICO_FORMMAIL_MAILMIDSEP',"\n--------\n") ;

define('_MD_PICO_FORMMAIL_BLOCK_POSTCONFIRM','<div class="confirmMsg">以下の内容で送信しますか<br /><input type="submit" value="送信する" /> <input type="submit" name="cancel" value="キャンセル" /></div>') ;
define('_MD_PICO_FORMMAIL_BLOCK_ERROR_BEGIN','<div class="errorMsg"><ul>入力項目に不備があります') ;
define('_MD_PICO_FORMMAIL_BLOCK_ERROR_END','</ul></div>') ;

define('_MD_PICO_FORMMAIL_ERRFMT_MISSING_REQUIRED','<li>必須項目 %s に入力がありません</li>') ;
define('_MD_PICO_FORMMAIL_ERRFMT_INVALID_EMAIL','<li>%s のメールアドレス形式が正しくありません</li>') ;
define('_MD_PICO_FORMMAIL_ERRFMT_INVALID_NUMBER','<li>%s には数字のみを入力してください</li>') ;
define('_MD_PICO_FORMMAIL_ERRFMT_INVALID_GENERAL','<li>%s の形式が正しくありません</li>') ;
define('_MD_PICO_FORMMAIL_ERRFMT_MISSING_SELECTED','<li>%s を選択してください</li>') ;
define('_MD_PICO_FORMMAIL_ERRFMT_INVALID_OPTION','<li>%s にあり得ない選択肢が選ばれています</li>') ;
define('_MD_PICO_FORMMAIL_ERRFMT_ATLEAST_ONE','<li>%s から一つ以上を選択してください</li>') ;


?>