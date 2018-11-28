<?php

use yii\helpers\Html;
use app\components\GridView;

$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app','Admins'),
        'url'=>['admin-admin/index']
    ]
];

?>

<div class="admin-index">

    <h1>
        <?= Html::encode(Yii::t('app','Deleted Users')) ?>
    </h1>

    <p>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute'=>'deleted_dt',
                'filter'=>false,
                'value'=>function($model) {return new \app\components\EDateTime($model->deleted_dt);}
            ],
            'deleted_email:email',
            'deleted_first_name',
            'deleted_last_name',

            [
                'attribute'=>'is_user_profile_delete',
                'value' => function($model) {
                    return $model->userProfileDeleteLabel();
                },
                'format' => 'html',
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'is_user_profile_delete',
                    \app\models\User::userProfileDeleteList(),
                    ['class' => 'form-control','prompt'=>'']
                ),
            ],

            [
                'class' => 'app\components\ActionColumn',
                'template' => '{update} {undelete-user}',
                'buttons' => [
                    'undelete-user' => function($url, $model, $key) {
                        $url.='&pjaxForcePost=1';
                        $params = [
                            'title' => Yii::t('app', 'Revert deletion'),
                            'onclick' => 'yii.pjaxConfirm("' . Yii::t('app', 'Do you really want to undelete this user?') . '",this,event)',
                        ];

                        //$params['data-method'] = 'post';

                        return Html::a(
                            '<span class="glyphicon glyphicon-repeat"></span>',
                            $url,
                            $params
                        );
                    },
                ]
            ],
        ],
    ]); ?>

</div>