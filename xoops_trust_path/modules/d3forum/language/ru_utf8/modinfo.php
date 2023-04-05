<?php

if (defined('FOR_XOOPS_LANG_CHECKER')) {
	$mydirname = 'd3forum';
}
$constpref = '_MI_' . strtoupper($mydirname);

if (defined('FOR_XOOPS_LANG_CHECKER') || !defined($constpref . '_LOADED')) {

    define($constpref . '_LOADED', 1);

    // The name of this module
    define($constpref . '_NAME', 'Форум');

    // A brief description of this module
    define($constpref . '_DESC', 'Дублируемый модуль для управления комментариями и форумами.');

    // Names of blocks for this module (Not all module has blocks)
    define($constpref . '_BNAME_LIST_TOPICS', 'Темы');
    define($constpref . '_BDESC_LIST_TOPICS', 'Это многоцелевой блок. Вы можете разместить несколько его экземпляров.');
    define($constpref . '_BNAME_LIST_POSTS', 'Собщения');
    define($constpref . '_BNAME_LIST_FORUMS', 'Форумы');

    // admin menu
    define($constpref . '_ADMENU_CATEGORYACCESS', 'Права категорий');
    define($constpref . '_ADMENU_FORUMACCESS', 'Права форумов');
    define($constpref . '_ADMENU_ADVANCEDADMIN', 'Расш. адм.');
    define($constpref . '_ADMENU_POSTHISTORIES', 'прошлое');
    define($constpref . '_ADMENU_MYLANGADMIN', 'языки');
    define($constpref . '_ADMENU_MYTPLSADMIN', 'Шаблоны');
    define($constpref . '_ADMENU_MYBLOCKSADMIN', 'Блокирует разрешения');
    define($constpref . '_ADMENU_MYPREFERENCES', 'Настройки');

    // configurations
    define($constpref . '_TOP_MESSAGE', 'Собщение в заголовке форума [ html ]');
    define($constpref . '_TOP_MESSAGEDEFAULT', '<h2>Форумы</h2><p>Чтобы приступить к просмотру сообщений, выберите категорию и форум, который хотите посетить, из списка внизу.</p>');
    define($constpref . '_SHOW_BREADCRUMBS', 'Включить хлебные крошки');
    define($constpref . '_SHOW_RSS', 'Включить RSS');
    define($constpref . '_DEFAULT_OPTIONS', 'Фильтры по умолчанию');
    define($constpref . '_DEFAULT_OPTIONSDSC', 'Укажите имена фильтров через запятую ",". Пример: smiley,xcode,br,number_entity<br>Доступные Варианты: special_entity, html attachsig, u2t_marked');
    define($constpref . '_USENAME', 'Показать имя пользователя или настоящее имя');
    define($constpref . '_USENAMEDESC', "Выберите имя для отображения: 'uname'(user ID) или 'name'(Настоящее имя). значение по умолчанию (пользователь ID): 'uname'");
    define($constpref . '_USENAME_UNAME', "use'uname'(user ID)");
    define($constpref . '_USENAME_NAME', "use'name'(Настоящее имя)");
    define($constpref . '_ALLOW_HTML', 'Разрешить HTML');
    define($constpref . '_ALLOW_HTMLDSC','Не относитесь к этой опции с пренебрежением. Злобный юзер может легко воспользоваться этой уязвимостью для осуществеления своих злобных и коварных замыслов.');
    define($constpref . '_ALLOW_TEXTIMG','Разрешить отображение внешних картинок в сообщениях');
    define($constpref . '_ALLOW_TEXTIMGDSC','Некоторые плохие люди могут воспользоваться данной возможностью для того, чтобы узнать ip-адрес или ПО посетителей вашего форума.');
    define($constpref . '_ALLOW_SIG','Разрешить подпись');
    define($constpref . '_ALLOW_SIGDSC','');

    define($constpref . '_ALLOW_SIGIMG','Разрешить отображения внешних картинок в подписи');
    define($constpref . '_ALLOW_SIGIMGDSC','Некоторые плохие люди могут воспользоваться данной возможностью для того, чтобы узнать ip-адрес или ПО посетителей вашего форума.');
    define($constpref . '_USE_VOTE','Использовать голосования');
    define($constpref . '_USE_SOLVED','Искользовать возможность РЕШАТЬ темы');
    define($constpref . '_ALLOW_MARK','Разрешить помечать темы');
    define($constpref . '_ALLOW_HIDEUID','Разрешить зарегестрированным пользователям оставлять сообщения анонимно');
    define($constpref . '_POSTS_PER_TOPIC','Максимальное количество сообщения в теме');
    define($constpref . '_POSTS_PER_TOPICDSC','');
    define($constpref . '_HOT_THRESHOLD','Порог для горячих тем');
    define($constpref . '_HOT_THRESHOLDDSC','');
    define($constpref . '_TOPICS_PER_PAGE','Количество тем на странице при просмотре форума');
    define($constpref . '_TOPICS_PER_PAGEDSC','');

    define($constpref . '_VIEWALLBREAK','Количество тем на странице при просмотре пересекающихся форумов');
    define($constpref . '_VIEWALLBREAKDSC','');
    define($constpref . '_SELFEDITLIMIT','Временной интервал пользовательского редактирования (сек)');
    define($constpref . '_SELFEDITLIMITDSC','Чтобы разрешить пользователю редактировать собственные сообщения установите plus value as seconds. Чтобы запретить - сделайте его 0.');
    define($constpref . '_SELFDELLIMIT','Временной интервал для пользовательского удаления (сек)');
    define($constpref . '_SELFDELLIMITDSC','Чтобы разрешить пользователям удалять собственные сообщения установите plus value as seconds. Чтобы запретить - установите 0. В любом случае любые родительски сообщения не могут быть удалены.');
    define($constpref . '_CSS_URI','URI для CSS данного модуля');
    define($constpref . '_CSS_URIDSC','можно использовать относительный или абсолютный путь. по умолчанию: index.css');
    define($constpref . '_IMAGES_DIR','Каталог для файлов картинок');
    define($constpref . '_IMAGES_DIRDSC','требуется относительный путь в директории модуля. по умолчанию: images');
    define($constpref . '_BODY_EDITOR', 'Editor');
    define($constpref . '_BODY_EDITORDSC', 'Редактор WYSIWYG будет включен только на форумах, поддерживающих HTML. Если форумы экранируют специальные символы HTML, xoopsdhtml будет отображаться автоматически.');

    define($constpref . '_ANONYMOUS_NAME','Имя анонимного пользователя');
    define($constpref . '_ANONYMOUS_NAMEDSC','');
    define($constpref . '_ICON_MEANINGS','Значение иконок');
    define($constpref . '_ICON_MEANINGSDSC','Укажите альтернативное значение иконок. Каждое значение должно быть разделено символом (|). Первое значение соответствует файлу "posticon0.gif".');
    define($constpref . '_ICON_MEANINGSDEF','нет|норма|несчастлив|счастлив|супер|ерунда|доклад|вопрос');
    define($constpref . '_GUESTVOTE_IVL','Голосование анонимными пользователями');
    define($constpref . '_GUESTVOTE_IVLDSC','Установите это значение в 0, чтобы запретить голосовать анонимным пользователям. Другое число в этом поле означает время (в секундах), втечение которого разрешено голосование с этого же IP.');

    define($constpref . '_ANTISPAM_GROUPS', 'Anti-SPAM ');
    define($constpref . '_ANTISPAM_GROUPSDSC', 'Spam filter settings can be applied to User groups. If guests are not allowed to post, you can leave all unchecked.');
    define($constpref . '_ANTISPAM_CLASS', ' Anti-SPAM Class name');
    define($constpref . '_ANTISPAM_CLASSDSC', 'If you disable Anti-SPAM for guests, leave input field blank. Default class name: defaultmobile<br>Available options : defaultmobilesmart, japanese and japanesemobilesmart require WizMobile by Gusagi or hyp_common ktai-renderer by Nao-pon.');
    define($constpref . '_RSS_SHOW_HIDDEN', 'Enable RSS Show hidden topics');
    define($constpref . '_RSS_SHOW_HIDDENDSC', 'Show hidden topics from comment-integration.');
    define($constpref . '_RSS_HIDDEN_TITLE', 'Enable RSS Show Title of hidden topics');
    define($constpref . '_RSS_HIDDEN_TITLEDSC', 'Default title used when empty value.');

    // Notify Categories
    define($constpref . '_NOTCAT_TOPIC', 'Текущая тема');
    define($constpref . '_NOTCAT_TOPICDSC', 'Уведомления для данной темы');
    define($constpref . '_NOTCAT_FORUM', 'Текущий форум');
    define($constpref . '_NOTCAT_FORUMDSC', 'Уведомления для данного форума');
    define($constpref . '_NOTCAT_CAT', 'Текущая категория');
    define($constpref . '_NOTCAT_CATDSC', 'Уведомления данной категории');
    define($constpref . '_NOTCAT_GLOBAL', 'Текущий модуль');
    define($constpref . '_NOTCAT_GLOBALDSC', 'Уведомления данного модуля');

    // Each Notifications
    define($constpref . '_NOTIFY_TOPIC_NEWPOST', 'Новое сообщение в теме');
    define($constpref . '_NOTIFY_TOPIC_NEWPOSTCAP', 'Уведомлять меня о всех новых сообщениях в данной теме');
    define($constpref . '_NOTIFY_TOPIC_NEWPOSTSBJ', '[{X_SITENAME}] {X_MODULE}:{TOPIC_TITLE} Новое сообщение в теме');

    define($constpref . '_NOTIFY_FORUM_NEWPOST', 'Новое сообщение в форуме');
    define($constpref . '_NOTIFY_FORUM_NEWPOSTCAP', 'Уведомлять меня о всех новых сообщениях в данном форуме.');
    define($constpref . '_NOTIFY_FORUM_NEWPOSTSBJ', '[{X_SITENAME}] {X_MODULE}:{FORUM_TITLE} Новое сообщение в форуме');

    define($constpref . '_NOTIFY_FORUM_NEWTOPIC', 'Новая тема в форуме');
    define($constpref . '_NOTIFY_FORUM_NEWTOPICCAP', 'Уведомлять меня о всех новых темах в данном форуме.');
    define($constpref . '_NOTIFY_FORUM_NEWTOPICSBJ', '[{X_SITENAME}] {X_MODULE}:{FORUM_TITLE} Новая тема в форуме');

    define($constpref . '_NOTIFY_CAT_NEWPOST', 'Новое сообщение в категории');
    define($constpref . '_NOTIFY_CAT_NEWPOSTCAP', 'Уведомлять меня о всех новых сообщениях в категории.');
    define($constpref . '_NOTIFY_CAT_NEWPOSTSBJ', '[{X_SITENAME}] {X_MODULE}:{CAT_TITLE} Новое сообщение в категории');

    define($constpref . '_NOTIFY_CAT_NEWTOPIC', 'Новая тема в категории');
    define($constpref . '_NOTIFY_CAT_NEWTOPICCAP', 'Уведомлять меня о всех новых темах в данной категории.');
    define($constpref . '_NOTIFY_CAT_NEWTOPICSBJ', '[{X_SITENAME}] {X_MODULE}:{CAT_TITLE} Новая тема в категории');

    define($constpref . '_NOTIFY_CAT_NEWFORUM', 'Новый форум в категории');
    define($constpref . '_NOTIFY_CAT_NEWFORUMCAP', 'Уведомлять меня о всех новых форумах в данной категории.');
    define($constpref . '_NOTIFY_CAT_NEWFORUMSBJ', '[{X_SITENAME}] {X_MODULE}:{CAT_TITLE} Новый форум в категории');

    define($constpref . '_NOTIFY_GLOBAL_NEWPOST', 'Новое сообщение (глобально)');
    define($constpref . '_NOTIFY_GLOBAL_NEWPOSTCAP', 'Уведомить меня о любых новых сообщениях.');
    define($constpref . '_NOTIFY_GLOBAL_NEWPOSTSBJ', '[{X_SITENAME}] {X_MODULE}: Новое сообщение');

    define($constpref . '_NOTIFY_GLOBAL_NEWTOPIC', 'Новая тема (глобально)');
    define($constpref . '_NOTIFY_GLOBAL_NEWTOPICCAP', 'Уведомить меня о любых новых темах.');
    define($constpref . '_NOTIFY_GLOBAL_NEWTOPICSBJ', '[{X_SITENAME}] {X_MODULE}: Новая тема');

    define($constpref . '_NOTIFY_GLOBAL_NEWFORUM', 'Новый форум (глобально)');
    define($constpref . '_NOTIFY_GLOBAL_NEWFORUMCAP', 'Уведомить меня о любых новых форумах.');
    define($constpref . '_NOTIFY_GLOBAL_NEWFORUMSBJ', '[{X_SITENAME}] {X_MODULE}: Новый форум');

    define($constpref . '_NOTIFY_GLOBAL_NEWPOSTFULL', 'Новое сообщение (полный текст)');
    define($constpref . '_NOTIFY_GLOBAL_NEWPOSTFULLCAP', 'Уведомить меня о любых новых сообщениях (включая полный текст в уведомлении).');
    define($constpref . '_NOTIFY_GLOBAL_NEWPOSTFULLSBJ', '[{X_SITENAME}] {POST_TITLE}');
    define($constpref . '_NOTIFY_GLOBAL_WAITING', 'Новое ожидание');
    define($constpref . '_NOTIFY_GLOBAL_WAITINGCAP', 'Уведомить меня о новых сообщениях, ожидающих одобрения. Только для администраторов');
    define($constpref . '_NOTIFY_GLOBAL_WAITINGSBJ', '[{X_SITENAME}] {X_MODULE}: Ожидает одобрения');

}
