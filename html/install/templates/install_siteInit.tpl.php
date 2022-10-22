<div class="confirmInfo" style="text-align:center"><?php echo _INSTALL_L36 ?></div>

<h3><?php echo _INSTALL_L37 ?></h3>
<p><input type="text" class="adminame" name="adminname" required></p>

<h3><?php echo _INSTALL_L38 ?></h3>
<p><input type="text" class="adminmail" name="adminmail" maxlength="60" required></p>

<h3><?php echo _INSTALL_L39 ?></h3>
<p><input type="password" class="adminpass" name="adminpass" id="pass_insert" required></p>

<h3><?php echo _INSTALL_L74 ?></h3>
<p><input type="password" class="adminpass2" name="adminpass2" id="pass_confirm" required> <span class="badge"></span></p>

<h3><?php echo _INSTALL_L77 ?></h3>

<p>
    <select name="timezone">
	<?php $timezones = $this->v( 'timezones' ); ?>
	<?php foreach ( $this->v( 'timediffs' ) as $timediff => $text ) : ?>
	<?php if ( $timediff == $this->v( 'current_timediff' ) ) : ?>
        <option value="<?php echo $timezones[ $timediff ] ?>"
                selected="selected"><?php echo $text ?></option>
		<?php else : ?>
            <option value="<?php echo $timezones[ $timediff ] ?>"><?php echo $text ?></option>
		<?php endif ?>
    <?php endforeach ?>
</select>
</p>
<script>
    $('#pass_insert, #pass_confirm').on('keyup', function () {
        if ($('#pass_insert').val() == $('#pass_confirm').val()) {
            $('.badge').html('<?php echo _INSTALL_L23 ?>').css('color', 'green');
        } else
            $('.badge').html('<?php echo _INSTALL_L24 ?>').css('color', 'red');
    });
</script>

