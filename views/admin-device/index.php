<?php

use yii\helpers\Html;
use app\components\GridView;

$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app','Blockierte Device IDs'),
        'url'=>['admin-site/index']
    ]
];

?>

<div class="admin-index">

    <h1>
        <?= Html::encode(Yii::t('app','Blockierte Device IDs')) ?>
    </h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'device_uuid',
            [
                'label'=>Yii::t('app', 'Nutzer'),
                'attribute'=>'user.first_name',
                'value'=>function($model) {
                    return Html::a($model->user->name,['admin-user/update','id'=>$model->user_id],['data-pjax'=>0]);
                },
                'format'=>'raw'
            ],
            [
                'class' => 'app\components\ActionColumn',
                'template' => '{delete}'
            ],
        ],
    ]); ?>

</div>