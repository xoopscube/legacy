<?php

// common prepend
require dirname( __DIR__ ) . '/include/common_prepend.inc.php';

// controller
require_once dirname( __DIR__ ) . '/class/PicoControllerVoteContent.class.php';

$controller = new PicoControllerVoteContent( $currentCategoryObj );

$controller->execute( $picoRequest );

$controller->render();
