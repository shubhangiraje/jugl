<?php

use yii\helpers\Html;

$this->title=Yii::t('app','Updating question');

$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app','Fragen / Antworten'),
        'url'=>['admin-faq/index']
    ],
    [
        'label'=>$this->title,
    ]
];

?>

<div class="admin-update">
    <h1><?php echo Html::encode($this->title); ?></h1>
    <?= $this->render('_form', ['model' => $model]); ?>
</div>