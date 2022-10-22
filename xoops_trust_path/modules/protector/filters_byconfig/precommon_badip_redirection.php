<?php

// define it as you like :-)
define( 'PROTECTOR_BADIP_REDIRECTION_URI', 'https://yahoo.com/' );

class protector_precommon_badip_redirection extends ProtectorFilterAbstract {
	public function execute() {
		header( 'Location: ' . PROTECTOR_BADIP_REDIRECTION_URI );
		exit;
	}
}
