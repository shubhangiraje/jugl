<?php

use yii\helpers\Html;

$this->title=Yii::t('app','Import Glomex');

$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app','Video'),
        'url'=>['admin-video/index']
    ],
    [
        'label'=>$this->title,
    ]
];

?>

<div class="admin-update">

    <h1><?php echo Html::encode($this->title); ?></h1>
	<?=
    $import_result;
    ?>
    

</div>