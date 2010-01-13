<div class="reloadPlz">
<input type="button" onclick="location.reload();" value="<?php $this->_('File Install Action failed Retry Please.')?>">
</div>
<ul class="error">
<?php foreach ($this->getErrors() as $error):?>
<li><?php echo $error; ?></li>
<?php endforeach; ?>
</ul>
