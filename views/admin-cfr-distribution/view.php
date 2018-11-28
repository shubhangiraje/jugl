<?php

use yii\helpers\Html;
use app\components\GridView;

$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app','Cash for Likes'),
        'url'=>['admin-cfr-distribution/index']
    ],
    [
        'label'=>Yii::$app->formatter->asDate($cfrDistribution->dt, 'php:d.m.Y'),
    ]
];

?>

<div class="admin-index">

    <h1>
        <?= Html::encode(Yii::t('app','Cash for Likes')).' '.Yii::$app->formatter->asDate($cfrDistribution->dt, 'php:d.m.Y') ?>
    </h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
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
                'attribute'=>'user.email',
                'label'=>'Email',
                'value'=>function($model) {
                    return $model->user->email;
                },
                'format'=>'email',
                'filter'=> Html::activeTextInput($searchModel,'user_email',['class'=>'form-control'])
            ],
            [
                'attribute'=>'votes_count',
                'value'=>'votes_count'
            ],
            [
                'attribute'=>'jugl_sum',
                'value'=>'jugl_sum'
            ]
        ],
    ]); ?>

</div>