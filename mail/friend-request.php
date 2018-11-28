<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\Helper;

$message->setFrom(Yii::$app->params['emailFrom']);
$message->setSubject(Yii::$app->params['emailSubjectPrefix'].Yii::t('app','Friendship request'));

$acceptLink=Helper::toLoginedHashUrl('/friend-request-accept/'.$request->id,true);
$declineLink=Helper::toLoginedHashUrl('/friend-request-decline/'.$request->id,true);

?>

<?=Yii::t('app','Mr. {first_name} {last_name} ({nick_name}) hat Dir eine Freundschaftsanfrage gesendet.',$user->attributes)?><br/><br/>

<?=Yii::t('app','Freundschaft akzeptieren:')?> <?=Html::a($acceptLink,$acceptLink)?> <br/>
<?=Yii::t('app','Freundschaft ablehnen:')?> <?=Html::a($declineLink,$declineLink)?> <br/>





