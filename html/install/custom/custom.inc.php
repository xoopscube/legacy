<?php

    $wizardSeq->insertAfter('modcheck', 'modcheckext', _INSTALL_L81, 'start', _INSTALL_L80, false);
    $wizardSeq->insertAfter('modcheck_trust', 'modcheck_trustext', _INSTALL_L167, 'modcheck_trust', _INSTALL_L166, false);
    $wizardSeq->insertAfter('siteInit', 'insertData_theme', _INSTALL_L117);

    $wizardSeq->add('insertData_theme', _INSTALL_L116, 'finish', _INSTALL_L117);

//	function replaceAfter($after, $name, $title='', $next='', $next_btn='', $back='', $back_btn='', $reload='') {
    $wizardSeq->replaceAfter('langselect', 'start', _INSTALL_L80, 'modcheckext', _INSTALL_L81, 'langselect', _INSTALL_L103, false);
    $wizardSeq->replaceAfter('modcheckext', 'dbform', _INSTALL_L90, 'dbconfirm', _INSTALL_L91, 'langselect', _INSTALL_L103, false);
    $wizardSeq->replaceAfter('dbconfirm', 'dbsave', _INSTALL_L92, 'modcheck_trustext', _INSTALL_L167, 'dbconfirm', _INSTALL_L53, false);
