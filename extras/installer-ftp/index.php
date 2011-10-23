<?php
require_once dirname(__FILE__).'/app/Hdinstaller_Controller.php';
Hdinstaller_Controller::main('Hdinstaller_Controller', array('index', '*'), 'index');
