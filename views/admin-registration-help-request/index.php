<?php

use yii\helpers\Html;
use app\components\GridView;
use app\components\EDateTime;
use app\components\ActiveForm;

$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app','Registration Help Requests'),
        'url'=>['admin-site/index']
    ]
];

$this->params['fullWidth']=true;

?>

<div class="admin-index">

    <h1>
        <?= Html::encode(Yii::t('app','Registration Help Requests')) ?>
    </h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pjax'=>true,
        'columns' => [
            [
                'attribute'=>'dt',
                'format'=>'date',
                'filter'=>false,
            ],
            'ip',
            'first_name',
            'last_name',
            'company_name',
            [
                'attribute'=>'birthday',
                'format'=>'date',
                'filter'=>false
            ],
            [
                'attribute'=>'email',
                'format'=>'raw',
                'value'=>function($model) {
                    return $model->user_id ? \yii\helpers\Html::a(
                        \yii\helpers\Html::encode($model->email),
                        \yii\helpers\Url::to(['admin-user/update','id'=>$model->user_id]),['data-pjax'=>0]
                    ):\yii\helpers\Html::encode($model->email);
                }
            ],
            'phone',
            [
                'attribute'=>'sex',
                'value'=>function($model) {return $model->getSexLabel();},
                'filter'=>false,
            ],
            [
                'attribute'=>'step'
            ],
            [
                'class' => 'app\components\ActionColumn',
                'template' => '{delete-request}',
                'buttons' => [
                    'delete-request' => function($url, $model, $key) {
                        $url.='&pjaxForcePost=1';

                        $params = [
                            'title' => Yii::t('app', 'Endgültig löschen'),
                            'onclick' => 'yii.pjaxConfirm("' . Yii::t('app', 'Do you really want to delete this item?') . '",this,event)',
                        ];

                        //$params['data-method'] = 'post';

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