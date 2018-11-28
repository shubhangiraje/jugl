<?php
use yii\helpers\Html;

$message->setFrom(Yii::$app->params['emailFrom']);
$message->setSubject(Yii::t('app','Bestätigung Deiner Registrierung bei jugl.net'));

?>

<?=Yii::t('app','Vielen Dank für Deine Registrierung bei jugl.net. Um Deine Registrierung abschließen zu können, klicke bitte auf den nachfolgenden Link:')?><br/><br/>
<?=Html::a($link,$link)?>
<br/><br/>
<?=Yii::t('app','Mit freundlichen Grüßen,<br/>Dein jugl.net Team')?>
