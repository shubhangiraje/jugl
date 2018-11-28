<?php
use yii\helpers\Html;

$message->setFrom(Yii::$app->params['emailFrom']);
$message->setSubject(Yii::$app->params['emailSubjectPrefix'].Yii::t('app','Passwort vergessen'));

?>

<?=Yii::t('app','Zum Zur&uuml;cksetzen Deines Passwortes bitte den folgenden Link klicken')?>:</b> <?=Html::a($link,$link)?>
