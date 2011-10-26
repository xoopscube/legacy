<?php

// definitions for editing blocks

// Appended by Xoops Language Checker -GIJOE- in 2008-09-17 13:09:55
define('_MB_PICO_PROCESSBODY','پیمایش بدنه سند دینامیک');

define('_MB_PICO_TAGSNUM','نمایش');
define('_MB_PICO_TAGSLISTORDER','سفارشی کردن برای نمایش');
define('_MB_PICO_TAGSSQLORDER','سفارشی کردن برای تهیه خروجی');

define("_MB_PICO_CATLIMIT","تعیین شاخه");
define("_MB_PICO_CATLIMITDSC","بلوک تمام شاخه‌ها را در بر میگیرد.  عدد صفر برای شاخه‌ی اصلی است  . شاخه هایی را که میخواهید در بلوک نمایش داده شود  با وارد کردن عدد مربوطه ( وزن چینش) مشخص کرده و با کاما عدد‌ها را از هم جدا کنید");
define('_MB_PICO_PARENTCAT','شاخه مادر');
define('_MB_PICO_PARENTCATDSC','زیر شاخه‌های که مستقیما وابسته اند به این شاخه مادر نمایش داده میشوند. شما عدد شاخه‌های مادر را مشخص کنید و آنها را با کاما از هم جدا کنید .');
define("_MB_PICO_SELECTORDER","سفارش به وسیله‌ی");
define("_MB_PICO_CONTENTSNUM","تعداد مواردی که نمایش داده میشود");
define("_MB_PICO_THISTEMPLATE","الگوی (منبع) بلوک");
define('_MB_PICO_DISPLAYBODY','بدنه سند را نیز نمایش بده');
define("_MB_PICO_CONTENT_ID","ش.ش سند ");
define('_MB_PICO_TAGSNUM','نمایش');
define('_MB_PICO_TAGSLISTORDER','سفارشی کردن برای نمایش');
define('_MB_PICO_TAGSSQLORDER','سفارشی کردن برای تهیه خروجی');

// LTR or RTL
if( defined( '_ADM_USE_RTL' ) ) {
	@define( '_ALIGN_START' , _ADM_USE_RTL ? 'right' : 'left' ) ;
	@define( '_ALIGN_END' , _ADM_USE_RTL ? 'left' : 'right' ) ;
} else {
	@define( '_ALIGN_START' , 'right' ) ; // change it right for RTL
	@define( '_ALIGN_END' , 'left' ) ;  // change it left for RTL
}



