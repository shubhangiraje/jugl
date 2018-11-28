<?php

use yii\helpers\Html;



$this->title=Yii::t('app', 'Updating text');

$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app', 'Texte bearbeiten').' - '.$model->getLabel(),
        'url'=>['admin-default-text/index', 'category'=>$model->category]
    ],
    [
        'label'=>$this->title
    ]
];

?>

<div class="admin-default-text-create">

    <h1><?= Html::encode($this->title); ?></h1>

    <?= $this->render('_form', [
        'model' => $model
    ]); ?>

</div>