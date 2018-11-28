<?php

use yii\helpers\Html;
use app\components\GridView;
use app\components\EDateTime;

$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app','Users Validation'),
        'url'=>['admin-user-validation/index']
    ]
];

?>

<div class="admin-index">

    <h2>
        <?= Html::encode(Yii::t('app','Nutzer deren Identität manuell zur Erstauszahlung überprüft werden muss!')) ?>
    </h2>

    <p>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute'=>'status',
                'value'=>function($model) { return $model->statusLabel; },
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'status',
                    $searchModel->getStatusList(),
                    ['class' => 'form-control']
                )
            ],
            'email',
            'first_name',
            'last_name',
            'nick_name',
            'balance',
            [
                'class' => 'app\components\ActionColumn',
                'template' => '<span class="btn-edit-large">{update}</span>'
            ],
        ],
    ]); ?>

</div>