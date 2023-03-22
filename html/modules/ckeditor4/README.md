![X-Updare Store](https://img.shields.io/website?down_color=red&down_message=Offline&label=X-Update%20Store&style=for-the-badge&up_color=308311&up_message=online&url=https%3A%2F%2Fxoopscube.xyz%2Fuploads%2Fxupdatemaster%2Fstores_json_V1.txt)
[![XOOPSCube powered-by-electricity](https://img.shields.io/badge/Powered%20by-Electricity-face74?style=for-the-badge&labelColor=203244&logo=data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxZW0iIGhlaWdodD0iMWVtIiB2aWV3Qm94PSIwIDAgMjQgMjQiPjxwYXRoIGZpbGw9IiNmYWNlNzQiIGQ9Ik0xNC42OSAyLjIxTDQuMzMgMTEuNDljLS42NC41OC0uMjggMS42NS41OCAxLjczTDEzIDE0bC00Ljg1IDYuNzZjLS4yMi4zMS0uMTkuNzQuMDggMS4wMWMuMy4zLjc3LjMxIDEuMDguMDJsMTAuMzYtOS4yOGMuNjQtLjU4LjI4LTEuNjUtLjU4LTEuNzNMMTEgMTBsNC44NS02Ljc2Yy4yMi0uMzEuMTktLjc0LS4wOC0xLjAxYS43Ny43NyAwIDAgMC0xLjA4LS4wMnoiLz48L3N2Zz4=)](https://github.com/xoopscube)
[![XCL](https://img.shields.io/badge/XCL-Made%20with%20passion-b0201d?style=for-the-badge&labelColor=991015&logo=data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxZW0iIGhlaWdodD0iMWVtIiB2aWV3Qm94PSIwIDAgMjQgMjQiPjxwYXRoIGZpbGw9IndoaXRlIiBkPSJtMTIgMjEuMzVsLTEuNDUtMS4zMkM1LjQgMTUuMzYgMiAxMi4yNyAyIDguNUMyIDUuNDEgNC40MiAzIDcuNSAzYzEuNzQgMCAzLjQxLjgxIDQuNSAyLjA4QzEzLjA5IDMuODEgMTQuNzYgMyAxNi41IDNDMTkuNTggMyAyMiA1LjQxIDIyIDguNWMwIDMuNzctMy40IDYuODYtOC41NSAxMS41M0wxMiAyMS4zNVoiLz48L3N2Zz4=)](https://github.com/xoopscube)

[![Project Status: Active – The project has reached a stable, usable state and is being actively developed.](https://www.repostatus.org/badges/2.0.0/active.svg)](https://github.com/xoopscube/xcl)
![License GPL](https://img.shields.io/badge/License-GPL-green)
![X-Updare Store](https://img.shields.io/badge/XOOPSCube%20Package-XCL-blue)

## ///// — CKEditor4 :: WYSIWYG editor + Web-based File manager

**CKEditor Wysiwig Editor for XCL. Full version with plugins for BBCode and X-elFinder File Manager**  

Test and customize here :    
[https://xoopscube.github.io/ckeditor4/](https://xoopscube.github.io/ckeditor4/)    


-----

MODULE |  CKEditor4
------------ | -------------
Description  | Battle-tested WYSIWYG editor with X-elFinder web-based file manager running on JavaScript + PHP.
Render Engine| Smarty v2 and XCube Layout
Version      | 4.20.2
Author       | @nao-pon Naoki Sawada
Maintainer   | Update @gigamaster Nuno Luciano (XCL7)
Copyright    | 2005-2023 Authors
License      | XCL module is distributed under a GPL 2.3 License.
License      | elFinder is distributed under a 3-Clause BSD License.


##### :computer: The Minimum Requirements



          Apache, Nginx, etc. PHP 7.2.x
          MySQL 5.6, MariaDB  InnoDB utf8 / utf8mb4
          XCL version 2.3.+



-----

## Overview

CKEditor 4 is built from plugins, just like XOOPSCube is built from modules, which makes it easy to create a custom build tailored to your needs. Nevertheless, to make the initial trial and installation process easier, from the three pre-configured installation packages (Basic, Standard and Full), we have chose to customize the Full package for learning about the available features and setups.

Following the recommendation of the CKEditor Team, the installation packages are just predefined setups that aim to satisfy some common use cases. It is always recommended to build a custom CKEditor4 package adjusted to your production website special needs.

## Features of module CKEditor4

The XOOPSCube module CKEditor4 is released by default with a customized Full package bundle and provides out of the box:

- **Control Panel** preferences settings e.g. toolbars, user group permissions, ui color.
- **Localization** languages installed by default : English, French, Japanese, Portuguese.
- **Template** a Single File Component for frontend and backend.
- **Automatic change of editor** HTML or BBCode depending on modules and user group permissions.
- **Automatic switch of ToolBar** based on modules preferences and user group permissions.
- **BBCode editor** extends the CKEditor standard bbcode plugin
- **HTML editor** with custom Toolbar for each user group
- **Extra Plugins** customized CodeMirror, oEmbed, Paste (raw text, formatted or code).
- **PHP mode** for PHP code blocks without the <?php opening tag.
- **Smarty mode** for Smarty Template Engine code blocks.
- **elFinder** open-source web file manager with cloud storage settings.



## Control Panel Preferences


![CKEditor Settings](https://raw.githubusercontent.com/xoopscube/ckeditor4/master/ckeditor-settings.png)    

**CKEditor's settings**   

- Toolbar UI Color
- "config.toolbar" for administrators (i.e webmaster)
- "config.toolbar" for special group (i.e. moderators)
- "config.toolbar" for registered users
- "config.toolbar" for guests (comments, forum)
- "config.extraPlugins"
- "config.customConfig"
- "config.enterMode"
- "config.shiftEnterMode"
- "config.allowedContent"
- "config.autoParagraph"

**X-elFinder web file manager**

- Set X-elFinder directory name for server browser.
- Upload Target of Drag & Drop (X-elFinder)
- Init image size of Drag & Drop(X-elFinder)


## Extra Plugins

[CKEditor-CodeMirror-Plugin](https://github.com/w8tcha/CKEditor-CodeMirror-Plugin) by @w8tcha  
Syntax Highlighting for the CKEditor (Source View and Source Dialog) with the CodeMirror Plugin

![Plugin CodeMirror](https://raw.githubusercontent.com/xoopscube/ckeditor4/b9c72dc150ecb490bf835222468a38d9d5249eb6/codemirror.png)

## Parameters
 
 If you're using the default  ``dhtmltarea `` you don't need to change the value.
 
 Otherwise you need to escape the value as follows:    

 ``<{ck4dhtmltarea value=VALUE|escape editor=html}>``
 
 ``<{ck4dhtmltarea value=VALUE|escape editor=bbcode}>``

| Option | Description |
| ------------- | ------------- |
| `editor` | The editor to use (html or bbcode) |
| `toolbar` | You can specify the toolbar to display in JSON format |



## Support

For topics, questions, and requests about CKEditor, please refer to [CKEditor](https://ckeditor.com/docs/index.html).    
For topics, questions, and requests about elFinder, please refer to [elFinder](https://github.com/Studio-42/elFinder). 

* Author : [nao-pon/xelfinder - GitHub](https://github.com/nao-pon/ckeditor4)    
* Maintainer : [gigamaster - XCL ^2.3.x](https://github.com/xoopscube/ckeditor4)
