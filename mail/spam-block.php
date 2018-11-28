<?php
use yii\helpers\Html;

$message->setFrom(Yii::$app->params['emailFrom']);
$message->setSubject(Yii::$app->params['emailSubjectPrefix'].Yii::t('app','Spammer gemeldet'));

?>

<?=Yii::t('app','Sie wurden als Spammer gemeldet und Ihr Profil wurde gesperrt. Bitte melden Sie sich beim Administrator vom jugl.net unter support@jugl.net')?>