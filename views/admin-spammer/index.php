<?php

use yii\helpers\Html;
use app\components\GridView;
use app\components\EDateTime;
use app\components\user;
$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app','Spammers'),
        'url'=>['admin-spammer/index']
    ]
];

$this->params['fullWidth']=true;

?>

<div class="admin-index">

    <h1>
        <?= Html::encode(Yii::t('app','Spammers')) ?>
    </h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pjax'=>false,
        'columns' => [
            [
                'attribute'=>'spam_reports',
                'options'=>['style'=>'width: 100px;']
            ],
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
                'attribute'=>'registration_dt',
                'format'=>'date',
                'filter'=>
                    \kartik\date\DatePicker::widget([
                        'model'=>$searchModel,
                        'attribute'=>'registration_dt_from',
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
                        'attribute'=>'registration_dt_to',
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
                'attribute'=>'last_spam_report_dt',
                'format'=>'date',
                'filter'=>
                    \kartik\date\DatePicker::widget([
                        'model'=>$searchModel,
                        'attribute'=>'last_spam_report_dt_from',
                        'readonly'=>true,
                        'pluginOptions'=>[
                            'clearBtn'=>true
                        ],
                        'options'=>['style'=>'width:100px;background:white;cursor:pointer;'],
                        'layout'=>'<span class="input-group-addon" style="width:55px;">'.Yii::t('app','From').'</span>{input}{picker}{remove}'

                    ]).
                    \kartik\date\DatePicker::widget([
                        'model'=>$searchModel,
                        'attribute'=>'last_spam_report_dt_to',
                        'readonly'=>true,
                        'pluginOptions'=>[
                            'clearBtn'=>true
                        ],
                        'options'=>['style'=>'width:100px;background:white;cursor:pointer;'],
                        'layout'=>'<span class="input-group-addon" style="width:55px;">'.Yii::t('app','To').'</span>{input}{picker}{remove}'
                    ]),
                'options'=>['style'=>'width: 150px;']
            ],
            'email',
            [
                'attribute'=>'avatar_file_id',
                'format'=>'raw',
                'value'=>function($model) {
                    return \yii\helpers\Html::a(
                        \yii\helpers\Html::img($model->getAvatarThumbUrl('avatar'),['style'=>'width:50px;height:50px;border-radius:25px;']),
                        \yii\helpers\Url::to(['admin-user/update','id'=>$model->id]),['data-pjax'=>0]
                    );
                }
            ],
            'first_name',
            'last_name',
            'nick_name',
            'balance',
            [
                'class' => 'app\components\ActionColumn',
                'template' => '{update} {block-user} {delete-user}',
                'controller' => 'admin-user',
                'buttons' => [
                    'block-user' => function($url, $model, $key) {
                        if ($model->status!=\app\models\User::STATUS_ACTIVE) return '';

                        $params = [
                            'title' => Yii::t('app', 'Vorrübergehend deaktivieren'),
                            'onclick' => 'yii.pjaxConfirm("' . Yii::t('app', 'Do you really want to block this user?') . '",this,event)',
                            //'onclick' => 'if (!confirm("' .  . '")) {event.preventDefault();event.stopPropagation();}',
                        ];

                        $params['data-method'] = 'post';

                        return Html::a(
                            '<span class="glyphicon glyphicon-ban-circle"></span>',
                            $url,
                            $params
                        );
                    },
                    'delete-user' => function($url, $model, $key) {
                        if ($model->status==\app\models\User::STATUS_DELETED) return '';

                        $params = [
                            'title' => Yii::t('app', 'Endgültig löschen'),
                            'onclick' => 'yii.pjaxConfirm("' . Yii::t('app', 'Do you really want to delete this item?') . '",this,event)',
                            //'onclick' => 'if (!confirm("' .  . '")) {event.preventDefault();event.stopPropagation();}',
                        ];

                        $params['data-method'] = 'post';

                        return Html::a(
                            '<span class="glyphicon glyphicon-trash"></span>',
                            $url,
                            $params
                        );
                    }
                ]
            ],
        ],
    ]); ?>

</div>