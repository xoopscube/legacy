<?php

// define('XPRESS_EVENT_DEBUG',1);

if (!function_exists('xpress_debug')) {
	function xpress_debug($title = '',$ditail_show = false)
	{
		$module_dirpath = dirname(dirname(__FILE__));
		$root_path = dirname(dirname(dirname(dirname(__FILE__))));
		$_debug_file = $module_dirpath . '/wp-content/xpress_debug.log';
		$_fp = fopen($_debug_file, 'a');
		$stamp = date("Y/m/d G:i:s" , time());
		$backtraces = array_reverse(debug_backtrace());
		fwrite($_fp, "\n*********************************************************************************************************\n");
		fwrite($_fp, $title . '(' . $stamp . ")\n");
		fwrite($_fp, '$_SERVER[]' . "\n");	
		$srerver = "\t" . str_replace("\n","\n\t",sprint_r($_SERVER));
		fwrite($_fp, $srerver . "\n\n");

		fwrite($_fp, "BACK TRACE" . "\n");	
		foreach($backtraces as $backtrace){
		$trace = $backtrace['file']. "\tLINE(" . $backtrace['line'] . ")\t" . $backtrace['function']  . "()\n";
		$trace = str_replace($root_path,"",$trace);
		$trace = str_replace("\\","/",$trace);
		$trace = str_replace($root_path,"",$trace);
		$trace = "\t" . $trace;

		$trace_ditail = "\t" . str_replace("\n","\n\t\t",sprint_r($backtrace));
		if ($ditail_show)
			fwrite($_fp, $trace . $trace_ditail . "\n");
		else
			fwrite($_fp, $trace . "\n");
		}
		fclose($_fp);
	}
}

if (!function_exists('xpress_debug_message')) {
	function xpress_debug_message($message = '')
	{
		$module_dirpath = dirname(dirname(__FILE__));
		$root_path = dirname(dirname(dirname(dirname(__FILE__))));
		$_debug_file = $module_dirpath . '/wp-content/xpress_debug.log';
		$_fp = fopen($_debug_file, 'a');
		$stamp = date("Y/m/d G:i:s" , time());
		fwrite($_fp, "\n*********************************************************************************************************\n");
		fwrite($_fp, '(' . $stamp . ")\n");
		fwrite($_fp, $message . "\n");	
		fclose($_fp);
	}
}

if (!function_exists('sprint_r')) {
    function sprint_r($var) {
             ob_start();
             print_r($var);
             $ret = ob_get_contents();
             ob_end_clean();
      return $ret;
    }
}

function xpress_error_handler($errno,$errstr,$errfile,$errline,$errcontext) {
	$module_dirpath = dirname(dirname(__FILE__));
	$root_path = dirname(dirname(dirname(dirname(__FILE__))));
	
	$show_backtrace = true;

	// Time stamp of error entry
	$dt = date("Y-m-d H:i:s (T)");

	// define an assoc array of error string
	// in reality the only entries we should
	// consider are E_WARNING, E_NOTICE, E_USER_ERROR,
	// E_USER_WARNING and E_USER_NOTICE
	$errortype = array (
		E_ERROR          => "Error",
		E_WARNING        => "Warning",
		E_PARSE          => "Parsing Error",
		E_NOTICE          => "Notice",
		E_CORE_ERROR      => "Core Error",
		E_CORE_WARNING    => "Core Warning",
		E_COMPILE_ERROR  => "Compile Error",
		E_COMPILE_WARNING => "Compile Warning",
		E_USER_ERROR      => "User Error",
		E_USER_WARNING    => "User Warning",
		E_USER_NOTICE    => "User Notice",
		E_STRICT          => "Runtime Notice"
	);
	if (strstr($errstr, 'Use of undefined constant xpress_debug_message - assumed') !== false) return;
	// set of errors for which a var trace will be saved
	$user_errors = array(E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE);

	$err = "<errorentry>\n";
	$err .= "\t<datetime>" . $dt . "</datetime>\n";
	$err .= "\t<errornum>" . $errno . "</errornum>\n";
	$err .= "\t<errortype>" . $errortype[$errno] . "</errortype>\n";
	$err .= "\t<errormsg>" . $errstr . "</errormsg>\n";
	$err .= "\t<scriptname>" . $errfile . "</scriptname>\n";
	$err .= "\t<scriptlinenum>" . $errline . "</scriptlinenum>\n";
	$err .= "\t<errcontext>" . $errcontext . "</errcontext>\n";

	if (in_array($errno, $user_errors)) {
		$err .= "\t<vartrace>" . wddx_serialize_value($vars, "Variables") . "</vartrace>\n";
	}
	$err .= "</errorentry>\n\n";

	$err_trace = '';
	if ($show_backtrace){	
		$backtraces = array_reverse(debug_backtrace());
		$err_trace .= "BACK TRACE\n";
		foreach($backtraces as $backtrace){
			$trace = @$backtrace['file']. "\tLINE(" . @$backtrace['line'] . ")\t" . @$backtrace['function']  . "()\n";
			$trace = str_replace($root_path,"",$trace);
			$trace = str_replace("\\","/",$trace);
			$trace = str_replace($root_path,"",$trace);
			$trace = "\t" . $trace;
			$err_trace .= $trace;
		}
	}
	$head = "\n***** XPressME ERROR LOG ****************************************************************************************************\n";
	$message = $head . $err . $err_trace;
	$_debug_file = $module_dirpath . '/wp-content/xpress_error.log';
	if ($errno != E_STRICT) {
		$_fp = fopen($_debug_file, 'a');
		fwrite($_fp, $message);	
		fclose($_fp);
	}
}
?>