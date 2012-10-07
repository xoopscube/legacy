<?php
// $Id: sqlutility.php,v 1.1 2007/05/15 02:35:14 minahito Exp $
// sqlutility.php - defines utility class for MySQL database
/**
 * @package     kernel
 * @subpackage  database
 * 
 * @author	    Kazumi Ono	<onokazu@xoops.org>
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 */

/**
 * provide some utility methods for databases
 * 
 * @author Kazumi Ono <onokazu@xoops.org>
 * @copyright copyright (c) 2000-2003 XOOPS.org
 * 
 * @package kernel
 * @subpackage  database
 */
class SqlUtility
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
 	* @return  boolean  always true
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
					// Backquotes or no backslashes before 
					// quotes: it's indeed the end of the 
					// string -> exit the loop
                	else if ($string_start == '`' || $sql[$i-1] != '\\') {
						$string_start      = '';
                   		$in_string         = false;
                    	break;
                	}
                	// one or more Backslashes before the presumed 
					// end of string...
                	else {
						// first checks for escaped backslashes
                    	$j                     = 2;
                    	$escaped_backslash     = false;
						while ($i-$j > 0 && $sql[$i-$j] == '\\') {
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
                    	else {
							$i++;
                    	}
                	} // end if...elseif...else
            	} // end for
        	} // end if (in string)
        	// We are not in a string, first check for delimiter...
        	else if ($char == ';') {
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
        	else if (($char == '"') || ($char == '\'') || ($char == '`')) {
				$in_string    = true;
				$string_start = $char;
        	} // end else if (is start of string)

        	// for start of a comment (and remove this comment if found)...
        	else if ($char == '#' || ($char == ' ' && $i > 1 && $sql[$i-2] . $sql[$i-1] == '--')) {
            	// starting position of the comment depends on the comment type
           		$start_of_comment = (($sql[$i] == '#') ? $i : $i-2);
            	// if no "\n" exits in the remaining string, checks for "\r"
            	// (Mac eol style)
           		$end_of_comment   = (strpos(' ' . $sql, "\012", $i+2))
                              ? strpos(' ' . $sql, "\012", $i+2)
                              : strpos(' ' . $sql, "\015", $i+2);
           		if (!$end_of_comment) {
                // no eol found after '#', add the parsed part to the returned
                // array and exit
					// RMV fix for comments at end of file
               		$last = trim(substr($sql, 0, $i-1));
					if (!empty($last)) {
						$ret[] = $last;
					}
               		return true;
				} else {
                	$sql     = substr($sql, 0, $start_of_comment) . ltrim(substr($sql, $end_of_comment));
                	$sql_len = strlen($sql);
                	$i--;
            	} // end if...else
        	} // end else if (is comment)
    	} // end for

    	// add any rest to the returned array
    	if (!empty($sql) && trim($sql) != '') {
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
		$pattern = "/^(INSERT INTO|CREATE TABLE|ALTER TABLE|UPDATE|ALTER SEQUENCE)(\s)+([`]?)([^`\s]+)\\3(\s)+/siU";
		$pattern2 = "/^(DROP TABLE)(\s)+([`]?)([^`\s]+)\\3(\s)?$/siU";

		$pattern3 = "/^(CREATE INDEX|CREATE UNIQUE INDEX)(\s)+([^\"\s]+)(\s)?ON(\s)+([^`\s]+)(\s)*\(([^\(\)]+)\)/siU";

		if (preg_match($pattern, $query, $matches) || preg_match($pattern2, $query, $matches)) {
			$replace = "\\1 ".$prefix."_\\4\\5";
			$matches[0] = preg_replace($pattern, $replace, $query);
			return $matches;
		}

        // PosgreSQL で CREATE INDEX するので
        if(preg_match($pattern3, $query, $matches)){
            $replace = "\\1 \\3 ON ".$prefix."_\\6 (\\8)";
			$matches[0] = preg_replace($pattern3, $replace, $query);
            return $matches;
        }

		return false;
	}
    //---------- for PostgreSQL : see phpPgAdmin-5.0.3
    /**
     * A private helper method for executeScript that advances the
     * character by 1.  In psql this is careful to take into account
     * multibyte languages, but we don't at the moment, so this function
     * is someone redundant, since it will always advance by 1
     * @param &$i The current character position in the line
     * @param &$prevlen Length of previous character (ie. 1)
     * @param &$thislen Length of current character (ie. 1)
     */
    private
    function advance_1(&$i, &$prevlen, &$thislen) {
        $prevlen = $thislen;
        $i += $thislen;
        $thislen = 1;
    }

    /**
     * Private helper method to detect a valid $foo$ quote delimiter at
     * the start of the parameter dquote
     * @return True if valid, false otherwise
     */
    private
    function valid_dolquote($dquote) {
        // XXX: support multibyte
        return (preg_match('/^[$][$]/', $dquote) || preg_match('/^[$][_[:alpha:]][_[:alnum:]]*[$]/', $dquote));
    }

    /**
     * Executes an SQL script as a series of SQL statements.  Returns
     * the result of the final step.  This is a very complicated lexer
     * based on the REL7_4_STABLE src/bin/psql/mainloop.c lexer in
     * the PostgreSQL source code.
     * XXX: It does not handle multibyte languages properly.
     * @param $name Entry in $_FILES to use
     * @param $callback (optional) Callback function to call with each query,
                                   its result and line number.
     * @return True for general success, false on any failure.
     */
    //function executeScript($name, $callback = null) {}
    function splitPgSqlFile(&$ret, $sql_string) {
//        global $data;
//
//        // This whole function isn't very encapsulated, but hey...
//        $conn = $data->conn->_connectionID;
//        if (!is_uploaded_file($_FILES[$name]['tmp_name'])) return false;
//
//        $fd = fopen($_FILES[$name]['tmp_name'], 'r');
//        if (!$fd) return false;

        // Build up each SQL statement, they can be multiline
        $query_buf = null;
        $query_start = 0;
        $in_quote = 0;
        $in_xcomment = 0;
        $bslash_count = 0;
        $dol_quote = null;
        $paren_level = 0;
        $len = 0;
        $i = 0;
        $prevlen = 0;
        $thislen = 0;
        $lineno = 0;

        // Loop over each line in the file
//        while (!feof($fd)) {
//            $line = fgets($fd);
        $_lines = explode("\n", $sql_string);
        foreach($_lines as $line){
            $lineno++;

            // Nothing left on line? Then ignore...
            if (trim($line) == '') continue;
            if (preg_match("/^#/", $line)) continue;

            $len = strlen($line);
            $query_start = 0;

            /*
             * Parse line, looking for command separators.
             *
             * The current character is at line[i], the prior character at line[i
             * - prevlen], the next character at line[i + thislen].
             */
            $prevlen = 0;
            $thislen = ($len > 0) ? 1 : 0;

            for ($i = 0; $i < $len; self::advance_1($i, $prevlen, $thislen)) {

                /* was the previous character a backslash? */
                if ($i > 0 && substr($line, $i - $prevlen, 1) == '\\')
                    $bslash_count++;
                else
                    $bslash_count = 0;

                /*
                 * It is important to place the in_* test routines before the
                 * in_* detection routines. i.e. we have to test if we are in
                 * a quote before testing for comments.
                 */

                /* in quote? */
                if ($in_quote !== 0)
                {
                    /*
                     * end of quote if matching non-backslashed character.
                     * backslashes don't count for double quotes, though.
                     */
                    if (substr($line, $i, 1) == $in_quote &&
                        ($bslash_count % 2 == 0 || $in_quote == '"'))
                        $in_quote = 0;
                }

                /* in or end of $foo$ type quote? */
                else if ($dol_quote) {
                    if (strncmp(substr($line, $i), $dol_quote, strlen($dol_quote)) == 0) {
                        self::advance_1($i, $prevlen, $thislen);
                        while(substr($line, $i, 1) != '$')
                            self::advance_1($i, $prevlen, $thislen);
                        $dol_quote = null;
                    }
                }

                /* start of extended comment? */
                else if (substr($line, $i, 2) == '/*')
                {
                    $in_xcomment++;
                    if ($in_xcomment == 1)
                        self::advance_1($i, $prevlen, $thislen);
                }

                /* in or end of extended comment? */
                else if ($in_xcomment)
                {
                    if (substr($line, $i, 2) == '*/' && !--$in_xcomment)
                        self::advance_1($i, $prevlen, $thislen);
                }

                /* start of quote? */
                else if (substr($line, $i, 1) == '\'' || substr($line, $i, 1) == '"') {
                    $in_quote = substr($line, $i, 1);
                }

                /*
                 * start of $foo$ type quote?
                 */
                //else if (!$dol_quote && $this->valid_dolquote(substr($line, $i))) {
                else if (!$dol_quote && self::valid_dolquote(substr($line, $i))) {
                    $dol_end = strpos(substr($line, $i + 1), '$');
                    $dol_quote = substr($line, $i, $dol_end + 1);
                    //self::advance_1($i, $prevlen, $thislen);
                    self::advance_1($i, $prevlen, $thislen);
                    while (substr($line, $i, 1) != '$') {
                        //self::advance_1($i, $prevlen, $thislen);
                        self::advance_1($i, $prevlen, $thislen);
                    }

                }

                /* single-line comment? truncate line */
                else if (substr($line, $i, 2) == '--')
                {
                    $line = substr($line, 0, $i); /* remove comment */
                    break;
                }

                /* count nested parentheses */
                else if (substr($line, $i, 1) == '(') {
                    $paren_level++;
                }

                else if (substr($line, $i, 1) == ')' && $paren_level > 0) {
                    $paren_level--;
                }

                /* semicolon? then send query */
                else if (substr($line, $i, 1) == ';' && !$bslash_count && !$paren_level)
                {
                    $subline = substr(substr($line, 0, $i), $query_start);
                    /* is there anything else on the line? */
                    if (strspn($subline, " \t\n\r") != strlen($subline))
                    {
                        /*
                         * insert a cosmetic newline, if this is not the first
                         * line in the buffer
                         */
                        if (strlen($query_buf) > 0)
                            $query_buf .= "\n";
                        /* append the line to the query buffer */
                        $query_buf .= $subline;
                        $query_buf .= ';';

                        // Execute the query (supporting 4.1.x PHP...). PHP cannot execute
                        // empty queries, unlike libpq
/*
                        if (function_exists('pg_query'))
                            $res = @pg_query($conn, $query_buf);
                        else
                            $res = @pg_exec($conn, $query_buf);
                        // Call the callback function for display
                        if ($callback !== null) $callback($query_buf, $res, $lineno);
                        // Check for COPY request
                        if (pg_result_status($res) == 4) { // 4 == PGSQL_COPY_FROM
                            while (!feof($fd)) {
                                $copy = fgets($fd, 32768);
                                $lineno++;
                                pg_put_line($conn, $copy);
                                if ($copy == "\\.\n" || $copy == "\\.\r\n") {
                                    pg_end_copy($conn);
                                    break;
                                }
                            }
                        }
*/
                        $ret[] = $query_buf;
                    }

                    $query_buf = null;
                    $query_start = $i + $thislen;
                }

                /*
                 * keyword or identifier?
                 * We grab the whole string so that we don't
                 * mistakenly see $foo$ inside an identifier as the start
                 * of a dollar quote.
                 */
                // XXX: multibyte here
                else if (preg_match('/^[_[:alpha:]]$/', substr($line, $i, 1))) {
                    $sub = substr($line, $i, $thislen);
                    while (preg_match('/^[\$_A-Za-z0-9]$/', $sub)) {
                        /* keep going while we still have identifier chars */
                        self::advance_1($i, $prevlen, $thislen);
                        $sub = substr($line, $i, $thislen);
                    }
                    // Since we're now over the next character to be examined, it is necessary
                    // to move back one space.
                    $i-=$prevlen;
                }
            } // end for

            /* Put the rest of the line in the query buffer. */
            $subline = substr($line, $query_start);
            if ($in_quote || $dol_quote || strspn($subline, " \t\n\r") != strlen($subline))
            {
                if (strlen($query_buf) > 0)
                    $query_buf .= "\n";
                $query_buf .= $subline;
            }

            $line = null;

        } // end while

        /*
         * Process query at the end of file without a semicolon, so long as
         * it's non-empty.
         */
        if (strlen($query_buf) > 0 && strspn($query_buf, " \t\n\r") != strlen($query_buf))
        {
            // Execute the query (supporting 4.1.x PHP...)
/*
            if (function_exists('pg_query'))
                $res = @pg_query($conn, $query_buf);
            else
                $res = @pg_exec($conn, $query_buf);
            // Call the callback function for display
            if ($callback !== null) $callback($query_buf, $res, $lineno);
            // Check for COPY request
            if (pg_result_status($res) == 4) { // 4 == PGSQL_COPY_FROM
                while (!feof($fd)) {
                    $copy = fgets($fd, 32768);
                    $lineno++;
                    pg_put_line($conn, $copy);
                    if ($copy == "\\.\n" || $copy == "\\.\r\n") {
                        pg_end_copy($conn);
                        break;
                    }
                }
            }
*/
            $ret[] = $query_buf;
        }

//        fclose($fd);

        return true;
    }
}
?>
