<?php

//--------------------------------------------------------------------------
// This file has only doxygen command comments for generating documents.
// Because PHP is script file, too many comments causes performance problem.
// This file is an independent file which is not called from any files, and
// affects only doxygen's working.
//--------------------------------------------------------------------------

/**
 * \page xoops_version xoops_version.php
 * xoops_version.php is a kind of manifesto to be recoginized Legacy's module. This is a
 * traditional method since XOOPS2. xoops_version.php is just PHP file defining $modversion
 * map Array (std::map<string, mixed>). It's possible to write normal program code in the
 * file, but Legacy doesn't recommend it. xoops_version.php should be written only
 * definitions.
 * 
 * \code
 *   $modversion[{option_item_name}] = {option_item_value}; 
 * \endcode
 * 
 * Legacy finish loading modinfo.php in the current language directory, before it loads
 * xoops_version.php. By that, it's possible to use all constants defined by modinfo.php
 * in xoops_version.php. 
 * 
 * \par \e bool cube_style
 * When Legacy's proccess of parsing xoops_version.php conflicts with X2's it, this value
 * determines the parser's behavior. If this value is true, Legacy does not use the same
 * parsing as X2 JP and X2.  
 * 
 * \par \e string name [requreid]
 * A name of the module.
 * 
 * \par \e float version  [requreid]
 * A version of the module. This value is float like 1.23, but is stored as integer
 * after *100. So this item has to be written clearly like the following examples;
 * \li $modversion['version'] = 1.00;
 * \li $modversion['version'] = 1.01; // (not 1.1)
 * \li $modversion['version'] = 1.10; // (not 1.1)
 * 
 * \par \e string description
 * A summary of the module.
 * 
 * \par \e string author
 * A name of author.
 * 
 * \par \e string credits
 * A credits of the module.
 * 
 * \par \e string help
 * A file name of the module help file, if the module has it.
 * 
 * \par \e string license
 * A description about the license of the module. Normally the license is GPL. But, this
 * item is used to describe about the license of a part of the module, if the module need
 * it.
 * 
 * \par \e bool official [deprecated]
 * This is a deprecated item.
 * 
 * \par \e string image
 * A file name (offset-path) of the iconfile to be displayed in the module management.
 * 
 * \par \e string dirname [required]
 * This is a unique name to be recognized by Legacy, and has to equal with the recommended
 * directory name.
 * 
 * \par \e array templates
 * This is a vector array of the pair of \e file and \e description. Descriptions are not
 * required.
 * \code
 *   $modversion['templates'][1]['file'] = {filename}; 
 *   $modversion['templates'][1]['descriptions'] = {description};
 * \endcode
 * indecies' values are not used. It's possible to start from [0].
 * \li \e file A file path (offset file-path from the templates directory)
 * \li \e descriptions A description of the template. It's not must.
 * 
 */

?>