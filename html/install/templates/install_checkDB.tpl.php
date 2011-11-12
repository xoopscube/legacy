<div style="width:500px; margin:0 auto;">
<table>
  <tr><td align="left">
<?php foreach($this->v('checks') as $check) { ?>
    <?php echo $check ?><br />
<?php } ?>
  </td></tr>
</table>
<?php if (is_array($this->v('msgs'))) foreach($this->v('msgs') as $msg) { ?>
<div class="confirmInfo"><?php echo $msg ?></div>
<?php } ?>
</div>
