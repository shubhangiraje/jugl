<?php
use yii\helpers\Html;

$message->setFrom(Yii::$app->params['emailFrom']);
$message->setSubject(Yii::$app->params['emailSubjectPrefix'].Yii::t('app','Ihr Profil Gesperrt'));

?>

<?=Yii::t('app','Sie haben 4 Mahnungen, Ihr Profil wurde gesperrt. Bitte melden Sie sich beim Administrator vom jugl.net unter support@jugl.net')?>