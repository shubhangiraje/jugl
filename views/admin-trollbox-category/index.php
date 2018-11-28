<?php

use yii\helpers\Html;
use app\components\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TrollboxCategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Kategorien für Forumbeiträge';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trollbox-category-index">

    <h1>
        <?= Html::a(Yii::t('app', 'Create'), ['create'], ['class' => 'btn btn-success pull-right']) ?>
        <?= Html::encode($this->title) ?>
    </h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'title',

            [
                'class' => 'app\components\ActionColumn',
                'template' => '{moveUp} {moveDown} {update} {delete}',
                'buttons' => app\components\ModelSortableBehavior::actionColumnSortingButtons(),
            ],
        ],
    ]); ?>

</div>
