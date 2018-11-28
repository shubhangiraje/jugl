<?php

use yii\helpers\Html;
use app\components\GridView;

$this->params['breadcrumbs']=array_merge(
    \app\components\Helper::paramHierarchyBreadcrumbData($model->param),[[
    'label'=>'Value "'.$model.'"'
]]);

?>

<div class="admin-update">

    <h1><?php echo Html::encode($this->title); ?></h1>

    <?=
    $this->render('_form', array(
            'model' => $model));
    ?>

</div>

