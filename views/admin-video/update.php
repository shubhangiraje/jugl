<?php

use yii\helpers\Html;

$this->title=Yii::t('app','Update Video');

$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app','Update Video'),
        'url'=>['admin-video/index']
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