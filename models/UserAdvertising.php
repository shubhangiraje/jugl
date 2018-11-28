<?php

namespace app\models;

use Yii;
use \app\components\EDateTime;

class UserAdvertising extends \app\models\base\UserAdvertising
{
		
    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','Id'),
            'advertising_id' => Yii::t('app','Werbung Id'),
			'user_id' => Yii::t('app','User id'),
			'dt' => Yii::t('app','Datum angesehen'),
			'status' => Yii::t('app','Status'),   
			'advertising_bonus' => Yii::t('app','User Bonus'),   
        ];
    }
	
	 /**
     * @inheritdoc 
     */
    
	
	private static function processAccept($userId,$advertising_id) {
        $trx=Yii::$app->db->beginTransaction();

        $Advertising=Advertising::findOne(['id'=>$advertising_id]);
		$UserAdvertising = UserAdvertising::find()->where(['user_id'=>$userId, 'advertising_id'=>$advertising_id])->orderBy(['id'=>SORT_DESC])->one();
        
        $comment = Yii::t('app','Du hast einen Werbebonus von [sum][/sum] für das Anschauen der [advertising:{advertisingid}]"Banner Werbung"[/advertising] erhalten',[
                'advertisingid'=>$Advertising->id,
                'advertisingTitle'=>$Advertising->advertising_display_name]
        );
 
        $commentOut = Yii::t('app',' [user][/user] Hat Dich zu jugl.net eingeladen. Deshalb gibst Du [sum][/sum] Deiner Einnahmen für das Anschauen der [advertising:{advertisingid}]"Banner Werbung"[/advertising] an [user][/user] ab',[
				'advertisingid'=>$Advertising->id,
                'advertisingTitle'=>$Advertising->advertising_display_name]
        );

        $commentInRef = Yii::t('app','Hat die [advertising:{advertisingid}]"Banner Werbung"[/advertising] angeschaut. Dafür erhältst Du anteilig [sum][/sum]',[
                'advertisingid'=>$Advertising->id,
                'advertisingTitle'=>$Advertising->advertising_display_name]
        );

        $commentOutRef = Yii::t('app','[user][/user] Hat Dich zu jugl.net eingeladen. Deshalb gibst Du [sum][/sum] Deiner Einnahmen für das Anschauen der [advertising:{advertisingid}]"Banner Werbung"[/advertising] an [user][/user]',[
                'advertisingid'=>$Advertising->id,
                'advertisingTitle'=>$Advertising->advertising_display_name,
                'user'=>Yii::$app->user->identity->name]
        );

        Yii::$app->user->identity->packetCanBeSelected();
        Yii::$app->user->identity->distributeReferralPayment($UserAdvertising->advertising_bonus,$UserAdvertising->user,\app\models\BalanceLog::TYPE_IN,\app\models\BalanceLog::TYPE_IN_REF,\app\models\BalanceLog::TYPE_IN_REF_REF, $comment, 0, $commentOut, $commentInRef, $commentOutRef,true,true);

        $trx->commit();
       
    }
	
	public static function accept($userId,$advertising_id) {
        $result=static::processAccept($userId,$advertising_id);
        Yii::info("[$userId:$advertising_id] ".$result,'advertising_bonus');
        return $result;
    }
}
