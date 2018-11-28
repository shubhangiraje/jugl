<?php
use yii\helpers\Html;

$message->setFrom(Yii::$app->params['emailFrom']);
$message->setSubject(Yii::$app->params['emailSubjectPrefix'].Yii::t('app','Payout request processed'));

?>

<?=Yii::t('app','Your payout request for {sum} was processed',[
    'sum'=>$model->currency_sum.'&euro;'
])?>
