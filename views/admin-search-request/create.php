<?php

use yii\helpers\Html;
use app\components\GridView;

$this->title=Yii::t('app', 'Creating Search Request');
$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app','Advertising'),
        'url'=>['admin-advertising/index']
    ],
    [
        'label'=>$this->title,
    ]
];
?>
<div class="admin-create">

    <h1><?php echo Html::encode($this->title); ?></h1>
		
    <?=
    $this->render('_createform', ['model' => $model,'isModel'=>$isModel]);
    ?>

</div>
