<?php

use yii\helpers\Html;
use app\components\GridView;
use app\models\Country;

$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app','Gruppenchat'),
        'url'=>['admin-trollbox-message/index']
    ]
];

$this->params['fullWidth']=true;

?>

<div class="admin-index">

    <h1><?= Html::encode(Yii::t('app','Gruppenchat')) ?></h1>

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
                'attribute'=>'trollbox_category_id',
                'value'=>function($model) {return $model->trollboxCategory->title;},
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'trollbox_category_id',
                    \app\models\TrollboxCategory::getFrontList(),
                    ['class' => 'form-control','prompt'=>'']
                ),
                'options'=>['style'=>'width: 180px;']
            ],
            [
                'attribute'=>'status',
                'value'=>function($model) {return $model->statusLabel;},
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'status',
                    $searchModel->getStatusList(),
                    ['class' => 'form-control','prompt'=>'']
                ),
                'options'=>['style'=>'width: 130px;']
            ],
            [
                'attribute'=>'file_id',
                'format'=>'image',
                'value'=>function($model) {return $model->file ? $model->file->getThumbUrl('adminImagePreview'):null;}
            ],
            [
                'attribute'=>'text',
                'value'=>function($model) {
                    return '<div class="description-break-word">'.$model->text.'<div>';
                },
                'format'=>'raw'
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
                'value'=>'votes_up'
            ],
            [
                'attribute'=>'votes_down',
                'value'=>'votes_down'
            ],
            [
                'label'=>Yii::t('app', 'An wen gepostet '),
                'attribute'=>'visible',
                'value'=>function($model) {
                    $result = '';
                    if($model->visible_for_all) {
                        $result .= '<div>'.Yii::t('app', 'Alle').'</div>';
                    }

                    if($model->visible_for_followers) {
                        $result .= '<div>'.Yii::t('app', 'Abos').'</div>';
                    }

                    if($model->visible_for_contacts) {
                        $result .= '<div>'.Yii::t('app', 'Kontakte').'</div>';
                    }
                    return $result;
                },
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'visible',
                    \app\models\TrollboxMessage::getVisibilityList(),
                    ['class' => 'form-control','prompt'=>'']
                ),
                'format'=>'raw',
                'options'=>['style'=>'width: 150px;']
            ],

            [
                'class' => 'app\components\ActionColumn',
                'template' => '{update} {pin} {unpin}',
                'buttons' => [
                    'pin' => function($url, $model, $key) {
                        if (!$model->is_sticky) {
                            return Html::a(
                                '<div class="pin-button"><span class="glyphicon glyphicon-pushpin"></span></div>',
                                ['admin-trollbox-message/set-sticky','id'=>$model->id,'returl'=>Yii::$app->request->absoluteUrl],
                                [
                                    'title' => Yii::t('app', 'Mark as sticky'),
                                    'data-method' => 'post',
                                    'data-pjax' => '0'
                                ]
                            );
                        }
                    },
                    'unpin' => function($url, $model, $key) {
                        if ($model->is_sticky) {
                            return Html::a(
                                '<div class="unpin-button"><span class="glyphicon glyphicon-pushpin"></span></div>',
                                ['admin-trollbox-message/unset-sticky','id'=>$model->id,'returl'=>Yii::$app->request->absoluteUrl],
                                [
                                    'title' => Yii::t('app', 'Unmark as sticky'),
                                    'data-method' => 'post',
                                    'data-pjax' => '0'
                                ]
                            );
                        }
                    },
                ]
            ],
        ],
    ]); ?>

</div>