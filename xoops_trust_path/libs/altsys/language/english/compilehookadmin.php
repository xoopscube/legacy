<?php

define( '_TPLSADMIN_INTRO', 'Introducing the Template Compilation Hook');

define( '_TPLSADMIN_DESC', '
Compile hooks provide an easy way to insert visual editing helpers into your templates and collect Smarty variables. 
These features are accessible only within frontend templates and duplicatable modules, for which they have been written. ');

define( '_TPLSADMIN_NOTE', 'Important : Although the visual helpers are intended to highlight the structure of your layout and templates, 
there are limitations to feature-based recognition, for example, of components and custom templates! ');

define( '_TPLSADMIN_TASK_Title', 'Why and when to perform this task!');
define( '_TPLSADMIN_TASK', '
You can use the compiled templates to complete the following tasks:<br>
<ul>
<li>structural overview facilitating the recognition of functional design flaws</li>
<li>insert overlay elements which are rendered for each included component and template
<li>insert code comments to facilitate source code editing</li>
<li>detect and resolve differences between the design of a template and its implementation.</li> 
<li>generate application code used in templates and collect the Smarty variables.</li>
</ul>');

define( '_TPLSADMIN_CACHE_TITLE', 'Compiled Templates');
define( '_TPLSADMIN_CACHE_DESC' , 'The source templates remains unchanged, in most cases, you can delete all compiled template files and execute <b>Normalise</b>. The templates removed from the cache are immediately regenerated.' );

define( '_TPLSADMIN_FMT_MSG_ENCLOSEBYCOMMENT' , '%d cached templates wrapped with tplsadmin comments.');
define( '_TPLSADMIN_DT_ENCLOSEBYCOMMENT' , 'Add comments into the source code');
define( '_TPLSADMIN_DD_ENCLOSEBYCOMMENT' , 'Add HTML comments at the beginning and end of each template. Since it does not affect the design, it is recommended for source code editing.');
define( '_TPLSADMIN_CNF_ENCLOSEBYCOMMENT' , 'Wrap cached templates with a comment "tplsadmin". Confirm to proceed or cancel!');


define( '_TPLSADMIN_FMT_MSG_ENCLOSEBYBORDEREDDIV' , '%d cached templates were wrapped in div tags.');
define( '_TPLSADMIN_DT_ENCLOSEBYBORDEREDDIV' , 'Add div tags around templates.');
define( '_TPLSADMIN_DD_ENCLOSEBYBORDEREDDIV' , 'Each template is wrapped with a div tag and a link to the edit controller. While this affects the overall design, you can easily identify the template you want to edit.');
define( '_TPLSADMIN_CNF_ENCLOSEBYBORDEREDDIV' , 'Wrap cached templates with div tags. Confirm to proceed or cancel !');

define( '_TPLSADMIN_FMT_MSG_HOOKSAVEVARS' , '%d logic implemented in the compiled cache to collect template variables.');
define( '_TPLSADMIN_DT_HOOKSAVEVARS' , 'Insert logic to collect template variables');
define( '_TPLSADMIN_DD_HOOKSAVEVARS' , 'The first step of getting the information of templates variables in your site. The template vars infos will be collected when the front-end is displayed. If all templates you want to edit are displayed, get template vars info by underlying buttons.');
define( '_TPLSADMIN_CNF_HOOKSAVEVARS' , 'Templates compiled in cache will implement the logic to collect model variables. Do you want to continue?');

define( '_TPLSADMIN_FMT_MSG_REMOVEHOOKS' , '%d template caches have been normalized');
define( '_TPLSADMIN_DT_REMOVEHOOKS' , 'Normalize compiled template caches');
define( '_TPLSADMIN_DD_REMOVEHOOKS' , 'This removes comments/div tags/logic implemented by hooks from each compiled template cache.');
define( '_TPLSADMIN_CNF_REMOVEHOOKS' , 'Confirm to proceed and remove the hooks!');

define( '_TPLSADMIN_MSG_CLEARCACHE' , 'Cached templates were removed !');
define( '_TPLSADMIN_MSG_CREATECOMPILECACHEFIRST' , 'There are no compiled templates in cache. First, create the compiled templates in cache by browsing to the public side of your site.');

define( '_TPLSADMIN_CNF_DELETEOK' , 'Confirm to proceed and Delete!');

define( '_TPLSADMIN_DT_GETTPLSVARSINFO_DW' , 'Generate DreamWeaver Extension with template variables');
define( '_TPLSADMIN_DD_GETTPLSVARSINFO_DW' , 'Ensure that the extension is supported on the version of the application on which it is being installed. Open the Extension Manager.<br>Extract the download archive.<br>Run the files with the .mxi extension and follow the installation dialogues.<br>The snippets for template variables of your site will be usable after restarting DreamWeaver.<br> Learn how to use add-ons or extensions : https://helpx.adobe.com/ie/dreamweaver/using/extensions.html');

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

define( '_TPLSADMIN_NUMCAP_COMPILEDCACHES' , 'Templates compiled in the cache directory');
define( '_TPLSADMIN_NUMCAP_TPLSVARS' , 'Templates compiled with Smarty variables');
