<?php

namespace app\models;

use Yii;


class PayOutELVForm extends \app\components\Model {
    public $iban;
    public $bic;
    public $kontoinhaber;

    public function rules() {
        return [
          [['iban','bic','kontoinhaber'],'required'],
        ];
    }

    public function attributeLabels() {
        return [
            'iban'=>Yii::t('app','IBAN'),
            'bic'=>Yii::t('app','BIC'),
            'kontoinhaber'=>Yii::t('app','Kontoinhaber'),
        ];
    }
}