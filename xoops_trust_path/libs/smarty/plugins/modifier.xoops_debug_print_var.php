<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty xoops_debug_print_var modifier plugin
 * Avoid inifinit loop for XOOP Cube Smarty Debug
 *
 * Type:     modifier<br>
 * Name:     xoops_debug_print_var<br>
 * Purpose:  formats variable contents for display in the console
 * @author   Monte Ohrt <monte at ohrt dot com>
 * @author   HIKAWA Kilica
 * @param array|object
 * @param integer
 * @param integer
 * @return string
 */
function smarty_modifier_xoops_debug_print_var($var, $depth = 0, $length = 40)
{
    $_replace = array(
        "\n" => '<i>\n</i>',
        "\r" => '<i>\r</i>',
        "\t" => '<i>\t</i>'
    );

    switch (gettype($var)) {
        case 'array' :
            $results = '<b>Array (' . count($var) . ')</b>';
            foreach ($var as $curr_key => $curr_val) {
                $results .= '<br>' . str_repeat('&nbsp;', $depth * 2)
                    . '<b>' . strtr($curr_key, $_replace) . '</b> =&gt; '
                    . smarty_modifier_xoops_debug_print_var($curr_val, ++$depth, $length);
                    $depth--;
            }
            break;
        
        case 'object' :
            $object_vars = get_object_vars($var);
			if ($depth){
				$results .= '<br>';
		        $results = '<b>' . get_class($var) . ' Object (' . count($object_vars) . ')</b>';
			}else{
				$results .= '<br>' . str_repeat('&nbsp;', $depth * 2);
          		$results = '<b> -&gt;' . get_class($var) . ' Object (' . count($object_vars) . ')</b>';
			}
//            $results .= '<pre>';
//            $results .= htmlspecialchars(print_r($object_vars ,true));
//            $results .= '<pre>';
//            break;
            //block infinite loop
	        foreach ($object_vars as $curr_key => $curr_val) {
				if ( gettype($curr_val) == 'object' ) {
					if ($depth){
						$results .= '<br>';
					}else{
						$results .= '<br>' . str_repeat('&nbsp;', $depth * 2);
					}
					$results .= '<b> -&gt;' . strtr($curr_key, $_replace) . '</b> = '. get_class($curr_val) . ' Object (' . count(get_object_vars($var)) . ')';
				}else{
					if ($depth){
						$results .= '<br>';
					}else{
						$results .= '<br>' . str_repeat('&nbsp;', $depth * 4);
					}
					$results .= '<b> -&gt;' . strtr($curr_key, $_replace) . '</b> = ';
	            	$results .= smarty_modifier_xoops_debug_print_var($curr_val, ++$depth, $length);
	         	}
	         	
	            $depth--;
        	}
          break;

/*
        case 'object' :
            $object_vars = get_object_vars($var);
            $results = '<b>' . get_class($var) . ' Object (' . count($object_vars) . ')</b>';
            foreach ($object_vars as $curr_key => $curr_val) {
                $results .= '<br>' . str_repeat('&nbsp;', $depth * 2)
                    . '<b> -&gt;' . strtr($curr_key, $_replace) . '</b> = '
                    . smarty_modifier_debug_print_var($curr_val, ++$depth, $length);
                    $depth--;
            }
            break;
*/
        case 'boolean' :
        case 'NULL' :
        case 'resource' :
            if (true === $var) {
                $results = 'true';
            } elseif (false === $var) {
                $results = 'false';
            } elseif (null === $var) {
                $results = 'null';
            } else {
                $results = htmlspecialchars((string) $var);
            }
            $results = '<i>' . $results . '</i>';
            break;
        case 'integer' :
        case 'float' :
            $results = htmlspecialchars((string) $var);
            break;
        case 'string' :
            $results = strtr($var, $_replace);
            if (strlen($var) > $length ) {
                $results = substr($var, 0, $length - 3) . '...';
            }
            $results = htmlspecialchars('"' . $results . '"');
            break;
        case 'unknown type' :
        default :
            $results = strtr((string) $var, $_replace);
            if (strlen($results) > $length ) {
                $results = substr($results, 0, $length - 3) . '...';
            }
            $results = htmlspecialchars($results);
    }

    return $results;
}

/* vim: set expandtab: */

?>
