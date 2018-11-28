<?php

use yii\helpers\Html;
use app\components\GridView;

$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app','Payin Packets'),
        'url'=>['admin-pay-in-packet/index']
    ]
];

?>

<div class="admin-index">

    <h1>
        <?= Html::a(Yii::t('app', 'Create'), ['create'], ['class' => 'btn btn-success pull-right']) ?>

        <?= Html::encode(Yii::t('app','Payin Packets')) ?>
    </h1>

    <p>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'jugl_sum',
            [
                'attribute'=>'currency_sum',
                'format'=>'html',
                'value'=>function($model) {return $model->currency_sum.'&euro;';}
            ],
            [
                'class' => 'app\components\ActionColumn',
                'template' => '{update} {delete}'
            ],
        ],
    ]); ?>

</div>