<?php

use yii\helpers\Html;
use app\components\GridView;


$this->params['breadcrumbs']=\app\components\Helper::interestHierarchyBreadcrumbData($model);

?>

<div class="admin-update">

    <h1><?php echo Html::encode($this->title); ?></h1>

    <?=
    $this->render('_form', array(
            'model' => $model));
    ?>

</div>

<?php if ($model->level<=2) { ?>
<div class="admin-index">

    <h1>
        <?php

            if ($model->level == 1) {
                echo Html::a(Yii::t('app', 'Create subcategories'), ['create','id'=>$model->id], ['class' => 'btn btn-success pull-right']);
                echo Html::encode(Yii::t('app','Subcategories'));
            }

            if($model->level == 2) {
                echo Html::a(Yii::t('app', 'Create theme filter'), ['create','id'=>$model->id], ['class' => 'btn btn-success pull-right']);
                echo Html::encode(Yii::t('app','Theme filter'));
            }

        ?>
    </h1>

    <p>
    </p>

    <?php

    $columns=[];

    if ($model->level<2) {
        $columns=array_merge($columns,[
            [
                'attribute'=>'file_id',
                'format'=>'image',
                'value'=>function($model) {return $model->file ? $model->file->getThumbUrl('adminImagePreview'):null;}
            ],
        ]);
    }

    $columns=array_merge($columns,[
        'title',
        [
            'attribute' => 'offer_view_bonus',
            'value' => 'offer_view_bonus',
            'visible'=>($_REQUEST['type']=='OFFER')
        ],
        [
            'attribute' => 'search_request_bonus',
            'value' => 'search_request_bonus',
             'visible'=>($_REQUEST['type']=='SEARCH_REQUEST')
        ],
        [
            'class' => 'app\components\ActionColumn',
            'template' => '{moveUp} {moveDown} {update} {delete}',
            'buttons' => app\components\ModelSortableBehavior::actionColumnSortingButtons()
        ],
    ]);

    echo GridView::widget([
        'dataProvider' => $interestsDataProvider,
        'columns' => $columns,
    ]); ?>
</div>
<?php } ?>

<div class="admin-index">

    <h1>

        <?php
            if ($model->level == 1) {
                echo Html::a(Yii::t('app', 'Create param'), ['admin-param/create','id'=>$model->id], ['class' => 'btn btn-success pull-right']);
                echo Html::encode(Yii::t('app','Params parent interests'));
            }

            if($model->level == 2) {
                echo Html::a(Yii::t('app', 'Create param'), ['admin-param/create','id'=>$model->id], ['class' => 'btn btn-success pull-right']);
                echo Html::encode(Yii::t('app','Params subcategories'));
            }
        ?>


    </h1>

    <p>
    </p>

    <?=GridView::widget([
        'dataProvider' => $paramsDataProvider,
        'columns' => [
            'title',
            [
                'class' => 'app\components\ActionColumn',
                'template' => '{moveUp} {moveDown} {update} {delete}',
                'buttons' => app\components\ModelSortableBehavior::actionColumnSortingButtons(),
                'controller' => 'admin-param'
            ],
        ]
    ]); ?>
</div>

<?php /*
<div class="admin-index">

    <h1>
        <?= Html::a(Yii::t('app', 'Create'), ['admin-interest-param-value/create','id'=>$model->id], ['class' => 'btn btn-success pull-right']) ?>

        <?= Html::encode(Yii::t('app','Binded Params Values')) ?>
    </h1>

    <p>
    </p>

    <?=GridView::widget([
        'dataProvider' => $interestParamValueDataProvider,
        'columns' => [
            'param',
            'paramValue',
            [
                'class' => 'app\components\ActionColumn',
                'template' => '{update} {delete}',
                'buttons' => app\components\ModelSortableBehavior::actionColumnSortingButtons(),
                'controller' => 'admin-interest-param-value'
            ],
        ]
    ]); ?>
</div>
*/ ?>
