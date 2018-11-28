<?php

use yii\helpers\Html;

$this->title=Yii::t('app', 'Erstellen Immobilie für Tokens');

$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app','Immobilien für Tokens'),
        'url'=>['token-deposit-guarantee/index']
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
        'model' => $model,
        'TDGFiles' => $TDGFiles
    ));
    ?>

</div>