<?php

namespace app\models;

use Yii;


class AddUserBalanceForm extends \app\components\Model
{
    public $distribute;
    public $sum;
    public $comments;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['sum','comments','distribute'], 'required'],
            [['distribute'],'safe'],
            [['sum'], 'number'],
        ];
    }

    public function attributeLabels() {
        return [
            'sum'=>Yii::t('app','Betrag'),
            'comments'=>Yii::t('app','Kommentar'),
            'distribute'=>Yii::t('app','Distribute payment in hierarchy')
        ];
    }
}
