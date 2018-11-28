<?php

use yii\helpers\Html;
use app\components\Thumb;

?>
<div class="preview-image">
    <?=Html::img(Thumb::createUrl($file->getUrl(),'adminImagePreview'))?>
    <span class="btn-delete-file glyphicon glyphicon-remove"></span>
</div>




