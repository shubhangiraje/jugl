<?php

namespace app\models;

use Yii;


class PayOutPayPalForm extends \app\components\Model {
    public $email;

    public function rules() {
        return [
          [['email'],'required'],
          [['email'],'email']
        ];
    }

    public function attributeLabels() {
        return [
            'email'=>Yii::t('app','E-Mail')
        ];
    }
}