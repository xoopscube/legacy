<?php

foreach ( $this->v( 'checks' ) as $check ) {

	echo $check . '<br>';
}

?>

<br>

<h3><?php echo _INSTALL_L11 ?></h3>

<p class="data"><?php echo XOOPS_ROOT_PATH ?></p>

<h3><?php echo _INSTALL_L12 ?></h3>

<p class="data"><?php echo XOOPS_URL ?></p>

<div class="confirmInfo"><?php echo _INSTALL_L13 ?></div>
