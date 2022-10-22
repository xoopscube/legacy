<?php

if (is_array($this->v('checks'))) {
	foreach ($this->v('checks') as $check) {
		echo '<p>'. $check .'</p>';
	}
}

$this->e( 'message' );
