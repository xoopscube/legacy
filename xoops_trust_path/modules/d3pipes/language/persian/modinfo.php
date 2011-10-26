<?php

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'd3pipes' ;
$constpref = '_MI_' . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {




// Appended by Xoops Language Checker -GIJOE- in 2009-01-18 07:46:41
define($constpref.'_COM_ORDER','سفارشی کردن سیستم یکپارچه سازی نظر ها');

// Appended by Xoops Language Checker -GIJOE- in 2008-11-18 04:46:02
define($constpref.'_INDEXKEEPPIPE','نمایش لوله های به روز شده در صفحه اصلی این ماژول در صورت امکان');

// Appended by Xoops Language Checker -GIJOE- in 2008-05-20 05:59:23
define($constpref.'_COM_VIEW','نمایش سیستم یکپارچه سازی نظر ها');
define($constpref.'_COM_POSTSNUM','بیشترین تعداد پست های نمایش داده شده در سیستم یکپارچه سازی نظر ها');

define( $constpref.'_LOADED' , 1 ) ;

// The name of this module
define($constpref."_NAME","لوله ها دی 3");

// A brief description of this module
define($constpref."_DESC","ماژول متحد کننده قابل انعطاف");

// admin menus
define($constpref.'_ADMENU_PIPE','لوله ها') ;
define($constpref.'_ADMENU_CACHE','کش') ;
define($constpref.'_ADMENU_CLIPPING','گیره ها') ;
define($constpref.'_ADMENU_JOINT','اتصال بخش اول') ;
define($constpref.'_ADMENU_JOINTCLASS','گروه قسمت اول') ;
define($constpref.'_ADMENU_MYLANGADMIN','زبان') ;
define($constpref.'_ADMENU_MYTPLSADMIN','تمپلیت ها') ;
define($constpref.'_ADMENU_MYBLOCKSADMIN','بلاک ها / دسترسی ها') ;
define($constpref.'_ADMENU_MYPREFERENCES','ویژگی ها') ;

// blocks
define($constpref.'_BNAME_ASYNC','آخرین ورودی ها (Async)') ;
define($constpref.'_BNAME_SYNC','آخرین ورودی ها (Sync)') ;

// configs
define($constpref.'_INDEXTOTAL','مجموع ورودی ها در صفحه اصلی ماژول');
define($constpref.'_INDEXEACH','بیشترین ورودی ها از هر لوله در صفحه اصلی ماژول');
define($constpref.'_ENTRIESAPIPE','تعداد وردی های نمایش داده شده در هر لوله');
define($constpref.'_ENTRIESAPAGE','وارد کردن یک صفحه در فهرست گیره ها');
define($constpref.'_ENTRIESARSS','تعداد ورودی ها RSS/Atom');
define($constpref.'_ENTRIESSMAP','تعداد ورودی xml برای نقشه سایت که در گوگل اسکن شود');
define($constpref.'_ARCB_FETCHED','انقضا شدن اتوماتیک با آوردن زمان (روز)');
define($constpref.'_ARCB_FETCHEDDSC','تعداد روزی را که بعد از آن این گیره ات ( گیرهینگ ها) پاک شوند تعیین کنید. 0 به معنی غیر فعال کردن انقضا اتوماتیک است. کیلیپ های که نظر / هایلایت شده اند پاک نمیشوند.');
define($constpref.'_INTERNALENC','سیستم کد گذاری داخلی');
define($constpref.'_FETCHCACHELT','زمان مورد نیاز برای گرفتن یک مورد (ثانیه)');
define($constpref.'_REDIRECTWARN','آماده باش اگر آدرس rss/atom تغییر کرد');
define($constpref.'_SNP_MAXREDIRS','حداکثر تعداد ریدایرکت ها برای اسنوپی');
define($constpref.'_SNP_MAXREDIRSDSC','بعد از ساخت موفقیت آمیز لوله ها این گزینه را بر روی 0 بگذارید');
define($constpref.'_SNP_PROXYHOST','نام هاست سرور پروکسی');
define($constpref.'_SNP_PROXYHOSTDSC','با استفاده از FQDN مشخص کنید. به طور معمول آن را خالی بگذارید. یک FQDN محل یک کامپیوتر خاص را در DNS مشخص خواهد نمود. با استفاده از FQDN می توان بسادگی محل کامپیوتر در دامنه مربوطه را مشخص و به آن دستیابی نمود. FQDN یک نام ترکیبی است که در آن نام ماشین (Host) و نام دامنه مربوطه قرار خواهد گرفت .');
define($constpref.'_SNP_PROXYPORT','پرت سرویس دهنده پروکسی');
define($constpref.'_SNP_PROXYUSER','نام کاربری سرویس دهنده پروکسی');
define($constpref.'_SNP_PROXYPASS','واژه رمز سرویس دهنده پروکسی');
define($constpref.'_SNP_CURLPATH','مسیر curl (پیش فرض: /usr/bin/curl)');
define($constpref.'_TIDY_PATH',' مسیر tidy (پیش فرض: /usr/bin/tidy)');
define($constpref.'_XSLTPROC_PATH','مسیر xsltproc (پیش فرض: /usr/bin/xsltproc)');
define($constpref.'_UPING_SERVERS','به روز کردن پینگ (ping) سرور ها');
define($constpref.'_UPING_SERVERSDSC','Write a RPC end point starting with "http://" a line.<br />اگر شما میخواهید پینگ (ping) را تمدید کنید, حرف " E" را به آخر آدرس اضافه کنید.<br />RPC پروتکلی است که توسط سیستم عامل ویندوز استفاده می گردد . RPC ، یک مکانیزم ارتباطی را ارائه و این امکان را فراهم می نماید که برنامه در حال اجراء بر روی یک کامپیوتر قادر به اجراء کد موجود بر روی یک سیستم از راه دور گردد');
define($constpref.'_UPING_SERVERSDEF',"http://blogsearch.google.com/ping/RPC2 E\nhttp://rpc.weblogs.com/RPC2 E\nhttp://ping.blo.gs/ E");
define($constpref.'_CSS_URI','CSS آدرس');
define($constpref.'_CSS_URIDSC','مسیر داخلی( داخل ماژول) یا خارجی( از جای دیگر) قابل تنظیم است. مسیر پیش فرض: {mod_url}/index.css');
define($constpref.'_IMAGES_DIR','محل قرار گیری تصاویر');
define($constpref.'_IMAGES_DIRDSC','مسیر مورد نظر را در شاخه های ماژول تنظیم کنید. پیش فرض: images');
define($constpref.'_COM_DIRNAME','یکسان سازی پیام ها: نام انجمن در d3forum');
define($constpref.'_COM_FORUM_ID','یکسان سازی پیام ها:ID انجمن');

}


?>