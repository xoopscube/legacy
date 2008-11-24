<?php

//--------------------------------------------------------------------------
// This file has only doxygen command comments for generating documents.
// Because PHP is script file, too many comments causes performance problem.
// This file is an independent file which is not called from any files, and
// affects only doxygen's working.
//--------------------------------------------------------------------------

 /**
 * \page documens How to R/W Doxygen
 * 
 * XOOPS Cube recommends Doxygen than PHPDOC. And, it has the special rule about how to
 * write comments.
 * 
 * \section doxygen_basic Basic Rule
 * 
 *  \li Only API guidance are published.
 *  \li Recommended Doxygen setting is "HIDDEN PRIVATE" and "HIDDEN INTERNAL". This
 *      setting is for developers who don't touch body of XCube.
 *  \li When Doxygen does not hidden private and internal, it generates documents for
 *      internal developers who touches XCube.
 *  \li For distal entities, uses "seal (hidden) comments". Seal comments are the special
 *      comment which is added one of comment mark like "////". (This seal comment is
 *      broken, by using the special filter for Doxygen)
 *  \li It should prepare the recommended Doxygen setting having exceptional directories,
 *      not to generate documents from third libraries like smarty and etc.
 * 
 * \section doxygen_stereotype Stereotype
 * [Stereotype] represents more detailly type information. It is appended to 'brief'
 * explanations of classes, member variables and methods.
 * 
 * \subsection doxygen_stereotype_class Stereotype of Classes
 * \li [Abstract] Abstract class (or interface). Impossible to call constructor.
 * \li [Final] Final class. NOT inherentable.
 * \li [Secret Agreement] Only the specific class has to use the class. The condition is
 *     written in 'attention'.
 * 
 * \subsection doxygen_stereotype_mvariables Stereotype of Member variables
 * \li [READ ONLY] In only PHP4 code, it's possible to access directly instead of get
 *     access method.
 * \li [Secret Agreement] Only the specific class has to use it.
 * 
 * \subsection doxygen_stereotype_method Stereotype of Methods
 * \li [Abstract] Pure virtual functions.
 * \li [Static] Static methods.
 * \li [Overload] Virtual overloaded methods by variable-length parameters or checking
 *     object type (classname).
 * \li [Final] Impossible to override in sub-class.
 * \li [New] The method is hiding base class' method. (If base class' method is abstract
 *     method, this stereotype is not needed.)
 * \li [Secret Agreement] Only the specific class has to use the method.  The condition
 *     is written in 'attention'. (If the method is 'internal', this stereotype is not
 *     needed. But, 'attention' has to explain about "Secret Agreement" for co-workers)
 */

?>