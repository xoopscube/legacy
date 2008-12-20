<?php
// $Id: images.php,v 1.1 2007/05/15 02:35:21 minahito Exp $
//%%%%%% Image Manager %%%%%


define('_MD_IMGMAIN','Image Manager Main');

define('_MD_ADDIMGCAT','Add Image Category:');
define('_MD_EDITIMGCAT','Edit Image Category:');
define('_MD_IMGCATNAME','Category Name:');
define('_MD_IMGCATRGRP','Select groups for image manager use:<br /><br /><span style="font-weight: normal;">These are groups allowed to use the image manager for selecting images but not uploading. Webmaster has automatic access.</span>');
define('_MD_IMGCATWGRP','Select groups allowed to upload images:<br /><br /><span style="font-weight: normal;">Typical usage is for moderator and admin groups.</span>');
define('_MD_IMGCATWEIGHT','Display order in image manager:');
define('_MD_IMGCATDISPLAY','Display this category?');
define('_MD_IMGCATSTRTYPE','Images are uploaded to:');
define('_MD_STRTYOPENG','This can not be changed afterwards!');
define('_MD_INDB',' Store in the database (as binary "blob" data)');
define('_MD_ASFILE',' Store as files (in uploads directory)<br />');
define('_MD_RUDELIMGCAT','Are you sure that you want to delete this category and all of its images files?');
define('_MD_RUDELIMG','Are you sure that you want to delete this images file?');

define('_MD_FAILDEL', 'Failed deleting image %s from the database');
define('_MD_FAILDELCAT', 'Failed deleting image category %s from the database');
define('_MD_FAILUNLINK', 'Failed deleting image %s from the server directory');
?>