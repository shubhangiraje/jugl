<?php

namespace app\components;

use Yii;

class CountValidator extends \yii\validators\Validator
{
    // max count
    public $max;

    public function init()
    {
        parent::init();
        if ($this->message === null) {
            $this->message = Yii::t('app', '{attribute} is invalid".');
        }
    }

    protected function validateValue($value)
    {
        if ($this->max && count($value)>$this->max) {
            return [$this->message,['max'=>$this->max]];
        }
    }
}
