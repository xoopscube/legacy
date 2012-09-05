# XOOPS X (ten) Distribution

* Based on [xoopscube/legacy:master](https://github.com/xoopscube/legacy)
* Add altsys
* Add XOOPS Protector
* Add [X-update](https://github.com/XoopsX/xupdate)
* Automatic deletion of directory `install` and chmod -w `mainfile.php`
* Enhanced Smarty plugin dirctory setting
 * xoops_trust_path/settings/site_default.dist.ini

```ini
[Smarty]
ResourceDiscoveryOrder=Theme,ThemeD3,ThemeDefault,ThemeDefaultD3,DbTplSet
```
* and etc.

## Web site

[XOOPS X (ten) Distribution Pack](http://www.xoopscube.net/)

## Quick install

Please change `T="../xoops_trust_path"` by your liking.

### On the shell (sh, bash)

```bash
cd [DOCUMENT ROOT]
T="../xoops_trust_path";curl -kL github.com/XoopsX/installer/raw/master/install.sh|sed "s#<T>#$T#"|sh
```

### On the shell (csh)

```csh
cd [DOCUMENT ROOT]
set T="../xoops_trust_path";curl -kL github.com/XoopsX/installer/raw/master/install.sh|sed "s#<T>#$T#"|sh
```

### On the web

* Upload "[install.cgi](https://github.com/XoopsX/installer/raw/master/install.cgi)" into [DOCUMENT ROOT] & chmod +x install.cgi
* Access to `install.cgi?../xoops_trust_path` with web browser (change `../xoops_trust_path` by your liking)

***


##Legacy

* [About](#about)
* [License](#license)
* [Requirements](#requirements)
* [Features](#features)
* [Download](#download)
* [Installation](#installation)
* [Overview](#overview)
* [Screenshots](#screenshots)
* [Extensibility](#extensibility)
* [Modules](#modules)
* [Themes](#themes)
* [Documentation](#documentation)
* [Site and Support](#site-and-support)
* [Contribute](#contribute)

***

About
====

**XOOPS Cube** is an Open Source Web Application Platform built with PHP and MySQL empowering webmasters to make a custom content management system and create dynamic and content rich websites with ease.

**XOOPS Cube Legacy** is a Simple, Secure and Scalable content management system and the package distribution of XOOPS Cube Project Team.

**The XOOPS Cube Project** is friendly managed, developed and supported by a volunteer group with a multidisciplinary focus to provide a variety of different perspectives and ideas to consider in further development and design. XCL Team spirit  aims to strengthen everyone's social network, share the essential knowledge and understanding of the “spirit of open source” necessary to encourage creativity.

***

License
====

###XOOPS Cube  

XOOPS Cube was started from scratch and the XOOPS Cube Core source code is released under the BSD licence.

 
###XOOPS Cube Legacy  

XOOPS Cube Legacy source code which is one of base modules to ensure compatibility with old versions of Xoops 2 is released under a GPL licence.

***

Requirements
====

Whether you plan to create your own personal or corporate community web site with news, forums, blog, photo album, portfolio, etc. You need a reliable Web Host running Apache, MySQL and PHP.

XOOPS Cube will run smoothly on a LAMP environment. In most cases your site will be hosted on the popular open source web platform consisting of Linux, Apache, MySQL, and PHP. Though XOOPS Cube can run on any other Operating System (OS) as well.


<table>
<tr>
<th align="center">
Server
</th>
<th align="center">
Database</th>
<th align="center">
Language</th>
</tr>
<tr>
<td align="center">Apache version 2.xx</td>
<td align="center">MySQL version 5.xx</td>
<td align="center">PHP version 5.xx</td>
</tr>
<tr>
<td align="center"><a href="http://apache.org/"><img src="http://xoopscube.org/uploads/fckeditor/logo-apache.png" alt="Apache" title="Apache" /></a></td>
<td align="center"><br />
<br />
<a href="http://mysql.com/"><img src="http://xoopscube.org/uploads/fckeditor/logo-mysql.png" alt="Mysql" title="Mysql" /></a></td>
<td align="center"><a href="http://php.net/"><img src="http://xoopscube.org/uploads/fckeditor/logo-php.png" alt="php" title="php" /></a></td>
</tr>
<tr>
<td align="center">http://apache.org/</td>
<td align="center">http://www.mysql.com/</td>
<td align="center">http://php.net/</td>
</tr>
</table>

###Software

Useful Wikipedia articles with tables comparing general and technical information of Proprietary software, Free and open-source software.  

<img src="http://xoopscube.org/uploads/fckeditor/server_database.png"> <a href="http://en.wikipedia.org/wiki/List_of_AMP_packages"> Local Test Server</a> : List of Apache窶溺ySQL窶撤HP packages  
<img src="http://xoopscube.org/uploads/fckeditor/script_edit.png"> <a href="http://en.wikipedia.org/wiki/Source_code_editor"> Source Code Editor</a> : Some well-known source code editors  
<img src="http://xoopscube.org/uploads/fckeditor/picture_edit.png"> <a href="http://en.wikipedia.org/wiki/Comparison_of_raster_graphics_editors"> Graphics Editor</a> : Comparison of raster graphics editors  
<img src="http://xoopscube.org/uploads/fckeditor/computer_go.png"> <a href="http://en.wikipedia.org/wiki/FTP_clients"> FTP Clients</a> : Comparison of FTP client software  

***

Features
====
Below is a list of some of XOOPS Cube Legacy features.

* Easy install procedure based on a wizard
* Modular architecture
* Module API for unlimited expandability
* Simple core to build up a custom cms
* Debug function for easy development
* Group-based permission system
* Intuitive Interface for management
* Smarty Template engine
* Caching mechanism
* Themes and Templates management
* Embedded WYSIWYG HTML Editor
* Free choice of PHP, JS, CSS frameworks
* Abundant third-party modules and extensions (preloads to customize functionality)

***

Download
====

The source code of XOOPS Cube Legacy is available on [Github](https://github.com/xoopscube/legacy)

XOOPS Cube Legacy packages are available at [downloads](https://github.com/xoopscube/legacy/downloads) area.


Installation
====

XOOPS Cube Legacy Installation Guide

http://xoopscube.sourceforge.net/documentation/

***

Overview
====

XOOPS Cube Legacy Administration

Graphical User Interface (GUI)



Screenshots
====

Screenshots and description.

### Legacy System Preferences

<img src="https://lh4.googleusercontent.com/-Rnhk9YUrttg/SB6gFWZAAQI/AAAAAAAAAe4/OT62wYd3w4I/s756/xcl_admin_settings.png">

### User Groups Management

<img src="http://gigamaster.myht.org/uploads/imgcad6d972581f658bc849f.png">

### Modules Management

<img src="http://gigamaster.myht.org/uploads/img84a2a7de03c93e7932649.png">

### Blocks Management

<img src="http://gigamaster.myht.org/uploads/imgb2e53f9cd9e93013b68c3.png">

### Themes Management

<img src="http://gigamaster.myht.org/uploads/img02a99ae6c063f277b2d71.png">


***

Extensibility
====
You can add functions to your website by installing modules. Modules can be easily add by installation wizard.
XOOPS Cube 2.1 has high compatibility with the earlier XOOPS 2.0.x. We recommend the last modules generation, known as Cube modules and D3 modules which can be easily duplicated and renamed but also provide a GUI to manage the language catalog and templates.
XOOPS Cube functionality can also be easily extended with Preloads - one file class based extension.

***

Modules
====

***

Themes
====

***

Documentation
====

XOOPS Cube Legacy API documentation generated by Apigen 2.7.0

http://xoopscube.org/documentation/api

Site and Support
====

http://xoopscube.org

http://xoopscube.jp

Contribute
====

XOOPS Cube Legacy is open source project community driven. We encourage everyone in the community to contribute their knowledge and expertise.
Everyone in the community benefits from every [enhancement request](https://github.com/xoopscube/legacy/issues), submit [bug report](https://github.com/xoopscube/legacy/issues) and [patch](https://github.com/xoopscube/legacy/pulls) implemented to improve **Legacy**. 
You can influence what happens to Open Source and the direction for **Legacy** future growth.