<?php

use yii\helpers\Html;
use app\components\GridView;


$this->title=Yii::t('app','Updating Offer').' "'.$model.'"';

$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app','Offers'),
        'url'=>['admin-offer/index']
    ],
    [
        'label'=>$this->title,
    ]
];

?>

<div class="admin-update clearfix">

    <h1><?php echo Html::encode($this->title); ?></h1>

    <?=
    $this->render('_form', array(
            'model' => $model,'isModel'=>$isModel,'offerFiles'=>$offerFiles));
    ?>



<div class="admin-update">
<br /><br />
   
	<ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active"><a href="#tab1" role="tab" data-toggle="tab"><?=Yii::t('app','Allgemeine Angebot Interessen')?></a></li>
		<li role="presentation"><a href="#tab2" role="tab" data-toggle="tab"><?=Yii::t('app','Spamliste')?></a></li>
    </ul>
	<div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="tab1">
			 <h1><?= Html::encode(Yii::t('app','Offer Requests')) ?></h1>

			<?=GridView::widget([
				'dataProvider' => $OfferRequestsDataProvider,
				'columns' => array_merge(
					[
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
					],
					$model->type!=\app\models\Offer::TYPE_AUCTION ? []:
					[
						'bet_price',
						'bet_period',
						'bet_active_till'
					],
					[
						'description',
						[
							'class' => 'app\components\ActionColumn',
							'template' => '{update} {delete}',
							'controller' => 'admin-offer-request'
						]
					]
				)
			]); ?>
		</div>
		<div role="tabpanel" class="tab-pane" id="tab2">
			<h1><?= Html::encode(Yii::t('app','Spamliste')) ?></h1>
			<?=GridView::widget([
				'dataProvider' => $OfferSpamlistDataProvider,
				'columns' => array_merge(
				[
					[
							'attribute'=>'user.name',
							'label'=>'Nutzer',
							'value'=>function($OfferSpamlistDataProvider) {return Html::a($OfferSpamlistDataProvider->user->name,['admin-user/update','id'=>$OfferSpamlistDataProvider->user_id],['data-pjax'=>0]);},
							'format'=>'raw'
					],
					'comment',
					[
                        'attribute'=>'dt',
                        'label'=>Yii::t('app','Date'),
                        'value'=>function($OfferSpamlistDataProvider) {return (new \app\components\EDateTime($OfferSpamlistDataProvider->dt));}
                    ],
				
				]
			)
				
			]); ?>
		</div>
	</div>
	</div>
</div>
