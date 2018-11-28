<?php

use yii\helpers\Url;

?>
<footer>
<!--    <div class="footer-socials">-->
<!--        <a href="/"><img src="/static/images/footer/facebook-icon.png" alt="Facebook" /></a>-->
<!--        <a href="/"><img src="/static/images/footer/twitter-icon.png" alt="Twitter" /></a>-->
<!--        <a href="/"><img src="/static/images/footer/gplus-icon.png" alt="Google Plus" /></a>-->
<!--    </div>-->

    <div class="footer-bottom">
        <div class="container clearfix">
            <?php if ($this->context->route!='site/my') { ?>
            <div class="footer-menu">
                <a href="<?=Url::to(['site/view','view'=>'ueber-uns'])?>"><?=Yii::t('app', 'Über Uns')?></a>
                <a href="<?=Url::to(['site/view','view'=>'impressum'])?>"><?=Yii::t('app', 'Impressum')?></a>
                <?php /*<a href="<?=Url::to(['site/view','view'=>'agbs'])?>"><?=Yii::t('app', 'AGBs')?></a> */?>
                <a href="<?=Url::to(['site/view','view'=>'datenschutz'])?>"><?=Yii::t('app', 'Datenschutz')?></a>
                <a href="<?=Url::to(['site/view','view'=>'nutzungsbedingungen'])?>"><?=Yii::t('app', 'Nutzungsbedingungen')?></a>
            </div>
            <?php } else { ?>
            <div class="footer-menu">
                <a ui-sref="ueberUns"><?=Yii::t('app', 'Über Uns')?></a>
                <a ui-sref="impressum"><?=Yii::t('app', 'Impressum')?></a>
                <a ui-sref="datenschutz"><?=Yii::t('app', 'Datenschutz')?></a>
                <?php /* <a ui-sref="agbs"><?=Yii::t('app', 'AGBs')?></a> */?>
                <a ui-sref="nutzungsbedingungen"><?=Yii::t('app', 'Nutzungsbedingungen')?></a>
                <?php /* <a ui-sref="datenschutz"><?=Yii::t('app', 'Datenschutz')?></a> */ ?>
            </div>
            <?php } ?>

            <div class="footer-copyrights">
                Copyright &copy; <?php echo date("Y");?> <a href="/"><img src="/static/images/footer/footer-logo.png" alt="jugl.net" /></a>
            </div>
        </div>
    </div>
	<!-- Global Site Tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-106459144-1"></script>
	<script>
	  window.dataLayer = window.dataLayer || [];
	  function gtag(){dataLayer.push(arguments)};
	  gtag('js', new Date());

	  gtag('config', 'UA-106459144-1');
	</script>
	
	<div class="advertising-items" style="width:0px; height:0px; z-index:-1; position:absolute; overflow:hidden;">
	
		<?php $modelForumTop = \app\models\Advertising::getAdvertising('forumtop', 'sponsorads'); ?>
		<?php if($modelForumTop->status == 1 && $modelForumTop->advertising_position != '' && $modelForumTop->id != '' && $modelForumTop->user_bonus != '' && $modelForumTop->link != ''){ ?>
		<div class="advertising-item" data-position="<?php echo $modelForumTop->advertising_position; ?>" data-id="<?php echo $modelForumTop->id; ?>" data-user-bonus="<?php echo $modelForumTop->user_bonus; ?>">
			<script type="text/javascript" src="<?php echo $modelForumTop->link; ?>"></script>
		</div>
		<?php } ?>
		
		<?php $modelForumBottom = \app\models\Advertising::getAdvertising('forumbottom', 'sponsorads'); ?>
		<?php if($modelForumBottom->status == 1 && $modelForumBottom->advertising_position != '' && $modelForumBottom->id != '' && $modelForumBottom->user_bonus != '' && $modelForumBottom->link != ''){ ?>
		<div class="advertising-item" data-position="<?php echo $modelForumBottom->advertising_position; ?>" data-id="<?php echo $modelForumBottom->id; ?>" data-user-bonus="<?php echo $modelForumBottom->user_bonus; ?>">
			<script type="text/javascript" src="<?php echo $modelForumBottom->link; ?>"></script>
		</div>
		<?php } ?>
	</div>

</footer>


