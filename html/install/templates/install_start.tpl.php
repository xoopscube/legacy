<?php if($message){ ?>
<p align="center"><?php $this->e('welcome')?></p>
<?php }else{ ?>
<div class="confirmInfo"><?php $this->e('message') ?></div>
</div>
<?php } ?>
