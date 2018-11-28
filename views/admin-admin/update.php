<?php

use yii\helpers\Html;

$this->title=Yii::t('app','Updating Admin').' "'.$model.'"';

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

<div class="admin-update">

    <h1><?php echo Html::encode($this->title); ?></h1>

    <?=
    $this->render('_form', array(
            'model' => $model));
    ?>

</div>