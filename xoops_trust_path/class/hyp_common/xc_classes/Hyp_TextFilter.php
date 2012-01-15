<?php
/*
 * Created on 2011/11/17 by nao-pon http://xoops.hypweb.net/
 * $Id: Hyp_TextFilter.php,v 1.4 2012/01/15 00:08:40 nao-pon Exp $
 */

class Hyp_TextFilter extends Legacy_TextFilter
{
    var $hypInternalTags = array('email', 'siteimg', 'img', 'siteurl', 'url');
    var $hypEscTags      = array('quote', 'color', 'font', 'size', 'b', 'c', 'd', 'i', 'u');
    var $hypBypassTags   = array('fig');

    function Hyp_TextFilter() {
        parent::Legacy_TextFilter();
        $this->mMakeXCodeConvertTable->add('Hyp_TextFilter::makeXCodeConvertTable', XCUBE_DELEGATE_PRIORITY_3);
        $this->mMakeXCodeConvertTable->add(array(& $this, 'getXcodeBBcode'), XCUBE_DELEGATE_PRIORITY_FINAL);
    }

    function makeXCodeConvertTable(& $patterns, & $replacements) {
        if ($key = array_search('/\[quote\]/sU', $patterns)) {
            $replacements[0][$key] = $replacements[1][$key] = '<div class="paragraph">'._QUOTEC.'<div class="xoopsQuote"><blockquote>';
        }
        if ($key = array_search('/\[\/quote\]/sU', $patterns)) {
            $replacements[0][$key] = $replacements[1][$key] = '</blockquote></div></div>';
        }
        $patterns[] = "/\[quote sitecite=([^\"'<>]*)\]/sU";
        $replacements[0][] = $replacements[1][] = '<div class="paragraph">'._QUOTEC.'<div class="xoopsQuote"><blockquote cite="'.XOOPS_URL.'/\\1">';
    }

    function getXcodeBBcode($patterns, $replacements) {
    	$_arr = $this->hypBypassTags;
    	foreach($patterns as $_pat) {
    		if (preg_match('#^/\\\\\[([a-zA-Z0-9_-]+)\b#', $_pat, $_match)) {
   				$_arr[] = $_match[1];
    		}
    	}
    	$this->hypBypassTags = array_unique(array_diff($_arr, $this->hypEscTags, $this->hypInternalTags));
    }

    // Over write
    function getInstance(&$instance) {
        if (empty($instance)) {
            $instance = new Hyp_TextFilter();
        }
    }

    // Over write
    function toShowTarea($text, $html = 0, $smiley = 1, $xcode = 1, $image = 1, $br = 1, $x2comat = false, $cache = 1) {
        if ($html != 1) {
            $text = $this->renderWikistyle($text, $html, $smiley, $xcode, $image, $br, $cache);
        } else {
            $text = $this->preConvertXCode($text, $xcode);
            $text = $this->makeClickable($text);
            if ($smiley != 0) $text = $this->smiley($text);
        }
        if ($xcode != 0) $text = $this->convertXCode($text, $image);
        if (!$html) {
            $text = $this->renderWikistyleFinsher($text);
        }
        if ($html && $br != 0) $text = $this->nl2Br($text);
        if ($html) $text = $this->postConvertXCode($text, $xcode, $image);
        return $text;
    }

	// Over write
	function toPreviewTarea($text, $html = 0, $smiley = 1, $xcode = 1, $image = 1, $br = 1, $x2comat=false) {
		return $this->toShowTarea($text, $html, $smiley, $xcode, $image, $br, $x2comat, 0);
	}

    // Original function
    function renderWiki_getEscTags () {
        rsort($this->hypEscTags);
        return $this->hypEscTags;
    }

    // Original function
    function renderWiki_getBypassTags () {
        rsort($this->hypBypassTags);
        return $this->hypBypassTags;
    }

    // Original function
    function &renderWikistyle($text, $html = 0, $smiley = 1, $xcode = 1, $image = 1, $br = 1, $use_cache = 0)
    {
        static $pat = array();
        static $rep = array();

        $className = get_class($this);

        $br = ($br)? 1 : 0;
        $use_cache = ($use_cache)? 1 : 0;
        $smiley = ($smiley)? 1 : 0;
        $image = ($image)? 1 : 0;

        // xpWiki
        if (! class_exists('XpWiki')) {
            include XOOPS_TRUST_PATH . '/modules/xpwiki/include.php';
        }

        $render = XpWiki::getSingleton(XPWIKI_RENDERER_DIR);

        // pukiwiki.ini.php setting
        $render->setIniRoot('line_break', $br);
        $render->setIniRoot('render_use_cache', $use_cache);
        $render->setIniRoot('use_extra_facemark', 1);
        $render->setIniRoot('usefacemark', $smiley);
        $render->setIniRoot('render_cache_min', 1440); // 1day
        $render->setIniRoot('link_target', '_blank');
        $render->setIniRoot('nowikiname', 1);
        $render->setIniRoot('show_passage', 0);
        $render->setIniRoot('no_slashes_commentout', 1);

        if ($xcode) {
            if (! isset($pat[$className][$image])) {
                // BB Code code
                $pat[$className][$image][] = '/(?:\r\n|\r|\n)?\[code](?:\r\n|\r|\n)?(.*)(?:\r\n|\r|\n)?\[\/code\](?:\r\n|\r|\n)?/sUS';
                $rep[$className][$image][] = "\n".'#code(){{{'."\n".'$1'."\n".'}}}'."\n";

                // BB Code email
                $pat[$className][$image][] = '/\[email](.+?)\[\/email]/iS';
                $rep[$className][$image][] = '$1';

                // BB Code url
                $pat[$className][$image][] = '/\[url=([\'"]?)((?:ht|f)tp[s]?:\/\/[!~*\'();\/?:\@&=+\$,%#\w.-]+)\\1\](.+)\[\/url\]/esUS';
                $rep[$className][$image][] = '\'[[\'.Hyp_TextFilter::renderWiki_ret2br(\'$3\').\':$2]]\'';

                $pat[$className][$image][] = '/\[url=([\'"]?)([!~*\'();\/?:\@&=+\$,%#\w.-]+)\\1\](.+)\[\/url\]/esUS';
                $rep[$className][$image][] = '\'[[\'.Hyp_TextFilter::renderWiki_ret2br(\'$3\').\':http://$2]]\'';

                $pat[$className][$image][] = '/\[siteurl=([\'"]?)\/?([!~*\'();?:\@&=+\$,%#\w.-][!~*\'();\/?:\@&=+\$,%#\w.-]+)\\1\](.+)\[\/siteurl\]/esUS';
                $rep[$className][$image][] = '\'[[\'.Hyp_TextFilter::renderWiki_ret2br(\'$3\').\':http:///$2]]\'';

                // BB Code quote
                $pat[$className][$image][] = '/(\[quote[^\]]*])(?:\r\n|\r|\n)(?![<>*|,#: \t+-])/S';
                $rep[$className][$image][] = "\n\n$1";
                $pat[$className][$image][] = '/(?:\r\n|\r|\n)*\[\/quote\]/S';
                $rep[$className][$image][] = '[/quote]'."\n\n";

                if ($image) {
                    // BB Code image with align
                    $pat[$className][$image][] = '/\[img\s+align=([\'"]?)(left|center|right)\1(?:\s+title=([\'"]?)([^\'"][^\]\s]*?)\3)?(?:\s+w(?:idth)?=([\'"]?)([\d]+?)\5)?(?:\s+h(?:eight)?=([\'"]?)([\d]+?)\7)?]([!~*\'();\/?:\@&=+\$,%#\w.-]+)\[\/img\]/US';
                    $rep[$className][$image][] = '&ref($9,$2,"t:$4",mw:$6,mw:$8);';

                    // BB Code image normal
                    $pat[$className][$image][] = '/\[img(?:\s+title=([\'"]?)([^\'"][^\]\s]*?)\1)?(?:\s+w(?:idth)?=([\'"]?)([\d]+?)\3)?(?:\s+h(?:eight)?=([\'"]?)([\d]+?)\5)?]([!~*\'();\/?:\@&=+\$,%#\w.-]+)\[\/img\]/US';
                    $rep[$className][$image][] = '&ref($7,"t:$2",mw:$4,mw:$6);';
                } else {
                    // BB Code image with align
                    $pat[$className][$image][] = '/\[img\s+align=([\'"]?)(left|center|right)\1(?:\s+title=([\'"]?)([^\'"][^\]\s]*?)\3)?(?:\s+w(?:idth)?=([\'"]?)([\d]+?)\5)?(?:\s+h(?:eight)?=([\'"]?)([\d]+?)\7)?]([!~*\'();\/?:\@&=+\$,%#\w.-]+)\[\/img\]/US';
                    $rep[$className][$image][] = '&ref($9,"t:$4",noimg);';

                    // BB Code image normal
                    $pat[$className][$image][] = '/\[img(?:\s+title=([\'"]?)([^\'"][^\]\s]*?)\1)?(?:\s+w(?:idth)?=([\'"]?)([\d]+?)\3)?(?:\s+h(?:eight)?=([\'"]?)([\d]+?)\5)?]([!~*\'();\/?:\@&=+\$,%#\w.-]+)\[\/img\]/US';
                    $rep[$className][$image][] = '&ref($7,"t:$2",noimg);';
                }

				// BB Code siteimage with align
				$pat[$className][$image][] = '/\[siteimg\s+align=([\'"]?)(left|center|right)\1(?:\s+title=([\'"]?)([^\'"][^\]\s]*?)\3)?(?:\s+w(?:idth)?=([\'"]?)([\d]+?)\5)?(?:\s+h(?:eight)?=([\'"]?)([\d]+?)\7)?]\/?([!~*\'();?\@&=+\$,%#\w.-][!~*\'();\/?\@&=+\$,%#\w.-]+?)\[\/siteimg\]/US';
				$rep[$className][$image][] = '&ref(http:///$9,$2,"t:$4",mw:$6,mw:$8);';

				// BB Code siteimage normal
				$pat[$className][$image][] = '/\[siteimg(?:\s+title=([\'"]?)([^\'"][^\]\s]*?)\1)?(?:\s+w(?:idth)?=([\'"]?)([\d]+?)\3)?(?:\s+h(?:eight)?=([\'"]?)([\d]+?)\5)?]\/?([!~*\'();?\@&=+\$,%#\w.-][!~*\'();\/?\@&=+\$,%#\w.-]+?)\[\/siteimg\]/US';
				$rep[$className][$image][] = '&ref(http:///$7,"t:$2",mw:$4,mw:$6);';

                // Some BB Code Tags, Contents allows xpWiki rendering.
                if ($_reg = join('|', $this->renderWiki_getEscTags())) {
                    $pat[$className][$image][] = '/\[\/?(?:' . $_reg . ')(?:(?: |=)[^\]]+)?\]/eS';
                    $rep[$className][$image][] = '\'[ b 6 4 ]\' . base64_encode(\'$0\') . \'[ / b 6 4 ]\'';
                }

                // Other or Unknown BB Code Tags, All part escapes.
                if ($_reg = join('|', $this->renderWiki_getBypassTags())) {
                    $pat[$className][$image][] = '/\[(' . $_reg . ')(?:\b[^\]]+)?].+\[\/\\1\]/esUS';
                    $rep[$className][$image][] = '\'[ b 6 4 ]\' . base64_encode(\'$0\') . \'[ / b 6 4 ]\'';
                }

            }

            $text = preg_replace($pat[$className][$image], $rep[$className][$image], $text);

        }

        if ($text = $render->transform($text, XPWIKI_RENDERER_DIR)) {
            if (isset($pat[$className])) {
                // BB Code decode
                $text = preg_replace(
                        '/\[ b 6 4 ](.+?)\[ \/ b 6 4 ]/eS',
                        'Hyp_TextFilter::renderWiki_base64decode(\'$1\',\''.$render->root->word_breaker.'\')',
                        $text);
            }

            // XOOPS Quote style
            $text = str_replace(
                array('<blockquote>','</blockquote>'),
                array('<div class="paragraph">'._QUOTEC.'<div class="xoopsQuote"><blockquote>','</blockquote></div></div>'),$text
            );
        }

        return $text;
    }

    // Original function
    function renderWiki_ret2br($text)
    {
        $text = str_replace('\\"', '"', $text);
        return str_replace(array("\r\n", "\r", "\n"), '&br;', $text);
    }

    // Original function
    function renderWiki_base64decode($text, $word_breaker) {
        return str_replace(array('<','>','\\"'),array('&lt;','&gt;','"'),base64_decode(strip_tags(str_replace($word_breaker, '', $text))));
    }

    // Original function
    function renderWikistyleFinsher($input) {
        //$input = str_replace(array("\x07", "\x08"), array('<div>', '</div>'), $this->renderWikistyleParagraphRegularize($input));
        $input = $this->renderWikistyleParagraphRegularize($input);
        return $input;
    }

    // Original function
    function renderWikistyleParagraphRegularize($input) {
        // remove <p> include block elements.
        $regex = '#<p>((?:[^<]+|<(?!/?p[^>]*?>)|(?R))+)</p>#';
        if (is_array($input)) {
            if (preg_match('/<(?:div|p|pre|code)/i', $input[1])) {
                //$input = '<div>' . $input[1] . '</div>';
                //$input = "\x07" . $input[1]. "\x08";
                $input = $input[1];
            } else {
                return $input[0];
            }
        }
        return preg_replace_callback($regex, array(& $this, 'renderWikistyleParagraphRegularize'), $input);
    }
}