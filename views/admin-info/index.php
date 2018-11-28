<?php

use yii\helpers\Html;
use app\components\GridView;

$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app','i-Informationen'),
        'url'=>['admin-info/index']
    ]
];

?>

<div class="admin-index">

    <h1>
        <?= Html::encode(Yii::t('app','i-Informationen')) ?>
    </h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'title_de',
            'view',
            [
                'class' => 'app\components\ActionColumn',
                'template' => '{update} {commetns}',
                'buttons'=> [
                    'commetns' => function($url, $model, $key) {
                        $params = [
                            'title' => Yii::t('app', 'Kommentare'),
                            'class'=>'action-btn',
                            'data-pjax'=>0
                        ];
                        $url = \yii\helpers\Url::to(['/admin-info-comment/list', 'id'=>$model->id]);
                        return Html::a('<span class="glyphicon glyphicon-comment"></span>', $url, $params);

                    }
                ]
            ],

        ],
    ]); ?>

</div>