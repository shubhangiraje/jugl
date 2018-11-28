<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\UserTeamFeedback */

$this->title = Yii::t('app','Update Feedback');
$this->params['breadcrumbs']=[
    ['label'=>Yii::t('app','Users'), 'url'=>['admin-user/index']],
    ['label'=>$model->user->name, 'url'=>['admin-user/update', 'id'=>$model->user->id]],
    ['label'=>$this->title,]
];
?>
<div class="user-team-feedback-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
