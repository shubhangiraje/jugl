<?php
use yii\helpers\Html;
use yii\helpers\Url;

$message->setFrom(Yii::$app->params['emailFrom']);
$message->setSubject(Yii::$app->params['emailSubjectPrefix'].Yii::t('app','Friendship request declined'));

?>

<?=Yii::t('app','User {first_name} {last_name} ({nick_name}) declined your friendship request.',$friend->attributes)?>



