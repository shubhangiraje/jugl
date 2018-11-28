<?php
use yii\helpers\Html;
use yii\helpers\Url;

$message->setFrom(Yii::$app->params['emailFrom']);
$message->setSubject(Yii::$app->params['emailSubjectPrefix'].Yii::t('app','Erinnerung'));

//$link=\app\components\Helper::toLoginedHashUrl('/friends-invitation/invite',true);
?>

<?=nl2br(Yii::t('app',"Hallo,\nwir wollten Dich nur noch einmal höflich daran erinnern, dass Du Dich noch nicht in der JuglApp eingeloggt hast."))?>
