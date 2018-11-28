<?php

namespace app\models;

use Yii;
use \app\components\EDateTime;


class OfferView extends \app\models\base\OfferView
{
    private static function processAccept($userId,$offerId,$code) {
        $trx=Yii::$app->db->beginTransaction();

        $offerView=OfferView::findOne(['offer_id'=>$offerId,'user_id'=>$userId]);

        if (!$offerView) {
            return Yii::t('app','invalid offerview');
        }

        if ($offerView->code!=$code) {
            return Yii::t('app','invalid code');
        }

        $offerView->lockForUpdate();
        $offerView->offer->lockForUpdate();

        if ($offerView->offer->status!=\app\models\Offer::STATUS_ACTIVE || $offerView->offer->view_bonus_used+abs($offerView->offer->view_bonus*(100+\app\models\Offer::VIEW_BONUS_PERCENT_JUGL+\app\models\Offer::VIEW_BONUS_PERCENT_PARENT)/100)>$offerView->offer->view_bonus_total+1e-6) {
            return Yii::t('app','Offer expired');
        }

        if ($offerView->got_view_bonus>0) {
            return Yii::t('app','you already got werbebonus');
        }

        /*
                if ($offerView->offer->view_bonus>$offerView->offer->user->balance) {
                    return ['result'=>'not enough funds'];
                }
        */
        $now=new EDateTime();
        $minDt=(new EDateTime($offerView->dt))->modify('+'.\app\models\Setting::get('VIEW_BONUS_DELAY').' second');
        $maxDt=$minDt->modifiedCopy('+60 second');

        if ($now<$minDt || $now>$maxDt) {
            return Yii::t('app','invalid time');
        }

        $offerView->got_view_bonus=$offerView->offer->view_bonus;

        $comment = Yii::t('app','Du hast einen Werbebonus von [sum][/sum] für das Lesen der Werbung [offer:{offerId}]"{offerTitle}"[/offer] erhalten',[
                'offerId'=>$offerView->offer->id,
                'offerTitle'=>$offerView->offer->title]
        );

        $commentOut = Yii::t('app','Hat Dich zu jugl.net eingeladen. Deshalb gibst Du [sum][/sum] Deiner Einnahmen für Du hast Werbung gelesen und einen Werbebonus erhalten“ an [user][/user] ab',[
                'offerId'=>$offerView->offer->id,
                'offerTitle'=>$offerView->offer->title]
        );

        $commentInRef = Yii::t('app','Hat die Werbung [offer:{offerId}]"{offerTitle}"[/offer] gelesen. Dafür erhältst Du anteilig [sum][/sum]',[
                'offerId'=>$offerView->offer->id,
                'offerTitle'=>$offerView->offer->title]
        );

        $commentOutRef = Yii::t('app','Hat Dich zu jugl.net eingeladen. Deshalb gibst Du [sum][/sum] Deiner Einnahmen für „{user} hat Werbung gelesen“ an [user][/user]',[
                'offerId'=>$offerView->offer->id,
                'offerTitle'=>$offerView->offer->title,
                'user'=>Yii::$app->user->identity->name]
        );

        //$offerView->offer->user->addBalanceLogItem(\app\models\BalanceLog::TYPE_OUT, -$offerView->offer->view_bonus, $offerView->offer->user, $comment);
        Yii::$app->user->identity->distributeReferralPayment($offerView->offer->view_bonus,$offerView->user,\app\models\BalanceLog::TYPE_IN,\app\models\BalanceLog::TYPE_IN_REF,\app\models\BalanceLog::TYPE_IN_REF_REF, $comment, 0, $commentOut, $commentInRef, $commentOutRef);

        if ($offerView->offer->view_bonus_total>0) {
            $oldViewBonusUsedPercent = $offerView->offer->view_bonus_used / $offerView->offer->view_bonus_total * 100;

            $offerView->offer->view_bonus_used += $offerView->offer->view_bonus*(100+\app\models\Offer::VIEW_BONUS_PERCENT_JUGL+\app\models\Offer::VIEW_BONUS_PERCENT_PARENT)/100;
            if ($offerView->offer->user->parent) {
                $offerView->offer->user->parent->distributeReferralPayment(
                    $offerView->offer->view_bonus*\app\models\Offer::VIEW_BONUS_PERCENT_PARENT/100,
                    $offerView->offer->user,
                    \app\models\BalanceLog::TYPE_IN,\app\models\BalanceLog::TYPE_IN_REF,\app\models\BalanceLog::TYPE_IN_REF_REF,
                    Yii::t('app','Du hast [user][/user] zu Jugl.net eingeladen. Es hat die Anzeige [offer:{offerId}]"{offerTitle}"[/offer] geschaltet und {user} hat diese gelesen. Dafür erhältst Du [sum][/sum]',[
                        'offerId'=>$offerView->offer->id,
                        'offerTitle'=>$offerView->offer->title,
                        'user'=>$offerView->user->name
                    ])
                );
            }

            $newViewBonusUsedPercent = $offerView->offer->view_bonus_used / $offerView->offer->view_bonus_total * 100;

            if ($oldViewBonusUsedPercent < 90 && $newViewBonusUsedPercent >= 90) {
                \app\models\UserEvent::addOfferBudgetUsed90($offerView->offer);
            }
        }

        if ($offerView->offer->view_bonus*(100+\app\models\Offer::VIEW_BONUS_PERCENT_JUGL+\app\models\Offer::VIEW_BONUS_PERCENT_PARENT)/100+$offerView->offer->view_bonus_used>$offerView->offer->view_bonus_total) {
            \app\models\UserEvent::addOfferBudgetUsed100($offerView->offer);
            //$offerView->offer->view_bonus=0;
            $offerView->offer->status = Offer::STATUS_EXPIRED;
            // unused budget will be returned in on before save
            //$offerView->offer->restoreBonus();
        }

        $offerView->save();
        $offerView->offer->save();

        Yii::$app->user->identity->packetCanBeSelected();

        $trx->commit();

        return true;
    }

    public static function accept($userId,$offerId,$code) {
        $result=static::processAccept($userId,$offerId,$code);

        if ($result!==true) {
            $offer=\app\models\Offer::findOne($offerId);
            UserEvent::addSystemMessage($userId,Yii::t('app','Ups! Da waren die anderen Jugler schneller! Das Werbebudget für die Anzeige [offer:{offerId}]"{offerTitle}"[/offer] wurde aufgebraucht, während Du Dir die Werbung angesehen hast. Bitte nicht ärgern, es gibt noch viele weitere Möglichkeiten, bei Jugl Geld zu verdienen. Hast Du Dir schon Deinen individuellen Filter gesetzt? Dann ist Deine Chance viel größer, dass Dir das nicht noch einmal passiert.',[
                'offerId'=>$offerId,
                'offerTitle'=>$offer ? $offer->title:'',
                //'result'=>$result
            ]));
        }

        Yii::info("[$userId:$offerId:$code] ".$result,'viewbonus');
        return $result;
    }

    public function attributeLabels()
    {
        return [
            'offer_id' => Yii::t('app','Offer ID'),
            'user_id' => Yii::t('app','User ID'),
            'dt' => Yii::t('app','Dt'),
            'code' => Yii::t('app','Code'),
            'got_view_bonus' => Yii::t('app','Got View Bonus'),
        ];
    }
}
