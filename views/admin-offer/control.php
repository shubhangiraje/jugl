<?php

use yii\helpers\Html;
use app\components\GridView;
use app\components\EDateTime;
use yii\bootstrap\Modal;

$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app','Offers'),
        'url'=>['admin-offer/index']
    ],
    [
        'label'=>Yii::t('app', 'Zu kontrollieren'),
    ]
];


$this->params['fullWidth']=true;

?>


<?php Modal::begin([
    'id'=> 'modal-offer-accept',
    'header' => '<h4 class="modal-title">'.Yii::t('app', 'Werbung freigegeben').'</h4>',
]); ?>
<div id="modal-offer-accept-content"></div>
<?php Modal::end(); ?>

<?php Modal::begin([
    'id'=> 'modal-offer-reject',
    'header' => '<h4 class="modal-title">'.Yii::t('app', 'Werbung abgelehnt').'</h4>',
]); ?>
<div id="modal-offer-reject-content"></div>
<?php Modal::end(); ?>


<?php

$validationStatusList=\app\models\Offer::getValidationStatusList();
unset($validationStatusList[\app\models\Offer::VALIDATION_STATUS_NOT_REQUIRED]);
unset($validationStatusList[\app\models\Offer::VALIDATION_STATUS_ACCEPTED]);
unset($validationStatusList[\app\models\Offer::VALIDATION_STATUS_REJECTED]);

?>

<div class="admin-index">

    <h1><?= Html::encode(Yii::t('app','Angebote zu kontrollieren')) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsive'=>false,
        'pjax'=>false,
        'columns' => [
            [
                'attribute'=>'validation_status',
                'value'=>function($model) { return $model->validationStatusLabel; },
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'validation_status',
                    $validationStatusList,
                    ['class' => 'form-control','prompt'=>'']
                ),
                'width'=>'120px'
            ],
            [
                'label'=>Yii::t('app','Kategorie'),
                'value'=>function($model) {return $model->offerInterests[0]->level1Interest->title;},
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'level1_interest_id',
                    \app\models\Interest::getLevel1List(\app\models\Interest::TYPE_OFFER),
                    ['class' => 'form-control','prompt'=>'']
                )
            ],
            [
                'attribute'=>'create_dt',
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
                'attribute'=>'active_till',
                'format'=>'date',
                'filter'=>
                    \kartik\date\DatePicker::widget([
                        'model'=>$searchModel,
                        'attribute'=>'active_till_from',
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
                        'attribute'=>'active_till_to',
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
                'filter'=>
                    Html::activeTextInput($searchModel,'user_name',['class'=>'form-control'])
            ],
            [
                'label'=>'Builder',
                'value'=>function($model) {
                    $data = '<div class="table-picture-box">';
                    foreach($model->files as $image) {
                        $img = $image->getThumbUrl('offer');
                        $data.= Html::a(Html::img($img), $img, ['class'=>'fancybox', 'rel'=>$model->id]);
                    }
                    $data.='</div>';
                    return $data;
                },
                'format'=>'raw',
                'contentOptions'=> [
                    'class'=>'td-pictures'
                ]
            ],

            [
                'header'=>'Titel der Anzeige'.($model->validation_status != \app\models\Offer::VALIDATION_STATUS_REJECTED?'<br>Beschreibung':''),
                'value'=>function($model) {
                    $result='<div>'.$model->title.'</div>';
                    if($model->validation_status != \app\models\Offer::VALIDATION_STATUS_REJECTED) {
                        $result.='<div class="offer-description">'.$model->description.'</div>';
                    }
                    return $result;
                },
                'format'=>'raw',
                'options'=>[
                    'style'=>['width'=>'200px']
                ]
            ],
            'price',
            'delivery_days',
            'view_bonus',
            'view_bonus_total',
            'view_bonus_used',
            'buy_bonus',
            'receivers_count',
            [
                'class' => 'app\components\ActionColumn',
                'template' => '{update}',
                'options'=>[
                    'style'=>['width'=>'40px']
                ]
            ],
            [
                'label'=>'Ja',
                'header' => '<span class="glyphicon glyphicon-ok" style="color:green;"></span><div>'.Yii::t('app', 'Ja').'</div>',
                'value'=>function($model) {
                    if($model->validation_status==\app\models\Offer::VALIDATION_STATUS_ACCEPTED) {
                        return '<span class="glyphicon glyphicon-ok" style="color:#c3c3c3;"></span>';
                    }
                    if($model->validation_status==\app\models\Offer::VALIDATION_STATUS_REJECTED) {
                        return '';
                    }

                    $params = [
                        'title' => Yii::t('app', 'Annehmen'),
                        'onclick' => '$("#modal-offer-accept").modal("show").find("#modal-offer-accept-content").load($(this).attr("value"));',
                        'value'=>\yii\helpers\Url::to(['admin-offer/accept', 'id'=>$model->id]),
                        'class'=>'action-btn'
                    ];
                    return Html::button('<span class="glyphicon glyphicon-ok" style="color:green;"></span>', $params);

                },
                'format'=>'raw',
                'width'=>'66px'
            ],
            [
                'label'=>'Später',
                'header' => '<span class="icon-awaiting-later"></span><div>'.Yii::t('app', 'Später').'</div>',
                'value'=>function($model) {
                    if($model->validation_status==\app\models\Offer::VALIDATION_STATUS_REJECTED || $model->validation_status==\app\models\Offer::VALIDATION_STATUS_ACCEPTED) {
                        return '';
                    }
                    $params = [
                        'title' => Yii::t('app', 'Später'),
                        'data-pjax' => '0'
                    ];
                    $url = ['admin-offer/pause', 'id'=>$model->id];
                    if($model->validation_status==\app\models\Offer::VALIDATION_STATUS_AWAITING_LATER) {
                        return Html::a('<span class="glyphicon glyphicon-pause" style="color:#00a2e8;"></span>', $url, $params);
                    }
                    return Html::a('<span class="icon-awaiting-later"></span>', $url, $params);
                },
                'format'=>'raw',
                'width'=>'66px'
            ],
            [
                'label'=>'Nein',
                'header' => '<span class="glyphicon glyphicon-remove" style="color:red;"></span><div>'.Yii::t('app', 'Nein').'</div>',
                'value'=>function($model) {
                    if($model->validation_status==\app\models\Offer::VALIDATION_STATUS_REJECTED) {
                        return '<span class="glyphicon glyphicon-remove" style="color:#c3c3c3;"></span>';
                    }
                    if($model->validation_status==\app\models\Offer::VALIDATION_STATUS_ACCEPTED) {
                        return '';
                    }
                    $params = [
                        'title' => Yii::t('app', 'Ablehnen'),
                        'onclick' => '$("#modal-offer-reject").modal("show").find("#modal-offer-reject-content").load($(this).attr("value"));',
                        'value'=>\yii\helpers\Url::to(['admin-offer/reject', 'id'=>$model->id]),
                        'class'=>'action-btn'
                    ];
                    return Html::button('<span class="glyphicon glyphicon-remove" style="color:red;"></span>', $params);
                },
                'format'=>'raw',
                'width'=>'66px'
            ]
        ],
    ]); ?>

</div>


