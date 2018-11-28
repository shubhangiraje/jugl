<?php

use yii\helpers\Html;

?>
<form id="frm" action="https://checkout.wirecard.com/page/init.php" method="post">
    <?php foreach($data['formParams'] as $item) { ?>
    <input type="hidden" name="<?=Html::encode($item['name'])?>" value="<?=Html::encode($item['value'])?>"/>
    <?php } ?>
</form>
<script>document.getElementById('frm').submit();</script>
