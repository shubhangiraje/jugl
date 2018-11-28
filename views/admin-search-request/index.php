<?php

use yii\helpers\Html;
use app\components\GridView;
use app\components\EDateTime;
use yii\bootstrap\Modal;

$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app','Search Requests'),
        'url'=>['admin-search-request/index']
    ]
];

$this->params['fullWidth']=true;

?>

<?php Modal::begin([
    'id'=> 'modal-search-request-delete',
    'header' => '<h4 class="modal-title">'.Yii::t('app', 'Suchaufträge löschen').'</h4>',
]); ?>
<div id="modal-search-request-delete-content"></div>
<?php Modal::end(); ?>



<div class="admin-index">

    <h1>
		<?= Html::a(Yii::t('app', 'Create'), ['create'], ['class' => 'btn btn-success pull-right']) ?>
		<?= Html::a(Yii::t('app', 'Tradetracker Importieren'), ['importtradetracker'], ['class' => 'btn btn-success pull-right', 'style'=>'margin-right: 15px']) ?>
        <?= Html::encode(Yii::t('app','Search Requests')) ?>
    </h1>

    <p>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pjax'=>false,
        'columns' => [
            [
                'attribute'=>'status',
                'value'=>function($model) { return $model->statusLabel; },
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'status',
                    $searchModel->getStatusList(),
                    ['class' => 'form-control','prompt'=>'']
                )
            ],
            [
                'label'=>'Category',
                'value'=>function($model) {return $model->searchRequestInterests[0]->level1Interest->title;},
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'level1_interest_id',
                    \app\models\Interest::getLevel1List(\app\models\Interest::TYPE_SEARCH_REQUEST),
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
            'title',
            'price_from',
            'price_to',
            'bonus',
            [
                'class' => 'app\components\ActionColumn',
                'template' => '{update}'.hasAccess('admin-search-request/delete',' {delete-search-request}'),
                'buttons' => [
                    'delete-search-request' => function($url, $model, $key) {
                        if($model->status!=\app\models\SearchRequest::STATUS_DELETED) {
                            $params = [
                                'title' => Yii::t('app', 'Vorrübergehend aktivieren'),
                                'onclick' => '$("#modal-search-request-delete").modal("show").find("#modal-search-request-delete-content").load($(this).attr("value"));',
                                'value'=>$url,
                                'class'=>'action-btn'
                            ];
                            return Html::button('<span class="glyphicon glyphicon-trash"></span>', $params);
                        }
                    },
                ]
            ]


/*
            'email',
            [
                'attribute'=>'avatar_file_id',
                'format'=>'raw',
                'value'=>function($model) {
                    return \yii\helpers\Html::a(
                        \yii\helpers\Html::img($model->getAvatarThumbUrl('avatar'),['style'=>'width:50px;height:50px;border-radius:25px;']),
                        \app\components\Helper::toLoginedHashUrl('/user-profile/'.$model->id)
                    );
                }
            ],
            'first_name',
            'last_name',
            'nick_name',
            'balance',
*/
        ],
    ]); ?>

</div>
