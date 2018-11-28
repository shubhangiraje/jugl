<?php
use yii\helpers\Html;

/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\MessageInterface the message being composed */
/* @var $content string main view render result */

?>
<?php $this->beginPage() ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?= Yii::$app->charset ?>" />
    <meta name="viewport" content="width=device-width"/>
    <title><?= Html::encode($this->title) ?></title>

    <?php $this->head() ?>
</head>
<body>
    <?php $this->beginBody() ?>

    <table align="center" style="width: 100%; max-width: 730px; background: #014a81; border-collapse:collapse;" >
        <tr align="center"><td><img src="<?=$message->embedImage('/static/images/mail/header-bg-email.jpg')?>" style="max-width: 100%" alt="logo" /></td></tr>
        <tr>
            <td style="padding: 0 30px 30px;">
                <?php if ($this->params['customLayout']) { ?>
                    <?= $content ?>
                <?php } else { ?>
                    <table width="100%" align="center" style="background: #ffffff; border-collapse: separate; border: 1px solid #ffffff; border-radius: 20px; padding: 10px 15px;">
                        <tr><td><p style="font-family: Tahoma, Verdana, sans-serif; font-size: 17px; color: #32404d;"><?= $content ?></p></td></tr>
                    </table>
                <?php } ?>
            </td>
        </tr>
    </table>
    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>



