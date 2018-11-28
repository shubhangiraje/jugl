<?php

use yii\helpers\Html;
use app\components\GridView;

$this->params['breadcrumbs']=[
    [
        'label'=>$type=='OFFER' ? Yii::t('app','Interessen für Werbung'):Yii::t('app','Interessen für Suchaufträge'),
        'url'=>['admin-interests/index?type='.$type]
    ]
];

?>

<div class="admin-index">

    <h1>
        <?= Html::a(Yii::t('app', 'Create interest'), ['create','type'=>$_REQUEST['type']], ['class' => 'btn btn-success pull-right']) ?>

        <?= Html::encode(Yii::t('app','Common interests')) ?>
    </h1>

    <p>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute'=>'file_id',
                'format'=>'image',
                'value'=>function($model) {return $model->file ? $model->file->getThumbUrl('adminImagePreview'):null;}
            ],
            'title',
            [
                'attribute' => 'offer_view_bonus',
                'value' => 'offer_view_bonus',
                'visible'=>($_REQUEST['type']=='OFFER')
            ],
            [
                'attribute' => 'search_request_bonus',
                'value' => 'search_request_bonus',
                'visible'=>($_REQUEST['type']=='SEARCH_REQUEST')
            ],
            [
                'class' => 'app\components\ActionColumn',
                'template' => '{moveUp} {moveDown} {update} {delete}',
                'buttons' => app\components\ModelSortableBehavior::actionColumnSortingButtons(),
            ],
        ],
    ]); ?>

</div>