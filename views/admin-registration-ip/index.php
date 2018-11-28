<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\GridView;
use app\components\EDateTime;
use app\components\ActiveForm;

$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app','Registration IP Stats'),
        'url'=>['admin-site/index']
    ]
];

?>

<div class="admin-index">

    <h1>
        <?= Html::encode(Yii::t('app','Registration IP Stats')) ?>
    </h1>

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'method'=>'get',
        'fieldConfig'=>[
            'template'=>"{label}\n<div class=\"col-sm-6\">{input}</div>",
            'labelOptions'=>['class'=>'col-sm-6 control-label'],
            'inputOptions'=>['class'=>'form-control'],
        ]
    ]); ?>

    <style>
        form label.control-label {
            padding-right: 0;
            padding-left: 0;
        }
    </style>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-4">
                <?php
                    echo \kartik\date\DatePicker::widget([
                        'model'=>$searchModel,
                        'attribute'=>'date_from',
                        'pluginOptions'=>[
                            'clearBtn'=>true
                        ],
                        //'options'=>['style'=>'width:100px;background:white;cursor:pointer;'],
                        //'addon'=>['<span style="display:inline-block;width:35px;">'.Yii::t('app','From').'</span>'],
                        'layout'=>'<span class="input-group-addon" style="width:55px;">'.Yii::t('app','R.-Datum von').'</span>{input}{picker}{remove}'
                    ])
                ?>
            </div>
            <div class="col-sm-4">
                <?php
                    echo \kartik\date\DatePicker::widget([
                    'model'=>$searchModel,
                    'attribute'=>'date_to',
                    'pluginOptions'=>[
                        'clearBtn'=>true
                    ],
                    //'options'=>['style'=>'width:100px;background:white;cursor:pointer;'],
                    //'addon'=>['<span style="display:inline-block;width:35px;">'.Yii::t('app','From').'</span>'],
                    'layout'=>'<span class="input-group-addon" style="width:55px;">'.Yii::t('app','R.-Datum bis').'</span>{input}{picker}{remove}'
                ])
                ?>
            </div>
            <div class="col-sm-4 text-right">
                <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
                <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end() ?>
    <br/>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'id'=>'registration_ip_stats_list',
        //'filterModel' => $searchModel,
        //'pjax'=>true,
        'columns' => [
            [
                'attribute'=>'registration_ip',
                'value'=>function($row) {
                    return \yii\helpers\Html::a($row['registration_ip'],['admin-user/index','pjax'=>'0','UserSearch[registration_ip]'=>$row['registration_ip']]);
                },
                'format'=>'raw',
                'width'=>'300px',
                'label'=>Yii::t('app','Registration IP'),
            ],
            [
                'attribute'=>'cnt',
                'label'=>Yii::t('app','Registrations'),
                'width'=>'100px'
            ],
        ],
    ]); ?>

</div>