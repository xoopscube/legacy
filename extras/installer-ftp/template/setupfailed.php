<div id="main">
    <div class="confirmMsg"><?php $this->_('Server settings failure. Check error messages below.');?></div>
	<ul class="error">
<?php if (is_error('ftp_functions')): ?>
		<li><?php $this->_('PHP FTP library is not loaded. You cannot run this Installer on this server.')?></li>
<?php endif;?>
<?php if (is_error('safe_mode')): ?>
		<li><?php $this->_('You cannot use this Installer when PHP safe_mode is ON.')?></li>
<?php endif;?>
<?php if (is_error('tar')): ?>
		<li><?php $this->_('System tar command cannot run.')?></li>
<?php endif;?>
<?php if (is_error('wget')): ?>
		<li><?php $this->_('System wget or curl command cannot run.')?></li>
<?php endif;?>
<?php if (is_error('chmod')): ?>
		<li><?php $this->_('System chmod command cannot run.')?></li>
<?php endif;?>
<?php if (is_error('tmp_dir')): ?>
		<li><?php $this->_('Check "tmp" directory permission is set to 777.')?><br />
			<input type="text" onfocus="this.select();" style="width:90%;" value="<?php $this->e('tmp777')?>">
        </li>
<?php endif;?>
	</ul>
</div>
