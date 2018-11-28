<?php

use yii\helpers\Html;
use app\components\GridView;

$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app','News'),
        'url'=>['admin-news/index']
    ]
];

?>

<div class="admin-index">

    <h1>
        <?= Html::a(Yii::t('app', 'Create'), ['create'], ['class' => 'btn btn-success pull-right']) ?>
        <?= Html::encode(Yii::t('app','News')) ?>
    </h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'title_de',
            [
                'attribute'=>'image_file_id',
                'format'=>'image',
                'value'=>function($model) {return $model->imageFile ? $model->imageFile->getThumbUrl('adminImagePreview'):null;}
            ],
            [
                'attribute'=>'dt',
                'format'=>'date',
                'options'=>['style'=>'width: 100px;']
            ],
            [
                'class' => 'app\components\ActionColumn',
                'template' => '{update} {delete}'
            ],
        ],
    ]); ?>

</div>