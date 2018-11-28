<?php if (!$data['message']) { ?>
    <form action="https://checkout.wirecard.com/page/init.php" method="post" id="frm">
        <?php foreach($data['formParams'] as $param) { ?>
        <input type="hidden" name="<?=$param['name']?>" value="<?=htmlspecialchars($param['value'],ENT_QUOTES)?>"/>
        <?php } ?>
    </form>
    <script>document.getElementById('frm').submit();</script>
<?php } else { ?>
    <?=$data['message']?>
<?php } ?>