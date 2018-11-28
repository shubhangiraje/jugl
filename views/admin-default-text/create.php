<?php

use yii\helpers\Html;

$this->title=Yii::t('app', 'Text erstellen');

$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app','Texte bearbeiten').' - '.\app\models\DefaultText::getCategoryLabel($_REQUEST['category']),
        'url'=>['admin-default-text/index', 'category'=>$_REQUEST['category']]
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