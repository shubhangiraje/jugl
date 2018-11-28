<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SearchRequestComment */

$this->title = 'Update comment';

$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app','Search Requests'),
        'url'=>['admin-search-request/index']
    ],
    [
        'label'=>Yii::t('app','Search Request').' "'.$model->searchRequest.'"',
        'url'=>['admin-search-request/update','id'=>$model->search_request_id]
    ],
    [
        'label'=>$this->title,
    ]
];

?>
<div class="search-request-comment-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
