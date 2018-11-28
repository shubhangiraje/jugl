<?php
use yii\helpers\Html;

$message->setFrom(Yii::$app->params['emailFrom']);
$message->setSubject(Yii::$app->params['emailSubjectPrefix'].Yii::t('app','Jugls aufladen'));

?>

<?=Yii::t('app','Du hast erfolgreich Jugls aufgeladen.')?>

