<?php

use yii\helpers\Html;
use app\components\GridView;


$this->title=Yii::t('app','Updating Offer Request').' "'.$model.'"';

$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app','Offers'),
        'url'=>['admin-offer/index']
    ],
    [
        'label'=>Yii::t('app','Offer').' "'.$model->offer.'"',
        'url'=>['admin-offer/update','id'=>$model->offer_id]
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
