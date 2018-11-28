<?php

use yii\helpers\Html;
use app\components\GridView;


$this->title=Yii::t('app','Updating Search Request Offer').' "'.$model.'"';

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

<div class="admin-update">

    <h1><?php echo Html::encode($this->title); ?></h1>

    <?=
    $this->render('_form', array(
            'model' => $model));
    ?>

</div>
