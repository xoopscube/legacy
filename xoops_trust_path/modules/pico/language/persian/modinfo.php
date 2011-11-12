<?php

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'pico' ;
$constpref = '_MI_' . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {





// Appended by Xoops Language Checker -GIJOE- in 2009-01-18 18:29:24
define($constpref.'_COM_ORDER','سفارشی کردن سیستم یکپارچه سازی نظر ها');
define($constpref.'_COM_POSTSNUM','بیشترین پست های نمایش داده شده در سیستم یکپارچه سازی نظر ها');

// Appended by Xoops Language Checker -GIJOE- in 2008-12-02 16:22:07
define($constpref.'_AUTOREGISTCLASS','نام کلاس برای ثبت / عدم ثبت فایل های HTML بسته بندی شده');

// Appended by Xoops Language Checker -GIJOE- in 2008-11-19 04:29:54
define($constpref.'_ADMENU_TAGS','تگ ها');

// Appended by Xoops Language Checker -GIJOE- in 2008-10-01 12:11:21
define($constpref.'_URIM_CLASS','class mapping URI');
define($constpref.'_URIM_CLASSDSC','ارگ شما میخواهید URI Mapper را باطل کنید این گزینه را ویرایش کنید. The default value is PicoUriMapper');

define( $constpref.'_LOADED' , 1 ) ;

// The name of this module
define($constpref."_NAME","پیکو");

// A brief description of this module
define($constpref."_DESC","ماژولی برای ساخت اسناد استاتیک");

// admin menus
define( $constpref.'_ADMENU_CONTENTSADMIN' , 'لیست اسناد' ) ;
define( $constpref.'_ADMENU_CATEGORYACCESS' , 'دسترسی شاخه‌ها' ) ;
define( $constpref.'_ADMENU_IMPORT' , 'وارد کردن / هم زمان کردن' ) ;
define($constpref.'_ADMENU_EXTRAS','اضافی');
define($constpref.'_ADMENU_MYLANGADMIN','تنظیمات زبان');
define($constpref.'_ADMENU_MYTPLSADMIN','الگو‌ها');
define($constpref.'_ADMENU_MYBLOCKSADMIN','بلوک‌ها / دسترسی‌ها');
define($constpref.'_ADMENU_MYPREFERENCES','ویژگی‌ها');

// configurations
define($constpref.'_USE_WRAPSMODE','قرار گرفتن در ماژول منتظر‌ها برای تایید');
define($constpref.'_USE_REWRITE','فعال کردن روش mod_rewrite');
define($constpref.'_USE_REWRITEDSC','بستگی به محیط شما دارد. اگر این گزینه را فعال کنید, نام آدرس‌های .htaccess.rewrite_wraps(with wraps) یا htaccess.rewrite_normal(without wraps) یا .htaccess در XOOPS_ROOT_PATH/modules/(dirname)/ تغییر میکند');
define($constpref.'_WRAPSAUTOREGIST','فعال سازی ثبت خودکار صفحات HTML در پایگاه داده‌ها');
define($constpref.'_TOP_MESSAGE','توضیحات شاخه‌ی اصلی');
define($constpref.'_TOP_MESSAGEDEFAULT','');
define($constpref.'_MENUINMODULETOP','نمایش منو در صفحه‌ی اصلی ماژول');
define($constpref.'_LISTASINDEX',"نمایش فهرست اسناد در شاخه‌ی اصلی");
define($constpref.'_LISTASINDEXDSC','با انتخاب بله لیست  اسناد به صورت خودکار در شاخه‌ی اصلی قرار میگیرد.   اگر نه را انتخاب کنید اسناد  بر اساس اولیت خود نمایش داده میشوند');
define($constpref.'_SHOW_BREADCRUMBS','نمایش مسیر صفحه (breadcrumbs)');
define($constpref.'_SHOW_PAGENAVI','نمایش صفحه‌ی راهبری');
define($constpref.'_SHOW_PRINTICON','نمایش آیکن  چاپگر');
define($constpref.'_SHOW_TELLAFRIEND','نمایش آیکن تماس با دوستان');
define($constpref.'_SEARCHBYUID','فعال کردن صفحات ساخته شده برای سازنده');
define($constpref.'_SEARCHBYUIDDSC','قرار دادن لیست اسناد  در پروفایل سازنده‌ی سند. اگر از این  ماژول برای ساخت صفحات استاتیک استفاده میکنید این گزینه را خاموش کنید.');
define($constpref.'_USE_TAFMODULE','استفاده از ماژول تماس با دوستان ');
define($constpref.'_FILTERS','تنظیمات پیش فرض فیلتر');
define($constpref.'_FILTERSDSC','کلمات انتخابی را با | از هم جدا کنید(pipe)');
define($constpref.'_FILTERSDEFAULT','htmlspecialchars|smiley|xcode|nl2br');
define($constpref.'_FILTERSF','فیلتر‌های اجباری');
define($constpref.'_FILTERSFDSC','فیلتر‌های ورودی را به وسیله‌ی , ( کاما) از هم جدا کنید. فیلتر:LAST به این معنیست که در حالت آخرین فیلتر‌ها درست عمل شده است. مابقی فیلتر‌ها هم در فاز اول درست عمل کرده اند.');
define($constpref.'_FILTERSP','فیلتر‌های ممنوع');
define($constpref.'_FILTERSPDSC','فیلتر‌های ورودی را به وسیله‌ی , ( کاما) از هم جدا کنید');
define($constpref.'_SUBMENU_SC','نمایش اسناد در یک زیر منو ');
define($constpref.'_SUBMENU_SCDSC','به طور پیش فرض فقط شاخه‌ها نمایش داده میشوند. اگر این گزینه را فعال کنید اسناد که گزینه‌ی "نمایش در منو" آن‌ها فعال است هم در  منو‌ی اصلی سایت نمایش داده میشوند');
define($constpref.'_SITEMAP_SC','نمایش اسناد در ماژول نقشه‌ی سایت');
define($constpref.'_USE_VOTE','فعال سازی قابلیت رای دادن');
define($constpref.'_GUESTVOTE_IVL','رای دادن مهمان‌ها');
define($constpref.'_GUESTVOTE_IVLDSC',' با انتخاب 0 امکان رای دادن مهمان‌ها را بگیرید. بقیه‌ی اعداد زمان(ثانیه) رای دادن هر ip میباشد');
define($constpref.'_HTMLHEADER','سرفصل HTML مشترک');
define($constpref.'_ALLOWEACHHEAD','سر فصل HTML اختصاصی برای هر سند');
define($constpref.'_CSS_URI','آدرس پرونده‌های CSS در ماژول');
define($constpref.'_CSS_URIDSC','مسیر داخلی( داخل ماژول) یا خارجی( از جای دیگر) قابل تنضیم است. مسیر پیش فرض: {mod_url}/index.php?page=main_css');
define($constpref.'_IMAGES_DIR','محل قرار گیری تصاویر');
define($constpref.'_IMAGES_DIRDSC','مسیر مورد نظر را در شاخه‌های ماژول تنظیم کنید. پیش فرض: images');
define($constpref.'_BODY_EDITOR','ویرایشگر متن اصلی( بدنه‌ی اصلی)');
define($constpref.'_HTMLPR_EXCEPT','گروه‌های که میتوانند از پالایش به وسیله پالایشگر HTML اجتناب کنند');
define($constpref.'_HTMLPR_EXCEPTDSC','پست‌های که به وسیله کاربرانی که جز گروه‌های مشخص شده رو به رو نیستند ارسال شده است باید  مطابق اصول امنیتی HTML به وسیله پالایشگر HTML  ، پالایش شود  در پروتکتو >=3.14. این ویرایشگر در PHP4 کار نمیکند');
define($constpref.'_HISTORY_P_C','چه تعداد اصلاح (سند) در پایگاه داده‌ها ذخیره شود');
define($constpref.'_MLT_HISTORY','کمترین عمر هر اصلاح ( ثانیه)');
define($constpref.'_BRCACHE','زمان نگاه داری پرونده‌ی ذخیره‌ساز برای  پرونده‌های تصویری (فقط در حالت wraps)');
define($constpref.'_BRCACHEDSC','پرونده‌های به غیر از آن HTML توسط مرور گر شما در مدت زمان مشخص شده بر حسب ثانیه ذخیره‌ساز میشوند (0 به معنی غیر فعال است)');
define($constpref.'_EF_CLASS','کلاس برای extra_fields');
define($constpref.'_EF_CLASSDSC','وقتی این گزینه را تغییر دهید که به یک handler ( دسته گذار ) مفید تر برای extra_fields نیاز دارید. مقدار پیش فرض : PicoExtraFields');
define($constpref.'_EFIMAGES_DIR','شاخه extra_fields');
define($constpref.'_EFIMAGES_DIRDSC','یک مسیر در XOOPS_ROOT_PATH را مشخص کنید. ابتدا آن را بسازید و به آن دسترسی 777 بدهید. شاخه پیش فرض) uploads/(module dirname)');
define($constpref.'_EFIMAGES_SIZE','ابعدا تصویر اضافی');
define($constpref.'_EFIMAGES_SIZEDSC','(عرض اصلی)x(ارتفاع اصلی) (عرض کوچیک)x(ارتفاع کوچیک) مقدار پیش فرض) 480x480 150x150');
define($constpref.'_IMAGICK_PATH','مسیر کتابخانه گرافیکی ImageMagick');
define($constpref.'_IMAGICK_PATHDSC','در حالت عادی این قسمت را خالی بگذارید, و یا آن را مشابه مثال رو به رو تنظیم کنید /usr/X11R6/bin/');
define($constpref.'_COM_DIRNAME','یکسان سازی پیام ها: نام انجمن در d3forum');
define($constpref.'_COM_FORUM_ID','یکسان سازی پیام ها:ش.ش انجمن ');
define($constpref.'_COM_VIEW','دیدن نظر‌های یکپارچه');

// blocks
define($constpref.'_BNAME_MENU','منو');
define($constpref.'_BNAME_CONTENT','سند');
define($constpref.'_BNAME_LIST','لیست');
define($constpref.'_BNAME_SUBCATEGORIES','زیر شاخه‌ها');
define($constpref.'_BNAME_MYWAITINGS','پست‌های منتظر تایید من');
define($constpref.'_BNAME_TAGS','کلمات کلیدی');

// Notify Categories
define($constpref.'_NOTCAT_GLOBAL', 'سراسری');
define($constpref.'_NOTCAT_GLOBALDSC', 'اطلاع رسانی در مورد این ماژول');
define($constpref.'_NOTCAT_CATEGORY','شاخه');
define($constpref.'_NOTCAT_CATEGORYDSC',' اطلاع رسانی در مورد این شاخه');
define($constpref.'_NOTCAT_CONTENT','سند');
define($constpref.'_NOTCAT_CONTENTDSC', ' اطلاع رسانی در مورد این سند');

// Each Notifications
define($constpref.'_NOTIFY_GLOBAL_WAITINGCONTENT', 'منتظر‌ها برای تایید');
define($constpref.'_NOTIFY_GLOBAL_WAITINGCONTENTCAP', 'چنانچه تغییر و یا پستی منتظر تایید است من را با خبر کن(فقط برای اطلاع رسانی مدیران و وبمستران)');
define($constpref.'_NOTIFY_GLOBAL_WAITINGCONTENTSBJ', '[{X_SITENAME}] {X_MODULE}: waiting');
define($constpref.'_NOTIFY_GLOBAL_NEWCONTENT','سندی جدید');
define($constpref.'_NOTIFY_GLOBAL_NEWCONTENTCAP','اگر یک سند جدید ثبت شد من را با خبر کن. (فقط برای سند‌ها مجاز باشد)');
define($constpref.'_NOTIFY_GLOBAL_NEWCONTENTSBJ','[{X_SITENAME}] {X_MODULE} : سندی جدید');
define($constpref.'_NOTIFY_CATEGORY_NEWCONTENT','سند جدید');
define($constpref.'_NOTIFY_CATEGORY_NEWCONTENTCAP','وقتی یک سند جدید ثبت شد من را با خبر کن. (فقط نظر‌های تایید شده)');
define($constpref.'_NOTIFY_CATEGORY_NEWCONTENTSBJ','[{X_SITENAME}] {X_MODULE}:{CAT_TITLE} یک سند جدید {CONTENT_SUBJECT}');
define($constpref.'_NOTIFY_CONTENT_COMMENT','نظر جدید');
define($constpref.'_NOTIFY_CONTENT_COMMENTCAP','وقتی یک نظر جدید فرستاده شد من را با خبر کن. (فقط نظر‌های تایید شده)');
define($constpref.'_NOTIFY_CONTENT_COMMENTSBJ','[{X_SITENAME}] {X_MODULE} : یک نظر جدید');

}

?>