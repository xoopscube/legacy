<?php
/**
 * Removes comment and splits large sql files into individual queries
 * Function from phpMyAdmin (https://phpwizard.net/projects/phpMyAdmin/)
 * @package    database
 * @version    XCL 2.5.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Other authors Minahito, 2007/05/15
 * @author     Other authors Kazumi Ono (aka onokazu)
 * @author     gandon, 2001
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 */

class sqlutility
{
    /**
    * Function from phpMyAdmin (http://phpwizard.net/projects/phpMyAdmin/)
    *
    * Removes comment and splits large sql files into individual queries
    *
    * Last revision: September 23, 2001 - gandon
    *
    * @param   array    the splitted sql commands
    * @param   string   the sql commands
    * @return  bool  always true
    * @access  public
    */
    public static function splitMySqlFile(&$ret, $sql)
    {
        $sql               = trim($sql);
        $sql_len           = strlen($sql);
        $char              = '';
        $string_start      = '';
        $in_string         = false;

        for ($i = 0; $i < $sql_len; ++$i) {
            $char = $sql[$i];

           // We are in a string, check for not escaped end of
           // strings except for backquotes that can't be escaped
           if ($in_string) {
               for (;;) {
                   $i         = strpos($sql, $string_start, $i);
                    // No end of string found -> add the current
                    // substring to the returned array
                   if (!$i) {
                       $ret[] = $sql;
                       return true;
                   }

                   if ('`' === $string_start || '\\' !== $sql[$i - 1]) {
                       $string_start      = '';
                       $in_string         = false;
                       break;
                   }
                   // Backquotes or no backslashes before
                   // quotes: it's indeed the end of the
                   // string -> exit the loop
                   // one or more Backslashes before the presumed
                   // end of string...

                   // first checks for escaped backslashes
                   $j                     = 2;
                   $escaped_backslash     = false;
                   while ($i-$j > 0 && '\\' === $sql[$i - $j]) {
                       $escaped_backslash = !$escaped_backslash;
                       $j++;
                   }
                   // ... if escaped backslashes: it's really the
                   // end of the string -> exit the loop
                   if ($escaped_backslash) {
                       $string_start  = '';
                       $in_string     = false;
                       break;
                   }
                   // ... else loop

                   $i++; // end if...elseif...else
               } // end for
           } // end if (in string)
            // We are not in a string, first check for delimiter...
            elseif (';' === $char) {
                // if delimiter found, add the parsed part to the returned array
                $ret[]    = substr($sql, 0, $i);
                $sql      = ltrim(substr($sql, min($i + 1, $sql_len)));
                $sql_len  = strlen($sql);
                if ($sql_len) {
                    $i      = -1;
                } else {
                    // The submited statement(s) end(s) here
                    return true;
                }
            } // end else if (is delimiter)
            // ... then check for start of a string,...
            elseif (('"' === $char) || ('\'' === $char) || ('`' === $char)) {
                $in_string    = true;
                $string_start = $char;
            } // end else if (is start of string)

            // for start of a comment (and remove this comment if found)...
            elseif ('#' === $char || (' ' === $char && $i > 1 && '--' == $sql[$i - 2] . $sql[$i - 1])) {
                // starting position of the comment depends on the comment type
                   $start_of_comment = (('#' === $sql[$i]) ? $i : $i - 2);
                // if no "\n" exits in the remaining string, checks for "\r"
                // (Mac eol style)
                   $end_of_comment   = (strpos(' ' . $sql, "\012", $i+2))
                              ?: strpos(' ' . $sql, "\015", $i + 2);
                if (!$end_of_comment) {
                    // no eol found after '#', add the parsed part to the returned
                // array and exit
                    // RMV fix for comments at end of file
                       $last = trim(substr($sql, 0, $i-1));
                    if (!empty($last)) {
                        $ret[] = $last;
                    }
                    return true;
                }

                $sql     = substr($sql, 0, $start_of_comment) . ltrim(substr($sql, $end_of_comment));
                $sql_len = strlen($sql);
                $i--; // end if...else
            } // end else if (is comment)
        } // end for

        // add any rest to the returned array
        if (!empty($sql) && '' !== trim($sql)) {
            $ret[] = $sql;
        }
        return true;
    }

    /**
     * add a prefix.'_' to all tablenames in a query
     *
     * @param   string  $query  valid SQL query string
     * @param   string  $prefix prefix to add to all table names
     * @return  mixed   FALSE on failure
     */
    public static function prefixQuery($query, $prefix)
    {
        $pattern = "/^(INSERT INTO|CREATE TABLE|ALTER TABLE|UPDATE)(\s)+([`]?)([^`\s]+)\\3(\s)+/siU";
        $pattern2 = "/^(DROP TABLE)(\s)+([`]?)([^`\s]+)\\3(\s)?$/siU";
        if (preg_match($pattern, $query, $matches) || preg_match($pattern2, $query, $matches)) {
            $replace = "\\1 ".$prefix."_\\4\\5";
            $matches[0] = preg_replace($pattern, $replace, $query);

            // CREATE TABLE force utf8 to utf8mb4 - MySQL ENGINE = InnoDB
            // DB Engine use default (remove MyISAM)
            if ($matches[1] === 'CREATE TABLE') {
                $matches[0] = preg_replace('/ ENGINE=MyISAM/i', '', $matches[0]);
                if (!preg_match("/ CHARACTER SET /i", $matches[0])) {
                    //$matches[0] .= ' CHARACTER SET utf8';
                    $matches[0] .= ' CHARACTER SET utf8mb4 ';
                }
            }
            return $matches;
        }
        return false;
    }
}
