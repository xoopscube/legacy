<p><?php echo _INSTALL_L128 ?></p>
<select name="lang">
<?php for($i=0; $i <count($this->v('languages')); $i++) { ?>
  <option value="<?php $this->e('languages',$i) ?>" <?php $this->e('selected',$i) ?>><?php $this->e('languages',$i) ?></option>
<?php } ?>
</select>
