<?php

namespace app\models;

use Yii;

class SearchRequestFavorite extends \app\models\base\SearchRequestFavorite
{
    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'user_id' => Yii::t('app','User ID'),
            'search_request_id' => Yii::t('app','Search Request ID'),
        ];
    }
}
