<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TrollboxCategory */

$this->title = Yii::t('app', 'Bearbeiten').': '.$model->title;
$this->params['breadcrumbs'][] = ['label' => 'Kategorien für Forumbeiträge', 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Bearbeiten');
?>
<div class="trollbox-category-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
