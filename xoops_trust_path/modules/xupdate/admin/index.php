<?php
/**
 * @file
 * @brief The page controller in the directory
 * @package xupdate
 * @version $Id$
**/

$root =& XCube_Root::getSingleton();
$root->mContext->mModule->setAdminMode(true);

$headerScript = $root->mContext->getAttribute( 'headerScript' );
$headerScript->addStylesheet( '/modules/xupdate/style.css' );

$root->mController->executeHeader();
$root->mController->execute();


$xoopsLogger=&$root->mController->getLogger();
$xoopsLogger->stopTime();
$root->mController->executeView();

?>
