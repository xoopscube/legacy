<div class="confirmInfo"><?php $this->_('Set your FTP account information.');?></div>


<table class="outer">
<thead><caption><?php $this->_('Login for this server and access validation.');?></caption></thead>
<tr>
	<th><?php $this->fn('ftp_username')?></th><td><?php $this->f('ftp_username', 'id="ftp_username"')?></td>
</tr>
<tr>
	<th><?php $this->fn('ftp_password')?></th><td><?php $this->f('ftp_password', 'id="ftp_password"')?></td>
</tr>
<tr>
	<td colspan="2" class="footer"><button type="button" id="ftpcheck_b"><?php $this->_('Check if the parameters are valid.')?></button></td>
</tr>
</table>

