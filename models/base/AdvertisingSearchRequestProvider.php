<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "advertising_search_request_provider".
 *
 * @property integer $id
 * @property integer $provider_id
 * @property string $auth_token
 */
 
 
class AdvertisingSearchRequestProvider extends \app\components\ActiveRecord
{
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'advertising_search_request_provider';
    }

    /**
     * @inheritdoc
     */
	public function rules()
    {
       return [
            [['provider_id', 'auth_token'], 'required'],
			[['provider_id'], 'integer'],
        ];
    }
}
