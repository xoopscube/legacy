<?php
/*
 * Created on 2007/11/27 by nao-pon http://hypweb.net/
 * $Id: Conf_EUC-KR.php,v 1.2 2011/07/29 01:37:52 nao-pon Exp $
 */

// Encoding hint
$_LANG['encode_hint'] = 'ª×';

// Accept language
$const['ACCEPT_UILANG'] = 'ko,en';

// Array for normalization of page name. Characters that cannot be used.  [ ] < > # & " :
$root->pagename_illegality = array('[',  ']',  '<',  '>',  '#',  '&',  '"',  ':');
$root->pagename_normalizer = array('£Û', '£Ý', '£¼', '£¾', '££', '£¦', '¡±', '£º');
