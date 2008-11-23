<table align="center"><tr><td align="left">
<?php foreach($this->v('reports') as $report) { ?>
    <?php echo $report ?><br />
<?php } ?>
</td></tr></table>
<p><?php $this->e('message')?></p>
