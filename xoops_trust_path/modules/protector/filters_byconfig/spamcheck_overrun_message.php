<?php

class protector_spamcheck_overrun_message extends ProtectorFilterAbstract {
	public function execute() {
		// header( 'Location: https://google.com/' ) ; // redirect somewhere
		echo 'Your post looks like SPAM'; // write any message as you like
		exit;
	}
}
