<?php
use yii\helpers\Html;

$message->setFrom(Yii::$app->params['emailFrom']);
$message->setSubject(Yii::$app->params['emailSubjectPrefix'].Yii::t('app','Person Validation failed'));

$link=\yii\helpers\Url::toRoute(['site/my','#'=>'/funds/payout'],true);

?>

<?=Yii::t('app','Leider war die Validierung Deiner Person bei jugl.net nicht erfolgreich. Der Grund dafür ist')?>:<br/><br/>
<?=Html::encode($model->validation_failure_reason)?><br/><br/>
<?=Yii::t('app','Bitte führe die Validierung erneut durch')?> <?=Html::a($link,$link)?>
