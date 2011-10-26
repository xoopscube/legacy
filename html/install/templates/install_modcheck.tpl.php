<div style="width:500px; margin:0 auto;">
<table align="center"><tr><td align="left">
<?php foreach($this->v('checks') as $check) { ?>
    <?php echo $check ?><br />
<?php } ?>
</td></tr></table>
<div class="confirmInfo"><?php $this->e('message') ?></div>
</div>
