<?php

class protector_crawler_overrun_message extends ProtectorFilterAbstract {
	public function execute() {
		// header( 'Location: https://google.com/' ) ; // redirect somewhere
		echo 'You have accessed too many times while short term'; // write any message as you like
		exit;
	}
}
