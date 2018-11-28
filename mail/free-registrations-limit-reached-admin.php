<?php
use yii\helpers\Html;

$message->setFrom(Yii::$app->params['emailFrom']);
$message->setSubject(Yii::$app->params['emailSubjectPrefix'].Yii::t('app','Einladungskontingent wurde erreicht'));

?>

<?=Yii::t('app','Der Nutzer {user} hat sein Einladungskontingent aufgebraucht', ['user'=>$user->name]);