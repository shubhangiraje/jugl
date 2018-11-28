<?php

use yii\helpers\Html;
use app\components\GridView;



$this->params['breadcrumbs']=\app\components\Helper::paramHierarchyBreadcrumbData($model);

?>

<div class="admin-update">

    <h1><?php echo Html::encode($this->title); ?></h1>

    <?=
    $this->render('_form', array(
            'model' => $model));
    ?>

</div>

<?php if ($model->type==\app\models\Param::TYPE_LIST) { ?>
    <div class="admin-index">

        <h1>
            <?= Html::a(Yii::t('app', 'Create'), ['admin-param-value/create','id'=>$model->id], ['class' => 'btn btn-success pull-right']) ?>

            <?= Html::encode(Yii::t('app','Values')) ?>
        </h1>

        <p>
        </p>

        <?=GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                'title',
                [
                    'class' => 'app\components\ActionColumn',
                    'template' => '{moveUp} {moveDown} {update} {delete}',
                    'buttons' => app\components\ModelSortableBehavior::actionColumnSortingButtons(),
                    'controller' =>'admin-param-value'
                ],
            ]
        ]); ?>
    </div>
<?php } ?>
