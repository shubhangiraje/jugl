<?php

namespace app\models;

use Yii;

class InterestParamValue extends \app\models\base\InterestParamValue
{
    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'param_id' => Yii::t('app','Param'),
            'param_value_id' => Yii::t('app','Param Value'),
        ];
    }

    public function __toString() {
        return $this->param.' - '.$this->paramValue;
    }
}
