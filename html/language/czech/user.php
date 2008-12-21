<?php
// $Id: user.php,v 1.2 2008/08/30 08:52:33 minahito Exp $
//%%%%%%		File Name user.php 		%%%%%
define('_US_NOTREGISTERED','Nejste registrovaným uživatelem?  Zaregistrujte se <a href="register.php">zde</a>.');
define('_US_LOSTPASSWORD','Ztratili jste své heslo?');
define('_US_NOPROBLEM','Nevadí, jen zadejte svou emailovou adresu, kterou jste zadali pøi registraci.');
define('_US_YOUREMAIL','Váš email: ');
define('_US_SENDPASSWORD','Zaslat heslo');
define('_US_LOGGEDOUT','Jste odhlášeni');
define('_US_THANKYOUFORVISIT','Dìkujeme za Vaši návštìvu!');
define('_US_INCORRECTLOGIN','Pøihlášení se nezdaøilo!');
define('_US_LOGGINGU','Uživatel %s.<br />Vítejte!');

// 2001-11-17 ADD
define('_US_NOACTTPADM','Tento uživatel ještì nebyl aktivován nebo byl deaktivován.<br />Prosíme, spojte se s administrátorem tohoto serveru.');
define('_US_ACTKEYNOT','Špatný aktivaèní klíè!');
define('_US_ACONTACT','Vybraný uživatel již byl aktivován!');
define('_US_ACTLOGIN','Váš úèet byl aktivován. Nyní se mùžete pøihlásit.');
define('_US_NOPERMISS','Nemáte oprávnìní k provedení této akce!');
define('_US_SURETODEL','Opravdu chcete smazat Váš úèet?');
define('_US_REMOVEINFO','Tímto budou z naší databáze odstranìny veškeré informace o Vás.');
define('_US_BEENDELED','Váš úèet byl smazán.');
//

//%%%%%%		File Name register.php 		%%%%%
define('_US_USERREG','Uživatelská registrace');
define('_US_NICKNAME','Uživatel');
define('_US_EMAIL','Email');
define('_US_ALLOWVIEWEMAIL','Ostatní uživatelé uvidí mùj email');
define('_US_WEBSITE','WWW');
define('_US_TIMEZONE','Èasová zóna');
define('_US_AVATAR','Ikona');
define('_US_VERIFYPASS','Kontrola hesla');
define('_US_SUBMIT','Potvrdit');
define('_US_USERNAME','Uživatel');
define('_US_FINISH','Dokonèit');
define('_US_REGISTERNG','Nelze registrovat nové uživatele.');
define('_US_MAILOK','Pøijímat obèasná oznámení emailem <br />od administrátorù nebo moderátorù?');
define('_US_DISCLAIMER','Prohlášení');
define('_US_IAGREE','Souhlasím z výše uvedeným');
define('_US_UNEEDAGREE', 'Omlouváme se, ale musíte odsouhlasit naše prohlášení, jinak se nelze registrovat.');
define('_US_NOREGISTER','Omlouváme se, ale web je nyní zavøen pro nové registrace uživatelù.');


// %s is username. This is a subject for email
define('_US_USERKEYFOR','Aktivaèní klíè pro uživatele %s');

define('_US_YOURREGISTERED','Nyní jste registrováni. Byl Vám odeslán email, obsahující aktivaèní klíè. Postupujte prosím podle instrukcí v této zprávì.');
define('_US_YOURREGMAILNG','Nyní jste registrováni. Bohužel se nepodaøilo odeslat aktivaèní email na Vámi zadanou adresu z dùvodu problémù na našem serveru. Omlouváme se za tuto komplikaci a prosíme Vás o upozornìní našeho administrátora sereru na tuto situaci. Dìkujeme za pochopení.');
define('_US_YOURREGISTERED2','Nyní jste registrováni. Vyèkejte prosím na aktivaci Vašeho úètu administrátorem. Budete o tom informováni emailem. Budeme se snažit co nejdøíve.');

// %s is your site name
define('_US_NEWUSERREGAT','Registrace uživatele na %s');
// %s is a username
define('_US_HASJUSTREG','%s byl právì registrován!');

define('_US_INVALIDMAIL','CHYBA: Neplatný email');
define('_US_EMAILNOSPACES','CHYBA: Email nesmí obsahovat mezery.');
define('_US_INVALIDNICKNAME','CHYBA: Neplatné uživatelské jméno');
define('_US_NICKNAMETOOLONG','Uživatelské jméno je pøíliš dlouhé. Musí být kratší než %s znakù.');
define('_US_NICKNAMETOOSHORT','Uživatelské jméno je pøíliš krátké. Musí být delší než %s znakù.');
define('_US_NAMERESERVED','CHYBA: Jméno je rezervováno.');
define('_US_NICKNAMENOSPACES','Ve jménu nesmí být žádné mezery.');
define('_US_NICKNAMETAKEN','CHYBA: Uživatelské jméno je již obsazeno.');
define('_US_EMAILTAKEN','CHYBA: Emailová adresa je již registrována.');
define('_US_ENTERPWD','CHYBA: Musíte si zvolit heslo.');
define('_US_SORRYNOTFOUND','Omlouváme se, odpovídající info o uživateli nebylo nalezeno.');




// %s is your site name
define('_US_NEWPWDREQ','Požadavek na nové heslo na %s');
define('_US_YOURACCOUNT', 'Váš úèet na %s');

define('_US_MAILPWDNG','mail_password: nelze aktualizovat uživatelská data. Spojte se se správcem');

// %s is a username
define('_US_PWDMAILED','Heslo pro uživatele %s bylo odesláno.');
define('_US_CONFMAIL','Potvrzovací zpráva pro uživatele %s byla odeslána.');
define('_US_ACTVMAILNG', 'Selhalo odeslání upozornìní pro %s.');
define('_US_ACTVMAILOK', 'Upozornìní pro uživatele %s bylo odesláno.');

//%%%%%%		File Name userinfo.php 		%%%%%
define('_US_SELECTNG','Nebyl vybrán žádný uživatel! Jdìte zpìt a zkuste znovu.');
define('_US_PM','PM');
define('_US_ICQ','ICQ');
define('_US_AIM','AIM');
define('_US_YIM','YIM');
define('_US_MSNM','Windows Live ID');
define('_US_LOCATION','Mìsto');
define('_US_OCCUPATION','Zamìstnání');
define('_US_INTEREST','Zájmy');
define('_US_SIGNATURE','Podpis');
define('_US_EXTRAINFO','Doplnìní');
define('_US_EDITPROFILE','Upravit úèet');
define('_US_LOGOUT','Odhlásit');
define('_US_INBOX','Pøijaté zprávy');
define('_US_MEMBERSINCE','Èlenem od');
define('_US_RANK','Zaøazení');
define('_US_POSTS','Komentáøe/Pøíspìvky');
define('_US_LASTLOGIN','Poslední pøihlášení');
define('_US_ALLABOUT','Vše o uživateli %s');
define('_US_STATISTICS','Statistika');
define('_US_MYINFO','Moje info');
define('_US_BASICINFO','Základní informace');
define('_US_MOREABOUT','Více o mì');
define('_US_SHOWALL','Zobrazit vše');

//%%%%%%		File Name edituser.php 		%%%%%
define('_US_PROFILE','Profil');
define('_US_REALNAME','Pravé jméno');
define('_US_SHOWSIG','Vždy pøipojit mùj podpis');
define('_US_CDISPLAYMODE','Zpùsob zobrazení komentáøù');
define('_US_CSORTORDER','Øazení komentáøù');
define('_US_PASSWORD','Heslo');
define('_US_TYPEPASSTWICE','(pro zmìnu vyplòte heslo dvakrát)');
define('_US_SAVECHANGES','Uložit zmìny');
define('_US_NOEDITRIGHT',"Promiòte, ale nemáte práva pro editaci tohoto uživatele.");
define('_US_PASSNOTSAME','Zadaná hesla nejsou shodná.');
define('_US_PWDTOOSHORT','Promiòte, Vaše heslo musí být nejménì <b>%s</b> znakù dlouhé.');
define('_US_PROFUPDATED','Váš profil byl aktualizován!');
define('_US_USECOOKIE','Uložit Vaše uživatelské jméno do cookies ve vašem poèítaèi na dobu jednoho roku.');
define('_US_NO','Ne');
define('_US_DELACCOUNT','Smazat úèet');
define('_US_MYAVATAR', 'Moje ikona');
define('_US_UPLOADMYAVATAR', 'Nahrát vlastní ikonu');
define('_US_MAXPIXEL','Max. pixelù');
define('_US_MAXIMGSZ','Max. velikost ikony (Bytù)');
define('_US_SELFILE','Vybrat soubor');
define('_US_OLDDELETED','Vaše stará ikona byla smazána!');
define('_US_CHOOSEAVT', 'Vyberte si ikonu se seznamu');

define('_US_PRESSLOGIN', 'Zvolte tlaèítko níže pro pøihlášení');

define('_US_ADMINNO', 'Ve skupinì <b>Webmaster</b> musí zùstat alespoò jeden uživatel');
define('_US_GROUPS', 'Skupiny uživatele');
?>
