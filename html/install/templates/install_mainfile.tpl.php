<div style="width:500px; margin:0 auto;">
<table align="center">
<tr><td align="left">
<?php foreach($this->v('checks') as $check) { ?>
    <?php echo $check ?><br />
<?php } ?>
<br />
</td></tr>
</table>
<table valign="top" class="separate" border="0" cellpadding="5" cellspacing="1" width="98%">
<tr>
  <td align="left" class="bg3"><b><?php echo _INSTALL_L11?></b></td>
  <td class="bg1"><b><?php echo XOOPS_ROOT_PATH?></b></td>
</tr>
<tr>
  <td align="left" class="bg3"><b><?php echo _INSTALL_L12?></b></td>
  <td class="bg1"><b><?php echo XOOPS_URL?></b></td>
</tr>
</table>
<div class="confirmMsg"><?php echo _INSTALL_L13 ?></div>
</div>
