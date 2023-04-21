<?php

define( '_TPLSADMIN_INTRO', 'Представляем хук для компиляции шаблонов');

define( '_TPLSADMIN_DESC', 'Перехватчики компиляции обеспечивают простой способ вставки помощников визуального редактирования в ваши шаблоны и сбора переменных Smarty.
Эти функции доступны только в шаблонах внешнего интерфейса и дублируемых модулях, для которых они были написаны. ');

define( '_TPLSADMIN_NOTE', "Важный ! Хотя визуальные помощники предназначены для выделения структуры вашего макета и шаблонов, существуют ограничения на распознавание по признакам, например, компонентов и пользовательских шаблонов.");

define( '_TPLSADMIN_TASK_Title', 'Зачем и когда выполнять эту задачу!');
define( '_TPLSADMIN_TASK', "Скомпилированные шаблоны можно использовать для выполнения следующих задач:<br>
<ul>
<li>структурный обзор, облегчающий распознавание недостатков функционального дизайна</li>
<li>вставить элементы наложения, которые отображаются для каждого включенного компонента и шаблона.
<li>вставьте комментарии к коду, чтобы облегчить редактирование исходного кода</li>
<li>обнаружение и устранение различий между дизайном шаблона и его реализацией.</li>
<li>генерировать код приложения, используемый в шаблонах, и собирать переменные Smarty.</li>
</ul>");

define( '_TPLSADMIN_CACHE_TITLE', 'скомпилированные модели');
define( '_TPLSADMIN_CACHE_DESC' , 'Компиляция ( Перехват ) создает новый набор файлов, а исходный шаблон остается неизменным, в большинстве случаев вы можете выполнить <b>Normalise</b>
чтобы удалить все кэшированные и скомпилированные файлы шаблона.' );

define( '_TPLSADMIN_FMT_MSG_ENCLOSEBYCOMMENT' , '%d кешированные шаблоны, обернутые комментариями tplsadmin.');
define( '_TPLSADMIN_DT_ENCLOSEBYCOMMENT' , 'Добавление комментариев в исходный код');
define( '_TPLSADMIN_DD_ENCLOSEBYCOMMENT' , 'Добавьте HTML-комментарии в начале и в конце каждого шаблона. Поскольку это не влияет на дизайн, рекомендуется для редактирования исходного кода.');
define( '_TPLSADMIN_CNF_ENCLOSEBYCOMMENT' , 'Скомпилированный кэш шаблона будет заключен в комментарии "tplsadmin". Подтвердите продолжение или отмените!');


define( '_TPLSADMIN_FMT_MSG_ENCLOSEBYBORDEREDDIV' , '%d кеши шаблонов были заключены в теги div');
define( '_TPLSADMIN_DT_ENCLOSEBYBORDEREDDIV' , 'Добавьте теги div вокруг шаблонов.');
define( '_TPLSADMIN_DD_ENCLOSEBYBORDEREDDIV' , 'Заключите каждый шаблон в рамку div и вставьте ссылку на экран редактирования шаблона. Это может испортить ваш дизайн, но дает вам наиболее интуитивно понятный опыт редактирования.');
define( '_TPLSADMIN_CNF_ENCLOSEBYBORDEREDDIV' , 'Оберните кэшированные шаблоны тегами div. Подтвердите, чтобы продолжить, или отмените!');

define( '_TPLSADMIN_FMT_MSG_HOOKSAVEVARS' , '%d Логика, реализованная в скомпилированном кэше для сбора переменных шаблона.');
define( '_TPLSADMIN_DT_HOOKSAVEVARS' , 'Вставьте логику для сбора переменных шаблона');
define( '_TPLSADMIN_DD_HOOKSAVEVARS' , 'The first step of getting the information of templates variables in your site. The template vars infos will be collected when the front-end is displayed. If all templates you want to edit are displayed, get template vars info by underlying buttons.');
define( '_TPLSADMIN_CNF_HOOKSAVEVARS' , 'Модели, скомпилированные в кэше, реализуют логику для сбора переменных модели. Вы хотите продолжать?');

define( '_TPLSADMIN_FMT_MSG_REMOVEHOOKS' , '%d Кэшированные шаблоны нормализованы!');
define( '_TPLSADMIN_DT_REMOVEHOOKS' , 'Нормализуйте скомпилированные шаблоны.');
define( '_TPLSADMIN_DD_REMOVEHOOKS' , 'Это удаляет комментарии, теги div и логику Smarty из всех скомпилированных шаблонов.');
define( '_TPLSADMIN_CNF_REMOVEHOOKS' , 'Подтвердите, чтобы продолжить, и удалите крючки!');


define( '_TPLSADMIN_MSG_CLEARCACHE' , 'Кэшированные шаблоны удалены!.');
define( '_TPLSADMIN_MSG_CREATECOMPILECACHEFIRST' , 'Скомпилированных шаблонов нет. Просмотрите свой внешний интерфейс, чтобы отобразить свои страницы и кэшировать скомпилированные шаблоны.');

define( '_TPLSADMIN_CNF_DELETEOK' , 'Подтвердите, чтобы продолжить, и Удалить или Отменить!');


define( '_TPLSADMIN_DT_GETTPLSVARSINFO_DW' , 'Создайте расширение DreamWeaver с переменными шаблона.');
define( '_TPLSADMIN_DD_GETTPLSVARSINFO_DW' , 'Сначала откройте Диспетчер расширений.<br>Извлеките архив загрузки.<br>Запустите файлы .mxi и следуйте инструкциям в диалоговых окнах установки.<br>Фрагменты переменных шаблона вашего сайта можно использовать после перезапуска DreamWeaver.');

define( '_TPLSADMIN_DT_GETTEMPLATES' , 'Скачать шаблоны.');
define( '_TPLSADMIN_DD_GETTEMPLATES' , 'Выберите набор шаблонов для загрузки и нажмите любую кнопку.');

define( '_TPLSADMIN_FMT_MSG_PUTTEMPLATES' , '%d Шаблоны были импортированы.');
define( '_TPLSADMIN_DT_PUTTEMPLATES' , 'Загрузить шаблоны.');
define( '_TPLSADMIN_DD_PUTTEMPLATES' , 'Выберите набор шаблонов, который хотите загрузить.<br>Выберите файловый архив <b>tar</b>, включая файлы шаблонов (.html)<br>Автоматически извлекает все файлы из архива в их абсолютное местоположение независимо от дерева. состав.');


define( '_TPLSADMIN_ERR_NOTUPLOADED' , 'No files are uploaded.');
define( '_TPLSADMIN_ERR_EXTENSION' , 'Это расширение не разрешено.');
define( '_TPLSADMIN_ERR_INVALIDARCHIVE' , 'Архив можно не распаковывать.');
define( '_TPLSADMIN_ERR_INVALIDTPLSET' , 'Указано недопустимое имя набора шаблонов.');

define( '_TPLSADMIN_ERR_NOTPLSVARSINFO' , 'Файлов с информацией о шаблонах vars нет.');

define( '_TPLSADMIN_NUMCAP_COMPILEDCACHES' , 'Шаблоны, скомпилированные в каталоге кеша');
define( '_TPLSADMIN_NUMCAP_TPLSVARS' , 'Шаблоны, скомпилированные для редактирования');
