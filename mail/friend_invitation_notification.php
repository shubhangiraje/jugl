<?php
use yii\helpers\Html;
use yii\helpers\Url;

$message->setFrom(Yii::$app->params['emailFrom']);
$message->setSubject(Yii::$app->params['emailSubjectPrefix'].Yii::t('app','Erinnerung'));

$link=\app\components\Helper::toLoginedHashUrl('/friends-invitation/invite',true);
?>

<?=Yii::t('app','Leider hast Du bisher noch keinen oder nur wenige Freunde zu Jugl.net erfolgreich eingeladen. Lade Deine Freunde ein, bevor es ein anderer macht!')?>
<br/><br/>
<a style="text-align: center; display: inline-block; text-decoration: none; background: #0092df; padding: 12px 40px; border-radius: 30px; color: #ffffff; font-size: 19px; font-family: Tahoma, sans-serif;"
    href="<?=$link?>"><b><?=Yii::t('app','Jetzt Freunde & Bekannte einladen')?></b></a>
