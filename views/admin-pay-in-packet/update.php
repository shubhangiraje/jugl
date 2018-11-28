<?php

use yii\helpers\Html;

$this->title=Yii::t('app','Updating Payin Packet');

$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app','Payin Packets'),
        'url'=>['admin-pay-in-packet/index']
    ],
    [
        'label'=>$this->title,
    ]
];

?>

<div class="admin-update">

    <h1><?php echo Html::encode($this->title); ?></h1>

    <?=
    $this->render('_form', array(
            'model' => $model));
    ?>

</div>