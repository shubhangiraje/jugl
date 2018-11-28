<?php

use yii\helpers\Html;
use app\components\GridView;

$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app','Settings'),
        'url'=>['admin-setting/index']
    ]
];

?>

<div class="admin-index">

    <h1>
        <?= Html::encode(Yii::t('app','Settings')) ?>
    </h1>

    <p>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute'=>'title',
                'value'=>'title',
                'options'=>['style'=>'width: 500px;']
            ],
            [
                'attribute'=>'value',
                'value'=>function($model) {
                    if($model->type == \app\models\Setting::TYPE_BOOL) {
                        if($model->value) {
                            return '<span class="glyphicon glyphicon-ok" style="color:green;"></span>';
                        } else {
                            return '<span class="glyphicon glyphicon-remove" style="color:red;"></span>';
                        }
                    }
                    return $model->value;
                },
                'format'=>'raw'
            ],

            [
                'class' => 'app\components\ActionColumn',
                'template' => '{update}'
            ],
        ],
    ]); ?>

</div>