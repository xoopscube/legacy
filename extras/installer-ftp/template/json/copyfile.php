<h3><?php $this->_('File Install Success');?></h3>

<div class="success"><?php $this->_('File Install Success');?></div>
<div id="xoopsURLcheckFailed" style="display:none;">
	<?php $this->_('Is your XOOPSCube SITE is this?');?><a id="xoops_url" href="<?php $this->e('xoops_url');?>"><?php $this->e('xoops_url');?></a><br />
	<?php $this->_('If this is not collect, Let\'s go to XOOOPSCube Installer manually.');?><br />
</div>

<h3><?php $this->_('Next STEP is XOOPSCube Install wizards.');?></h3>
<div id="xoopsURLcheckOK" style="display:none;">
	<table class="outer" id="xoopsWizardParams">
		<thead><caption><?php $this->_('Set database parameters below.');?></caption></thead>
		<tr><th><?php $this->_('XOOPSCube Language');?></th><td id="xoops_language_selector"></td></tr>
		<tr><th><?php $this->_('MySQL Database Host');?></th><td><input type="text" name="dbhost" size="20" value="localhost"></td></tr>
		<tr><th><?php $this->_('MySQL Database Name');?></th><td><input type="text" name="dbname" size="20"></td></tr>
		<tr><th><?php $this->_('MySQL Database User');?></th><td><input type="text" name="dbuname" size="20"></td></tr>
		<tr><th><?php $this->_('MySQL Database Password');?></th><td><input type="text" name="dbpass" size="20"></td></tr>
		<tr><th><?php $this->_('XOOPS URL');?></th><td><?php $this->e('xoops_url');?></td></tr>
		<tr><th><?php $this->_('XOOPS ROOT PATH');?></th><td><?php $this->fv('xoops_root_path');?></td></tr>
		<tr><th><?php $this->_('XOOPS TRUST PATH');?></th><td><?php $this->fv('xoops_trust_path');?></td></tr>
		<tr><th><?php $this->_('XOOPS COOKIE Path');?></th><td><input type="text" name="cookie_path" size="40" value="<?php $this->e('xoops_cookie_path');?>"></td></tr>
		<tr><th><?php $this->_('Admin Username');?></th><td><input type="text" name="adminname" size="20" id="uname"></td></tr>
		<tr><th><?php $this->_('Admin Mail');?></th><td><input type="text" name="adminmail" size="40"></td></tr>
		<tr><th><?php $this->_('Admin Password');?></th><td><input type="password" name="adminpass" size="20" id="upass"></td></tr>
		<tr><th><?php $this->_('Admin Password(Confirm)');?></th><td><input type="password" name="adminpass2" size="20"></td></tr>
		<tr>
			<td colspan="2" class="footer"><button type="button" id="installXOOPS_b"><?php $this->_('install XOOPSCube')?></button>
			<input type="hidden" name="root_path" value="<?php $this->fv('xoops_root_path');?>" />
			<input type="hidden" name="trust_path" value="<?php $this->fv('xoops_trust_path');?>" />
			<input type="hidden" name="database" value="mysql" />
			<input type="hidden" name="prefix" value="<?php $this->e('prefix');?>" />
			<input type="hidden" name="salt" value="<?php $this->e('salt');?>" />
			<input type="hidden" name="db_pconnect" value="0" />
			<input type="hidden" name="xoops_url" value="<?php $this->e('xoops_url');?>" />
			<input type="hidden" name="memory_limit" value="32M"/>
			<input type="hidden" name="default_theme" value="hd_default"/>
			</td>
		</tr>
	</table>
	<div id="wizardProcess" style="display:none;">
		<ul class="indicator" id="wizardIndicator"><li class="process"><?php $this->_('Start XOOPSCube Install Wizard.')?></li></ul>
	</div>
</div>
