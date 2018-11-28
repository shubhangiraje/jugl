<?php

namespace app\components;

use Yii;
use app\models\File;

class FileIdValidator extends \yii\validators\Validator
{
    public function init()
    {
        parent::init();
        if ($this->message === null) {
            $this->message = Yii::t('app', '{attribute} is invalid".');
        }
    }

    protected function validateValue($value)
    {
        if (!File::getIdFromProtected($value)) {
            return $this->message;
        }
    }
}
