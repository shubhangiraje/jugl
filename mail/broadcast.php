<?php
use yii\helpers\Html;

$message->setFrom(Yii::$app->params['emailFrom']);
$message->setSubject(Yii::$app->params['emailSubjectPrefix'].Yii::t('app','Message from Administration'));

?>

<?=Html::encode($model->text);?>
<br /><br />
<?=Yii::t('app', 'Um keine Administrator Emails mehr zu erhalten, klicke auf folgenden Link:')?>
<?=Html::a($model->decline,$model->decline);?>