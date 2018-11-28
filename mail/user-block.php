<?php
use yii\helpers\Html;

$message->setFrom(Yii::$app->params['emailFrom']);
$message->setSubject(Yii::$app->params['emailSubjectPrefix'].Yii::t('app','Dein Konto wurde gesperrt'));

?>

<?=Yii::t('app','Ihr Profil wurde geblockt.')?><br>
<?= Yii::t('app','Die kann folgende Gründe haben:') ?><br>
<?= Yii::t('app','1. Verstoß gegen die AGBs') ?><br>
<?= Yii::t('app','2. Verdacht auf Fakeprofile in Ihrem Level 1') ?><br>
<?= Yii::t('app','3. Verdacht auf gefälschte Daten') ?>