<?php

use yii\helpers\Html;
use app\components\GridView;

$label = \app\models\DefaultText::getCategoryLabel($_REQUEST['category']);

$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app', 'Texte bearbeiten').' - '.$label,
        'url'=>['admin-default-text/index', 'category'=>$_REQUEST['category']]
    ]
];

?>

<div class="admin-default-text-index">

    <h1>
        <?= Html::a(Yii::t('app', 'Create'), ['create', 'category'=>$_REQUEST['category']], ['class' => 'btn btn-success pull-right']) ?>
        <?= Html::encode($label) ?>
    </h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'text',
            [
                'class' => 'app\components\ActionColumn',
                'template' => '{update} {delete}'
            ],
        ],
    ]); ?>

</div>