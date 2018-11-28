<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\InfoComment */

$this->title = Yii::t('app', 'Update comment');
$this->params['breadcrumbs'][] = ['label' => 'i-Informationen', 'url' => ['/admin-info/index']];
$this->params['breadcrumbs'][] = [
    'label' => $model->info->title_de ? Yii::t('app','Commentare').': '.$model->info->title_de : Yii::t('app','Commentare').': '.$model->info->view,
    'url' => ['list', 'id'=>$model->info_id]
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="info-comment-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
