<?php
//
// Created on 2006/11/09 by nao-pon http://hypweb.net/
// $Id: dbsync.lng.php,v 1.1 2010/03/06 08:20:30 nao-pon Exp $
//

$msg = array(
	'title_update'  => 'Update Page Information DB',
	'msg_adminpass' => 'Admin password',
	'msg_all' => 'Initialization and a reset of all',
	'msg_select' => 'Initialization and a reset of select',
	'msg_hint' => 'Check all at the time of initial introduction.',
	'msg_init' => 'Page infomation DB',
	'msg_count' => 'Page counter DB',
	'msg_noretitle' => 'An existing page maintains title information.',
	'msg_retitle' => 'An existing page acquires title information again, too.',
	'msg_plain_init' => 'Text DB for searches and DB for link between pages',
	'msg_plain_init_notall' => 'Text DB for searches treats only an empty page.',
	'msg_plain_init_all' => 'Processes all pages. (It takes time.)',
	'msg_attach_init' => 'Attached file information DB',
	'msg_progress_report' => 'Progress:',
	'msg_now_doing' => 'Now processing in a server side.<br />Keep open this page until display "All processing was completed" to a lower progress screen.',
	'msg_next_do' => '<span style="color:blue;">Stopped processing by a limit for execute time of a server.<br />Click "Do continue" of a lower progress screen bedrock, and please work to last.</span>',
	'msg_moreinfo' => 'Detailed report',
	'msg_background' => 'Do not process now, and process it one by one at the background.',
	'btn_submit'    => 'Go!',
	'btn_next_do'    => 'Do continue.',
	'msg_done'      => 'All processing was completed.',
	'msg_usage'     => "
* Description

:Update Page Information DB|
Scan all page files and rebuild page information DB.

* Notice

Please wait a while, after clicking 'Run' button.

Max PHP execution time on this server is set to &font(red,b){%1d}; seconds.
So, this process will be paused at every &font(red,b){%2d}; seconds and will show 'Continue' button.
If you see 'Continue' button, you should click this to complete this procedure.

* Run

Please click 'Go!' button.
If you cannot see 'Go!' button, you should login as a Administrator user.

Options marked * mean, they have not beed processed yet.",
);
?>