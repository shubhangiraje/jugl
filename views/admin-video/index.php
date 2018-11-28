<?php

use yii\helpers\Html;
use app\components\GridView;

$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app','Video'),
        'url'=>['admin-video/index']
    ]
];

?>

<div class="admin-index">
		<?= Html::a(Yii::t('app', 'Alle Videos löschen'), ['deleteall'], ['class' => 'btn btn-danger pull-right', 'style' => 'margin-right:10px;']) ?>
		<?= Html::a(Yii::t('app', 'Import Dailymotion'), ['importdailymotion'], ['class' => 'btn btn-success pull-right', 'style' => 'margin-right:10px;']) ?>
		<?= Html::a(Yii::t('app', 'Bonus vergeben'), ['updatebonus'], ['class' => 'btn btn-primary pull-right', 'style' => 'margin-right:10px;']) ?>	
		<?php /* Html::a(Yii::t('app', 'Statistik'), ['statistics'], ['class' => 'btn btn-primary pull-right', 'style' => 'margin-right:10px;']) */?>
		
    <h1><?= Html::encode(Yii::t('app','Video')) ?></h1>

   
    <?= 
	
	GridView::widget([
        'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'pjax'=>false,
        'columns' => [
			[
                'attribute'=>'cat_name',
                'value'=>function($model) {
                    return '<div class="description-break-word1">'.$model->cat_name.'<div>';
                },
                'format'=>'raw'
            ],
			[
                'attribute'=>'name',
                'value'=>function($model) {
                    return '<div class="description-break-word">'.$model->name.'<div>';
                },
                'format'=>'raw'
            ],
			'clip_duration',
			'language',	
			'bonus',
			[
                'class' => 'app\components\ActionColumn',
                'template' => '{update}'
            ],
			
     
        ],
    ]); ?>

</div>