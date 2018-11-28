<?php

use yii\helpers\Html;
use app\components\GridView;
use app\components\EDateTime;
use app\components\ActiveForm;

$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app','Midglied werden'),
        'url'=>['admin-site/index']
    ]
];

$this->params['fullWidth']=true;

?>

<div class="admin-index">

    <h1>
        <?= Html::encode(Yii::t('app','Midglied werden')) ?>
    </h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pjax'=>true,
        'columns' => [
            [
                'attribute'=>'registration_dt',
                'format'=>'date',
                'filter'=>false,
            ],
            'first_name',
            'last_name',
            'email',
            'phone',
            [
                'class' => 'app\components\ActionColumn',
                //'template' => '{delete-invite-me}',
                'template' => '',
                'buttons' => [
                    'delete-invite-me' => function($url, $model, $key) {
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