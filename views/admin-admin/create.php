<?php

use yii\helpers\Html;

$this->title=Yii::t('app', 'Creating Admin');

$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app','Admins'),
        'url'=>['admin-admin/index']
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