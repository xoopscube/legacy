# Change Log

All notable changes to this project will be documented in this file.

## The XOOPSCube Project 2025

#### Git Repository  

Refer please to repository Legacy Commits  

## [2.5.0 PHP8.2] 2025-04-25

Update maintenance PHP 8.2.x

- Add recovery.php (audit locales)
- Add TemplateAutoUpdate.class.php
- Altsys
  - Audit Translations
- Module d3forum v2.5
  - Admin dashboard
  - Overview activity
- Module Legacy v2.5
  - Block AI assistant
  - Warnings and refactoring
- Module Message v2.5 
  - UI ajax endpoint
  - UI user list 
  - Smarty plugins
  - Removed dependencies suggest.js
- Module Pico v2.5
  - Admin dashboard 
  - Fix deprecated warnings
- Module Protector v4.0
  - Admin Dashboard
  - IP Ban, safe list
  - Log viewer
  - Notification (auto admins)
  - Notification Test treath
  - Proxy Dashboard
  - Proxy Plugins manager 
  - Permissions 
  - Threat Intelligence 
  - Security Advisor UI
  - Stats overview
- Deprecated warnings
  - htmlspecialchars, strlen, trim, preg_replace
  - Passing null to parameter of type array|string
  - Smarty templates undefined array key
- Themes removed to repository
- Translations (WIP)
  - English
  - Français
  - 日本語, Japanese
  - Português
  - Pусский, Russian

## [2.5.0 PHP8.2] 2025-03-27

Update maintenance PHP 8.2.x  

- Add system block AI Assistant
- Deprecated preg_replace()
- Fix Module->modinfo
- Fix undefined array key
- Fix altsys langage manager

## [2.4.0 RC PHP8] 2024-04-20

- Update Web Install Wizard
- Update Bundle Modules
- Update CKEditor4 extra plugins, darkmode
- Update Default Themes
- Update Templates
- Update jquery & UI
- Avatars, emoji, images
- Update Pico GD Library
- Update plugin json
- Add Pico Activity Overview
- Removed deprecated files


## [2.3.3 RC PHP8] 2023-04-18

**Update modules**  

- altsys v2.33.3
- Core modules v2.33.3
- d3Forum v2.33.3
- Message v2.43.3
- Pico v2.44.3
- Protector v3.58.3
- Sitemap v2.33.3
- X-elFinder v2.64.3
- X-Update Manager v2.34.3
- update libs xelfinder and smarty plugins
- Smarty PHP8 v2.6.33-dev
- Translations (WIP)   
English, Français, 日本語, Português, Pусский

## [2.3.3 RC PHP8] 2023-04-16

- fix warnings PHP7.4 - PHP 8.0
- array, iscountable, and typos
- add Smarty constants, global translation
- add Smarty Forms examples
- built-in Help documentation
- control panel and dashboard options
- clean-up CSS dark and light theme
- Composer gui-based
- Composer trust path vendor
- merge JavaScript utilities
- Protector ip security list
- replace aria-label with title for action tooltip
- review css and php metrics
- Update Smarty plugins
- Fix fields, controller, XelFinder, X-Update manager
- Server information for Apache and Nginx path

## [2.3.3 RC PHP8] 2023-04-04

- Add language russian utf-8
- Fix language constants
- AltSys ( Components ) fix clone template set
- Use Edit Profile
- User Info Profile
- Debug Mode Smarty (class template)
- D3 Forum templates PHP8
- D3 Pico templates PHP8
- D3 Pico - Extra Smarty Forms
- Design Formmail send automatically to email
- Form Response stored in DB ( Control Panel )
- Pico search form by ID
- Fix warning Render and AdminRender
- Theme Default fix warning PHP8
- Theme Bootstrap 5 warning PHP8
- Sitemap Fix Block - fatal error PHP8

## [2.3.3 RC PHP8] 2023-03-31

- add language Russian
- altsys ui components php8
- common / Bootstrap v5.3.0-alpha2
- common / PicoCSS + pico grid
- common / x-layout
- Fix snooy not working with proxy server
- Admin blocks
- Admin Theme
- Admin Menu Avatar
- Admin menu Messages
- component card block
- component filter and search
- control view grid, list localstorage
- copy-past generated htaccess
- Dropdown filter sort and search
- Dropdown Action menu
- Install wizard - removed unused images
- Meta tags webmaster tools
- Module.class check constants
- Module d3forum php8
- Module Message v.2.43
- Module Pico v.2.44
- Moodule Pico Smarty Form Design
- Module X-Update v2.34
- Module X-elFinder v2.64.0'
- Module Sitemap v2.33.1
- Templates CSS link to /common/picocss
- Theme Bootstrap v5.3.0-alpha2
- Theme Default PicoCSS
- CSS metrics - card input width #235
- Admin block - loginfo PHP8 #239
- Admin Search preferences - Undefined constant PHP8 #241
- Admin UIX Action Control + localstorage #242
- Type Safe Element tray flag PHP8 #243
- Module class getAdminMenu array #245
- Solved PHP8 warnings by using Smarty modifier |default #247
- GUI Installation check - refresh after recursively delete directory install #248
- GUI theme localstorage - templates site-closed + redirect #249
- AltSys Components - version number #250
- GUI AltSys Components - Tips for Custom Blocks #251
- RRender - Banner Management - SortKeys and CurrentClient #252
- PHP8 - Admin Dashboard Preload - required parameter #253
- Render Admin theme and templates #254
- AdminDasboard Preload attributes #255
- Unsupported operand types: string + int #261
- Pico Design Smarty Form Build email validation #262
- Update AdminDashboard Preload
- Update modules templates

## [2.3.3 rc PHP8] 2023-03-23

- Merge backend, frontend and branch PHP8 

## [2.3.1rc] 2023-03-02

### Added 

- Admin icons
- Admin alert
- Admin Navbar dropdown 
- Admin Notify onchange
- Anchor ip ban, close site, cool uri
- Bootstrap 5.3.1

### Changed

- Admin Navbar
- Admin Blocks
- Admin Notifications
- Admin Templates
- Core modules templates

### Fixed

- CKEditor4 (index, help)
- Admin Control Panel
- Admin Dashboard


### Removed

- Removed unused style
- Removed unused components
- Removed X-Update ThemeFinder


## [2.3.1] 2023-02-20

### Changed

- (c) 2005-2023 The XOOPSCube Project

## [2.3.1] 2022-12-20

### Version 2.32.1 beta

###Update modules  

- AltSys (Components)
- CKEditor
- D3Forum
- Legacy System
- Legacy Renderer
- Message
- Pico
- Profile
- Protector
- Sitemap
- Standard Cache
- User Groups
- X-elFinder
- X-Update manager 

### Feature and Fix
- Fix #184 #186 #187 #189 #193 #194 #200  
- Feat #185 #188 #189 #190 #191 #197 #201 #202  
- Add start page to editable templates  
- Add site close to editable templates  
- Enhanced language constants  

## [2.3.1] 2022-11-22

### Added 
- Theme PicoCSS 
- Module D3Forum RSS preferences
- Module Pico RSS preferences  
- Modules language constants
- Upload folders for modules  
- Wrap content sample  

### Changed
- Core Modules templates
- Default Theme
- Helper Override preferences
- Notification  
- Pagenavi (class)
- Pagenavi Accessible aria-current  
- Plugin Smarty function pagenavi
- Private Message tabs 
- Private Message dynamic dropdown menu
- Private Message forward to email
- Revision history (content items)
- Sitemap Message
- Sitemap control to edit
- Smarty truncate subject
- Tree (CSS) Posts and Topics
- X-Update library path 
- X-Update Manager Help SSL  
  check Disable SSL certificate   
  if certificate expired

### Fixed
- Admin theme color mode
- Admin color active elements 
- Admin dashboard
- CKEditor revert icons css 
- Icons SVG ( cleanup )
- Install typo chmod
- Footer credits
- Mailer::$LE
- Theme Prototype Dark mode
- Turn all topics solved (icon)
- Textarea size overflow 

### Removed
- Bootstrap Framework  

### Update
- HTMLPurifier
- PEAR (library path)

## [2.3.1] 2022-10-22 

Refer to XCL Legacy 2.3 Commits  
Note: Actually theGitHub branch  
is not accessible.     

## [2.3] 2022-04-10  

  
⚡ ? ? Merge XCL UI + Wizard 

✔️ Web Installation Wizard - UI Options :  
- Chmod Mainfile.php  
- Delete directory /install  
- Activate Preload - For Development purposes only !   

✔️ CSS install Wizard  
✔️ CSS User Notification  
✔️ Change language to constants  
✔️ Check list for installation - ☑️Check All  
✔️ Extended array of Trust Path directories  
✔️ Fix Notifications : Confirm, Error, Success  
✔️ Fix check file permissions  
✔️ Fix check directory permissions  
✔️ Make files and folders  
✔️ Display permissions [ 0777 ]  
✔️ User Management SQL key   

**Bugfix and User Interface**  

✔️ Add avatar to Dashboard  
✔️ Add extra fields to Dashboard  
- User Search  
- User View
- User Edit  

✔️ Changed MultiMailer  
✔️ Changed mail namespace use \PHPMailer  
✔️ Changed language define to const   
✔️ Changed MailJob Dashboard    
✔️ Changed Banners management  
✔️ Dashboard Breadcrumbs  
✔️ Dashboard Dropdown Sort/Filter   
✔️ Dashboard Data Tables consistency   
✔️ Dashboard Data Table prefix 'list_'  
✔️ Dashboard Control-Action dialog Help    
  
⚠️ NOTE  
? PDO blank page ( guess Wizard could not find the driver)  
? PostgreSQL ( same issue here )  


## [2.3] 2022-04-01  


⚡ ? ? Merge XCL PHP7 UI   

✔️ Add Dropdown Filer/Sort  
✔️ Add Renderer admin banner  
✔️ Add Renderer admin banner expired (edit)  
✔️ Add Settings / Definintion / PEAR_PATH  
✔️ Add Settings / Definintion / VENDOR_PATH  
✔️ Add x-Layout z-index  
✔️ Admin select option checked  
✔️ Admin :focus-visible ( Keyboard Accessibility )  
✔️ Admin select :focus-visible ( Keyboard Accessibility )  
✔️ Admin checkbox :focus-visible ( Keyboard Accessibility )  
✔️ Admin dashboard ( Keyboard Accessibility )  
✔️ Admin nav-header ( Keyboard Accessibility )  
✔️ Admin preference list  
✔️ Admin SVG icons  
✔️ Admin Tables CSS   
✔️ App Preloads :  
- Preload assignUid  
- Preload D3ForumCommonCSS  
- Preload debugOnlyAdmin  
- Preload EmailLogin  
- Preload Multilanguage  
- Preload MultiMenu  
- Preload SetupAltsysMgr  
   
✔️ Fixed AltSys $numrows  
✔️ Fixed AltSys extra_sides  
✔️ Fixed AltSys switch view code/diff  
✔️ Fixed AltSys template code/diff overflow-y  
✔️ Fixed AltSys Redirect message (if empty templates vars)    
✔️ Fixed Bootstrap -> pico templates update  
✔️ Fixed class declaration compatible  
✔️ Fixed custom properties (dashboard aside)  
✔️ Fixed deprecated each with foreach    
✔️ Fixed locale const : EN, FR, JA, PT  
✔️ Fixed Protector Mysql Database   
✔️ Fixed statement with ternary operator  
✔️ Fixed TextDiff  
✔️ Fixed user-menu to side-panel   
✔️ X-Update Theme Screenshot  
✔️ X-Update Store list (tabs) overflow-y  
✔️ X-Update Input readonly  
✔️ X-Update language Name/Title  
✔️ X-Update language Tips  
✔️ OS Browser Checker  
✔️ Clean-up files/folders  
? Admin Side Panel Webmasters Tools  
? Smarty Module isActive -> display [ icon/link ]  
? Smarty Debugger Dialog  
? LocalStorage Settings  
? LocalStorage Webmaster ToDo list  
? Module's Help Docs  
? Help Semantic Layout ( nav, article, section )  
? Help Word Censoring Options ( json )  
? Search Options ( json, multilanguage )  
? Input Email - regex  
? add tips create template set backup  
? UI - Buttons  
? UI - Define common ration eg. 144+2=288, 447x2=894  
? UI - Rename /common/bootstrap 4/5  


---
## [rc] 2022-01-21

⚡ ? ? Merge XCL PHP7 


---
## [rc] 2022-01-21

⚡ ? ? Merge XCL PHP7 

---
## [unreleased] 2021-12-10

⚡ ? ? Merge UI from packages XD and XF


### Added

- Add Theme Bootstrap /component
- Add 'edit block' to Theme Bootstrap
- Add Theme Bootstrap Template Front-Page
- Add CKEditor CSS for Firefox

### Changed

- D3 Forum Admin - Revision History #267
- D3 Forum Admin Template - Advanced #266
- D3 modules Admin Help #265
- User Module Templates with XLayout #268
- Theme Bootstrat layout (column left/right)

### Fixed

- Fix automatically refactored copyright notice and authors #264
- Fix root controller to Login Recovery
- Fix Reset New ( :where )
- Fix XCube DefType
- Fix XCube source comment typos
- Fix Legacy kernel (match XCube)

### Removed

- Removed old funtion
- Removed old theme function
- Removed Legacy include old function

---
## [unreleased] 2021-12-01

⚡ ? ? Merge UI from packages XD and XF

### Changed

- Admin Help
- App front page template
- Add dashboard templates
- Add input color
- CKEditor UI color
- CKEditor plugins
- CSS Custom Properties
- Default templates :
- Module altsys
- Module forum
- Module Legacy
- Module render
- Module Message
- Module Pico
- Module User
- Module elFinder
- PrismJS
- Fix Diff assert
- Fix Theme object
- Fix Helper flexbox
- Fix MySQL
- x-layout
- x-render-svg
- x-utils
- XelFinder UI options
- elFinder Themes (local)

---
## [unreleased] 2021-11-30

⚡ ? ? Merge UI from packages XD and XF

---
## [unreleased] 2021-11-09

? ⬆️ UPDATE jQuery 3.6.0  
? ⬆️ jQuery v3.6.0  
? ⬆️ jQuery UI - v1.13.0  
? ⬆️ PrismJS 1.25.0  
? ⬆️ XCL-Layout 2.3.1  
? ⬆️ XCL-SPA 2.3.1  

---
## [unreleased] 2021-06-14

? ⬆️ UPDATE @gigamaster merge X-elFinder 2.59 (@nao-pon)

---
## [unreleased] 2021-06-12

### Changed

- X-Util rename ui-nav-top
- X-Util prevent tabs from scrolling
- Component admin-help
- Abstract Category Object
- Abstract Group Object
- column side
- comment
- comment status
- enum.class
- Render System Banner setup
- Database Factory getDatabaseConnection

### Update

? ⬆️ UPDATE X-elFinder 2.58 @nao-pon
- [VD:abstract] Fixed #3151 support RAR5 lib
- [cmd:fullscreen] Fixed #3177 wrong fullscreen button caption
- [js:core] Supports cookie samesite attribute
- [VD:SFTP] Add new SFTP driver, via phpseclib library
- [js:core] Fixed #3193 auto-detection of baseUrl
- [js:upload] Fixed upload bug (#3264)
- [VD:abstract,php] make the thumbnail support webp (#3265)
- [php:core] Fixed #3250 error only variables can be passed by reference
- [VD:abstract] add 'phar:*' => 'text/x-php' into 'staticMineMap'
- [VD:abstract] Fixed #3181 add an option uploadMaxMkdirs
- [php:core] Add cwd param to proc_open (#3281)
- [VD:abstract] Bugfix of an option mimeDetect (#3291)
- [UI] Fixed #3302 problem of d&d when copy of UI command is disabled
- And some minor bug fixes


---
## [unreleased] 2021-06-07

⚡ ? ? [WIP] LOCALSTORAGE / Refactoring UI-X 2.3.1

---
## [lint version] 2021-05-27

⚡ ? ? [WIP] LOCAL-FIRST / Refactoring UI-X 2.3.1

---
## [lint version] 2021-03-27

### Added

- Default templates
- Inline svg
- SVG logo

### Update

- class, common, core
- install wizard, modules, themes
- default themes (templates)
- language ? fr utf-8 #238
- d3 modules (forum, pico, protector)
- xelfinder
- xupdate

###  Fixed

- D3 Deprecated DB
- Default templates
- Install Wizard SQL
- Notice and Warning D3 modules

---
## [unreleased] 2021-03-03

### Added

- Component front page

### Changed

- Default Theme Neumorphism (WIP)
- Theme default front page
- Templates of modules

---
## [unreleased] 2021-02-21

### Added

- CKEditor Setting Editor UI Color
- Default Theme Neumorphism (WIP) #232
- Theme built with XCLayout Helper #229
- Templates breadcrumbs #220
- Templates (D3Forum, Pico) #218
- Templates Blocks (Users, Online, Themes)
- Template Refirect Functions [50 legacy] #223
- SVG inline icons
- Smarty constants to Pico menu (En,Ja)
- Smarty breadcrumbs to D3 modules
- Render blocks admin to D3 modules
- Render preferences to D3 modules
- URL Rewriting - XCL Cool URI #234

### Changed

- Copyright 2021
- English constant (User)
- Form input number (max 100)
- Javascript popup centered
- Render Preferences of D3 modules
- SQL utf8mb4 varchar 255 to 191
- UI Avatar #217
- UI Backend breadcrumbs #220
- UI Frontend breadcrumbs #220
- XCL Default Theme Neumorphism
- XCLayout Helper Flexbox

###  Fixed

- Altsys fix stric check (blocks permissions)
- Admin Theme typos
- Admin SVG icons (bold, linear)
- CSS excludes ckeditor (breaks layout with raw javascript)
- Debugger nav (Graph)
- D3Forum category configs unserialize
- Fix d3forum_wysiwyg_body
- Help load with ajax (modal)
- Modules icons (SVG with custom dirname)
- Module filter (smarty single quote)
- Module list (form removed from component)
- Module Pico frontend overridable options
- Protector advisory protocol check
- Protector apache_get_version
- Protector breadcrumbs and nav #220
- User avatar (check if empty) #217
- SQL InnoDB utf8mb4 varchar 255 to 191
- Switch 'diff from file'
- Templates (inc) #233
- XCLayout Helper fix margin negative
- XCLayout Helper fix spacing

### Removed

- Protector deprecated mymenu inline
- Extra tags from default templates
- CSS classes of Bootstrap 4
- Xoops.css

### Update

- Icon Bundle (WIP)
- Settings (WIP)

---
## [unreleased] 2020-07-11

### Added

- Branch SQL InnoDB utf8mb4
- Wizard Installer 2nd step design

### Changed

- Altsys and blocks admin actions refactor code
- Altsys admin page layout
- class refactor code
- D3forum refactoring PSR2
- include refactor code
- Legacy refactor code
- SQL refactor InnoDB utf8mb4
- Smarty comment
- X-elFnder v.2.57 refactor code (speedup, PSR2)
- XOOPSCube (c) 2005-2020
- X-Update Store templates
- [WIP] UI Admin templates
- [WIP] Help ajax load, struture and semantics


###  Fixed

- function getcss
- installer database (utf8mb4)
- installer timezone
- Help semantic structure (section, article)
- PHP Null Coalesce Operator
- PHP stripos() function
- Pico MySQL 5.6 innoDB max key length for utf8mb4
- Wizard Installer 2nd step

### Removed

- deprecated code comments

### Update

-  xelFinder 2.57 settings for XCL


---
## [unreleased] 2020-07-05

### Added

- Pico Publication Workflow :
  - Content Expired
  - Content invisible
  - Waiting approval
  - Waiting release
- Template with category description
- Template with section 'waiting content'
- Add 'htmlspecialchars_decode' to waiting content
- Add 'preview waiting content' to 'viewcontent'

### Changed

- Ajax load Modules Help section or article 'id'
- Modules Help semantic structure (section, article)
- D3forum /class /main refactor code
- PHP Null Coalesce Operator
- PHP stripos() function

###  Fixed

- D3forum bugfix static
- Pico template smarty top description


### Removed

- Pico template 'switch_to_fckeditor'

### Update

-  xelFinder 2.57 @nao-pon
Note : for Nginx, go to module preferences and checkbox to "Not use PathInfo"

---
## [unreleased] 2020-07-03

### Added

- User templates
- inc user nav left
- inc user nav tabs
- inc user tab
- inc user tab home
- inc user tab pref
- inc user tab profile

### Changed

- user avatar edit
- user edit (user profile)
- user info (public profile)
- USER_LANG_AVATAR_WIDTH_HEIGHT (en, jp)
- XCL Theme Bootstrap 4.5 (css)
- xcl-bootstrap-default manifesto

###  Fixed

- strict warnings in php 7.4 @nbuy

### Removed

- HTML tags and css

### Update

- XCL Bootstrap default

---
## [unreleased] 2020-07-01

### Added

- [WIP] XCL front-end templates
- breadcrumbs to templates
- favicon.svg with prefers-color-scheme
- function xoopsPrintag to xoops.js
- Printer-friendly of any HTML element
- Legacy templates (notification, forms)
- Render templates (forms)
- User templates (forms)
- Pico templates (menu tree)
- Message current tab active (javascript, smarty)
- Message template (user avatar)
- Message settings info of purge schedule :speech_balloon:
- Message settings info of purge type :speech_balloon:
- Profile templates (tabs, timeline, private data)
- Responsive Tabs navigation
- Responsive Menu options
- Smarty comments

### Changed

- D3Forum refactor code
- Message language 'Blacklist User'
- Message language 'Forward to Email'
- Message titles truncate character length 50(...)
- Pico refactor code
- User refactor code (form fields)
- User Profile fields (private data)
- Fields : resume, interest, location, occupation
- Cube settings
- English localisation

###  Fixed

- Deprecated functions @gigamaster
- module message typo english
- Wizard Installer style

### Removed

- D3 modules CSS classes
- CSS maps
- Javascript maps
- HTML comments
- Message icons gif

### Update

- xElFinder

---
## [unreleased] 2020-06-25

### Added

- [WIP] Front-End User Interface
- Forms
- Notifications
- Pico
- Private Message
- User Profile
- User fields (private)

### Changed

- D3Forum refactor code
- Pico refactor code
- User refactor code (form fields)
- User Profile (private bio-data)
- Fields : resume, interest, location, occupation
- Cube default settings
- English translation (D3Forum, Pico, User)

### Fixed

- D3Forum deprecated function @gigamaster
- Cache settings
- Template cache (bug of refactor code)
- Resource template (bug of refactor code)
- Wizard Installer style

### Removed

- Bootstrap 4.5 default map, css and js
- Legacy CSS classes
- HTML tags (not used by templates)

### Update

- Module Message 2.4.0 @gigamaster
- xElFinder @Naoki Sawada

---
## [unreleased] 2020-06-19

### Added

- Bootstrap 4.5
- Theme XCL Bootstrap Default
- Theme D3Forum Templates
- resource.db.php @gigamaster
(add dirname to theme path /templates/module-name)

### Changed

- D3Forum refactor code  
- D3Forum english catalog  
- [WIP] D3Forum templates  
✅ custom layout  
✅ top  breadcrumbs  
✅ topics and posts table  
✅ post within a card  
✅ card header, body, footer  
✅ card footer with child nav (flex wrap)  
✅ pagination  
✅ external comments  
✅ inline SVG icons  
✅ modify post forms  
✅ advanced research  

###  Fixed

- D3Forum deprecated function @gigamaster
- D3Forum includes
- D3Forum lists
- D3Forum usort

### Removed

- D3 CSS classes
- HTML4 tags

---
## [unreleased] 2020-06-10

### Added

- Admin nav component
- Copy code sample with clipboardjs
- Guideline toggle view source code
- [WIP] Toggle filter search and sort
- [WIP] Components templates tree
- [WIP] Dashboard XCL desktop modal
- [WIP] Set of icons (inline SVG)

### Changed

- Altsys refactor code
- XCube refactor code
- Refactoring to save memory
- PrismJS import local script
- Help files semantic structure
- Help ajax load section or article

###  Fixed

- Buttons action with icon and text
- Buttons action with icon only
- Declaration of Render TplsetUploadAction
- Typos in dashboard and D3 modules
- UI-btn inherent and default styles
- [WIP] CSS variables

### Removed

- buttons fx anim
- CDN library loading javascript
- deprecated Xoops2 functions
- xoops_admin_menu_js
- xoops_module_get_admin_menu
- xoops_module_write_admin_menu

---
## [unreleased] 2020-06-08

### Added

- Code samples
- Custom code highlight
- Components templates
- Dashboard style guide
- [WIP] Icons for buttons
- Inline SVG icons for buttons
- Inline SVG background-color
- PrismJS + plugins
- PrismJS custom CSS

### Changed

- Code refactoring
- Components inherit default values
- Delegate and extend Admin UI
- [WIP] Button color
- [WIP] Button FX
- [WIP] Button icon
- [WIP] D3 templates
- [WIP] D3 Protector
- [WIP] Admin Dashboard
- [WIP] Admin Templates
- Ajax load component guide
- Ajax load module help sections
- jQuery-UI inherit CSS
- Dark Mode Switch
- Stylesheet to jQuery-UI

### Fixed

- ActionSearch Form
- [WIP] CSS variables
- Card block content padding
- [WIP] Default buttons
- Layout main max-width
- Layout for mobile
- ui-theme-manager.js
- ui-render-svg.js
- ui-render-viewport.js

### Removed

- Cleanup css
- Cleanup javascript
- Cleanup guide templates
- Cleanup SVG (inline)
- CSS custom properties
- Screen mode (mobile)

### Update

- jQuery 3.5.1

---
## [unreleased] 2020-05-29

### Added

- Components templates:
- Dashboard style guide
- ui-style-accordion
- ui-style-button
- ui-style-colors
- ui-style-date
- ui-style-fonts
- ui-style-form
- ui-style-menu
- ui-style-spacing
- ui-style-modal
- ui-style-table
- ui-style-tabs
- Theme Manger to /common
- SVG Render to /common

### Changed

- [WIP] Admin Dashboard
- [WIP] Admin Templates
- Ajax load style guide
- Ajax load help sections
- jQuery-UI custom CSS
- Dark Mode Switch
- Stylesheet to jQuery-UI

### Fixed

- ActionSearch - WARNING: count() #157
- CSS variables for Light Mode
- PHP74 module Altsys curly braces #205
- PHP74 module Pico curly braces #205
- SearchResultsForm - Notice #163

### Removed

- Cleanup css
- Cleanup javascript
- Darkmode from admin script
- SVG Render from admin script
- CSS custom properties

---
## [unreleased] 2020-05-11

### Added

- html/editorconfig
- spaces, indent_size = 4

### Changed

- Installer Wizard stylesheet
- Installer Wizard english
- Installer Wizard 'break' warning
- MultiTokenHandler extend #199
- Token numeric value to mt_srand #198
- XCube Core
- XCube_IniHandler

### Fixed

- ActionSearch - WARNING: count() #157
- AbstractFilterForm - Warning #195
- Advertising Statistics, client login #199
- CKEditor4Utiles class deprecated join #197
- Installer Wizard missing $key #196
- Kernel object Null coalesce operator @gigamaster
- Mixed Tables engine MyISAM ad InnoDB #194
- PreferenceEditForm Undefined index: confcat_id #63
- SearchResultsForm - Notice #163

### Removed

- Cleanup code refactoring
- Useless japanese files (fail to pass test)
- Unused phpmailer language files
- Theme Grid Flex Boilerplate
- Theme Legacy_default

---
## [unreleased] 2020-04-29

### Added

- New UI XCL Installer Wizard
- New front-end XCL Default Theme
- New Admin Dashboard Cube Panel
- PhpDoc Missing @return tag
- PhpDoc Non-canonical order of elements
- PHPDoc comment matches function/method signature
- SVG icons

### Changed

- Alias functions usage
- chop to rtrim
- Deprecated constructor
- __DIR__
- doubleval to floatval
- floatval to float
- fputs to fwrite
- $i to $iMax, $x to $xMax
- intval to int
- is_long to is_int
- is_subclass_of to instanceof
- join to implode
- sizeof to count
- socket_set_timeout to stream_set_timeout
- srand to mt_srand, rand to mt_rand
- Yoda condition

### Fixed

- PHP74 AdminDashboard function static
- is writable upload Dir
- PSR12 Missing parameter list
- PSR12 Missing visibility
- PSR12 Order of modifiers
- PSR12 Short form of type keywords
- PSR12 Usage of var
- Traditional syntax array

### Removed

- Jp deprecated files
- Unnecessary double quotes
- PNG graphics

### Update

- X-elFinder version 2.56

---
## [unreleased] 2020-02-29

### Added

- jQuery to common/js

### Changed

- Admin Dashboard
- Admin Template Module Card
- Admin Template Install List
- Render Local Javascripts

### Fixed

- Xupdate AbstractAction
- Xupdate ModuleStore Handler

### Removed

- Render CDN Javascripts

### Update

- X-elFinder version 2.53

---
## [unreleased] 2019-12-19

### Added

- Admin Theme version 0.0.4
- Buttons ( custom properties )
- Cards ( custom properties )
- Components ( Admin Templates: sections filter, sort )
- HTML5 Web Storage ( local storage )
- @media ( prefers-color-scheme: dark )
- @media ( prefers-reduced-motion: reduce )
- Select color-mode Light or Dark
- Script fallback to local
- UI-root components custom properties

### Changed

- Accessible Rich Internet Applications ( ARIA )
- Cards ( clean-up components html and css )
- Components ( Admin Templates )
- Custom Properties (alphabetical order )
- HTML5 Layout ( simple and scalable )
- Legacy Render System ( header script )
- Media Queries ( @media rule )
- SVG Icons ( simple vertical-align & scalable )
- Tables structure ( thead, tbody, tfoot )
- UI root consistency :
  replaced reference of fabric industry ( xs, md, xl... )
  with #xoopscube order/weight numeric values

### Fixed

- Altsys ( UI-Components )
- D3 Modules Templates
- .editorconfig [**.css]
  ( indent_style = tab
  indent_size = 2 )
- Javascript ( jQuery and Vanilla )
- Grid and Flex (simplified and scalable)
- X-Update Store Templates
- X-Update Store SVG Icons

### Removed

- Altsys Core Version check
- Deprecated Fck-htmlarea
- [WIP] Admin-UI ( test files )

### Update

- Admin Theme
- Altsys
- D3 Modules
- HTML Purifier HTML5 attributes
- Standards Compliant HTML Filtering

---
## [unreleased] 2019-11-29

### Added

- Admin Theme version 0.0.3
- CDN Script Fallback Local

### Changed

- HTML5
- Legacy Render System
- Modules icons svg
- Smarty modifier
- Templates (Admin)

### Fixed

- Legacy xoops error
- Lost Pass action
- User Login
- Xupdate Store AssetPreload class
- Xupdate Store preg_replace /e

### Removed

- [WIP] Admin-UI images
- Deprecated Fck-htmlarea

### Update

- HTML Purifier 4.12.0 - Standards Compliant HTML Filtering
- Xupdate Store phpseclib-2.0.23

---
## [unreleased] 2019-11-15

### Added

- Admin Theme (WIP)
- Theme Flex Starter (WIP)
- Admin Dashboard Preload

### Changed

- Admin Blocks & Dashboard
- Admin Dashboard Settings
- Archive_Zip preg_match
- Pico Form Process by HTML regex
- D3Forum Text Sanitizer
- Pico Text Sanitizer

### Fixed

- **class constructors**
- XoopsCommentRenderer
- XoopsDownloader
- XoopsTarDownloader
- XoopsObjectTree
- XoopsMediaUploader
- XoopsXmlRpcApi
- XoopsXmlRpcParser
- XoopsXmlRpcDocument
- XoopsXmlRss2Parser
- XoopsThemeSetParser
- XoopsGroupPermForm
- XoopsZipDownloader
- AltsysFormCheckboxGroup
- LegacyImagebodyObject
- Legacy_AbstractCacheInformation
- Legacy_ActionForm
- Legacy kernel criteria.class
- Legacy_Mailer
- Protector Postcommon HTMLPurify4everyone
- Protector Postcommon HTMLPurify4guest
- ShadePlus_ServiceServer
- ShadePlus_SoapClient
- nusoap_base
- LegacyTheme
- LegacyThemeHandler
- LegacyRenderThemeObject
- Profile_ActionFrame
- Profile_Action
- User_Permission
- User_PermissionModuleItem
- User_PermissionBlockItem
- User_PermissionSystemAdminItem
- User_LostPassMailDirector
- User_LostPass1MailBuilder
- UserMailjob_linkObject
- XoopsAvatar
- XoopsImage
- XoopsImagecategory
- XoopsImageset
- XoopsImagesetimg
- XoopsSubjecticon
- XCube_RenderCache

### Removed

- CKEditor4Utilities mysql_set_charset
- Legacy_Controller magic_quotes
- Protector patches
- Protector Myysql_query

### Update

- XelFinder v2.50


---
## [unreleased] 2019-10-19

### Added

- Accessibility Help #145
- Accessibility Checker #145
- Ajax Data Loading
- Build-config #146
- Code Snippets
- Content Templates
- Toolbar Editor
- oEmbed Media #147

### Changed

- ThemePreload comments translated from Ja to En
- MyConfig comments translated from Ja to En
- D3module changed _TRUST_PATH by _LIBRARY_PATH HTMLPurifier #131
- D3forum version
- Pico changed _TRUST_PATH by _LIBRARY_PATH #131
- Protector filters changed _TRUST_PATH by _LIBRARY_PATH #131
- Protector README
- Protector version ref 2.3.0
- D3Forum readme (merged)
- Pico changed english catalog
- SVG icons and graphics
- Site_default.ini required and recommended modules
- X-Update version
- X-Update stores urls

### Fixed

- Altsys gtickets NOTICE: Only variables should be passed by reference #136
- Altsys mygrouppermform NOTICE: Only variables should be passed by reference #137
- Altsys Text_Diff.php calling assert() with a string argument is deprecated #138
- Altsys Text_Diff.php deprecated constructors #139
- Class xoopsmultimailer deprecated constructors #140
- DEPRECATED: Non-static method Legacy_SiteClose #135
- EasyLex_SQLScanner constructor #141
- Kernel group call to undefined method #130
- Pico SQL Error Update in include transact_functions #128
- PicoModelContent #129
- PicoControllerVoteContent #129
- PicoControllerUpdateContent #132
- PicoControllerInsertCategory #129
- PicoControllerInsertContent #129
- PicoControllerGetHistory #129
- PicoControllerDiffHistories #129
- User RegistMailBuilder deprecated constructors
- xelFinder session by @nao-pon #102

### Removed

- Module Lecat #143
- Ckeditor html files
- Ckeditor skin moono-lisa #151
- Extra files for localization
- Protector CHANGES_OLD
- Protector MEMO_ja
- Protector README_PL.txt
- Protector TODO
- Protector version.txt

### Update

- Ckeditor v.4.13.0 #144
- Skin Moono Dark
- Default Theme
- Default Session
- Default General Settings #149
- D3Forum Preferences #150
- Render Preferences #150
- Search Options #150
- User Preferences #150
- CKEditor Preferences #150
- Pico Preferences #150
- xelFinder Preferences #150

---
## [unreleased] - 2019-10-09

### Changed

- revert xelFinderSession.class.php - fixCookieRegist error #102
- xoops_cookie and session to xcl_wap

### Fixed

- Old style constructor to "__construct" #98 Nobuhiro YASUTOMI aka nbuy
- Theme legacy_default - Smarty typo og:title #109
- MySQL 5.7.+ Strict Mode #97 Kilica
- xelFinder Use of mbstring.internal_encoding is deprecated #101
- xelfinder Admin failed to open googledrive.php #106
- xelfinder Couldn't find constant _DROPBOX_TOKEN #107
- xoopsmailer deprecated constructor and utf-8 #103
- Undefined variable: mods in Block Install List Action #104

### Update

- class, kernel, legacy, message "__construct" #98 Nobuhiro YASUTOMI aka nbuy
- Catalog english : install, legacy, profile, stdCache, user
- xelfinder version 2.50 by Naoki Sawada aka nao-pon

---
## [unreleased] - 2019-10-07

### Added

- Merged Module D3Forum 0.89.5

### Changed

- include/cp_functions.php --version
- include/cubecore_init.php --version

### Fixed

- Numeric value in install/class/settingmanager.php
- Legacy - Module Information #99
- MySQL 5.7.+ Strict Mode #97 by Kilica

### Update

- XCL and modules version (legacy,profile,stdCache,user)
- modules doc/help

---
## [unreleased] - 2019-09-25

### Removed

- Remove duplicated lib HTMLPurifier from Protector #94
- Remove dir "doc" (french) from Protector #94

### Fixed

- Protector path to TRUST_PATH/libs/htmlpurifier

### Update

- HTML Purifier 4.11.0

---
## [unreleased] - 2019-04-30

### Added

- Extra Meta Webmaster Tools
- Hack by Ryuji to prevent Legacy_redirect if AdelieDebug
- Fixes Pico Category

---
## [unreleased] - 2019-04-18

### Added

- Module lecat (temporary, to be released with elPackage)
- Module Pico
- Module Xelfinder
- login.php

### Fixed

- HTTPS everywhere : Chrome ! [#82](https://github.com/xoopscube/xcl/issues/82)
- Profile missing _MI_PROFILE_ADMENU_DATA_DOWNLOAD [#67](https://github.com/xoopscube/xcl/issues/67)
- Profile_Admin_DefinitionsListAction getBaseUrl [#66](https://github.com/xoopscube/xcl/issues/66)
- Module Uninstall Action $flag [#65](https://github.com/xoopscube/xcl/issues/65)
- Protector gtickets constructor [#64](https://github.com/xoopscube/xcl/issues/64)
- Meta Copyright 2019 [#62](https://github.com/xoopscube/xcl/issues/62)
- Legacy constructors [#43](https://github.com/xoopscube/xcl/issues/43)
- XCube constructors [#42](https://github.com/xoopscube/xcl/issues/42)
- XCube controller setupSession [#41](https://github.com/xoopscube/xcl/issues/41)
- Legacy_AdminControllerStrategy [#38](https://github.com/xoopscube/xcl/issues/38)
- Legacy_AbstractDebugger [#37](https://github.com/xoopscube/xcl/issues/37)
- Search Form Error [#36](https://github.com/xoopscube/xcl/issues/36)
- ProtectorFilterHandler deprecated constructor [#35](https://github.com/xoopscube/xcl/issues/35)
- ProtectorFilterAbstract constructor [#34](https://github.com/xoopscube/xcl/issues/34)
- Protector directory configs [#33](https://github.com/xoopscube/xcl/issues/33)
- smarty compiler class [#32](https://github.com/xoopscube/xcl/issues/82)
- ckeditor smarty function [#31](https://github.com/xoopscube/xcl/issues/31)

---
## [unreleased] - 2019-04-12

### Deprecated

- Deprecated constructors
- Variables passed by reference

### Fixed

- Core
- Legacy
- Search
- Sync branches
- Sync Trust Path

---
## [unreleased] - 2019-02-11

**XCL 2.3.0 Update to PHP7:**

### Added

- Merge branch 'master' into 2.3.0, Nao-pon

### Changed

- README.md, Gigamaster (Info opensource)
- upload /modules/xupdate
- upload modules/ckeditor4
- upload modules/altsys
- upload modules/protector
- installer en, ja_utf8
- language/english

### Deprecated

- PHP5 rem.

### Removed

- Remove ?> from PHP files, Kilica
- Move folder 'docs' to new repo
- Move folder 'extras' to new repo

### Fixed

- Update to LF, Kilica
- Update version /include
- Update /common
- update /core
- update /install
- update /kernel
- update /modules/legacyRender
- update /modules/message
- update modules/profile
- update modules/stdCache
- update modules/user
- update modules/ckeditor4 0.74
- Fixed installer template
- Kernel Object, Fix PHP7 Undefined index variable not initialized with Null coalesce operator
- XCube controller, Fix PHP7 Only variables should be passed by reference - XCube Session
- Legacy Controller, processModulePreload, Fix PHP7 Only variables should be passed by reference

### Security

- update class.phpmailer 5.2.27
