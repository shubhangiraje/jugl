<?php

use yii\helpers\Html;
use app\components\GridView;

$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app','Admins'),
        'url'=>['admin-admin/index']
    ],
    [
        'label'=>Yii::t('app','Action Log'),
    ],
];

?>

<div class="admin-index">

    <h1>
        <?= Html::encode(Yii::t('app','Action Log')) ?>
    </h1>

    <p>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute'=>'admin',
                'format'=>'raw',
                'value'=>function($model) { return Html::a($model->admin->name,['admin-admin/update','id'=>$model->admin_id]); },

                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'admin_id',
                    \app\models\Admin::getList(),
                    ['class' => 'form-control','prompt'=>'']
                )

            ],
            [
                'attribute'=>'dt',
                'value'=>function($model) { return new \app\components\EDateTime($model->dt); },
            ],
            [
                'attribute'=>'action',
                'value'=>function($model) { return $model->moduleName; },
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'action',
                    \app\models\AdminActionLog::getModulesMapping(),
                    ['class' => 'form-control','prompt'=>'']
                )
            ],
            [
                'attribute'=>'comment',
                'format'=>'raw',
                'value'=>function($model) {return $model->comment ? nl2br(\yii\helpers\Html::encode($model->comment)):'';}
            ]

        ],
    ]); ?>

</div>