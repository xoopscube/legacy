<?php

define( '_TPLSADMIN_FMT_MSG_ENCLOSEBYCOMMENT' , '%d template caches have been enclosed by tplsadmin comments');
define( '_TPLSADMIN_DT_ENCLOSEBYCOMMENT' , 'Insert comments');
define( '_TPLSADMIN_DD_ENCLOSEBYCOMMENT' , 'Two HTML comments will be inserted at the beginning and at the end of each template.<br>
Since this rarely breaks the layout of the template, this option is recommended in order to help designers identify components during the design process.');
define( '_TPLSADMIN_CNF_ENCLOSEBYCOMMENT' , 'Compiled template caches will be enclosed by tplsadmin comments. Confirm to proceed or cancel !');


define( '_TPLSADMIN_FMT_MSG_ENCLOSEBYBORDEREDDIV' , '%d template caches have been enclosed by div tags');
define( '_TPLSADMIN_DT_ENCLOSEBYBORDEREDDIV' , 'Insert div tags');
define( '_TPLSADMIN_DD_ENCLOSEBYBORDEREDDIV' , 'Each template will be enclosed by black-bordered div tags. A link for editing controller of tplsadmin will be added into each templates. Though this can often break the design, you can easily edit each template instantly.');
define( '_TPLSADMIN_CNF_ENCLOSEBYBORDEREDDIV' , 'Compiled template caches will be enclosed by div tags. Confirm to proceed or cancel !');

define( '_TPLSADMIN_FMT_MSG_HOOKSAVEVARS' , '%d template caches have been modified hooking logic to collect template variables');
define( '_TPLSADMIN_DT_HOOKSAVEVARS' , 'Insert logic to collect template variables');
define( '_TPLSADMIN_DD_HOOKSAVEVARS' , 'The first step of getting the information of templates variables in your site. The template vars infos will be collected when the front-end is displayed. If all templates you want to edit are displayed, get template vars info by underlying buttons.');
define( '_TPLSADMIN_CNF_HOOKSAVEVARS' , 'Compiled template caches will be implanted the logics to collect template variables. Are you OK?');

define( '_TPLSADMIN_FMT_MSG_REMOVEHOOKS' , '%d template caches have been normalized');
define( '_TPLSADMIN_DT_REMOVEHOOKS' , 'Normalize compiled template caches');
define( '_TPLSADMIN_DD_REMOVEHOOKS' , 'This removes comments/div tags/logic implemented by hooks from each compiled template cache.');
define( '_TPLSADMIN_CNF_REMOVEHOOKS' , 'Confirm to proceed and remove the hooks!');


define( '_TPLSADMIN_MSG_CLEARCACHE' , 'Template caches were removed.');
define( '_TPLSADMIN_MSG_CREATECOMPILECACHEFIRST' , 'There are no compiled template in cache. First, create the compiled templates in cache by browsing to the public side of your site.');

define( '_TPLSADMIN_CNF_DELETEOK' , 'Confirm to proceed and Delete!');


define( '_TPLSADMIN_DT_GETTPLSVARSINFO_DW' , 'Get info of template variables as DreamWeaver Extensions');
define( '_TPLSADMIN_DD_GETTPLSVARSINFO_DW' , 'Open Macromedia Extension Manager, first.<br>Extract the download archive.<br>Run the files with the .mxi extension and follow the installation dialogues.<br>The snippets for template variables of your site will be usable after restarting DreamWeaver.');

define( '_TPLSADMIN_DT_GETTEMPLATES' , 'Download templates');
define( '_TPLSADMIN_DD_GETTEMPLATES' , 'Select a set before pushing either button');

define( '_TPLSADMIN_FMT_MSG_PUTTEMPLATES' , '%d templates are imported.');
define( '_TPLSADMIN_DT_PUTTEMPLATES' , 'Upload templates');
define( '_TPLSADMIN_DD_PUTTEMPLATES' , 'Select a template set you want to upload/overwrite.<br>Select the file <b>tar</b> archive including the template files (.html)<br>Automatically extracts all files  from the archive to their absolute location no matter the tree structure.');


define( '_TPLSADMIN_ERR_NOTUPLOADED' , 'No files are uploaded.');
define( '_TPLSADMIN_ERR_EXTENSION' , 'This extension cannot be recognized.');
define( '_TPLSADMIN_ERR_INVALIDARCHIVE' , 'The archive is not extractable.');
define( '_TPLSADMIN_ERR_INVALIDTPLSET' , 'Invalid set name has been specified.');

define( '_TPLSADMIN_ERR_NOTPLSVARSINFO' , 'There are no template vars info files.');

define( '_TPLSADMIN_NUMCAP_COMPILEDCACHES' , 'Compiled template caches');
define( '_TPLSADMIN_NUMCAP_TPLSVARS' , 'Template vars info files');
