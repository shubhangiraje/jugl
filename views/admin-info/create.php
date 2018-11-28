<?php

use yii\helpers\Html;

$this->title=Yii::t('app','Creating');

$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app','i-Informationen'),
        'url'=>['admin-info/index']
    ],
    [
        'label'=>$this->title,
    ]
];

?>

<div class="admin-create">
    <h1><?php echo Html::encode($this->title); ?></h1>
    <?= $this->render('_form', ['model' => $model]); ?>
</div>