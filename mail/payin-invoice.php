<?php
use yii\helpers\Html;

$message->setFrom(Yii::$app->params['emailFrom']);
$message->setSubject(Yii::$app->params['emailSubjectPrefix'].Yii::t('app','Deine Rechnung'));

?>

<?= Yii::t('app','Hi'); ?>,<br><br>
<?= Yii::t('app','anbei erhälst Du die aktuelle jugl-Rechnung.'); ?><br><br>
<?= Yii::t('app','Mit freundlichen Grüßen'); ?>,<br>
<?= Yii::t('app','Dein jugl Team'); ?>

