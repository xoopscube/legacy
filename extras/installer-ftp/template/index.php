<div class="confirmInfo"><?php $this->_('Please select your language.');?></div>
<p>
<form action="./index.php" method="get" style="text-align:center">
<select name="lang">
<?php foreach($this->g('allow_language') as $lang): ?>
<option value="<?php echo $lang['lang']?>" <?php echo $this->g('cur_lang')==$lang['lang'] ?" selected":"" ?>><?php echo $lang['name']?></option>
<?php endforeach; ?>
</select>&nbsp;
<input type="submit" class="changelang" name="action_index" value="<?php echo $this->_('Change Language')?>">&nbsp;
<input type="submit" class="next" name="action_ftp" value="<?php echo $this->_('next')?>">
</form>
</p>

