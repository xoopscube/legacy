
<div class="confirmInfo"><?php $this->_('Finaly set Install Path.');?></div>


<table class="outer" id="package_list">
<thead><caption><?php $this->_('Set XOOPS Cube path for XOOPS_ROOT_PATH &amp; XOOPS_TRUST_PATH.');?></caption></thead>
<tr>
	<th><?php $this->fn('xoops_root_path')?></th><td><?php $this->f('xoops_root_path', 'id="xoops_root_path" style="width:80%"')?></td>
</tr>
<tr>
	<th><?php $this->fn('xoops_trust_path')?></th><td><?php $this->f('xoops_trust_path', 'id="xoops_trust_path" style="width:80%"')?></td>
</tr>
<tr>
	<td colspan="5" class="footer"><button type="button" id="xoopsParam_b"><?php $this->_('Download Package and Install. Submit?')?></button></td>
</tr>
</table>
