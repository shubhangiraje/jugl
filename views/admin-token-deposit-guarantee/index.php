<?php

use yii\helpers\Html;
use app\components\GridView;

$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app','Immobilien für Tokens'),
        'url'=>['admin-token-deposit-guarantee/index']
    ]
];

?>

<div class="admin-index">

    <h1>
        <?= Html::a(Yii::t('app', 'Create'), ['create'], ['class' => 'btn btn-success pull-right']) ?>
        <?= Html::encode(Yii::t('app','Immobilien für Tokens')) ?>
    </h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'title_de',
            [
                'format'=>'image',
                'label'=>Yii::t('app','Image'),
                'value'=>function($model) {return $model->tokenDepositGuaranteeFiles ? $model->tokenDepositGuaranteeFiles[0]->file->getThumbUrl('adminImagePreview'):null;}
            ],
            [
                'attribute'=>'show',
                'value'=>function($model) {return $model->show ? Yii::t('app','Ja'):Yii::t('app','Nein');}
            ],
            [
                'attribute'=>'sum_cost'
            ],
            [
                'attribute'=>'sum'
            ],
            [
                'attribute'=>'description_de',
            ],
            [
                'class' => 'app\components\ActionColumn',
                'template' => '{update} {delete}'
            ],
        ],
    ]); ?>

</div>