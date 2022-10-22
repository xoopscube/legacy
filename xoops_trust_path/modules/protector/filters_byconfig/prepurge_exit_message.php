<?php

class protector_prepurge_exit_message extends ProtectorFilterAbstract {
	public function execute() {
		// header( 'Location: https://google.com/' ) ; // redirect somewhere
		echo 'Protector detects attacking actions'; // write any message as you like
		exit;
	}
}
