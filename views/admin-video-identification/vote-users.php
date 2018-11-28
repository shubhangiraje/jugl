<?php

use yii\helpers\Html;
use app\components\GridView;
use app\models\Country;
use app\models\User;

$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app','Verifizierungsvideos'),
        'url'=>['admin-video-identification/index']
    ],
    [
        'label'=>Yii::t('app', 'Videoidentifikation des Users {user}', ['user'=>$trollboxMessage->user->getName()])
    ]
];

?>

<div class="admin-index">
    <h1><?= $vote==1 ? Yii::t('app', 'Benutzer mit Echt-Stimmen') : Yii::t('app', 'Benutzer mit Nicht-Echt-Stimmen') ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'pjax'=>false,
        'columns' => [
            [
                'attribute'=>'dt',
                'value'=>function($model) {return (new \app\components\EDateTime($model->dt))->format('d.m.Y H:i');},
                'options'=>['style'=>'width: 150px;']
            ],
            [
                'attribute'=>'user.first_name',
                'label'=>'Nutzer',
                'value'=>function($model) {
                    return Html::a($model->user->name,['admin-user/update','id'=>$model->user_id],['data-pjax'=>0]);
                },
                'format'=>'raw',
            ],
            [
                'attribute'=>'user.email',
                'label'=>'Email',
                'value'=>function($model) {
                    return $model->user->email;
                }
            ]
        ],
    ]); ?>

</div>