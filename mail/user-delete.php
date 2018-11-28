<?php
use yii\helpers\Html;

$message->setFrom(Yii::$app->params['emailFrom']);
$message->setSubject(Yii::$app->params['emailSubjectPrefix'].Yii::t('app','Dein Konto wurde gelöscht'));

?>

<?=Yii::t('app','Hiermit bestätigen wir Ihnen, dass Ihr Profil bei Jugl.net gelöscht wurde.') ?><br>
<?=Yii::t('app','Mit freundlichen Grüßen,') ?><br>
<?=Yii::t('app','Ihr Jugl-Team') ?>

