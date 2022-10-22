<?php

foreach ( $this->v( 'dbm_reports' ) as $report ) {
	echo $report . '<br>';
}

if ( is_array( $this->v( 'cm_reports' ) ) ) {

	foreach ( $this->v( 'cm_reports' ) as $report ) {
		echo $report . '<br>';
	}
}

foreach ( $this->v( 'mm_reports' ) as $report ) {
	echo $report . '<br>';
}

?>

<script type="text/javascript">
    (function () {
        var obj = new XMLHttpRequest();
        obj.open('POST', '../user.php', false);
        obj.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        obj.send('uname=<?php echo urlencode( $this->v( 'adminname' ) )?>&pass=<?php echo urlencode( $this->v( 'adminpass' ) )?>&xoops_login=1');
    })();
</script>
