<?php

use yii\helpers\Html;

$this->title=Yii::t('app', 'Bind Param Value');

$this->params['breadcrumbs']=array_merge(
    \app\components\Helper::interestHierarchyBreadcrumbData($model->interest),
    [[
        'label'=>$this->title,
    ]]
);

?>

<div class="admin-create">

    <h1><?php echo Html::encode($this->title); ?></h1>

    <?=
    $this->render('_form', array(
        'model' => $model));
    ?>

</div>