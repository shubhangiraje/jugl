<?php
use yii\helpers\Html;

$message->setFrom(Yii::$app->params['emailFrom']);
$message->setSubject(Yii::$app->params['emailSubjectPrefix'].Yii::t('app','Einladungskontingent wurde erreicht'));

?>

<?=Yii::t('app','Hallo')?> <?=Html::encode($user->name)?>,<br/><br/>

<?php if($user->packet == 'VIP') { ?>
    <?= Yii::t('app', 'Dein Einladungskontingent in der Premium-Mitgliedschaft wurde erreicht. Die Administratoren prüfen jetzt, ob Du Deine Mitglieder entsprechend beraten hast oder ob Fake Profile dabei sind. Sollte alles in Ordnung sein, wird Dein Einladungskontingent hochgesetzt.') ?>
<?php } else { ?>
    <?= Yii::t('app', 'Dein Einladungskontingent für Level 1 in der Basismitgliedschaft wurde erreicht. Mach ein Upgrade auf eine Premium-Mitgliedschaft um so viele Freunde einladen zu können, wie Du willst.') ?>
<?php } ?>
