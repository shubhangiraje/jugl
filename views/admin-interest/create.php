<?php

use yii\helpers\Html;

$this->title=Yii::t('app', 'Creating Interest');


switch ($model->level) {
    case 1:
        $this->title=Yii::t('app', 'Creating Interest');
        break;
    case 2:
        $this->title=Yii::t('app', 'Creating Subcategories');
        break;
    case 3:
        $this->title=Yii::t('app', 'Creating theme filter');
        break;
    default;
}


$this->params['breadcrumbs']=array_merge(\app\components\Helper::interestHierarchyBreadcrumbData($model->parent,$_REQUEST['type']),[
    'title'=>$this->title
]);

?>

<div class="admin-create">

    <h1><?php echo Html::encode($this->title); ?></h1>

    <?=
    $this->render('_form', array(
        'model' => $model));
    ?>

</div>