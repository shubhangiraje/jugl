<?php

namespace app\models;

use Yii;

class UserPhoto extends \app\models\base\UserPhoto
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
            [['user_id'], 'required'],
            ['file_id','safe'],
            [['user_id', 'file_id', 'sort_order'], 'integer']
        ];
    }
}
