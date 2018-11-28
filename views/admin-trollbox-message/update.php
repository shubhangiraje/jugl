<?php

use yii\helpers\Html;

$this->title=Yii::t('app','Gruppenchat "{title}"',['title'=>\yii\helpers\StringHelper::truncate($model->text,32)]);

$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app','Gruppenchat'),
        'url'=>['admin-trollbox-message/index']
    ],
    [
        'label'=>$this->title,
    ]
];

?>

<div class="admin-update">

    <h1><?php echo Html::encode(\yii\helpers\StringHelper::truncate($model->text,32)); ?></h1>

    <?=
    $this->render('_form', array(
            'model' => $model));
    ?>

    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active"><a href="#tab1" role="tab" data-toggle="tab"><?=Yii::t('app','Moderatoren')?></a></li>
        <li role="presentation"><a href="#tab2" role="tab" data-toggle="tab"><?=Yii::t('app','Freigaben / Ablehnungen')?></a></li>
        <li role="presentation"><a href="#tab3" role="tab" data-toggle="tab"><?=Yii::t('app','Benutzer gesperrt')?></a></li>
    </ul>

    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="tab1">
            <h1><?=Yii::t('app','Moderatoren')?></h1>
            <?=\app\components\GridView::widget([
                'dataProvider' => $groupChatModeratorLastVisitProvider,
                'columns' => [
                    [
                        'attribute'=>'dt',
                        'label'=>Yii::t('app','Date'),
                        'value'=>function($model) {return (new \app\components\EDateTime($model->dt));}
                    ],
                    [
                        'attribute'=>'moderator_user_id',
                        'label'=>Yii::t('app','Moderator'),
                        'value'=>function($model) {
                            return Html::a($model->moderatorUser->name,['admin-user/update','id'=>$model->moderator_user_id],['data-pjax'=>0]);
                        },
                        'format'=>'raw',
                    ],
                ]
            ]); ?>
        </div>

        <div role="tabpanel" class="tab-pane" id="tab2">
            <h1><?=Yii::t('app','Freigaben / Ablehnungen')?></h1>
            <?=\app\components\GridView::widget([
                'dataProvider' => $trollboxMessageStatusHistoryProvider,
                'columns' => [
                    [
                        'attribute'=>'dt',
                        'label'=>Yii::t('app','Date'),
                        'value'=>function($model) {return (new \app\components\EDateTime($model->dt));}
                    ],
                    [
                        'attribute'=>'status',
                        'value'=>function($model) {return $model->statusLabel;},
                        'options'=>['style'=>'width: 130px;']
                    ],
                    [
                        'attribute'=>'user_id',
                        'label'=>Yii::t('app','Moderator'),
                        'value'=>function($model) {
                            return Html::a(($model->user_id==$model->trollboxMessage->user_id ? 'Nutzer'.($model->user->is_moderator ? '/Moderator ':''):'').$model->user->name,['admin-user/update','id'=>$model->user_id],['data-pjax'=>0]);
                        },
                        'format'=>'raw',
                    ],
                ]
            ]); ?>
        </div>

        <div role="tabpanel" class="tab-pane" id="tab3">
            <h1><?=Yii::t('app','Benutzer gesperrt')?></h1>
            <?=\app\components\GridView::widget([
                'dataProvider' => $chatUserIgnoreProvider,
                'columns' => [
                    [
                        'attribute'=>'dt',
                        'label'=>Yii::t('app','Date'),
                        'value'=>function($model) {return (new \app\components\EDateTime($model->dt));}
                    ],
                    [
                        'attribute'=>'user_id',
                        'label'=>Yii::t('app','Nutzer'),
                        'value'=>function($model) {
                            return Html::a($model->ignoreUser->name,['admin-user/update','id'=>$model->ignore_user_id],['data-pjax'=>0]);
                        },
                        'format'=>'raw',
                    ],
                    [
                        'attribute'=>'user_id',
                        'label'=>Yii::t('app','Moderator'),
                        'value'=>function($model) {
                            return Html::a($model->moderatorUser->name,['admin-user/update','id'=>$model->moderator_user_id],['data-pjax'=>0]);
                        },
                        'format'=>'raw',
                    ],
                ]
            ]); ?>
        </div>
    </div>
</div>