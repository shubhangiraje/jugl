<?php
use yii\helpers\Html;

//$message->setFrom(Yii::$app->params['emailFrom']);
//$message->setSubject(Yii::$app->params['emailSubjectPrefix'].Yii::t('app','Invitation'));

$message->setFrom([Yii::$app->params['emailFrom']=>Yii::t('app', 'Jugl.net – Team')]);
$message->setSubject(Yii::t('app','Einladung von {first_name} {last_name}', ['first_name'=>$user->first_name, 'last_name'=>$user->last_name]));


$this->params['customLayout']=true;
?>

<table width="100%" align="center" style="background: #ffffff; border-collapse: separate; border: 1px solid #ffffff; border-radius: 20px; padding: 0 15px 35px;">
    <tr>
        <td colspan="2" align="center" style="padding: 20px 5px 5px"><h1 style="font-family: Tahoma, sans-serif; font-size: 33px; color: #32404d;"><?= Yii::t('app', 'Ich denke, Dir wird {link} gefallen', ['link'=>'<a style="color: #32404d" href="http://jugl.net">jugl.net</a>']) ?></h1></td>
    </tr>

    <tr>
        <td rowspan="2" valign="top" style="padding: 20px 17px;"><img src="<?=$message->embedImage('/static/images/mail/phone.jpg');?>" alt="jugl_phone"></td>
        <td align="left" valign="top" style="min-width: 250px; font-family: Tahoma, Verdana, sans-serif; font-size: 17px; color: #32404d; line-height: 27px; padding: 0 17px">

            <?php
                $text=Html::encode($text);
                $text=str_replace('{link}',$link,$text);
                $text=preg_replace('%https?://[^ \n\r]+%','<a href="$0">$0</a>',$text);
                $text=nl2br($text);
            ?>

            <?=$text?>
<?php /*
            <p style="padding: 7px 0"><?= Yii::t('app', '{username} möchte Dich einladen, der schnell wachsenden Gemeinde von {link} beizutreten.', ['username'=>'<b>'.Html::encode($user->first_name.' '.$user->last_name).'</b>', 'link'=>'<a style="color: #32404d" href="http://jugl.net">jugl.net</a>']) ?><p>
            <p style="padding: 7px 0"><?=nl2br(Html::encode($text))?></p>
            <p style="padding: 7px 0"><a href="https://youtu.be/X5h0JSLQP-Y">https://youtu.be/X5h0JSLQP-Y</a></p>
            <p style="padding: 7px 0"><?= Yii::t('app', 'Einfach auf den Link unten klicken, sich registrieren und Geld verdienen!') ?></p>
*/ ?>
        </td>
    </tr>

    <tr>
        <td style="padding: 40px 17px 40px">
            <a href="<?=$link?>" style="text-align: center; display: inline-block; text-decoration: none; background: #0092df; padding: 12px 40px; border-radius: 30px; color: #ffffff; font-size: 19px; font-family: Tahoma, sans-serif;"><b><?= Yii::t('app', 'Einladung akzeptieren') ?></b></a>
        </td>
    </tr>

</table>
