<?php

use yii\helpers\Html;
use app\components\GridView;

$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app','Admins'),
        'url'=>['admin-admin/index']
    ],
    [
        'label'=>Yii::t('app','Session Log'),
    ],
];

?>

<div class="admin-index">

    <h1>
        <?= Html::encode(Yii::t('app','Session Log')) ?>
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
                'attribute'=>'dt_start',
                'value'=>function($model) { return new \app\components\EDateTime($model->dt_start); },
            ],
            [
                'attribute'=>'dt_end',
                'value'=>function($model) {
                    $dt=new \app\components\EDateTime($model->dt_end);
                    return (new \app\components\EDateTime())<$dt ? '':$dt;
                },
            ],
        ],
    ]); ?>

</div>