<?php

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'protector' ;
$constpref = '_MI_' . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {




// Appended by Xoops Language Checker -GIJOE- in 2009-11-17 18:12:56
define($constpref.'_FILTERS','filters enabled in this site');
define($constpref.'_FILTERSDSC','specify file names inside of filters_byconfig/ separated with LF');
define($constpref.'_MANIPUCHECK','enable manipulation checking');
define($constpref.'_MANIPUCHECKDSC','notify to admin if your root folder or index.php is modified.');
define($constpref.'_MANIPUVALUE','value for manipulation checking');
define($constpref.'_MANIPUVALUEDSC','do not edit this field');

// Appended by Xoops Language Checker -GIJOE- in 2009-07-06 05:46:52
define($constpref.'_DBTRAPWOSRV','هیچ وقت _SERVER برای anti-SQL-Injection برسی نکن');
define($constpref.'_DBTRAPWOSRVDSC','بعضی از کارگذار ها اجازه میدهند سیستم تله گذاری پایگاه داده ها فعال باشد. این باعث اشتباه در تشخیص حمله تزریق به SQL میشود. اگر شما خطاهای دریافت کردید, این گزینه را روشن کنید. باید توجه داشته باشید که این گزینه باعث تضعیف سیستم تله گذاری لایه های پایگاه داده ها در برابر تزریق به sql میشود.');

// Appended by Xoops Language Checker -GIJOE- in 2009-01-14 11:10:53
define($constpref.'_DBLAYERTRAP','فعال سازی سیستم تله گذاری لایه های پایگاه داده ها در برابر تزریق به sql یا ( DB Layer trapping anti-SQL-Injection ) ');
define($constpref.'_DBLAYERTRAPDSC','همچنین حملات تزریق به SQL توسط این گزینه دفع میشوند. این ویژگی لازم دارد که توسط databasefactory پشتیبانی شود. شما میتوانید این گزینه را در صفحه مشاوره امنیتی برسی کنید.');

define( $constpref.'_LOADED' , 1 ) ;

// The name of this module
define($constpref."_NAME","ماژول امنيتي پروتکتور");

// A brief description of this module
define($constpref."_DESC","اين ماژول سايت زوپس شما را در برابر حملات ويروسي و لینک های داس نفوذ به پایگاه داده ها و ... محافظت ميکند.");

// Menu
define($constpref."_ADMININDEX","مرکز حفاظت");
define($constpref."_ADVISORY","مشاوره ی امنیتی");
define($constpref."_PREFIXMANAGER","مدیریت پیشوند نام جدول های پایگاه داده ها");
define($constpref.'_ADMENU_MYBLOCKSADMIN','دسترسی ها');

// Configs
define($constpref.'_GLOBAL_DISBL','غیر فعال کردن موقت حفاظت');
define($constpref.'_GLOBAL_DISBLDSC','تمام حفاظت ها به طور موقت غیر فعال میشود.<br />فراموش نکنید بعد از رفع نقص دوباره این گزینه را فعال کنید');

define($constpref.'_DEFAULT_LANG','ربان پیش فرض');
define($constpref.'_DEFAULT_LANGDSC','زبان مورد نظر خود را برای نمایش پیام های بعد از عنلکرد فایل common.php مشخص کنید');

define($constpref.'_RELIABLE_IPS','IP های قابل اطمینان');
define($constpref.'_RELIABLE_IPSDSC','IP ها را با | از هم جدا کنید . ابتدا ردیف را با ^ مشخص کنید, انتهای ردیف را با $.');

define($constpref.'_LOG_LEVEL','درجه ثبت کردن وقایع');
define($constpref.'_LOG_LEVELDSC','');

define($constpref.'_BANIP_TIME0','زمان تعلیق IP های غیر مجاز (ثانیه)');

define($constpref.'_LOGLEVEL0','هیچ');
define($constpref.'_LOGLEVEL15','آرام');
define($constpref.'_LOGLEVEL63','بی صدا');
define($constpref.'_LOGLEVEL255','کامل');

define($constpref.'_HIJACK_TOPBIT','تعداد بیت های IP که در هر جلسه محافظت میشود');
define($constpref.'_HIJACK_TOPBITDSC','مقابله با دزدی جلسه (Anti Session Hi-Jacking):<br />به طور پیش فرض 32(bit). (تمام بیت ها حفاظت شده است)<br />وقتی IP شما پایدار نیست, محدوده ی IP خود را باعددی مشخص از بیت ها تنظیم کنید.<br /> اگر IP شما در محدوده ای بین 192.168.0.0 تا 192.168.0.255 میتواند تغییر کنید عدد 24 ( بیت) را انتخاب کنید <br /><br />Session Hi-Jacking(دزدی جلسه): در اصل ترکیب ماهرانه ای است از دو نوع حمله ی دزدین IP و استراق سمع با این روش حمله یک مهاجم خودش را به جای شما جا زده و اقدام به ادامه دادن کار ها و فعالیت های شما در سایت مطابق خواسته ی خود میکند البته در حدی که شما دسترسی دارید');
define($constpref.'_HIJACK_DENYGP','گره های که IP اجازه ی تغییر در جلسه ( بین کاربر و زوپس) را ندارد');
define($constpref.'_HIJACK_DENYGPDSC','مقابله با دزدی جلسه (Anti Session Hi-Jacking):<br />گروه های که IP شان در جلسه بین زوپس و کاربر نباید تغییر کند انتخاب کنید.<br />(پیشنهاد میشود فقط برای وب مستر ها روشن باشد.)<br /><br />Session Hi-Jacking(دزدی جلسه): در اصل ترکیب ماهرانه ای است از دو نوع حمله ی دزدین IP و استراق سمع با این روش حمله یک مهاجم خودش را به جای شما جا زده و اقدام به ادامه دادن کار ها و فعالیت های شما در سایت مطابق خواسته ی خود میکند البته در حدی که شما دسترسی دارید');
define($constpref.'_SAN_NULLBYTE','پاک سازی بایت ها خالی');
define($constpref.'_SAN_NULLBYTEDSC','اگر در کارکتر های پایان بخش به طور مکرر از "\\0" استفاده شده بود  این حرکت  یک حمله ثبت شود<br />بایت های خالی با  فشار دادن کلید  space تغییر میکند<br />(به شدت توصیه میشود این گزینه روشن باشد)');
define($constpref.'_DIE_NULLBYTE','اگر بایت های خالی پیدا شد کاربر از سایت  بیرون انداخته شود');
define($constpref.'_DIE_NULLBYTEDSC','The terminating character "\\0" is often used in malicious attacks.<br />(قویا توصیه میشود این گزینه روشن باشد)');
define($constpref.'_DIE_BADEXT','اگر کاربر فایل   بد  بارگذای کرد از سایت بیرون انداخته شود');
define($constpref.'_DIE_BADEXTDSC','اگر به فایل بارگذاری شده به وسیله ی کاربر لینک .php بدی الحاق شده بود  , این ماژول کاربر را از سایت زوپس شما بیرون می اندازد.<br />اگر شما بکرات فایل های php  به ماژول های PukiWiki و B-Wiki اضافه میکنید این گزینه را خاموش کنید');
define($constpref.'_CONTAMI_ACTION','نوع عملکرد اگر یک  آلودگی پیدا شد');
define($constpref.'_CONTAMI_ACTIONDS','رفتاری را که میخواید در برابر کاربری که قصد وارد کمردن داده های نا مناسب به سیستم زوپس شما را دارد انجام شود مشخص کنید.<br />(پیشنهاد میشود بر روی صفحه ی سفید قرار دهید)');
define($constpref.'_ISOCOM_ACTION','نوع عملکرد وقتی نظرات منفرد به سیستم وارد شد');
define($constpref.'_ISOCOM_ACTIONDSC','جلوگیری ازوارد کردن داده های خطر ناک به SQL:<br />این گزینه را فعال کنید  برای وقتی که  علامت ها "/*" به صورت منفرد پیدا شد<br />معنی " پاک سازی داده ها از کد های خطرناک "در اینجا این است که علامت های "*/" جداگانه در جدول های پایگاه داده ها اضافه شوند<br />(سفارش میشود تنظیمها بر روی ((پاک سازی داده ها از کد های خطرناک)) باشد)');
define($constpref.'_UNION_ACTION','عملکرد وقتی که یک UNION پیدا شد');
define($constpref.'_UNION_ACTIONDSC','جلوگیری از وارد کردن داده های خطر ناک به SQL:<br />نوع عملکرد را وقتی ترکیب هم جنسی از پیوند به SQL پیدا شد مشخص کنید<br />معنی "پاک سازی داده ها از کد های خطرناک" این است کهunion" " را به "uni-on" تغییر میدهد . یعنی اتحاد ترکیبات هم  جنس را  از بین میبرد و مانع ورود داده های خطر ناک به پایگاه داده ها گردد<br />(سفارش میشود تنظیمها بر روی ((پاک سازی داده ها از کد های خطرناک)) باشد)');
define($constpref.'_ID_INTVAL','مقدار های عددی را حتما به متغیر های عددی تفسیر کن');
define($constpref.'_ID_INTVALDSC','تمام در خواست های که نام "*id" را دارند یک عدد صحیح در نظر گرفته شود.یعنی مانع قرار دادن هر چیز به جای عدد در جلوی  id  میشود<br />این گزینه از فرم شما در برابر   وارد کردن بعضی  از داده های XSS و SQL محافظت میکند<br />این گزینه در حالت پیش فرض روشن میباشد اما ممکن است  باعث بروز مشکل در برخی از ماژول ها شود.');
define($constpref.'_FILE_DOTDOT','حفاظت در برار پیمایش شاخه ها');
define($constpref.'_FILE_DOTDOTDSC','تمام نشانه های ".." را از همه درخواست های مشابه پیمایش شاخه ها حذف کن ');

define($constpref.'_BF_COUNT','Anti Brute Force');
define($constpref.'_BF_COUNTDSC','تعداد دفعاتی را که کاربر مهمان میتواند نام کاربری و پسورد خود رادر مدت 10 دقیقه وارد کند  مشخص کنید اگر او بعد از گذشت این زمان موفق به ورود (login) نشد IP او توسط سیستم بسته شده است<br /><br />این روش هک روشی است که در آن هکر یک سری کلمات را به عنوان پسورددر نرم افزار هک وارد کرده و این کلمات به ترتیب به عنوان پسورد کاربر چک میشود');

define($constpref.'_BWLIMIT_COUNT','محدودیت پهنای باند');
define($constpref.'_BWLIMIT_COUNTDSC','بیشترین دسترسی به mainfile.php را در زمان مشخص شده تعیین کنید. این مقدار در زمان های که پهنای باند کافی CPU را در اختیار دارید بهتر است بر روی ۰ باشد. عدد کمتر از ۱۰ در نظر گرفته نمیشود.');

define($constpref.'_DOS_SKIPMODS','ماژول های که در برابر حملات DoS/Crawler چک نمیشوند');
define($constpref.'_DOS_SKIPMODSDSC','نام ماژول ها را با | از هم جدا کنید.همچنین این اجازه برای ماژول ها ی چپ مفید خواهد بود.<br />منظور از Crawler روبات های خزنده ی موتور های جستجوگر میباشد');

define($constpref.'_DOS_EXPIRE','مدت زمان برای لود ( بار گذاری) بیش از اندازه (ثانیه)');
define($constpref.'_DOS_EXPIREDSC','این گزینه مشخص میکند زمان درخواست های لود شدن مکرر (F5 attack) و بار گذاری بیش از حد رباط های خزنده موتور های جستجو چقدر باشد.');

define($constpref.'_DOS_F5COUNT','تعداد ریفریش ها با F5 که یک حمله حساب میشود');
define($constpref.'_DOS_F5COUNTDSC','از حملات DoS پیشگیری میکند.<br />تعداد مجاز ((دوباره بارگذاری کردن)) صفحات را مشخص کنید  بیش از این تعداد حمله  حساب میشود.');
define($constpref.'_DOS_F5ACTION','عکس العمل در برابر حمله با F5');

define($constpref.'_DOS_CRCOUNT','شمار غیر صحیح روبات های خزنده');
define($constpref.'_DOS_CRCOUNTDSC','جلو گیری کردن از لود بیش از حد  به وسیله ی روبات های خزنده ی موتور های جستجو.<br />عدد وارد شده تعداد  مناسب ورود ربات های جستگور به سایت میباشد تعداد بیش از این یک حرکت غلط حساب میشود.');
define($constpref.'_DOS_CRACTION','عکس العمل در برابر لود بیش از حد به وسیله ی روبات های خزنده ی موتور های جستجو');

define($constpref.'_DOS_CRSAFE','رباط های خزنده مجاز به ورود');
define($constpref.'_DOS_CRSAFEDSC','روش به کار رفته برای جدا سازی رباط های خزنده متور های جستجو (User-Agent) از هم.<br />اگر به خوبی تنظیم شود ( خزنده در لیست رو به رو باشد), رابط های خزنده نمیتوانند لود بیش از حد ایجاد کنند.<br />نمونه :) /(msnbot|Googlebot|Yahoo! Slurp)/i');

define($constpref.'_OPT_NONE','هیچکدام( فقط گزارش)');
define($constpref.'_OPT_SAN','پاک سازی داده ها از کد های خطرناک ');
define($constpref.'_OPT_EXIT','صفحه ی سفید');
define($constpref.'_OPT_BIP','بستن IP (بدون محدودیت)');
define($constpref.'_OPT_BIPTIME0','بستن IP (مدت دار)');

define($constpref.'_DOSOPT_NONE','هیچکدام( فقط گزارش)');
define($constpref.'_DOSOPT_SLEEP','خواب');
define($constpref.'_DOSOPT_EXIT','صفحه ی سفید');
define($constpref.'_DOSOPT_BIP','بستن IP (بدون محدودیت)');
define($constpref.'_DOSOPT_BIPTIME0','بستن IP (مدت دار)');
define($constpref.'_DOSOPT_HTA','دفع کردن به وسیله .htaccess(آزمایشی)');

define($constpref.'_BIP_EXCEPT','گروه های که هیچ وقت جز IP بد ثبت نمیشوند');
define($constpref.'_BIP_EXCEPTDSC','کاربرانی که جز گروه انتخابی شما هستند هیچ وقت بن نمیشوند.<br />(پیشنهاد میشود فقط برای وب مستر ها روشن باشد)');

define($constpref.'_DISABLES','خصوصيات خطرناک در زوپس رو غير فعال کن');

define($constpref.'_BIGUMBRELLA','فعال کردن anti-XSS( محافظ بزرگ)');
define($constpref.'_BIGUMBRELLADSC','این گزینه از شما در برابر اکثر حمله های که  بر اساس آسیب پذیری XSS برنامه ریزی شده اند محافظت میکند. اما 100% نیست<br /><br />حملات XSS شبیه به حملات تزیق اسکریپ میباشد و هدف اصلی از آن هک کردن سایت نیست بلکه حمله به کاربران است در این نوع حمله مهاجم کد های خطر ناکی را در صفحات سایت وارد میکند که این کد ها کامپیوتر کاربر را آلوده میکند');

define($constpref.'_SPAMURI4U','محافظ- هرزنامه (anti-SPAM): تعداد لینک ها برای کابر معمولی');
define($constpref.'_SPAMURI4UDSC','اگر به تعداد عدد مشخص شده در کادر مقابل لینک در پست کاربر پیدا شد با پست او به عنوان هرز نامه برخورد گردد. با انتخاب صفر این گزینه را غیر فعال کنید.');
define($constpref.'_SPAMURI4G','محافظ- هرزنامه (anti-SPAM): تعدا لینک ها برای گروه ها');
define($constpref.'_SPAMURI4GDSC','اگر به تعداد عدد مشخص شده در کادر مقابل لینک در پست گروه ها پیدا شد با پست او به عنوان هرز نامه برخورد گردد. با انتخاب صفر این گزینه را غیر فعال کنید.');

}

?>