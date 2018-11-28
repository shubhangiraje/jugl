<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "advertising_search_request_state".
 *
 * @property integer $id
 * @property integer $conversion_id
 * @property integer $user_id
 * @property string $dt
 * @property string $provider_id
 * @property string $campaign_name
 * @property string $transactionType
 * @property string $transactionStatus
 * @property integer $numTouchPointsTotal
 * @property integer $numTouchPointsAttributed
 * @property double $attributableCommission
 * @property string $description
 * @property string $currency
 * @property double $commission
 * @property double $orderAmount
 * @property string $IP
 * @property string $registrationDate 
 * @property string $assessmentDate
 * @property string $clickToConversion
 * @property string $originatingClickDate
 * @property string $rejectionReason
 * @property integer $paidOut
 * @property integer $countryCode
 * @property integer $attributionModel
 */
 
class AdvertisingSearchRequestState extends \app\components\ActiveRecord
{
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'advertising_search_request_state';
    }

    /**
     * @inheritdoc
     */
	public function rules()
    {
       return [
            [['user_id', 'campaign_name', 'transactionType', 'transactionStatus', 'commission'], 'required'],
			[['user_id', 'numTouchPointsTotal', 'numTouchPointsAttributed'], 'integer'],
            [['dt', 'provider_id', 'description', 'currency', 'IP', 'assessmentDate', 'clickToConversion', 'originatingClickDate', 'rejectionReason', 'countryCode', 'attributionModel', 'paidOut'], 'safe'],
			[['attributableCommission', 'commission', 'orderAmount'], 'number'],
			[['conversion_id'], 'unique', 'message'=>Yii::t('app','duplicate Entry')],
        ];
    }
}
