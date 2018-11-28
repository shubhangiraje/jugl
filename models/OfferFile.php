<?php

namespace app\models;

use Yii;

class OfferFile extends \app\models\base\OfferFile
{
    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'file_id' => Yii::t('app','Abbildung'),
        ];
    }

    public function rules()
    {
        return [
            [['offer_id'], 'required'],
            ['file_id','safe'],
            [['offer_id', 'file_id', 'sort_order'], 'integer']
        ];
    }
}
