<div class="success"><?php $this->_('Successful login using FTP validation process.');?></div>

<div class="confirmInfo"><?php $this->_('Please input XOOPSCube repository url.');?></div>

<table class="outer">
<thead><caption><?php $this->_('For example Hodajuku distribution repository definition URL is <br> http://hodajuku.sourceforge.net/');?></caption></thead>
<tr>
	<th><?php $this->fn('repository_url')?></th><td><?php $this->f('repository_url', 'id="repository_url" style="width:80%;" ')?></td>
</tr>
<tr>
	<td colspan="2" class="footer"><button type="button" id="getRepository_b"><?php $this->_('get information from this repository.')?></button></td>
</tr>
</table>
