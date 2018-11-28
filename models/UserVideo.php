<?php

namespace app\models;

use Yii;
use \app\components\EDateTime;

class UserVideo extends \app\models\base\UserVideo
{
		
    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'video_id' => Yii::t('app','Video Id'),
            'user_id' => Yii::t('app','User Id'),
			'dt' => Yii::t('app','Datum angesehen'),
			'dt_full' => Yii::t('app','Datum und Zeit angesehen'),
			'bonus' => Yii::t('app','Werbebonus'),   
        ];
    }
	
	 /**
     * @inheritdoc 
     */
    
	
	private static function processAccept($userId,$videoid) {
        $trx=Yii::$app->db->beginTransaction();

        $UserVideo=UserVideo::findOne(['video_id'=>$videoid,'user_id'=>$userId]);
        
        $comment = Yii::t('app','Du hast einen Werbebonus von [sum][/sum] für das Anschauen des Videos [video:{videoid}]"{videoTitle}"[/video] erhalten',[
                'videoid'=>$UserVideo->video->video_id,
                'videoTitle'=>$UserVideo->video->name]
        );

        $commentOut = Yii::t('app',' [user][/user] Hat Dich zu jugl.net eingeladen. Deshalb gibst Du [sum][/sum] Deiner Einnahmen für das Anschauen des Videos [video:{videoid}]"{videoTitle}"[/video] an [user][/user] ab',[
               'videoid'=>$UserVideo->video->video_id,
                'videoTitle'=>$UserVideo->video->name]
        );

        $commentInRef = Yii::t('app','Hat das Video  [video:{videoid}]"{videoTitle}"[/video] angeschaut. Dafür erhältst Du anteilig [sum][/sum]',[
                'videoid'=>$UserVideo->video->video_id,
                'videoTitle'=>$UserVideo->video->name]
        );

        $commentOutRef = Yii::t('app','[user][/user] Hat Dich zu jugl.net eingeladen. Deshalb gibst Du [sum][/sum] Deiner Einnahmen für das Anschauen des Videos [video:{videoid}]"{videoTitle}"[/video] an [user][/user]',[
                'videoid'=>$UserVideo->video->video_id,
                'videoTitle'=>$UserVideo->video->name,
                'user'=>Yii::$app->user->identity->name]
        );

        //$offerView->offer->user->addBalanceLogItem(\app\models\BalanceLog::TYPE_OUT, -$offerView->offer->view_bonus, $offerView->offer->user, $comment);
        Yii::$app->user->identity->distributeReferralPayment($UserVideo->bonus,$UserVideo->user,\app\models\BalanceLog::TYPE_IN,\app\models\BalanceLog::TYPE_IN_REF,\app\models\BalanceLog::TYPE_IN_REF_REF, $comment, 0, $commentOut, $commentInRef, $commentOutRef,true,true);

         /*if ($UserVideo->user->parent) {
                $UserVideo->user->parent->distributeReferralPayment(
                    $UserVideo->bonus*\app\models\Video::VIEW_BONUS_PERCENT_PARENT/100,
                    $UserVideo->user,
                    \app\models\BalanceLog::TYPE_IN,\app\models\BalanceLog::TYPE_IN_REF,\app\models\BalanceLog::TYPE_IN_REF_REF,
                    Yii::t('app','Du hast [user][/user] zu Jugl.net eingeladen. Er hat das Video [video:{videoid}]"{videoTitle}"[/video] geschaut. Dafür erhältst Du [sum][/sum]',[
                        'videoid'=>$UserVideo->video->video_id,
						'videoTitle'=>$UserVideo->video->name,
                        'user'=>$UserVideo->user->first_name.' '.$UserVideo->user->last_name
                    ])
                );
          }*/
        Yii::$app->user->identity->packetCanBeSelected();

        $trx->commit();

       
    }
	
	public static function accept($userId,$videoid) {
        $result=static::processAccept($userId,$videoid);
        Yii::info("[$userId:$videoid] ".$result,'bonus');
        return $result;
    }
}
