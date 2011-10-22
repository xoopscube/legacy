<div style="width:500px;margin:0 auto;">
<table>
<tr>
<td align="left">
<?php foreach($this->v('dbm_reports') as $report) { ?>
    <?php echo $report ?><br />
<?php } ?>
</td></tr></table>
<table><tr><td align="left">
<?php if (is_array($this->v('cm_reports'))) { ?>
    <?php foreach($this->v('cm_reports') as $report) { ?>
        <?php echo $report ?><br />
    <?php } ?>
<?php } ?>
</td></tr></table>
<table><tr><td align="left">
<?php foreach($this->v('mm_reports') as $report) { ?>
    <?php echo $report ?><br />
<?php } ?>
</td></tr></table>
</div>
