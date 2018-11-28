<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "user_delivery_address".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $street
 * @property string $house_number
 * @property string $city
 * @property string $zip
 *
 * @property User $user
 */
class UserDeliveryAddress extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_delivery_address';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id'], 'integer'],
            [['street', 'house_number', 'city', 'zip'], 'string', 'max' => 128]
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne('\app\models\User', ['id' => 'user_id']);
    }
}
