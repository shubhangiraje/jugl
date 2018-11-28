<?php

use yii\helpers\Html;
use app\components\GridView;
use app\models\Country;
use app\models\User;

$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app','Verifizierungsvideos'),
        'url'=>['admin-video-identification/index']
    ]
];

$this->params['fullWidth']=true;

?>

<div class="admin-index">

    <h1><?= Html::encode(Yii::t('app','Verifizierungsvideos')) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pjax'=>false,
        'columns' => [
            [
                'attribute'=>'country',
                'value'=>function($model) {return $model->countryLabel;},
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'country',
                    Country::getList(),
                    ['class' => 'form-control','prompt'=>'']
                ),
                'options'=>['style'=>'width: 130px;']
            ],

            [
                'attribute'=>'video_identification_status',
                'label'=>Yii::t('app', 'Status'),
                'value'=>function($model) {return $model->user->getVideoIdentificationStatusLabel();},
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'video_identification_status',
                    User::getVideoIdentificationStatusList(),
                    ['class' => 'form-control','prompt'=>'']
                ),
                'options'=>['style'=>'width: 130px;']
            ],

            [
                'attribute'=>'file_id',
                'format'=>'raw',
                'value'=>function($model) {
                    return Html::a(Html::img($model->file->getThumbUrl('adminImagePreview')), $model->file->url, ['target'=>'_blank', 'class'=>'video-link']);
                }
            ],
            [
                'attribute'=>'dt',
                'format'=>'date',
                'filter'=>
                    \kartik\date\DatePicker::widget([
                        'model'=>$searchModel,
                        'attribute'=>'create_dt_from',
                        'readonly'=>true,
                        'pluginOptions'=>[
                            'clearBtn'=>true
                        ],
                        'options'=>['style'=>'width:100px;background:white;cursor:pointer;'],
                        //'addon'=>['<span style="display:inline-block;width:35px;">'.Yii::t('app','From').'</span>'],
                        'layout'=>'<span class="input-group-addon" style="width:55px;">'.Yii::t('app','From').'</span>{input}{picker}{remove}'
                    ]).
                    \kartik\date\DatePicker::widget([
                        'model'=>$searchModel,
                        'attribute'=>'create_dt_to',
                        'readonly'=>true,
                        'pluginOptions'=>[
                            'clearBtn'=>true
                        ],
                        'options'=>['style'=>'width:100px;background:white;cursor:pointer;'],
                        //'addon'=>['<span style="display:inline-block;width:35px;">'.Yii::t('app','To').'</span>'],
                        'layout'=>'<span class="input-group-addon" style="width:55px;">'.Yii::t('app','To').'</span>{input}{picker}{remove}'
                    ]),
                'options'=>['style'=>'width: 150px;']
            ],
            [
                'attribute'=>'user.first_name',
                'label'=>'Nutzer',
                'value'=>function($model) {
                    return Html::a($model->user->name,['admin-user/update','id'=>$model->user_id],['data-pjax'=>0]);
                },
                'format'=>'raw',
                'filter'=> Html::activeTextInput($searchModel,'user_name',['class'=>'form-control'])
            ],

            [
                'attribute'=>'device_uuid',
                'value'=>'device_uuid'
            ],

            [
                'attribute'=>'votes_up',
                'label'=>Yii::t('app', 'Echt'),
                'value'=>function($model) {
                    return $model->votes_up>0 ? Html::a($model->votes_up, ['admin-video-identification/vote-users', 'id'=>$model->id, 'vote'=>'1']) : $model->votes_up;
                },
                'format'=>'raw'
            ],
            [
                'attribute'=>'votes_down',
                'label'=>Yii::t('app', 'Nicht echt'),
                'value'=>function($model) {
                    return $model->votes_down>0 ? Html::a($model->votes_down, ['admin-video-identification/vote-users', 'id'=>$model->id, 'vote'=>'-1']) : $model->votes_down;
                },
                'format'=>'raw'
            ],
            [
                'label'=>'Echt / Nicht Echt',
                'value'=>function($model) {
                    if ($model->user->video_identification_status==User::VIDEO_IDENTIFICATION_STATUS_AWAITING) {
                        return Html::a('Echt', ['admin-video-identification/vote', 'id'=>$model->id, 'vote'=>'1'], ['class'=>'btn btn-sm btn-success', 'style'=>'margin: 5px']).
                               Html::a('Nicht Echt', ['admin-video-identification/vote', 'id'=>$model->id, 'vote'=>'-1'], ['class'=>'btn btn-sm btn-danger', 'style'=>'margin: 5px']);
                    } else {
                        return '';
                    }
                },
                'format'=>'raw'
            ],
            [
                'class' => 'app\components\ActionColumn',
                'template' => '{update}',
                'options'=>['style'=>'width: 70px;']
            ],

        ],
    ]); ?>

</div>