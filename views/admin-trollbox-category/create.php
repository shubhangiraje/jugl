<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\TrollboxCategory */

$this->title = Yii::t('app', 'Kategorie erstellen');
$this->params['breadcrumbs'][] = ['label' => 'Kategorien für Forumbeiträge', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="trollbox-category-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
