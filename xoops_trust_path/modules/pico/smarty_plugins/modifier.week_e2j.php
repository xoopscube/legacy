<?php

function smarty_modifier_week_e2j( $text ) {
	return str_replace( [ 'Mon', 'Tue', 'Wue', 'Thu', 'Fri', 'Sat', 'Sun' ], [
		'月',
		'火',
		'水',
		'木',
		'金',
		'土',
		'日'
	], $text );
}
