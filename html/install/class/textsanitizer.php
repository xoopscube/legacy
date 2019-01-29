<?php
// $Id: textsanitizer.php,v 1.1 2007/05/15 02:35:13 minahito Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author: Kazumi Ono (http://www.myweb.ne.jp/, http://jp.xoops.org/)        //
//         Goghs Cheng (http://www.eqiao.com, http://www.devbeez.com/)       //
// Project: The XOOPS Project (http://www.xoops.org/)                        //
// ------------------------------------------------------------------------- //
// This is subset and modified version of module.textsanitizer.php
if (get_magic_quotes_runtime()) {
    set_magic_quotes_runtime(0);
}

class textsanitizer
{

    /*
    * Constructor of this class
    * Gets allowed html tags from admin config settings
    * <br> should not be allowed since nl2br will be used
    * when storing data
    */

    public static function getInstance()
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new TextSanitizer();
        }
        return $instance;
    }

    public function &makeClickable(&$text)
    {
        $patterns = array("/([^]_a-z0-9-=\"'\/])([a-z]+?):\/\/([^, \r\n\"\(\)'<>]+)/i", "/([^]_a-z0-9-=\"'\/])www\.([a-z0-9\-]+)\.([^, \r\n\"\(\)'<>]+)/i", "/([^]_a-z0-9-=\"'\/])([a-z0-9\-_.]+?)@([^, \r\n\"\(\)'<>]+)/i");
        $replacements = array("\\1<a href=\"\\2://\\3\" rel=\"external\">\\2://\\3</a>", "\\1<a href=\"http://www.\\2.\\3\" rel=\"external\">www.\\2.\\3</a>", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>");
        $ret = preg_replace($patterns, $replacements, $text);
        return $ret;
    }

    public function &nl2Br($text)
    {
        $ret = preg_replace("/(\015\012)|(\015)|(\012)/", "<br />", $text);
        return $ret;
    }

    public function &addSlashes($text, $force=false)
    {
        if ($force) {
            $ret = addslashes($text);
            return $ret;
        }
        if (!get_magic_quotes_gpc()) {
            $text = addslashes($text);
        }
        return $text;
    }

    /*
    * if magic_quotes_gpc is on, stirip back slashes
    */
    public function &stripSlashesGPC($text)
    {
        if (get_magic_quotes_gpc()) {
            $text = stripslashes($text);
        }
        return $text;
    }

    /*
    *  for displaying data in html textbox forms
    */
    public function &htmlSpecialChars($text)
    {
        $text = preg_replace("/&amp;/i", '&', htmlspecialchars($text, ENT_QUOTES));
        return $text;
    }

    public function &undoHtmlSpecialChars(&$text)
    {
        $ret = preg_replace(array("/&gt;/i", "/&lt;/i", "/&quot;/i", "/&#039;/i"), array(">", "<", "\"", "'"), $text);
        return $ret;
    }

    /*
    *  Filters textarea form data in DB for display
    */
    public function &displayText($text, $html=false)
    {
        if (! $html) {
            // html not allowed
            $text =& $this->htmlSpecialChars($text);
        }
        $text =& $this->makeClickable($text);
        $text =& $this->nl2Br($text);
        return $text;
    }

    /*
    *  Filters textarea form data submitted for preview
    */
    public function &previewText($text, $html=false)
    {
        $text =& $this->stripSlashesGPC($text);
        return $this->displayText($text, $html);
    }

##################### Deprecated Methods ######################

    public function sanitizeForDisplay($text, $allowhtml = 0, $smiley = 1, $bbcode = 1)
    {
        if ($allowhtml == 0) {
            $text = $this->htmlSpecialChars($text);
        } else {
            //$config =& $GLOBALS['xoopsConfig'];
            //$allowed = $config['allowed_html'];
            //$text = strip_tags($text, $allowed);
            $text = $this->makeClickable($text);
        }
        if ($smiley == 1) {
            $text = $this->smiley($text);
        }
        if ($bbcode == 1) {
            $text = $this->xoopsCodeDecode($text);
        }
        $text = $this->nl2Br($text);
        return $text;
    }

    public function sanitizeForPreview($text, $allowhtml = 0, $smiley = 1, $bbcode = 1)
    {
        $text = $this->oopsStripSlashesGPC($text);
        if ($allowhtml == 0) {
            $text = $this->htmlSpecialChars($text);
        } else {
            //$config =& $GLOBALS['xoopsConfig'];
            //$allowed = $config['allowed_html'];
            //$text = strip_tags($text, $allowed);
            $text = $this->makeClickable($text);
        }
        if ($smiley == 1) {
            $text = $this->smiley($text);
        }
        if ($bbcode == 1) {
            $text = $this->xoopsCodeDecode($text);
        }
        $text = $this->nl2Br($text);
        return $text;
    }

    public function makeTboxData4Save($text)
    {
        //$text = $this->undoHtmlSpecialChars($text);
        return $this->addSlashes($text);
    }

    public function makeTboxData4Show($text, $smiley=0)
    {
        $text = $this->htmlSpecialChars($text);
        return $text;
    }

    public function makeTboxData4Edit($text)
    {
        return $this->htmlSpecialChars($text);
    }

    public function makeTboxData4Preview($text, $smiley=0)
    {
        $text = $this->stripSlashesGPC($text);
        $text = $this->htmlSpecialChars($text);
        return $text;
    }

    public function makeTboxData4PreviewInForm($text)
    {
        $text = $this->stripSlashesGPC($text);
        return $this->htmlSpecialChars($text);
    }

    public function makeTareaData4Save($text)
    {
        return $this->addSlashes($text);
    }

    public function &makeTareaData4Show(&$text, $html=1, $smiley=1, $xcode=1)
    {
        return $this->displayTarea($text, $html, $smiley, $xcode);
    }

    public function makeTareaData4Edit($text)
    {
        return htmlSpecialChars($text, ENT_QUOTES);
    }

    public function &makeTareaData4Preview(&$text, $html=1, $smiley=1, $xcode=1)
    {
        return $this->previewTarea($text, $html, $smiley, $xcode);
    }

    public function makeTareaData4PreviewInForm($text)
    {
        //if magic_quotes_gpc is on, do stipslashes
        $text = $this->stripSlashesGPC($text);
        return htmlSpecialChars($text, ENT_QUOTES);
    }

    public function makeTareaData4InsideQuotes($text)
    {
        return $this->htmlSpecialChars($text);
    }

    public function &oopsStripSlashesGPC($text)
    {
        return $this->stripSlashesGPC($text);
    }

    public function &oopsStripSlashesRT($text)
    {
        if (get_magic_quotes_runtime()) {
            $text =& stripslashes($text);
        }
        return $text;
    }

    public function &oopsAddSlashes($text)
    {
        return $this->addSlashes($text);
    }

    public function &oopsHtmlSpecialChars($text)
    {
        return $this->htmlSpecialChars($text);
    }

    public function &oopsNl2Br($text)
    {
        return $this->nl2br($text);
    }
}
