<?php
// $Id: xoopsmailerlocal.php,v 1.1 2008/03/09 02:32:38 xoopserver Exp $
// License http://creativecommons.org/licenses/by/2.5/br/
class XoopsMailerLocal extends XoopsMailer {
function XoopsMailerLocal(){
$this->XoopsMailer();
$this->charSet = 'UTF-8';
}
}
?>