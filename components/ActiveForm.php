<?php

namespace app\components;

class ActiveForm extends \yii\bootstrap\ActiveForm {
    public $labelInPlaceholder=false;

    public function field($model, $attribute, $options = []) {
        if ($this->labelInPlaceholder) {
            $options['inputOptions']['placeholder']=$model->getAttributeLabel($attribute);
        }

        return parent::field($model, $attribute, $options);
    }
}