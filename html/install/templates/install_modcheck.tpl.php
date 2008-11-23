<table align="center"><tr><td align="left">
<?php foreach($this->v('checks') as $check) { ?>
    <?php echo $check ?><br />
<?php } ?>
</td></tr></table>
<p><?php $this->e('message') ?>
