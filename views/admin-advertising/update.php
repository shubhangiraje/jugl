<?php

use yii\helpers\Html;

$this->title=Yii::t('app','Update Advertising');

$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app','Update Advertising'),
        'url'=>['admin-advertising/index']
    ],
    [
        'label'=>$this->title,
    ]
];

?>

<div class="admin-update">

    <h1><?php echo Html::encode($this->title); ?></h1>

    <?=
    $this->render('_form_update', ['model' => $model,'isModel'=>$isModel]);
    ?>

</div>