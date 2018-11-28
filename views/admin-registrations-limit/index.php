<?php

use yii\helpers\Html;
use app\components\GridView;
use app\components\EDateTime;

$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app','Einladungskontingent'),
        'url'=>['admin-registrations-limit/index']
    ]
];

$this->params['fullWidth']=true;

?>

<div class="admin-index">

    <h1>
        <?= Html::encode(Yii::t('app','Einladungskontingent')) ?>
    </h1>

    <p>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'pjax'=>false,
        'columns' => [
            [
                'label'=>Yii::t('app','Referrals'),
                'attribute'=>'cnt',
            ],
            [
                'label'=>Yii::t('app','Ref. limit'),
                'attribute'=>'lim',
            ],
            [
                'attribute'=>'status',
                'value'=>function($model) { return $model->statusLabel; },
            ],
            [
                'attribute'=>'registration_dt',
                'format'=>'date',
                'options'=>['style'=>'width: 150px;']
            ],
            'email',
            [
                'attribute'=>'avatar_file_id',
                'format'=>'raw',
                'value'=>function($model) {
                    return \yii\helpers\Html::a(
                        \yii\helpers\Html::img($model->getAvatarThumbUrl('avatar'),['style'=>'width:50px;height:50px;border-radius:25px;']),
                        \yii\helpers\Url::to(['admin-user/update','id'=>$model->id]),['data-pjax'=>0]
                    );
                }
            ],
            'first_name',
            'last_name',
            'nick_name',
            'balance',
        ],
    ]); ?>

</div>