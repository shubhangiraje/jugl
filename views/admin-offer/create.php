<?php

use yii\helpers\Html;
use app\components\GridView;


$this->title=Yii::t('app','Neue Werbung erfassen');

$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app','Offers'),
        'url'=>['admin-offer/index']
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
            'model' => $model,'isModel'=>$isModel,'offerFiles'=>$offerFiles));
    ?>

</div>
