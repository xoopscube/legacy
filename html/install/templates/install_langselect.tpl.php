<select name="lang">
	<?php
	for ( $i = 0; $i < (is_countable($this->v( 'languages' )) ? count( $this->v( 'languages' ) ) : 0); $i ++ ) {
		?>
        <option
                value="<?php $this->e( 'languages', $i ) ?>" <?php $this->e( 'selected', $i ) ?>><?php $this->e( 'languages', $i ) ?></option>
		<?php
	} ?>
</select>

<noscript>
    <style>
        .nojs{
            display: none; /* Do not display confirmation */
        }
    </style>
    <div class="confirmError"><h2>JavaScript is disabled. Please enabled JavaScript.</h2></div>
</noscript>

<div class="confirmInfo nojs"><?php echo _INSTALL_L128 ?></div>
<?php 
if (version_compare(PHP_VERSION, '7.4.0', '>')) {
    echo '<div class="confirmOk nojs">PHP version <code>' . PHP_VERSION . '</code></div>';
} 
?>

