<div class="success"><?php $this->_('The Repository is available.');?></div>

<div class="confirmInfo"><?php $this->_('Please select a package to install or update.');?></div>

<table class="outer" id="package_list">
<?php foreach ($this->g('repository_data') as $package_name => $pack_info) : ?>
<?php foreach ($pack_info as $info) : ?>
<tr>
	<td><input type="radio" name="target_package" id="target_package" value="<?php echo $package_name."@".$info['version']?>"></td>
	<th><?php $this->_('Package')?></th><td><?php echo $package_name?></td>
	<th><?php $this->_('Version')?></th><td><?php echo $info['version']?></td>
	<?php endforeach; ?>
</tr>
<?php endforeach; ?>
<tr>
	<td colspan="5" class="footer"><button type="button" id="selectPackage_b"><?php $this->_('Download selected package.')?></button></td>
</tr>
</table>
