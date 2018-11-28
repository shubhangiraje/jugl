<?php

use yii\helpers\Html;

$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app','Verifizierungsvideos'),
        'url'=>['admin-video-identification/index']
    ],
    [
        'label'=>Yii::t('app', 'Videoidentifikation des Users {user}', ['user'=>$model->user->getName()])
    ]
];

?>

<div class="admin-update">

    <h1><?= Yii::t('app', 'Videoidentifikation des Users {user}', ['user'=>$model->user->getName()]) ?></h1>

    <?= $this->render('_form', ['model' => $model]); ?>

</div>