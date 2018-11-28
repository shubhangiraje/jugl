<?php

use yii\helpers\Html;
use app\components\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\InfoCommentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$info->title_de ? $this->title=Yii::t('app','Commentare').': '.$info->title_de : $this->title = Yii::t('app','Commentare').': '.$info->view;

$this->params['breadcrumbs']=[
    ['label'=>Yii::t('app','i-Informationen'), 'url'=>['admin-info/index']],
    ['label'=>$this->title]
];

?>

<div class="info-comment-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pjax'=>false,
        'columns' => [

            [
                'attribute'=>'dt',
                'format'=>'html',
                'value'=> function($model) {
                    return !empty($model->dt) ? (new \app\components\EDateTime($model->dt)) : '';
                },
                'filter'=>
                    \kartik\date\DatePicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'dt',
                        'readonly'=>true,
                        'pluginOptions' => [
                            'format' => 'yyyy-mm-dd',
                            'autoclose'=>true
                        ],
                        'options'=>['style'=>'width:100px;background:white;cursor:pointer;']
                    ]),
                'contentOptions'=>['style'=>'width: 150px']
            ],

            [
                'attribute'=>'user_id',
                'value'=>function($model) {
                    return Html::a($model->user->name,['admin-user/update','id'=>$model->user_id], ['data-pjax'=>0]);
                },
                'format'=>'raw',
                'filter'=> Html::activeTextInput($searchModel,'user_name',['class'=>'form-control'])
            ],

            [
                'attribute'=>'file_id',
                'format'=>'raw',
                'value'=>function($model) {

                    if($model->file) {
                        if($model->file->ext=='mp4') {
                            return Html::a(Html::img($model->file->getThumbUrl('adminImagePreview')), $model->file->link, ['target'=>'_blank', 'class'=>'video-link']);
                        } else {
                            return Html::a(Html::img($model->file->getThumbUrl('adminImagePreview')), $model->file->getThumbUrl('fancybox'), ['class'=>'fancybox']);
                        }
                    } else {
                        return null;
                    }
                }
            ],

            [
                'attribute'=>'comment',
                'value'=>function($model) {
                    return '<div class="description-break-word">'.$model->comment.'<div>';
                },
                'format'=>'raw'
            ],

            [
                'attribute'=>'lang',
                'value' => function($model) {
                    return $model->lang;
                },
                'format' => 'html',
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'lang',
                    \app\components\Helper::getLangList(),
                    ['class' => 'form-control','prompt'=>'']
                ),
                'contentOptions'=>['style'=>'width: 80px; text-align: center']
            ],

            [
                'class' => 'app\components\ActionColumn',
                'template' => '{edit-comment} {delete-comment}',
                'buttons'=>[
                    'edit-comment' => function($url, $model, $key) {
                        $params = [
                            'title' => Yii::t('app', 'Update'),
                            'class'=>'action-btn',
                            'data-pjax'=>0
                        ];
                        $url = \yii\helpers\Url::to(['/admin-info-comment/update', 'id'=>$model->id]);
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, $params);

                    },
                    'delete-comment' => function($url, $model, $key) {
                        $params = [
                            'title' => Yii::t('app', 'Delete'),
                            'onclick' => 'if (!confirm("'.Yii::t('app','Do you really want to delete this item?').'")) {event.preventDefault();event.stopPropagation();}',
                            'class'=>'action-btn'
                        ];
                        $url = \yii\helpers\Url::to(['/admin-info-comment/delete', 'id'=>$model->id]);
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url.'&pjaxForcePost=1', $params);

                    },

                ],
                'options'=>['style'=>'width: 80px; text-align: center']
            ],
        ],
    ]); ?>
</div>
