<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Include the {@link shared.make_timestamp.php} plugin
 */
require_once $smarty->_get_plugin_filepath('shared', 'make_timestamp');
/**
 * Smarty date_format modifier plugin
 *
 * Type:     modifier<br>
 * Name:     date_format<br>
 * Purpose:  format datestamps via strftime<br>
 * Input:<br>
 *         - string: input date string
 *         - format: strftime format for output
 *         - default_date: default date if $string is empty
 * @link https://smarty.php.net/manual/en/language.modifier.date.format.php
 *          date_format (Smarty online manual)
 * @author   Monte Ohrt <monte at ohrt dot com>
 * @param string
 * @param string
 * @param string
 * @return string|void
 * @uses smarty_make_timestamp()
 */
function smarty_modifier_date_format($string, $format = null, $default_date = null)
{
    if ($string != '') {
        $timestamp = smarty_make_timestamp($string);
    } elseif ($default_date != '') {
        $timestamp = smarty_make_timestamp($default_date);
    } else {
        return '';
    }

    if ($format === null) {
        $format = '%b %e, %Y';
    }

    // Convert strftime format to DateTime format
    $formatMap = array(
        '%a' => 'D',
        '%A' => 'l',
        '%b' => 'M',
        '%B' => 'F',
        '%c' => 'c',
        '%d' => 'd',
        '%H' => 'H',
        '%I' => 'h',
        '%j' => 'z',
        '%m' => 'm',
        '%M' => 'i',
        '%p' => 'A',
        '%S' => 's',
        '%U' => 'W',
        '%w' => 'w',
        '%W' => 'W',
        '%y' => 'y',
        '%Y' => 'Y',
        '%e' => 'j',
    );

    $dateFormat = strtr($format, $formatMap);
    $date = new DateTime();
    $date->setTimestamp($timestamp);
    
    return $date->format($dateFormat);
}

/* vim: set expandtab: */

?>
