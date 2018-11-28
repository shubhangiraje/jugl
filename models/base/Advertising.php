<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "advertising".
 *
 * @property integer $id
 * @property string $advertising_name
 * @property double $advertising_total_bonus
 * @property integer $advertising_total_views
 * @property integer $advertising_total_clicks
 * @property integer $banner
 * @property integer $link 
 * @property string $dt
 * @property integer $status
 * @property string $advertising_position 
 * @property string $provider 
 * @property string $banner_height 
 * @property string $banner_width
 * @property string $advertising_type
 * @property double $user_bonus
 * @property string $advertising_display_name
 * @property integer $popup_interval
 * @property string $display_date
 * @property integer $country_id
 *
 * @property AdvertisingInterest[] $advertisingInterests

 
 */
class Advertising extends \app\components\ActiveRecord
{
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'advertising';
    }

    /**
     * @inheritdoc
     */
	public function rules()
    {
         return [
            [['advertising_name', 'advertising_display_name', 'provider', 'user_bonus', 'advertising_total_bonus', 'advertising_total_views', 'display_date', 'popup_interval'], 'required'],
			[['advertising_total_views', 'advertising_total_clicks', 'status', 'popup_interval'], 'integer'],
            [['dt', 'link', 'banner', 'advertising_position', 'provider', 'banner_height', 'banner_width', 'advertising_type', 'advertising_display_name', 'click_interval', 'country_id'], 'safe'],
			[['user_bonus', 'advertising_total_bonus'], 'number']
        ];
    }
	
	/**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne('\app\models\User', ['id' => 'user_id']);
    }
	
	/**
     * @return \yii\db\ActiveQuery
     */
    public function getAdvertisingInterests()
    {
        return $this->hasMany('\app\models\AdvertisingInterest', ['advertising_id' => 'id']);
    }

}
