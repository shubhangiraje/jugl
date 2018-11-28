<?php
use yii\helpers\Html;

$message->setFrom(Yii::$app->params['emailFrom']);
$message->setSubject(Yii::$app->params['emailSubjectPrefix'].Yii::t('app','Payout request accepted'));

?>

<?=Yii::t('app','Your payout request for {sum} was accepted and your jugl balance was updated. You will be notified when payout will be processed.',[
    'sum'=>$model->currency_sum.'&euro;'
])?>
