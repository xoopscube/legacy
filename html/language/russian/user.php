<?php
// $Id$
//%%%%%%		File Name user.php 		%%%%%
define('_US_NOTREGISTERED','Еще не зарегистрировались? Никогда не поздно <a href="register.php">зарегистрироваться</a>.');
define('_US_LOSTPASSWORD','Забыли пароль?');
define('_US_NOPROBLEM','Нет проблем. Просто введите свой Email и мы напомним Вам Ваши данные.');
define('_US_YOUREMAIL','Ваш Email: ');
define('_US_SENDPASSWORD','Отправить пароль');
define('_US_LOGGEDOUT','Вы завершили свой сеанс');
define('_US_THANKYOUFORVISIT','Спасибо за то, что посетили наш сайт!');
define('_US_INCORRECTLOGIN','Ошибка авторизации.');
define('_US_LOGGINGU','Добро пожаловать, %s.');

// 2001-11-17 ADD
define('_US_NOACTTPADM','Выбраный пользователь выключен или еще не был активирован.<br />Свяжитесь с администратором для уточнения деталей.');
define('_US_ACTKEYNOT','Неверный ключ активации!');
define('_US_ACONTACT','Выбраный аккаунт уже активирован!');
define('_US_ACTLOGIN','Ваш аккаунт был активирован. Пожалуйста, авторизуйтесь используя Ваш пароль.');
define('_US_NOPERMISS','Извините, у Вас недостаточно прав, чтобы выполнить данное действие!');
define('_US_SURETODEL','Вы уверены, что хотите удалить свой аккаунт?');
define('_US_REMOVEINFO','Это действие удалит всю информацию о Вас из базы данных.');
define('_US_BEENDELED','Ваш аккаунт был успешно удален.');
//

//%%%%%%		File Name register.php 		%%%%%
define('_US_USERREG','Регистрация пользователя.');
define('_US_NICKNAME','Имя пользователя');
define('_US_EMAIL','Email');
define('_US_ALLOWVIEWEMAIL','Позволить другим пользователям видеть мой Email');
define('_US_WEBSITE','Веб-сайт');
define('_US_TIMEZONE','Временная зона');
define('_US_AVATAR','Аватар');
define('_US_VERIFYPASS','Повторить пароль');
define('_US_SUBMIT','Отправить');
define('_US_USERNAME','Имя пользователя');
define('_US_FINISH','Закончить');
define('_US_REGISTERNG','Не могу зарегистрировать нового пользователя.');
define('_US_MAILOK','Получать рассылаемые время от времени уведомления от <br />администраторов и модераторов?');
define('_US_DISCLAIMER','Правила пользования');
define('_US_IAGREE','Я согласен с вышеизложеным');
define('_US_UNEEDAGREE', 'Извините, Вы дожны согласиться с правилами предоставления сервиса чтобы зарегистрироваться');
define('_US_NOREGISTER','Извините, на данный момент регистрация новых пользователей приостановлена');


// %s is username. This is a subject for email
define('_US_USERKEYFOR','Ключ активации пользвоателя %s');

define('_US_YOURREGISTERED','Теперь вы зарегистрированы. Письмо содержащее ключ активации было отправлено в Ваш адрес. Следуйте инструкциям из этого письмо для активации аккаунта.');
define('_US_YOURREGMAILNG','Теперь вы зарегистрированы. Однако нам не удалось отправить письмо с ключем активации на Ваш почтовый адрес ввиду произошедшей внутренней ошибки сервера. Мы приносим свои извинения за доставленые неудобства. Пожалуйста, свяжитесь с вебмастером/администратором сайта и уведомите его о сложившейся ситуации.');
define('_US_YOURREGISTERED2','Теперь вы зарегистрированы. Пожалуйста, дождитесь активации Вашего аккаунта администратором. Вы получите письмо после того, как это произойдет. Активация может занять некоторое время, сохраняйте спокойствие.');

// %s is your site name
define('_US_NEWUSERREGAT','Новая регистрация пользователя на сайте %s');
// %s is a username
define('_US_HASJUSTREG','Пользователь %s только что был зарегистрирован!');

define('_US_INVALIDMAIL','ОШИБКА: Неверный email');
define('_US_EMAILNOSPACES','ОШИБКА: Email не должен содержать пробелов.');
define('_US_INVALIDNICKNAME','ОШИБКА: Неверное имя пользователя');
define('_US_NICKNAMETOOLONG','Имя пользователя слишком длинное. Должно быть меньше %s символов.');
define('_US_NICKNAMETOOSHORT','Имя пользователя слишком короткое. Должно содержать более %s символов.');
define('_US_NAMERESERVED','ОШИБКА: Имя пользователя зарезервировано.');
define('_US_NICKNAMENOSPACES','Имя пользователя не должно содержать пробелов.');
define('_US_NICKNAMETAKEN','ОШИБКА: Имя пользователя занято.');
define('_US_EMAILTAKEN','ОШИБКА: Email уже зарегистрирован.');
define('_US_ENTERPWD','ОШИБКА: Вы должны указать пароль.');
define('_US_SORRYNOTFOUND','Извините, не найдено соответствующей информации о пользователе.');




// %s is your site name
define('_US_NEWPWDREQ','На сайте %s запрошен новый пароль.');
define('_US_YOURACCOUNT', 'Ваш аккаунт на сайте %s');

define('_US_MAILPWDNG','mail_password: не могу обновить запись о пользователе. Свяжитесь с Администратором.');

// %s is a username
define('_US_PWDMAILED','ПАроль для пользователя %s отправлен.');
define('_US_CONFMAIL','Письмо с кодом активации для пользователя %s отправлено.');
define('_US_ACTVMAILNG', 'Ошибка отправки письма с уведомлением для пользователя %s');
define('_US_ACTVMAILOK', 'Письмо с уведомлением отпралено пользователю %s.');

//%%%%%%		File Name userinfo.php 		%%%%%
define('_US_SELECTNG','Пользователь не выбран. Вернитесь обратно и попробуйте еще раз.');
define('_US_PM','PM');
define('_US_ICQ','ICQ');
define('_US_AIM','AIM');
define('_US_YIM','YIM');
define('_US_MSNM','Windows Live ID');
define('_US_LOCATION','Откуда');
define('_US_OCCUPATION','Род занятий');
define('_US_INTEREST','Интересы');
define('_US_SIGNATURE','Подпись');
define('_US_EXTRAINFO','Дополнительная информация');
define('_US_EDITPROFILE','Редактировать профиль');
define('_US_LOGOUT','Выйти');
define('_US_INBOX','Входящие');
define('_US_MEMBERSINCE','Зарегистрирован');
define('_US_RANK','Ранг');
define('_US_POSTS','Комментариев/Сообщений');
define('_US_LASTLOGIN','Был на сайте');
define('_US_ALLABOUT','Все о %s');
define('_US_STATISTICS','Статистика');
define('_US_MYINFO','Обо мне');
define('_US_BASICINFO','Основная информация');
define('_US_MOREABOUT','Дополнительно');
define('_US_SHOWALL','Показать все');

//%%%%%%		File Name edituser.php 		%%%%%
define('_US_PROFILE','Профиль');
define('_US_REALNAME','Настоящее имя');
define('_US_SHOWSIG','Всегда добавлять мою подпись');
define('_US_CDISPLAYMODE','Режим отображения комментариев');
define('_US_CSORTORDER','Способ сортировки комментариев');
define('_US_PASSWORD','Пароль');
define('_US_TYPEPASSTWICE','(наберите новый пароль дважды чтобы сменить его)');
define('_US_SAVECHANGES','Сохранить');
define('_US_NOEDITRIGHT',"Извините, но у Вас недостаточно прав, чтобы редактировать информацию данного пользователя.");
define('_US_PASSNOTSAME','Пароли разные. А должны быть одинаковы.');
define('_US_PWDTOOSHORT','Извините, но минимальная длина пароля - <b>%s</b> символов.');
define('_US_PROFUPDATED','Ваш профиль обновлен.');
define('_US_USECOOKIE','Сохранять мое имя пользователя в cookie на 1 год');
define('_US_NO','Нет');
define('_US_DELACCOUNT','Удалить аккаунт');
define('_US_MYAVATAR', 'Мой аватар');
define('_US_UPLOADMYAVATAR', 'Загрузить Аватар');
define('_US_MAXPIXEL','Максимальные размеры (Pix)');
define('_US_MAXIMGSZ','Макс. объем файла (Bytes)');
define('_US_SELFILE','Выбрать файл');
define('_US_OLDDELETED','Ваш старый аватар будет удален!');
define('_US_CHOOSEAVT', 'Выберите аватар из списка доступных');

define('_US_PRESSLOGIN', 'Нажмите кнопку ниже чтобы авторизоваться.');

define('_US_ADMINNO', 'Пользователь из группц webmasters не может быть удален');
define('_US_GROUPS', 'Группы пользователей');
?>