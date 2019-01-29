<?php if (isset($message)) {
    ?>
<p align="center"><?php $this->e('message')?></p>
<?php 
} else {
    ?>
<div class="confirmInfo"><?php $this->e('welcome') ?></div>
<?php 
} ?>
