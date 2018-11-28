<?php

use yii\helpers\Html;
use app\components\GridView;

$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app','Advertising'),
        'url'=>['admin-advertising/index']
    ]
];
$this->params['fullWidth']=true;
?>

<div class="admin-index">
		
    <h1>
	<?= Html::a(Yii::t('app', 'Create'), ['create'], ['class' => 'btn btn-success pull-right']) ?>
	<?= Html::a(Yii::t('app', 'Statistics'), ['statistics'], ['class' => 'btn btn-success pull-right']) ?>
	<?= Html::encode(Yii::t('app','Advertising')) ?></h1>
	
	<?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
			'id',
            'advertising_name',
			'advertising_total_bonus',
			'advertising_total_views',
			'advertising_total_clicks',
			'status',
			'user_bonus',
			'click_interval',
			'popup_interval',
			[
                'attribute'=>'display_date',
                'format'=>'datetime'
            ],
            [
                'class' => 'app\components\ActionColumn',
                'template' => '{update} {delete}'
            ],
        ],
    ]); ?>

</div>