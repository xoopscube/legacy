<?php

	$wizardSeq->insertAfter('modcheck', 'modcheckext', 'is_writeable Check'	,'modcheck',_INSTALL_L82,true);
	$wizardSeq->insertAfter('modcheck_trust', 'modcheck_trustext', 'trust is_writeable Check','modcheck_trust',_INSTALL_L166,true);
	$wizardSeq->insertAfter('siteInit', 'insertData_theme', _INSTALL_L117);
	$wizardSeq->add('insertData_theme',  _INSTALL_L116, 'finish',     _INSTALL_L117);

//	function replaceAfter($after, $name, $title='', $next='', $next_btn='', $back='', $back_btn='', $reload='') {
	$wizardSeq->replaceAfter('langselect', 'start',_INSTALL_L80, 'modcheck',_INSTALL_L81,'langselect',_INSTALL_L103,false);
	$wizardSeq->replaceAfter('start', 'modcheck',_INSTALL_L82, 'modcheckext','is_writeable Check','langselect',_INSTALL_L103,true);
	$wizardSeq->replaceAfter('modcheckext', 'dbform',_INSTALL_L90, 'dbconfirm',_INSTALL_L91,'langselect',_INSTALL_L103,false);
	$wizardSeq->replaceAfter('dbsave', 'modcheck_trust',_INSTALL_L167, 'modcheck_trustext','trust is_writeable Check','langselect',_INSTALL_L103,true);
	?>
