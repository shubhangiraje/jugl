<?php

use yii\helpers\Html;

$this->title=Yii::t('app','Validating User').' "'.$model.'"';

$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app','Users Validation'),
        'url'=>['admin-user-validation/index']
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