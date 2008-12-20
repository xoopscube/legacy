<?php
// $Id$
//%%%%%%		File Name user.php 		%%%%%
define('_US_NOTREGISTERED','尚未注册吗？请点击<a href="register.php">这里</a>注册.');
define('_US_LOSTPASSWORD','忘记密码了？');
define('_US_NOPROBLEM','没关系，请填入您注册的Email来找回帐户信息');
define('_US_YOUREMAIL','您的Email: ');
define('_US_SENDPASSWORD','发送确认码/密码');
define('_US_LOGGEDOUT','您已经退出系统');
define('_US_THANKYOUFORVISIT','谢谢您光临本站！');
define('_US_INCORRECTLOGIN','登录错误');
define('_US_LOGGINGU','%s登录本站成功完成...欢迎光临本站');

// 2001-11-17 ADD
define('_US_NOACTTPADM','此帐号尚未开启，或是已被删除。<br>请联系网站管理员');
define('_US_ACTKEYNOT','启动码错误！');
define('_US_ACONTACT','您选择的帐号已经启动！');
define('_US_ACTLOGIN','您的帐号已经启动，请使用您注册时自定义的帐号与密码进行登录');
define('_US_NOPERMISS','对不起，您的权限不够，不能执行此项操作！');
define('_US_SURETODEL','您确定要删除您的帐号？');
define('_US_REMOVEINFO','此操作将会删除您在本站的所有个人资料');
define('_US_BEENDELED','帐号删除成功完成');
//

//%%%%%%		File Name register.php 		%%%%%
define('_US_USERREG','注册表单');
define('_US_NICKNAME','帐号');
define('_US_EMAIL','Email');
define('_US_ALLOWVIEWEMAIL','公开您的Email');
define('_US_WEBSITE','网站');
define('_US_TIMEZONE','时区');
define('_US_AVATAR','头像');
define('_US_VERIFYPASS','确认密码');
define('_US_SUBMIT','提交');
define('_US_USERNAME','帐号');
define('_US_FINISH','完成');
define('_US_REGISTERNG','无法注册新帐号');
define('_US_MAILOK','接收网站管理者及<br/>有相关权限的管理员发送的Email通知最新消息吗？');
define('_US_DISCLAIMER','注册前请详细阅读本站使用规定，<br>如果接受并愿意注册，请勾选接收规则<br>如果不接受请勿注册');
define('_US_IAGREE','我接收以上规则');
define('_US_UNEEDAGREE', '抱歉，你必须接受此规定才能注册');
define('_US_NOREGISTER','抱歉，目前我们不接收新会员');


// %s is username. This is a subject for email
define('_US_USERKEYFOR','%s的启动帐号');

define('_US_YOURREGISTERED','注册完成，但是帐号尚未启动，<br>系统将发送一份包含启动码的Email给您，<br>请依照该Email的提示回站启动您的帐号');
define('_US_YOURREGMAILNG','您好，虽然您已完成注册，但是因为系统发生故障，<br>所以无法发送Email给您，请与本站管理员联系');
define('_US_YOURREGISTERED2','注册完成，请耐心等待本站发送的Email确认');

// %s is your site name
define('_US_NEWUSERREGAT','新会员注册于%s');
// %s is a username
define('_US_HASJUSTREG','%s刚刚加入了我们！');

define('_US_INVALIDMAIL','ERROR: email出错');
define('_US_EMAILNOSPACES','ERROR: Email地址不能为空');
define('_US_INVALIDNICKNAME','ERROR: 帐号出错');
define('_US_NICKNAMETOOLONG','帐号太长，不能超过%s个字符');
define('_US_NICKNAMETOOSHORT','帐号太短，不能少于%s个字符');
define('_US_NAMERESERVED','ERROR: 不能使用系统保留字作为帐号');
define('_US_NICKNAMENOSPACES','帐号中不能含有空白字符');
define('_US_NICKNAMETAKEN','ERROR: 帐号已经被使用');
define('_US_EMAILTAKEN','ERROR: Email地址已经被使用');
define('_US_ENTERPWD','ERROR: 请提供密码');
define('_US_SORRYNOTFOUND','抱歉，帐号或者密码错误');




// %s is your site name
define('_US_NEWPWDREQ','密码在%s处被更新');
define('_US_YOURACCOUNT', '您曾在%s申请帐号');

define('_US_MAILPWDNG','mail密码:无法更新您的信息，请与管理员联系');

// %s is a username
define('_US_PWDMAILED','给%s的新密码已通过Email发出');
define('_US_CONFMAIL','给%s的确认信息已发出');
define('_US_ACTVMAILNG', '给%s发送邮件失败');
define('_US_ACTVMAILOK', '已发送确认信息给%s');

//%%%%%%		File Name userinfo.php 		%%%%%
define('_US_SELECTNG','没有选择使用者，请回上页重试');
define('_US_PM','个人信息');
define('_US_ICQ','ICQ号码');
define('_US_AIM','AIM帐号');
define('_US_YIM','雅虎YIM帐号');
define('_US_MSNM','微软MSN帐号');
define('_US_LOCATION','个人所在位置');
define('_US_OCCUPATION','职业');
define('_US_INTEREST','兴趣');
define('_US_SIGNATURE','签名');
define('_US_EXTRAINFO','额外信息');
define('_US_EDITPROFILE','编辑个人资料');
define('_US_LOGOUT','退出');
define('_US_INBOX','收件箱');
define('_US_MEMBERSINCE','注册日期');
define('_US_RANK','等级');
define('_US_POSTS','回复/发表文章数');
define('_US_LASTLOGIN','最近一次登录时间');
define('_US_ALLABOUT','所有关于%s');
define('_US_STATISTICS','统计');
define('_US_MYINFO','我的信息');
define('_US_BASICINFO','基本信息');
define('_US_MOREABOUT','其它信息');
define('_US_SHOWALL','显示全部');

//%%%%%%		File Name edituser.php 		%%%%%
define('_US_PROFILE','个人资料');
define('_US_REALNAME','真实姓名');
define('_US_SHOWSIG','发表文章时加入签名');
define('_US_CDISPLAYMODE','回复评论时显示模式');
define('_US_CSORTORDER','回复评论排列顺序');
define('_US_PASSWORD','密码');
define('_US_TYPEPASSTWICE','(输入2次新密码来确认变更)');
define('_US_SAVECHANGES','保存变更');
define('_US_NOEDITRIGHT',"抱歉，您没有权限可以编辑用户资料");
define('_US_PASSNOTSAME','密码和确认码不一致');
define('_US_PWDTOOSHORT','抱歉，系统预设密码必须至少有<b>%s</b>个字符');
define('_US_PROFUPDATED','个人资料已更新');
define('_US_USECOOKIE','在cookie中存储您的帐号信息(不包含密码)1年');
define('_US_NO','否');
define('_US_DELACCOUNT','删除帐号');
define('_US_MYAVATAR', '我的个人头像');
define('_US_UPLOADMYAVATAR', '上传头像');
define('_US_MAXPIXEL','最大宽度/高度(像素)');
define('_US_MAXIMGSZ','最大图片文件长度(Bytes)');
define('_US_SELFILE','选择文件');
define('_US_OLDDELETED','旧的个人头像将被删除！');
define('_US_CHOOSEAVT', '从下列头像中选择一个作为你的个人头像');

define('_US_PRESSLOGIN', '点击以下按钮登录系统');

define('_US_ADMINNO', '不能删除这个网站管理员群组的用户');
define('_US_GROUPS', '用户所属的群组');
?>