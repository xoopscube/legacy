<?php

//%%%%%%		File Name user.php 		%%%%%
define('_US_NOTREGISTERED','지금 바로 <a href="register.php">등록</a>하세요!');
define('_US_LOSTPASSWORD','패스워드를 분실하셨나요?');
define('_US_NOPROBLEM','걱정마세요! 먼저 등록시 사용하신 메일주소를 입력한 후 버튼을 클릭해 주세요');
define('_US_YOUREMAIL','등록 메일주소: ');
define('_US_SENDPASSWORD','보내기');
define('_US_LOGGEDOUT','로그아웃 되었습니다.');
define('_US_THANKYOUFORVISIT','사이트를 이용해 주셔서 감사합니다!');
define('_US_INCORRECTLOGIN','로그인 정보가 올바르지 않습니다!');
define('_US_LOGGINGU','%s님, 어서오세요. 로그인 처리중입니다.');

// 2001-11-17 ADD
define('_US_NOACTTPADM','선택하신 회원은 등록되어 있지 않거나, 혹은 아직 승인이 끝나지 않은 상태입니다.<br />자세한 내용은 관리자에게 문의해 주세요!');
define('_US_ACTKEYNOT','승인키(활성화키)가 올바르지 않습니다.');
define('_US_ACONTACT','선택하신 계정은 이미 승인이 끝난 상태입니다.(이미 활성화된 상태)');
define('_US_ACTLOGIN','계정이 승인(활성화)되었습니다. 등록하신 아이디와 패스워드로 로그인하시기 바랍니다.');
define('_US_NOPERMISS','이 정보는 변경하실 수 없습니다.');
define('_US_SURETODEL','님의 등록회원 계정을 정말로 삭제하실 건가요?');
define('_US_REMOVEINFO','계정을 삭제할 경우 모든 관련 회원정보도 모두 삭제됩니다.');
define('_US_BEENDELED','계정이 삭제되었습니다.');
//

//%%%%%%		File Name register.php 		%%%%%
define('_US_USERREG','회원등록');
define('_US_NICKNAME','아이디');
define('_US_EMAIL','메일');
define('_US_ALLOWVIEWEMAIL','메일주소 공개');
define('_US_WEBSITE','홈페이지');
define('_US_TIMEZONE','시간대');
define('_US_AVATAR','아바타');
define('_US_VERIFYPASS','패스워드확인');
define('_US_SUBMIT','보내기');
define('_US_USERNAME','회원명');
define('_US_FINISH','보내기');
define('_US_REGISTERNG','등록에 실패하였습니다.');
define('_US_MAILOK','이 사이트로부터의 정보메일을 수신허용함');
define('_US_DISCLAIMER','이용약관');
define('_US_IAGREE','위의 내용에 동의합니다');
define('_US_UNEEDAGREE', '죄송합니다만 등록하시려면 위의 이용약관에 동의하셔야만 합니다.');
define('_US_NOREGISTER','죄송합니다만 현재 신규등록을 받고 있지 않습니다.');


// %s is username. This is a subject for email
define('_US_USERKEYFOR','%s님의 승인키(활성화키)입니다.');

define('_US_YOURREGISTERED','등록이 완료되었습니다. 님의 메일주소로 승인키(활성화키)를 발송하였으니 메일의 지시에 따라 승인을 완료(계정활성화)하시기 바랍니다.');
define('_US_YOURREGMAILNG','등록이 완료되었습니다. 하지만 서버의 내부에러로 승인키(활성화키)를 담은 메일을 발송하는데 실패하였습니다. 정말로 죄송합니다만 사이트 관리자에게 문의해 주시기 바랍니다.');
define('_US_YOURREGISTERED2','등록이 완료되었습니다. 사이트 관리자가 님의 계정을 승인(활성화)하면 정식으로 등록처리됩니다. 승인(활성화)완료시엔 메일로 통보해 드립니다.');

// %s is your site name
define('_US_NEWUSERREGAT','%s 에 신규회원등록이 있었습니다');
// %s is a username
define('_US_HASJUSTREG','%s님이 신규 등록하셨습니다');

define('_US_INVALIDMAIL','ERROR: 올바르지 않은 메일주소입니다.');
define('_US_EMAILNOSPACES','ERROR: 메일주소에 공백은 사용하실 수 없습니다.');
define('_US_INVALIDNICKNAME','ERROR: 올바르지 않은 아이디(회원명)입니다.');
define('_US_NICKNAMETOOLONG','아이디가 너무 기네요! %s 문자 이내로 해주세요!');
define('_US_NICKNAMETOOSHORT','아이디가 너무 짧네요! %s 문자 이상으로 해주세요!');
define('_US_NAMERESERVED','ERROR: 이 아이디는 사용하실수 없습니다.(예약된 아이디임)');
define('_US_NICKNAMENOSPACES','아이디에 공백은 사용하실 수 없습니다.');
define('_US_NICKNAMETAKEN','ERROR: 이 아이디는 이미 사용중입니다.');
define('_US_EMAILTAKEN','ERROR: 이 메일주소는 이미 사용중입니다.');
define('_US_ENTERPWD','ERROR: 패스워드를 입력해 주세요!');
define('_US_SORRYNOTFOUND','죄송합니다만 회원정보가 존재하지 않습니다.');




// %s is your site name
define('_US_NEWPWDREQ','%s 에 신규 패스워드 발급요청이 있었습니다.');
define('_US_YOURACCOUNT', '%s 에서의 회원아이디');

define('_US_MAILPWDNG','mail_password: 회원정보 갱신에 실패하였습니다. 사이트 관리자에게 문의해 주시기 바랍니다.');

// %s is a username
define('_US_PWDMAILED','%s 님에게 패스워드를 발송하였습니다.');
define('_US_CONFMAIL','패스워드 재취득을 위한 링크가 기재된 메일을 %s님에게 발송하였습니다.');
define('_US_ACTVMAILNG', '%s님에게 메일을 발송하는데 실패하였습니다.');
define('_US_ACTVMAILOK', '%s님에게 성공적으로 메일을 발송하였습니다.');

//%%%%%%		File Name userinfo.php 		%%%%%
define('_US_SELECTNG','회원이 선택되지 않았습니다.');
define('_US_PM','PM쪽지');
define('_US_ICQ','ICQ');
define('_US_AIM','AIM');
define('_US_YIM','YIM');
define('_US_MSNM','Windows Live ID');
define('_US_LOCATION','주소');
define('_US_OCCUPATION','직업');
define('_US_INTEREST','취미');
define('_US_SIGNATURE','서명');
define('_US_EXTRAINFO','그외');
define('_US_EDITPROFILE','프로필 편집');
define('_US_LOGOUT','로그아웃');
define('_US_INBOX','수신함');
define('_US_MEMBERSINCE','등록일');
define('_US_RANK','등급');
define('_US_POSTS','투고 수');
define('_US_LASTLOGIN','최종 로그인 일시');
define('_US_ALLABOUT','%s님의 기본정보');
define('_US_STATISTICS','통계 정보');
define('_US_MYINFO','개인 정보');
define('_US_BASICINFO','기본정보');
define('_US_MOREABOUT','개인 상세정보');
define('_US_SHOWALL','모두 표시');

//%%%%%%		File Name edituser.php 		%%%%%
define('_US_PROFILE','프로필');
define('_US_REALNAME','이름');
define('_US_SHOWSIG','투고시 서명을 반드시 첨가함');
define('_US_CDISPLAYMODE','코멘트 표시 방식');
define('_US_CSORTORDER','코멘트 표시 순서');
define('_US_PASSWORD','패스워드');
define('_US_TYPEPASSTWICE','(패스워드를 갱신하실 경우만 입력해 주세요!)');
define('_US_SAVECHANGES','변경저장');
define('_US_NOEDITRIGHT',"이 회원의 정보를 변경할 권한을 가지고 있지 않습니다.");
define('_US_PASSNOTSAME','패스워드가 올바르지 않습니다. 확인을 위해 동일한 패스워드를 두번 입력하셔야만 합니다.');
define('_US_PWDTOOSHORT','패스워드는 최소 <b>%s</b>문자 이상이어야 합니다.');
define('_US_PROFUPDATED','프로필을 갱신하였습니다.');
define('_US_USECOOKIE','회원 아이디를 1년간 쿠키에 보존함');
define('_US_NO','No');
define('_US_DELACCOUNT','계정을 삭제');
define('_US_MYAVATAR', '아바타');
define('_US_UPLOADMYAVATAR', '아바타를 업로드');
define('_US_MAXPIXEL','최대 픽셀(Pixel)수');
define('_US_MAXIMGSZ','최대 파일사이즈(Bytes)');
define('_US_SELFILE','파일선택');
define('_US_OLDDELETED','이전 아바타 그림파일은 덮어쓰기 처리됩니다.');
define('_US_CHOOSEAVT', '아바타를 리스트에서 선택해 주시기 바랍니다.');

define('_US_PRESSLOGIN', '아래의 버튼을 클릭후 로그인해 주시기바랍니다.');

define('_US_ADMINNO', '관리자그룹에 속한 회원의 계정은 삭제하실수 없습니다.');
define('_US_GROUPS', '소속그룹');
?>