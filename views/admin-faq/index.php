<?php

use yii\helpers\Html;
use app\components\GridView;

$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app','Fragen / Antworten'),
        'url'=>['admin-faq/index']
    ]
];

?>

<div class="admin-index">

    <h1>
        <?= Html::a(Yii::t('app', 'Create'), ['create'], ['class' => 'btn btn-success pull-right']) ?>

        <?= Html::encode(Yii::t('app','Fragen / Antworten')) ?>
    </h1>

    <p>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute'=>'question_de',
                'value'=>'question_de',
                'options'=>['style'=>'width: 250px;']
            ],
            [
                'attribute'=>'response_de',
                'value'=>'response_de',
                'contentOptions'=> [
                    'class'=>'description'
                ]
            ],
            [
                'class' => 'app\components\ActionColumn',
                'template' => '{update} {delete}'
            ],
        ],
    ]); ?>

</div>