<?php

//--------------------------------------------------------------------------
// This file has only doxygen command comments for generating documents.
// Because PHP is script file, too many comments causes performance problem.
// This file is an independent file which is not called from any files, and
// affects only doxygen's working.
//--------------------------------------------------------------------------

/**
 * \page legacys_module_application_model Legacy's Module Application Model
 * Legacy is the base module which tries emulating the behavior of XOOPS2 JP as far as
 * possible. Module programs can access all public members of the singleton XCube_Root.
 * Module programmers can introduce their favorite APPLICATION-FRAMEWORK, but this page
 * explains about the foundation part of Legacy Module-Application Model.
 * 
 * \section what_is_a_legacys_module What is a module?
 * Modules for Legacy are just collection which contains programs and resources.
 * The Module Development Step is the following;
 * 
 * \li Makes a base directory with \e dirname under /modules.
 * \li Makes required directories under the directory.
 * \li Makes xoops_version.php as the manifesto.
 * \li Makes a sql file written about initializing database tables, if it is needed for
 *     the module.
 * \li Writes programs at page controllers.
 * 
 * Unrecognized module programs by Legacy are kicked and are not executed.
 * To execute modules, site owners will have to install the module in the module
 * management. For that, modules have to have xoops_version.php to be recognized as
 * Legacy's module programs by the Legacy. The minimum directory state of Legacy module
 * programs is the following;
 * 
 * \li {dirname}
 * \li {dirname}/xoops_version.php
 * \li {dirname}/index.php
 * \li {dirname}/class/
 * \li {dirname}/templates/
 * \li {dirname}/language/english/main.php
 * \li {dirname}/language/english/modinfo.php
 * \li {dirname}/admin/index.php
 * \li {dirname}/admin/templates/
 * 
 * \par xoops_version.php
 * As you know, this file describes the manifesto to be recognized as modules by the
 * Legacy. See \ref xoops_version.
 * 
 * \par index.php
 * Modules in the Legacy are classic page controller style, so modules have to contain
 * one or greater page controllers which receives requests. 
 * 
 * \par class
 * This directory is able to contain the specific class file which is defined under the
 * Legacy class naming convention. If module progams don't need such files, the directory
 * is not must. BTW, Like this directy, directories not having index.php should contain
 * dummy index.html.
 *
 * \par templates
 * This directory contains template resources. Names of the templates have to be listed
 * in xoops_version.php. 
 * 
 * \par language
 * This directory contains language files defining constants written in each language.
 * Basically, module programs have to provide english directory.
 * 
 * \par admin
 * This directory contains files providing programs for the control panel of Legacy.
 * 
 * \section legacys_module_program Legacy's Module Program
 * In Legacy, module programs have to be a kind of page controller. The following is the
 * most simplest example.
 * 
 * \code
 *   require_once "../../mainfile.php";
 *   require_once XOOPS_ROOT_PATH . "/header.php";
 *
 *   // Your program
 * 
 *   require_once XOOPS_ROOT_PATH . "/footer.php";
 * \endcode
 * 
 * Because module programs are placed under /module/{dirname}/, they have to go up to two
 * levels to get the setting file "mainfile.php" at the beginning. mainfile.php defines
 * basic constants and initializes Legacy base module.
 * 
 * Next, to start Legacy base module, calls "header.php". The file is under the installed
 * directory gotten by XOOPS_ROOT_PATH. After that, module programs can access all public
 * members of the singleton XCube_Root as the following;
 * 
 * \code
 *   $root =& XCube_Root::getSingleton();
 * \endcode
 * 
 * At the last, to present HTML page, calls "footer.php" which is placed to the same
 * directory as "header.php". Module programs should request presenting module's result
 * to the footer process through the render-target. The footer process will templates and
 * present HTML by rendering the render-target with the render-system.
 * 
 * It's possible to get the render-target from Legacy_AbstractModule::getRenderTarget().
 * The instance implements this interface is under the Legacy_HttpContext. Therefore, the
 * most simplest example is extended as the following;
 * 
 * \code
 *   require_once "../../mainfile.php";
 *   require_once XOOPS_ROOT_PATH . "/header.php";
 *
 *   $root =& XCube_Root::getSingleton();
 * 
 *   //
 *   // TODO: Your update logic
 *   //
 *
 *   $renderTarget =& $root->mContext->mModule->getRenderTarget();
 *   $renderTarget->setTemplateName("{your_template_filename}");
 * 
 *   // TODO: Set up $renderTarget to present results.
 * 
 *   require_once XOOPS_ROOT_PATH . "/footer.php";
 * \endcode
 * 
 * How should you write in xoops_version.php? How should you write in templates?
 * About them, you may read documents about XOOPS2 JP or XOOPS2, until the document
 * will be completed.
 * 
 * \section legacy_module_database_model Database
 * Each base module provides a database abstract layer to access the database. Module
 * programs can access the database by using the traditional database layer from XOOPS2,
 * after the Legacy starts connecting with the database.
 * \code
 *   $root =& XCube_Root::getSingleton();
 *   $db =& $root->mController->getDB();
 * \endcode
 * 
 * Normally the database layer is XoopsMySQLDatabase. For APIs, see that document.
 * 
 * \par Constants defined about Database settings
 * mainfile.php constructed by a user defines constants about Database settings. Modules
 * don't need to know about them, because the DB layer adjusts for modules by using them.
 * But, XOOPS_DB_PREFIX is important for modules. The constant defines a prefix for all
 * tables. Real table names are combined by prefix + table name. XoopsMySQLDatabase::prefix()
 * returns the real table name.
 * 
 * \code
 *   $sql = "SELECT * FROM " . $db->prefix('mymodule_students');
 * \endcode
 * 
 * \par Module tables creation
 * It's possible to create module tables when the module is installed. For that, the
 * module specifies initial SQL file in xoops_version.php. In addition, xoops_version has
 * to list names of installed module tables.
 * \code
 *   // Offset path from the 'dirname' directory
 *   $modversion['sql']['mysql'] = "sql/mysql.sql";
 *   $modversion['tables'][] = "mymodule_students";
 *   $modversion['tables'][] = "mymodule_teachers";
 * \endcode
 * The table name list is used for dropping tables automatically, when the module is
 * uninstalled. There are two formats to declare table names. For more informations, see
 * \ref xoops_version.
 * 
 * \attention
 * The Package_Legacy has installed many tables, but basically modules should not access
 * these tables directly by using a SQL query. Modules should access these 'informations'
 * by using objects. Objects are gotten by the traditional function xoops_gethandler().
 * 
 * \section legacy_module_templates_model Templates & Render-Target
 * In Legacy, fotter.php presents the result HTML page. Modules have to prepare
 * render-target assigned for module display bounds. The prepared render-target will be
 * rendered by the specified render-system. In Legacy, normally, the render-system is
 * Legacy_RenderSystem. That is a Smarty adapter.
 * 
 * In logic, module programs have to access the render-target by using its interface.
 * 
 * \code
 *   $root =& XCube_Root::getSingleton();
 * 
 *   $renderTarget =& $root->mContext->mModule->getRenderTarget();
 *   $renderTarget->setTemplateName("mymodule_helloworld");
 *   $renderTarget->setAttribute("name", "world");
 * \endcode
 * 
 * In templates, it's possible to use smarty's functions, because the template will be
 * rendered by the smarty lastly.
 * 
 * \code
 *   Hello, {$name|escape}!
 * \endcode
 * 
 * When the module depends on the special render-system, templates will be written in the
 * format of the render-system.
 * 
 * \section legacy_module_preload Module Preload
 * Modules can have preload called "module-preload".
 * 
 * \li Makes preload directory under the module directory. (/modules/{dirname}/preload)
 * \li Makes {filtername}.class.php defining {Dirname}_{filtername} class which is
 *     a sub-class of XCube_ActionFilter.
 * \li Puts the file to preload directory under {dirname} directory.
 * 
 * For example, if a module dirname is 'sample' and a filter name is 'loadLibrary',
 * the class name is 'Sample_loadLibrary'. (A first character of dirname has to become
 * upper)
 * 
 * The filter's preBlockFilter() of the module preload is inactive, because active
 * modules are determined after DB setup. When the Legacy processes preFilter(), it can
 * not know which modules are active. If it's must that a module preload uses preFilter(),
 * specifies the filter as primary filters in site_default.ini.php or site_custom.ini.php.
 * 
 * \code
 * [Legacy.PrimaryPreloads]
 * {class name}=/modules/{dirname}/preload/{filename}
 * \endcode
 * 
 * Example;
 * 
 * \code
 * [Legacy.PrimaryPreloads]
 * protectorLE_Filter=/modules/legacy/preload/protectorLE/protectorLE.class.php
 * \endcode
 * 
 * Primary preloads may be contained under a child directory of the preload directy,
 * because they are not normal preload.
 */

?>