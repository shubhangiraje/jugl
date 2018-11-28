<?php

use yii\helpers\Html;
use app\components\GridView;


$this->title=Yii::t('app','Updating Search Request').' "'.$model.'"';

$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app','Search Requests'),
        'url'=>['admin-search-request/index']
    ],
    [
        'label'=>$this->title,
    ]
];

?>

<div class="admin-update">
    <h1><?php echo Html::encode($this->title); ?></h1>
    <?= $this->render('_form', ['model' => $model, 'isModel'=>$isModel, 'searchRequestFiles'=>$searchRequestFiles]); ?>
</div>

<br>

<div class="admin-index">

    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active"><a href="#tab1" role="tab" data-toggle="tab"><?= Html::encode(Yii::t('app','Search Request Offers')) ?></a></li>
        <li role="presentation"><a href="#tab2" role="tab" data-toggle="tab"><?=Yii::t('app','Kommentare')?></a></li>
		<li role="presentation"><a href="#tab3" role="tab" data-toggle="tab"><?=Yii::t('app','Spamliste')?></a></li>
    </ul>

    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="tab1">
            <h1><?= Html::encode(Yii::t('app','Search Request Offers')) ?></h1>

            <?=GridView::widget([
                'dataProvider' => $searchRequestOffersDataProvider,
                'columns' => [
                    [
                        'attribute'=>'status',
                        'value'=>function($model) { return $model->statusLabel; },
                    ],
                    [
                        'attribute'=>'user.first_name',
                        'label'=>'Nutzer',
                        'value'=>function($model) {return Html::a($model->user->name,['admin-user/update','id'=>$model->user_id],['data-pjax'=>0]);},
                        'format'=>'raw'
                    ],
                    'description',
                    [
                        'attribute'=>'relevancy',
                        'value'=>function($model) {return $model->relevancy.'%';}
                    ],
                    [
                        'class' => 'app\components\ActionColumn',
                        'template' => '{update} {delete}',
                        'controller' => 'admin-search-request-offer'
                    ],
                ]
            ]); ?>
        </div>
        <div role="tabpanel" class="tab-pane" id="tab2">
            <h1><?= Html::encode(Yii::t('app','Kommentare')) ?></h1>

            <?= GridView::widget([
                'dataProvider' => $searchRequestCommentDataProvider,
                'pjax'=>true,
                'columns' => [

                    [
                        'attribute'=>'create_dt',
                        'filter'=>false,
                        'value'=>function($model) {return (new \app\components\EDateTime($model->create_dt));},
                        'options'=>['style'=>'width: 150px;']
                    ],

                    [
                        'attribute'=>'user_id',
                        'value'=>function($model) {
                            return Html::a($model->user->name,['admin-user/update','id'=>$model->user_id], ['data-pjax'=>0]);
                        },
                        'format'=>'raw'
                    ],
                    [
                        'attribute'=>'comment',
                        'value'=>function($model) {
                            return '<div class="description-break-word">'.$model->comment.'<div>';
                        },
                        'format'=>'raw'
                    ],

                    [
                        'attribute'=>'response',
                        'value'=>function($model) {
                            return '<div class="description-break-word">'.$model->response.'<div>';
                        },
                        'format'=>'raw'
                    ],

                    [
                        'class' => 'app\components\ActionColumn',
                        'template' => '{edit-comment} {delete-comment}',
                        'buttons'=>[
                            'edit-comment' => function($url, $model, $key) {
                                $params = [
                                    'title' => Yii::t('app', 'Update'),
                                    'class'=>'action-btn',
                                    'data-pjax'=>0
                                ];
                                $url = \yii\helpers\Url::to(['/admin-search-request-comment/update', 'id'=>$model->id]);
                                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, $params);

                            },
                            'delete-comment' => function($url, $model, $key) {
                                $params = [
                                    'title' => Yii::t('app', 'Delete'),
                                    'onclick' => 'if (!confirm("'.Yii::t('app','Do you really want to delete this item?').'")) {event.preventDefault();event.stopPropagation();}',
                                    'class'=>'action-btn'
                                ];
                                $url = \yii\helpers\Url::to(['/admin-search-request-comment/delete', 'id'=>$model->id]);
                                return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url.'&pjaxForcePost=1', $params);

                            }
                        ],
                        'options'=>['style'=>'width: 80px; text-align: center']
                    ],
                ],
            ]); ?>

        </div>
		<div role="tabpanel" class="tab-pane" id="tab3">
			<h1><?= Html::encode(Yii::t('app','Spamliste')) ?></h1>
			<?=GridView::widget([
				'dataProvider' => $searchRequestSpamlistDataProvider,
				'columns' => array_merge(
				[
					[
							'attribute'=>'user.name',
							'label'=>'Nutzer',
							'value'=>function($searchRequestSpamlistDataProvider) {return Html::a($searchRequestSpamlistDataProvider->user->name,['admin-user/update','id'=>$searchRequestSpamlistDataProvider->user_id],['data-pjax'=>0]);},
							'format'=>'raw'
					],
					'comment',
					[
                        'attribute'=>'dt',
                        'label'=>Yii::t('app','Date'),
                        'value'=>function($searchRequestSpamlistDataProvider) {return (new \app\components\EDateTime($searchRequestSpamlistDataProvider->dt));}
                    ],
				
				]
			)
				
			]); ?>
		</div>
    </div>

</div>
