<?php
use yii\helpers\Html;

$message->setFrom(Yii::$app->params['emailFrom']);
$message->setSubject(Yii::$app->params['emailSubjectPrefix'].Yii::t('app','Allerletzte Zahlungserinnerung!'));

?>

<?=Yii::t('app','Allerletzte Zahlungserinnerung! Du hast bereits dreimal nicht bezahlt. Bitte bezahle umgehend alle Deinen offenen Käufe sonst wirst du gesperrt!')?>