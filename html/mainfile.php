<?php
// $Id: mainfile.php,v 1.1 2007/05/15 02:34:30 minahito Exp $
/**
 * XOOPSCube is not installed, redirect to the installer 
 **/

// XOOPSCube is not installed yet.
if(! defined('XOOPS_INSTALL')){
    header('Location: install/index.php');
}

