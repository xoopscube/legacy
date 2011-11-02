<?php
function xp_permission_check($mydirname , $mydirpath) {
	global $ret ; // TODO :-D
	// permission check

    $error = false;
	if( defined( 'XOOPS_CUBE_LEGACY' ) ) {
		$GLOBALS["err_log"][] = "********************************* Error Log ********************************<br />";
	} else {
		$GLOBALS["err_log"][] = '<h4 style="border-bottom: 1px dashed rgb(0, 0, 0); text-align: left; margin-bottom: 0px;">Error Log</h4>';
    }
    
    if (!file_exists($mydirpath . '/wp-settings.php')){
		$GLOBALS["err_log"][] =  '<span style="color:#ff0000;">WordPress is not built in.</span><br />';
		$error = true;
	}
    $check_files = array('/templates/', '/wp-content/');
    foreach ($check_files as $check) {
    	$check_file = $mydirpath . $check;
        if (!is_dir($check_file)) {
           if ( file_exists($check_file) ) {
                @chmod($check_file, 0666);
                if (! is_writeable($check_file)) {
                    $GLOBALS["err_log"][] = "<span style=\"color:#ff0000;\">Permission Error $check_file is not writeable</span><br />";
                    $error = true;
				}
            }
        } else {
            @chmod($check_file, 0777);
            if (! is_writeable($check_file)) {
                $GLOBALS["err_log"][] = "<span style=\"color:#ff0000;\">Permission Error $check_file directory is not writeable</span><br />";
                $error = true;
            } else {
            	// Windows parmission check
            	$src_file = __FILE__ ;
				$newfile = $check_file . 'write_check.txt';
				if (!copy($src_file, $newfile)) {
                	$GLOBALS["err_log"][] = "<span style=\"color:#ff0000;\">Permission Error $check_file directory is not writeable</span><br />";
                	$error = true;
				} else {
					unlink($newfile);
				}
			}
        }
    }
    if($error) return false;
    
    return true;
}
?>