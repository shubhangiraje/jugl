<?php

use yii\helpers\Html;
use app\components\GridView;

$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app','Cash for Likes'),
        'url'=>['admin-cfr-distribution/index']
    ]
];

?>

<div class="admin-index">

    <h1>
        <?= Html::encode(Yii::t('app','Cash for Likes')) ?>
    </h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute'=>'dt',
                'value'=>'dt',
                'format'=>'date'
            ],
            [
                'attribute'=>'votes_count',
                'value'=>'votes_count'
            ],
            [
                'attribute'=>'jugl_sum',
                'value'=>'jugl_sum'
            ],
            [
                'class' => 'app\components\ActionColumn',
                'template' => '{view}'
            ],
        ],
    ]); ?>

</div>