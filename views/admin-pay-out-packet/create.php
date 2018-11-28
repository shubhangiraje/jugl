<?php

use yii\helpers\Html;

$this->title=Yii::t('app', 'Creating Payout Packet');

$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app','Payout Packets'),
        'url'=>['admin-pay-out-packet/index']
    ],
    [
        'label'=>$this->title,
    ]
];

?>

<div class="admin-create">

    <h1><?php echo Html::encode($this->title); ?></h1>

    <?=
    $this->render('_form', array(
        'model' => $model));
    ?>

</div>