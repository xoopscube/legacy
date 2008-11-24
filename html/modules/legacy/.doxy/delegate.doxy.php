<?php

//--------------------------------------------------------------------------
// This file has only doxygen command comments for generating documents.
// Because PHP is script file, too many comments causes performance problem.
// This file is an independent file which is not called from any files, and
// affects only doxygen's working.
//--------------------------------------------------------------------------

/**
 * \page legacy_delegate Event Delegates of Legacy
 * 
 *  \li \ref site_checklogin
 *  \li \ref site_checklogin_success
 * 
 * \section site_checklogin Site.CheckLogin
 *   Legacy_Controller delegates authenticating in Legacy_Controller::checkLogin().
 *   Authenication logic generates a XoopsUser object and sets it to $ppXoopsUser if the
 *   current request does login successful.
 * 
 *   \subsection site_checklogin_sig Signature
 *     \code
 *       function myfunc(&$ppXoopsUser)
 *     \endcode
 * 
 *   \subsection site_checklogin_param Parameters
 *     \li XoopsUser &$ppXoopsUser (XoopsUser**) - Receives a XoopsUser object from some auth system which allows login.
 * 
 * 
 * \section site_checklogin_success Site.CheckLogin.Success
 *   If the current user does login successful in Legacy_Controller::checkLogin(),
 *   Legacy_Controller raises this event after it initializes $xoopsUser and his environment.
 * 
 *   \subsection site_checklogin_success_sig Signature
 *     \code
 *       function myfunc(&$xoopsUser)
 *     \endcode
 * 
 *   \subsection site_checklogin_success_param Parameters
 *     \li XoopsUser &$xoopsUser - The current user who did login in Legacy_Controller::checkLogin()
 * 
 */

?>