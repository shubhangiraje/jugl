<?php
use yii\helpers\Html;

$message->setFrom(Yii::$app->params['emailFrom']);
$message->setSubject(Yii::$app->params['emailSubjectPrefix'].Yii::t('app','Person Validation success'));

$link=\yii\helpers\Url::toRoute(['site/my','#'=>'/funds/payout'],true);

?>

<?=Yii::t('app','Congratulations! You passed person validation and now you can order payout')?> <?=Html::a($link,$link)?>
