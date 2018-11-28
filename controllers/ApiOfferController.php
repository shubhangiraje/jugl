<?php

namespace app\controllers;

use app\components\EDateTime;
use app\models\OfferDraft;
use app\models\OfferRequest;
use app\models\OfferViewLog;							
use app\models\User;
use app\models\UserEvent;
use app\models\Country;
use Yii;
use app\models\Interest;
use app\components\Helper;
use \app\models\Offer;
use \app\models\OfferInterest;
use \app\models\OfferParamValue;
use \app\models\OfferView;
use yii\web\NotFoundHttpException;


class ApiOfferController extends \app\components\ApiController {

    public function actionPay($id) {
        $modelRequest=\app\models\OfferRequest::findOne($id);

        if (!$modelRequest ||
            $modelRequest->user_id!=Yii::$app->user->id ||
            $modelRequest->pay_status!=\app\models\OfferRequest::PAY_STATUS_INVITED) {
            return ['result'=>false];
        }

        $model=$modelRequest->offer;
        if (!$model) {
            return ['result'=>false];
        }


        switch($model->type) {
            case Offer::TYPE_AUCTION:
                $data['offerRequest']['bet_price'] = $modelRequest->bet_price;
                break;
            case Offer::TYPE_AUTOSELL:
                $data['offerRequest']['bet_price'] = $model->price;
                break;
        }

        $data['offer']=$model->toArray(['id','title','price','delivery_days','pay_allow_bank','pay_allow_paypal','pay_allow_jugl','pay_allow_pod']);
        $data['offer']['request']=$modelRequest->toArray(['id','pay_tx_id']);
        $data['offer']['price_jugls']=\app\models\Setting::get('EXCHANGE_JUGLS_PER_EURO')*$data['offerRequest']['bet_price'];
        $data['paypal_email']=$model->user->paypal_email;


        $data['address']=$model->user->toArray(['street','house_number','zip','city']);

        $data['deliveryAddresses']=[];
        foreach($modelRequest->user->userDeliveryAddresses as $da) {
            $data['deliveryAddresses'][]=$da->toArray(['street','house_number','zip','city']);
        }

        if (count($modelRequest->user->userDeliveryAddresses)>0) {
            $data['address']=$data['deliveryAddresses'][0];
        }

        $data['bankDatas']=[];
        foreach($model->user->userBankDatas as $bd) {
            $data['bankDatas'][]=$bd->toArray(['bic','iban','owner']);
        }

        return $data;
    }

    public function actionPayConfirm() {
        $id=Yii::$app->request->getBodyParams()['id'];

        $model=\app\models\OfferRequest::findOne($id);

        if (!$model || $model->offer->user_id!=Yii::$app->user->id) {
            throw new \yii\web\NotFoundHttpException;
        }

        if ($model->pay_status==\app\models\OfferRequest::PAY_STATUS_CONFIRMED) {
            return ['result'=>true];
        }

        if ($model->pay_status!=\app\models\OfferRequest::PAY_STATUS_PAYED) {
            throw new \yii\web\NotFoundHttpException;
        }

        $trx=Yii::$app->db->beginTransaction();

        $model->offer->user->lockForUpdate();

        $model->pay_status=\app\models\OfferRequest::PAY_STATUS_CONFIRMED;
        //$model->status=\app\models\Offer::STATUS_CLOSED;
        $model->save();
        $eventModels=UserEvent::addOfferPayConfirmed($model);

        if ($model->offer->buy_bonus>0) {
            $provisionMultiplier=\app\models\Setting::get('SELLBONUS_SELLER_PARENTS_PERCENT')/100;
            if ($model->offer->user->balance<$model->offer->buy_bonus*(1+$provisionMultiplier)) {
                return ['result'=>Yii::t('app','Du hast nicht genug Jugls')];
            }


            $offerRequest=$model;
            $offer=$model->offer;
            $provision=$offerRequest->offer->buy_bonus*$provisionMultiplier;

            if ($offer->buy_bonus > 0) {
                $offer->user->addBalanceLogItem(\app\models\BalanceLog::TYPE_OUT, -$offer->buy_bonus, $offerRequest->user, Yii::t('app', 'Hat Dein Produkt [offer:{offerId}]"{offerTitle}"[/offer] gekauft und erhältst einen Kaufbonus von {sum} [jugl][/jugl]',
                    [
                        'offerId' => $offer->id,
                        'offerTitle' => $offer->title,
                        'sum' => \app\components\Helper::formatPrice($offer->buy_bonus)
                    ]));
                $offerRequest->user->distributeReferralPayment($offer->buy_bonus, $offer->user, \app\models\BalanceLog::TYPE_IN, \app\models\BalanceLog::TYPE_IN_REF, \app\models\BalanceLog::TYPE_IN_REF_REF, Yii::t('app', 'Du hast das Produkt [offer:{offerId}]{offerTitle}[/offer] gekauft und erhältst einen Kaufbonus von {sum} [jugl][/jugl]',
                    [
                        'offerId' => $offer->id,
                        'offerTitle' => $offer->title,
                        'sum' => \app\components\Helper::formatPrice($offer->buy_bonus)
                    ]));

                if ($offerRequest->offer->user->parent) {
                    $comment = Yii::t('app', 'Hat Dich zu jugl.net eingeladen. Deshalb gibst Du {sum} [jugl][/jugl] ({percent}% vom Kaufbonus von [offer:{offerId}]"{offerTitle}"[/offer]) an {name} ab',
                        [
                            'offerId' => $offer->id,
                            'offerTitle' => $offer->title,
                            'name' => $offerRequest->offer->user->parent->name,
                            'percent' => \app\models\Setting::get('SELLBONUS_SELLER_PARENTS_PERCENT'),
                            'sum' => \app\components\Helper::formatPrice($provision)
                        ]);
                    $offerRequest->offer->user->addBalanceLogItem(\app\models\BalanceLog::TYPE_OUT, -$provision, $offerRequest->offer->user->parent, $comment);

                    $comment = Yii::t('app', 'Hat das Produkt [offer:{offerId}]"{offerTitle}"[/offer] verkauft. Da Du dieses Mitglied eingeladen hast, erhälst Du eine Kaufbonus-Provision von [sum][/sum]', [
                        'offerId' => $offer->id,
                        'offerTitle' => $offer->title,
                    ]);

                    $commentOut = Yii::t('app', 'Hat Dich zu jugl.net eingeladen. Deshalb gibst Du [sum][/sum] Deiner Einnahmen für „{user} hat ein Produkt verkauft und Du hast eine Kaufbonus-Provision erhalten“ an [user][/user] ab', [
                        'offerId' => $offer->id,
                        'offerTitle' => $offer->title,
                        'user' => $offerRequest->offer->user->name
                    ]);

                    $offerRequest->offer->user->parent->distributeReferralPayment($provision, $offerRequest->offer->user, \app\models\BalanceLog::TYPE_IN, \app\models\BalanceLog::TYPE_IN_REF, \app\models\BalanceLog::TYPE_IN_REF_REF, $comment, 0, $commentOut);
                }
            }

            if (!$model->offer->user->hasGoodBalance()) {
                return ['result'=>Yii::t('app','Du hast nicht genug Jugls')];
            }
        }

        $trx->commit();

        $data=['result'=>true];
        $data['events']=[];
        foreach($eventModels as $eventModel) {
            $data['events'][]=[
                    'id'=>$eventModel->id,
                    'type'=>$eventModel->type,
                    'text'=>$eventModel->text,
            ];
        }

        return $data;
    }

    public function actionOpenChat() {
        $data=Yii::$app->request->getBodyParams()['offer'];
        $offer=\app\models\Offer::findOne($data['id']);

        if (!$offer) {
            throw new \yii\web\NotFoundHttpException;
        }

        \app\components\ChatServer::sendSystemMessage(Yii::$app->user->id,$offer->user_id,
            Yii::t('app','Nachricht bezüglich des Angebots "{title}"',['title'=>$offer->title])
        );

        return ['result'=>true];
    }
/*
    public function actionGetReceiversAllCount() {
        $id=Yii::$app->request->getBodyParams()['offerRequestId'];
        $offerRequest=\app\models\OfferRequest::findOne($id);

        if (!$offerRequest) {
            throw new \yii\web\NotFoundHttpException;
        }

        \app\components\ChatServer::sendSystemMessage(Yii::$app->user->id,$offerRequest->user_id,
            Yii::t('app','Kannst Du Dein Gebot für das Angebot "{title}" bitte noch einmal wiederholen? Ich würde es gerne annehmen.',['title'=>$offerRequest->offer->title])
        );

        return ['result'=>true];
    }
*/
    public function actionPaySave() {
        $trx=Yii::$app->db->beginTransaction();

        Yii::$app->user->identity->lockForUpdate();

        $data=Yii::$app->request->getBodyParams()['pay'];

        $offerRequest=\app\models\OfferRequest::findOne($data['offer_request_id']);

        if (!$offerRequest || $offerRequest->user_id!=Yii::$app->user->id ||
            $offerRequest->pay_status!=\app\models\OfferRequest::PAY_STATUS_INVITED) {
            throw new \yii\web\NotFoundHttpException;
        }

        $offer=$offerRequest->offer;

        $errors=[];
        $data['$allErrors']=&$errors;

        $model=new \app\models\OfferPayForm;
        $model->offer=$offer;
        $model->offerRequest=$offerRequest;

        $model->load($data,'');


        if (!$model->validate()) {
            $data['$errors']=$model->getFirstErrors();
            $errors=array_unique(array_merge($errors,array_values($data['$errors'])));

            return ['pay'=>$data];
        }


        $offerRequest->pay_status=\app\models\OfferRequest::PAY_STATUS_PAYED;
        $offerRequest->pay_method=$model->resolved_pay_method;
        $offerRequest->pay_data=$model->resolved_pay_data;
        $offerRequest->delivery_address=$model->resolved_delivery_address;
        $offerRequest->save();

        $offerRequest->refresh();

        $deliveryAddressFields=[
            'user_id'=>$offerRequest->user_id,
            'street'=>$model->resolved_delivery_address_street,
            'house_number'=>$model->resolved_delivery_address_house_number,
            'zip'=>$model->resolved_delivery_address_zip,
            'city'=>$model->resolved_delivery_address_city
        ];

        $deliveryAddress=\app\models\UserDeliveryAddress::findOne($deliveryAddressFields);

        if ($deliveryAddress) {
            $deliveryAddress->delete();
        }

        $deliveryAddress=new \app\models\UserDeliveryAddress();
        $deliveryAddress->setAttributes($deliveryAddressFields);
        $deliveryAddress->save();

        $event=UserEvent::addOfferPayed($offerRequest);

        if ($offerRequest->pay_method==\app\models\OfferRequest::PAY_METHOD_JUGLS) {
            $offerRequest->pay_status=\app\models\OfferRequest::PAY_STATUS_CONFIRMED;
            $event->offerPayConfirm($offerRequest);
            //$offer->status=\app\models\Offer::STATUS_CLOSED;
            $offerRequest->save();
            UserEvent::addOfferPayConfirmed($offerRequest);

            switch($offer->type) {
                case Offer::TYPE_AUCTION:
                    $priceJugls=$offerRequest->bet_price*\app\models\Setting::get('EXCHANGE_JUGLS_PER_EURO');
                    break;
                case Offer::TYPE_AUTOSELL:
                    $priceJugls=$offer->price*\app\models\Setting::get('EXCHANGE_JUGLS_PER_EURO');
                    break;
            }

            $offerRequest->user->addBalanceLogItem(\app\models\BalanceLog::TYPE_OUT, -$priceJugls, $offerRequest->user, Yii::t('app','Du hast das Produkt [offer:{offerId}]"{offerTitle}"[/offer] gekauft und dafür {sum} [jugl][/jugl] bezahlt',
                [
                    'offerId'=>$offer->id,
                    'offerTitle'=>$offer->title,
                    'sum'=>\app\components\Helper::formatPrice($priceJugls)
                ]));
            $offer->user->addBalanceLogItem(\app\models\BalanceLog::TYPE_IN, $priceJugls, $offerRequest->user, Yii::t('app','Hat Dein Produkt [offer:{offerId}]"{offerTitle}"[/offer] gekauft. Du erhältst dafür {sum} [jugl][/jugl]',[
                'offerId'=>$offer->id,
                'offerTitle'=>$offer->title,
                'sum'=>\app\components\Helper::formatPrice($priceJugls)
            ]));

            if ($offer->buy_bonus > 0) {
                $offer->user->addBalanceLogItem(\app\models\BalanceLog::TYPE_OUT, -$offer->buy_bonus, $offerRequest->user, Yii::t('app','Hat Dein Produkt [offer:{offerId}]"{offerTitle}"[/offer] gekauft und erhältst einen Kaufbonus von {sum} [jugl][/jugl]',
                    [
                        'offerId'=>$offer->id,
                        'offerTitle'=>$offer->title,
                        'sum'=>\app\components\Helper::formatPrice($offer->buy_bonus)
                    ]));
                $offerRequest->user->distributeReferralPayment($offer->buy_bonus, $offer->user, \app\models\BalanceLog::TYPE_IN, \app\models\BalanceLog::TYPE_IN_REF, \app\models\BalanceLog::TYPE_IN_REF_REF, Yii::t('app','Du hast das Produkt [offer:{offerId}]{offerTitle}[/offer] gekauft und erhältst einen Kaufbonus von {sum} [jugl][/jugl]',
                    [
                        'offerId'=>$offer->id,
                        'offerTitle'=>$offer->title,
                        'sum'=>\app\components\Helper::formatPrice($offer->buy_bonus)
                    ]));

                $provisionMultiplier=\app\models\Setting::get('SELLBONUS_SELLER_PARENTS_PERCENT')/100;
                $provision=$offerRequest->offer->buy_bonus*$provisionMultiplier;
                if ($offerRequest->offer->user->parent) {
                    $comment = Yii::t('app','Hat Dich zu jugl.net eingeladen. Deshalb gibst Du {sum} [jugl][/jugl] ({percent}% vom Kaufbonus von [offer:{offerId}]"{offerTitle}"[/offer]) an {name} ab',
                        [
                            'offerId'=>$offer->id,
                            'offerTitle'=>$offer->title,
                            'name'=>$offerRequest->offer->user->parent->name,
                            'percent'=>\app\models\Setting::get('SELLBONUS_SELLER_PARENTS_PERCENT'),
                            'sum'=>\app\components\Helper::formatPrice($provision)
                        ]);
                    $offerRequest->offer->user->addBalanceLogItem(\app\models\BalanceLog::TYPE_OUT, -$provision, $offerRequest->offer->user->parent, $comment);

                    $comment = Yii::t('app', 'Hat das Produkt [offer:{offerId}]"{offerTitle}"[/offer] verkauft. Da Du dieses Mitglied eingeladen hast, erhälst Du eine Kaufbonus-Provision von [sum][/sum]',[
                        'offerId'=>$offer->id,
                        'offerTitle'=>$offer->title,
                    ]);

                    $commentOut = Yii::t('app', 'Hat Dich zu jugl.net eingeladen. Deshalb gibst Du [sum][/sum] Deiner Einnahmen für „{user} hat ein Produkt verkauft und Du hast eine Kaufbonus-Provision erhalten“ an [user][/user] ab',[
                        'offerId'=>$offer->id,
                        'offerTitle'=>$offer->title,
                        'user'=>$offerRequest->offer->user->name
                    ]);

                    $offerRequest->offer->user->parent->distributeReferralPayment($provision, $offerRequest->offer->user, \app\models\BalanceLog::TYPE_IN, \app\models\BalanceLog::TYPE_IN_REF, \app\models\BalanceLog::TYPE_IN_REF_REF, $comment, 0, $commentOut);
                }
            }

            if (!$offer->user->hasGoodBalance()) {
                $errors[]=Yii::t('app','Seller has no enough Jugls to pay Kaufbonus and Provision');
                return ['pay'=>$data];
            }
        }

        $trx->commit();

        return ['result'=>true];
    }


    private function parseIds($idsStr,&$level1Interest,&$level2Interest,&$level3Interests) {
        if ($idsStr=='') {
            $idsStr=\app\models\Interest::COMMON_INTEREST_ID;
        }

        if ($idsStr=='') {
            $level1Interest=new Interest();
            $level2Interest=new Interest();
            $level3Interests=[new Interest()];
            return false;
        }

        $ids=explode(',',$idsStr);

        $level3Interests=Interest::find()->andWhere(['id'=>$ids])->with(['parent','parent.parent','params','params.paramValues','interestParamValues'])->all();

        if (count($level3Interests)!=count($ids)) return false;

        if (count($level3Interests)==1 && $level3Interests[0]->level<3) {
            $interest=$level3Interests[0];
            if ($interest->level==1) {
                $level1Interest=$interest;
                $level2Interest=new Interest();
                $level3Interests=[new Interest()];
                return true;
            }
            if ($interest->level==2) {
                $level1Interest=$interest->parent;
                $level2Interest=$interest;
                $level3Interests=[new Interest()];
                return true;
            }
        }

        $level1Interest=$level3Interests[0]->parent->parent;
        $level2Interest=$level3Interests[0]->parent;

        foreach($level3Interests as $interest) {
            if ($interest->parent_id!=$level2Interest->id ||
                $interest->parent->parent_id!=$level1Interest->id) {
                return false;
            }
        }

        return true;
    }

    private function getParams($interests,&$params,&$paramsSelectedValue) {
        $params=[];
        $paramsSelectedValues=[];

        foreach($interests as $interest) {
            $params=array_merge($params,$interest->params);
            foreach($interest->interestParamValues as $ipv) {
                $paramsSelectedValues[$ipv->param_id][]=$ipv->param_value_id;
            }
        }

        $paramsSelectedValue=[];
        foreach($paramsSelectedValues as $paramId=>$values) {
            if (count(array_unique($values))==1) $paramsSelectedValue[$paramId]=$values[0];
        }
    }

    public function actionAdd($ids) {
//        if ($this->parseIds($ids,$level1Interest,$level2Interest,$level3Interests)===false) {
//            throw new \Exception("invalid ids passed: $ids");
//        };

        $this->parseIds($ids,$level1Interest,$level2Interest,$level3Interests);


        $data=[];
//        $activeTill=(new EDateTime())->modify('+1 month');
//        $data['offer']=[
//            'files'=>[],
//            'active_till_parts'=>[
//                'day'=>intval($activeTill->format('d')),
//                'month'=>intval($activeTill->format('m')),
//                'year'=>intval($activeTill->format('Y'))
//            ]
//        ];

        $data['offer']=[
            'allow_contact'=>1,
            'type'=>Offer::TYPE_AUCTION,
            'files'=>[],
            'active_till_parts'=>[
                'day'=>'',
                'month'=>'',
                'year'=>''
            ],
            'uf_sex'=>'A',
            'uf_packet'=>'ALL',
            'country_id'=>64,
            'is_active_immediately'=>1,
            'scheduled_dt_parts'=>[
                'day'=>'',
                'month'=>'',
                'year'=>'',
                'hours'=>'',
                'minutes'=>''
            ]
        ];

        $data['SELLBONUS_SELLER_PARENTS_PERCENT']=\app\models\Setting::get('SELLBONUS_SELLER_PARENTS_PERCENT');

        $data['offer']['offerInterests']=[];
        foreach($level3Interests as $interest) {
            $data['offer']['offerInterests'][]=[
                'level1Interest'=>$level1Interest->getShortData(),
                'level2Interest'=>$level2Interest->getShortData(),
                'level3Interest'=>$interest->getShortData(),
            ];
        }

        $this->getParams(array_merge(
            [
                $level1Interest,
                $level2Interest,
            ],
            $level3Interests
        ),$params,$paramsSelectedValue);

        $data['offer']['offerParamValues']=[];
        foreach($params as $param) {
            $pdata=[
                'param_value_id'=>$paramsSelectedValue[$param->id],
                'param_id'=>$param->id
            ];
            $pdata['param']=$param->toArray(['id','title','type','required']);
            $pdata['param']['values']=[];
            foreach($param->paramValues as $value) {
                $pdata['param']['values'][]=$value->toArray(['id','title']);
            }
            $data['offer']['offerParamValues'][]=$pdata;
        }

        $data['birthDayList']=Helper::assocToRecords(Helper::getDaysList());
        $data['birthMonthList']=Helper::assocToRecords(Helper::getMonthsList());
        $data['birthYearList']=Helper::assocToRecords(Helper::getYearsList(0,1));
        $data['hoursList']=Helper::assocToRecords(Helper::getHoursList());
        $data['minutesList']=Helper::assocToRecords(Helper::getMinutesList());

        
        $data['level1Interests']=[];
		$data['level1Interests'][] = '';
        foreach(Interest::find()->where('parent_id is null')->andWhere(['type'=>'OFFER'])->orderBy('sort_order')->all() as $interest) {
            $data['level1Interests'][]=$interest->toArray(['id','title']);
        }
        

//        $data['countries']=[];
//        foreach(\app\models\Country::find()->orderBy('sort_order')->all() as $country) {
//            $data['countries'][]=$country->toArray(['id','country']);
//        }
		$empty_option=array(0 => '');
		
        $data['countries'] = Helper::getCountriesList();
		$data['countries']= array_merge($empty_option, $data['countries']);


//        $data['usersWithSameInterestsCount']=intval(Yii::$app->db->createCommand("select count(distinct user_id) from user_interest where level1_interest_id=:interest_id and user_id!=:user_id",[
//            ':interest_id'=>$level1Interest->id,
//            ':user_id'=>Yii::$app->user->id
//        ])->queryScalar());

/* NEW FUNCTION usersWithSameInterestsCount
        $data['usersWithSameInterestsCount']=intval(Yii::$app->db->createCommand("select count(distinct user_interest.user_id) from user_interest left join user on user.id=user_interest.user_id where user_interest.level1_interest_id=:interest_id and user_interest.user_id!=:user_id and user.status=:status",[
            ':interest_id'=>$level1Interest->id,
            ':user_id'=>Yii::$app->user->id,
            ':status'=>\app\models\User::STATUS_ACTIVE
        ])->queryScalar());*/

        return $data;
    }

    public function actionGetReceiversCount() {
        $offer=new Offer;
        $offer->load(Yii::$app->request->getBodyParams()['offer'],'');

        $level1InterestId=Yii::$app->request->getBodyParams()['offer']['offerInterests'][0]['level1Interest']['id'];
        return ['receiversCount'=>$offer->getReceiversCount($level1InterestId,Yii::$app->user->id)];
    }
	
	public function actionGetReceiversAllCount() {

        $offer=new Offer;
        $offer->load(Yii::$app->request->getBodyParams()['offer'],'');

        $level1InterestId=Yii::$app->request->getBodyParams()['offer']['offerInterests'][0]['level1Interest']['id'];
		$level2InterestId= (Yii::$app->request->getBodyParams()['offer']['offerInterests'][0]['level2Interest']['id'] != NULL ? Yii::$app->request->getBodyParams()['offer']['offerInterests'][0]['level2Interest']['id'] : 0);
		$level3InterestId= (Yii::$app->request->getBodyParams()['offer']['offerInterests'][0]['level3Interest']['id'] != NULL ? Yii::$app->request->getBodyParams()['offer']['offerInterests'][0]['level3Interest']['id'] : 0);
        return ['receiversAllCount'=>$offer->getReceiversAllCount($level1InterestId,$level2InterestId,$level3InterestId,Yii::$app->user->id)];
    }

    public function actionSave() {
		
        $data=Yii::$app->request->getBodyParams()['offer'];
        $draftId=Yii::$app->request->getBodyParams()['draftId'];

        $errors=[];
        $data['$allErrors']=&$errors;

        $trx=Yii::$app->db->beginTransaction();
		
        if ($data['id']) {
            //$offer=$this->findModel($data['id']);
        } else {
            $offer=new Offer;
            $offer->user_id=Yii::$app->user->id;
            $offer->create_dt=(new EDateTime)->sqlDateTime();
        }

        $offer->setScenario($data['type']==Offer::TYPE_AD ? 'saveAd':($data['type']==Offer::TYPE_AUTOSELL ? 'saveAutoSell':'saveAuction'));

        $offer->load($data,'');

        if (implode('',$data['active_till_parts'])!=='') {
            $offer->active_till=implode('-',[
                $data['active_till_parts']['year'],
                ($data['active_till_parts']['month']<10 ? '0':'').$data['active_till_parts']['month'],
                ($data['active_till_parts']['day']<10 ? '0':'').$data['active_till_parts']['day']
            ]);

            if($offer->active_till < date('Y-m-d')) {
                $errors[]=Yii::t('app','Das eingegebene "Aktiv bis" - Datum darf nicht in der Vergangenheit liegen');
            }

        } else {
            //$offer->active_till=null;
            $offer->active_till=(new EDateTime())->modify('+6 months')->sqlDate();
        }

        if (!$data['is_active_immediately'] && implode('',$data['scheduled_dt_parts'])!=='') {
            $scheduled_date=implode('-',[
                $data['scheduled_dt_parts']['year'],
                ($data['scheduled_dt_parts']['month']<10 ? '0':'').$data['scheduled_dt_parts']['month'],
                ($data['scheduled_dt_parts']['day']<10 ? '0':'').$data['scheduled_dt_parts']['day']
            ]);

            $scheduled_time=implode(':',[
                ($data['scheduled_dt_parts']['hours']<10 ? '0':'').$data['scheduled_dt_parts']['hours'],
                ($data['scheduled_dt_parts']['minutes']<10 ? '0':'').$data['scheduled_dt_parts']['minutes'],
                '00'
            ]);

            $offer->scheduled_dt = $scheduled_date.' '.$scheduled_time;
        }


        if ($data['offerInterests'][0]['level2Interest']['id']) {
            $offer->offer_view_bonus = $data['offerInterests'][0]['level2Interest']['offer_view_bonus'];
			$offer->offer_view_total_bonus = $data['offerInterests'][0]['level2Interest']['offer_view_total_bonus'];
        } elseif ($data['offerInterests'][0]['level1Interest']['id']) {
            $offer->offer_view_bonus = $data['offerInterests'][0]['level1Interest']['offer_view_bonus'];
			 $offer->offer_view_total_bonus = $data['offerInterests'][0]['level1Interest']['offer_view_total_bonus'];
        }
		
		


        if ($offer->validate()) {

            if ($offer->view_bonus_total + $offer->buy_bonus <= Yii::$app->user->identity->balance) {
                $offer->save();
                Yii::$app->user->identity->addBalanceLogItem(\app\models\BalanceLog::TYPE_OUT, -$offer->view_bonus_total, Yii::$app->user->identity, Yii::t('app', 'Reservierung Werbebudget [offer:{offerId}]"{offerTitle}"[/offer]',['offerId'=>$offer->id,'offerTitle'=>$offer->title]));
            } else {
                $errors[]=Yii::t('app','NOT_ENOUGH_JUGL');
            }
        } else {
            $data['$errors']=$offer->getFirstErrors();
            $errors=array_unique(array_merge($errors,array_values($data['$errors'])));
        }

        $ids=[];
        foreach($data['offerInterests'] as $sri) {
            if ($sri['level3Interest']['id']) {
                $ids[] = $sri['level3Interest']['id'];
            }
        }

        if (empty($ids)) {
            $level2Id=$data['offerInterests'][0]['level2Interest']['id'];
            $level1Id=$data['offerInterests'][0]['level1Interest']['id'];
            if ($level2Id) {
                $ids[]=$level2Id;
            } elseif ($level1Id) {
                $ids[]=$level1Id;
            }
        }


        if (!$this->parseIds(implode(',',$ids),$level1Interest,$level2Interest,$level3Interests)) {
            array_unshift($errors, Yii::t('app','Kategorie darf nicht leer sein'));
        }

        if (!empty($errors)) {
            $trx->rollBack();
            return ['offer'=>$data];
        }

        $offer->relinkFilesWithSortOrder($data['files'],'files','offerFiles');

        // save param values
        foreach($offer->offerParamValues as $pv) {
            $pv->delete();
        }

        foreach($data['offerParamValues'] as $param) {
            $srpv=new OfferParamValue();
            $srpv->offer_id=$offer->id;
            $srpv->param_id=$param['param_id'];
            $srpv->param_value_id=$param['param_value_id'];
            $srpv->param_value=$param['param_value'];
            if ($srpv->param->required && $srpv->param_value_id.$srpv->param_value=='') {
                $errors[]=Yii::t('app','{param} darf nicht leer sein.',['param'=>$srpv->param->title]);
            }
			
            $srpv->save();
        }

        if (!empty($errors)) {
            $trx->rollBack();
            return ['offer'=>$data];
        }

        // save links to interests
        foreach($offer->offerInterests as $sri) {
            $sri->delete();
        }

        if ($this->parseIds(implode(',',$ids),$level1Interest,$level2Interest,$level3Interests)) {
            foreach($level3Interests as $level3Interest) {
                $sri = new OfferInterest();
                $sri->offer_id = $offer->id;
                $sri->level1_interest_id = $level1Interest->id;
                $sri->level2_interest_id = $level2Interest->id;
                $sri->level3_interest_id = $level3Interest->id;
								
                $sri->save();
            }
        }

        $offer->afterInsert();

        if ($offer->type==Offer::TYPE_AD) {
            Yii::$app->user->identity->packetCanBeSelected();
        }


        if(!empty($draftId)) {
            OfferDraft::deleteDraft($draftId);
        }

		$trx->commit();
        return ['result'=>true,'willBeValidated'=>$offer->status==\app\models\Offer::STATUS_AWAITING_VALIDATION];
    }

    public function actionDetails($id) {

        $model=Offer::find()->andWhere('id=:id',[':id'=>$id])->with([
            'offerInterests',
            'offerInterests.level1Interest',
            'offerInterests.level2Interest',
            'offerInterests.level3Interest',
            'offerParamValues',
            'offerParamValues.param',
            'files',
            'offerMyFavorites',
            'country'
        ])->one();

        if (!$model) {
            throw new \yii\web\NotFoundHttpException();
        }
/*
        if ($model->status!=\app\models\Offer::STATUS_ACTIVE) {
            $model->view_bonus=0;
            $model->save();
        }
*/
		$offerViewLogId = OfferViewLog::addView($model->id);
        $model->addCountOfferView();

        $data=$model->toArray([
            'id','type','allow_contact','title','description','price','delivery_days','view_bonus','buy_bonus',
            'zip','city','address','status','amount', 'show_amount', 'delivery_cost', 'active_till',
            'pay_allow_bank', 'pay_allow_paypal', 'pay_allow_jugl', 'pay_allow_pod', 'type','comment','count_offer_view'
        ]);

		$data['offer_view_log_id']=$offerViewLogId;
        $data['country']=$model->country->country;
        $data['favorite']=count($model->offerMyFavorites)>0;

        if (count($model->offerInterests)>0) {
            $data['level1Interest']=strval($model->offerInterests[0]->level1Interest);
            $data['level2Interest']=strval($model->offerInterests[0]->level2Interest);

            $level3Interests=[];
            foreach($model->offerInterests as $sri) {
                $level3Interests[]=$sri->level3Interest;
            }
            $data['level3Interests']=implode(', ',$level3Interests);
        }

        $data['images']=[];

        foreach($model->files as $image) {
            $data['images'][]=$image->getThumbUrl('offer');
        }

        if (empty($data['images'])) {
            $data['images']=[\app\components\Thumb::createUrl('/static/images/account/default_interest.png','offer')];
        }

        foreach($model->files as $image) {
            $data['bigImages'][]=$image->getThumbUrl('fancybox');
        }

        $data['paramValues']=[];
        foreach($model->offerParamValues as $pv) {
            $data['paramValues'][]=[
                'title'=>strval($pv->param),
                'value'=>$pv->param->type==\app\models\Param::TYPE_LIST ? strval($pv->paramValue):$pv->param_value
            ];
        }

        $data['user']=$model->user->getShortData(['rating', 'feedback_count', 'packet', 'impressum', 'agb', 'country_id']);
		/* NVII-MEDIA - Output Flag */
		$flagAry = Country::getListShort();
		$data['user']['flag'] = $flagAry[$data['user']['country_id']];
		/* NVII-MEDIA - Output Flag */
		
        $data['create_dt']=(new EDateTime($model->create_dt))->js();


        $squery=new \yii\db\Query;
        $squery->select([
            'relevancy'=>
            // level 3 relevancy
                '33.33*('.
                // count of matched level3 interests
                'SUM(IF(user_interest.level3_interest_id=offer_interest.level3_interest_id,1,0))+'.
                // or 1 if level1 & level2 interests matches
                'MAX(IF(offer_interest.level3_interest_id is null and (offer_interest.level2_interest_id=user_interest.level2_interest_id or offer_interest.level2_interest_id is null) and offer_interest.level1_interest_id=user_interest.level1_interest_id ,1,0))'.
                ')/'.
                // count of level3 interests in search request or 1
                'COALESCE(NULLIF(COUNT(DISTINCT offer_interest.level3_interest_id),0),1)+'.
                // level 2 relevancy
                '33.33*MAX(IF(user_interest.level2_interest_id=offer_interest.level2_interest_id or (offer_interest.level2_interest_id is null and user_interest.level1_interest_id=offer_interest.level1_interest_id),1,0))+'.
                // level 1 relevancy
                '33.33*MAX(IF(user_interest.level1_interest_id=offer_interest.level1_interest_id,1,0))'
        ])
            ->from('offer')
            ->innerJoin('offer_interest','offer_interest.offer_id=offer.id')
            ->innerJoin('user','user.id=offer.user_id')
            ->leftJoin('user_interest','user_interest.user_id=:user_id and (
                user_interest.level3_interest_id=offer_interest.level3_interest_id or
                user_interest.level2_interest_id=offer_interest.level2_interest_id or
                user_interest.level1_interest_id=offer_interest.level1_interest_id
                )',[':user_id'=>Yii::$app->user->id])
            ->where('offer.id=:id',[':id'=>$model->id]);
        $data['relevancy']=floor(0.5+$squery->scalar());


        $offerRequest = OfferRequest::findOne(['offer_id' => $model->id, 'user_id' =>Yii::$app->user->id]);
        $data['userAccepted']=boolval($offerRequest);
        $data['canAccept']=$model->canCreateRequest();

        $trx=Yii::$app->db->beginTransaction();

        if ($model->user_id!=Yii::$app->user->id && $model->status==\app\models\Offer::STATUS_ACTIVE && $model->view_bonus>0 && $model->view_bonus_used+abs($model->view_bonus)<$model->view_bonus_total+1e-6) {
            $offerView = OfferView::findOne(['user_id' => Yii::$app->user->id, 'offer_id' => $model->id]);
            if (!$offerView) {
                $offerView = new OfferView();
                $offerView->user_id = Yii::$app->user->id;
                $offerView->offer_id = $model->id;
                $offerView->dt = (new EDateTime())->sqlDateTime();
                $offerView->code = Yii::$app->security->generateRandomString(16);
                $offerView->save();
                if ($model->view_bonus > 0) {
                    $data['viewBonusCode'] = $offerView->code;
                }
            }
        }

        $trx->commit();

        $spamReport=new \app\models\UserSpamReport();
        $spamReport->user_id=Yii::$app->user->id;
        $spamReport->setOfferObject($model);
        $data['spamReported']=!$spamReport->validate(['object']);
        $data['viewBonusDelay']=intval(\app\models\Setting::get('VIEW_BONUS_DELAY'));

        return [
            'offer'=>$data
        ];
    }

    public function actionAcceptViewBonus() {
        $data=Yii::$app->request->getBodyParams();

        $result=\app\models\OfferView::accept(Yii::$app->user->id,$data['offer_id'],$data['code']);

        return ['result'=>$result];
    }

    protected function findModel($id)
    {
        if (($model = Offer::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionDelete() {
        return Yii::$app->db->transaction(function($db){
            $model=Offer::find()->andWhere(['id'=>Yii::$app->request->getBodyParams()['id'],'user_id'=>Yii::$app->user->id])->one();

            if (!$model) {
                throw new \yii\web\NotFoundHttpException();
            }

            $model->status=\app\models\Offer::STATUS_DELETED;
            $model->save();
            foreach($model->offerRequests as $offer) {
                if ($offer->status!=\app\models\OfferRequest::STATUS_ACCEPTED) {
                    $offer->status=\app\models\OfferRequest::STATUS_REJECTED;
                    $offer->save();
                }
            }

            return ['result'=>true];
        });
    }

    public function actionUndelete() {
        return Yii::$app->db->transaction(function($db){
            $model=Offer::find()->where(['id'=>Yii::$app->request->getBodyParams()['id'],'user_id'=>Yii::$app->user->id])->one();

            if (!$model) {
                throw new \yii\web\NotFoundHttpException();
            }

            $model->undelete();

            return ['result'=>true,'status'=>$model->status];
        });
    }

    public function actionUnlink() {
        return Yii::$app->db->transaction(function($db){
            $model=Offer::find()->where(['id'=>Yii::$app->request->getBodyParams()['id'],'user_id'=>Yii::$app->user->id])->one();

            if (!$model) {
                throw new \yii\web\NotFoundHttpException();
            }

            $result=$model->deleteUnlink();

            return ['result'=>$result];
        });
    }

    public function actionPause() {
        return Yii::$app->db->transaction(function($db){
            $model=Offer::find()->andWhere(['id'=>Yii::$app->request->getBodyParams()['id'],'user_id'=>Yii::$app->user->id])->one();

            if (!$model) {
                throw new \yii\web\NotFoundHttpException();
            }

            if (in_array($model->validation_status,[\app\models\Offer::VALIDATION_STATUS_NOT_REQUIRED,\app\models\Offer::VALIDATION_STATUS_ACCEPTED])) {
                switch($model->status) {
                    case \app\models\Offer::STATUS_ACTIVE:
                        $model->status=\app\models\Offer::STATUS_PAUSED;
                        break;
                    case \app\models\Offer::STATUS_PAUSED:
                        $budgetDelta=$model->view_bonus_total - $model->view_bonus_used;

                        if ($budgetDelta>$model->user->balance) {
                            return ['result'=>Yii::t('app','Du hast leider nicht genug Jugls auf Deinem Konto.')];
                        }
                        if ($budgetDelta>0) {
                            $model->user->addBalanceLogItem(\app\models\BalanceLog::TYPE_OUT, -$budgetDelta, $model->user, Yii::t('app', 'Reservierung Werbebudget [offer:{offerId}]"{offerTitle}"[/offer]',['offerId'=>$model->id,'offerTitle'=>$model->title]));
                        }

                        $model->status=\app\models\Offer::STATUS_ACTIVE;
                        break;
                }
            }
            $model->save();

            return ['result'=>true,'offer'=>$model->toArray(['id','status'])];
        });
    }

    public function actionUpdate($id) {
        $model=new \app\models\OfferUpdateForm();
        $offer=\app\models\Offer::findOne(['id'=>$id,'user_id'=>Yii::$app->user->id]);

        if (!$offer) {
            throw new \yii\web\NotFoundHttpException;
        }

        $model->without_bonus=$offer->view_bonus===null;
        $model->view_bonus=floatval($offer->view_bonus);
        $model->view_bonus_total=floatval($offer->view_bonus_total);
        $model->offer_id=$offer->id;
        $model->comment=$offer->comment;
        $dt=new \app\components\EDateTime($offer->active_till);
        $model->active_till_day=intval($dt->format('d'));
        $model->active_till_month=intval($dt->format('m'));
        $model->active_till_year=intval($dt->format('Y'));

        return [
            'offerUpdateForm'=>$model->attributes,
            'birthDayList'=>Helper::assocToRecords(Helper::getDaysList()),
            'birthMonthList'=>Helper::assocToRecords(Helper::getMonthsList()),
            'birthYearList'=>Helper::assocToRecords(Helper::getYearsList(0,1))
        ];
    }

    public function actionUpdateSave() {
        $data=Yii::$app->request->getBodyParams()['offerUpdateForm'];

        $errors=[];
        $data['$allErrors']=&$errors;

        $model=new \app\models\OfferUpdateForm();
        $offer=\app\models\Offer::findOne($data['offer_id']);
        if (!$offer || $offer->user_id!=Yii::$app->user->id) {
            throw new \yii\web\NotFoundHttpException();
        }

        $model->setScenario($offer->view_bonus===null ? $model::SCENARIO_WITHOUT_BONUS:$model::SCENARIO_WITH_BONUS);
        $model->load($data,'');

        $model->active_till=implode('-',[
            $model->active_till_year,
            ($model->active_till_month<10 ? '0':'').$model->active_till_month,
            ($model->active_till_day<10 ? '0':'').$model->active_till_day
        ]);

        if ($model->active_till < date('Y-m-d')) {
            $errors[]=Yii::t('app','Das eingegebene "Aktiv bis" - Datum darf nicht in der Vergangenheit liegen');
        }

        if (empty($errors) && $model->validate()) {
            $trx=Yii::$app->db->beginTransaction();
            $offer=\app\models\Offer::findOne($model->offer_id);

            if ($offer->canUpdateViewBonusTotal()) {
                if (!$offer->without_view_bonus) {
                    $budgetDelta=$model->view_bonus_total-$offer->view_bonus_total;
                    if ($offer->status==\app\models\Offer::STATUS_EXPIRED) {
                        $budgetDelta+=$offer->view_bonus_total-$offer->view_bonus_used;
                    }
                    $offer->view_bonus_total=$model->view_bonus_total;
                    $offer->view_bonus=$model->view_bonus;
                    if ($budgetDelta>0) {
                        $offer->user->addBalanceLogItem(\app\models\BalanceLog::TYPE_OUT, -$budgetDelta, $offer->user, Yii::t('app', 'Reservierung Werbebudget [offer:{offerId}]"{offerTitle}"[/offer]',['offerId'=>$offer->id,'offerTitle'=>$offer->title]));
                    }
                    if ($budgetDelta<0) {
                        $offer->user->addBalanceLogItem(\app\models\BalanceLog::TYPE_IN, -$budgetDelta, $offer->user, Yii::t('app', 'Rückbuchung Werbebudget [offer:{offerId}]"{offerTitle}"[/offer]',['offerId'=>$offer->id,'offerTitle'=>$offer->title]));
                    }
                }
                //$offer->status=\app\models\Offer::STATUS_ACTIVE;

                $offer->comment=$model->comment;
                $offer->active_till=$model->active_till;
                $offer->save();
            }

            $trx->commit();
            $offerData=$offer->toArray(['id','view_bonus','view_bonus_total','status','active_till']);
            return [
                'result'=>true,
                'offer'=>$offerData
            ];
        } else {
            $data['$errors']=$model->getFirstErrors();
            $errors=array_unique(array_merge($errors,array_values($data['$errors'])));
        }

        return ['offerUpdateForm'=>$data];
    }


    public function actionPreview() {

        $data=Yii::$app->request->getBodyParams()['offer'];

        $errors=[];
        $data['$allErrors']=&$errors;

        $offer=new Offer;
        $offer->user_id=Yii::$app->user->id;
        $offer->create_dt=(new EDateTime)->sqlDateTime();
        $offer->setScenario($data['type']==Offer::TYPE_AD ? 'saveAd':($data['type']==Offer::TYPE_AUTOSELL ? 'saveAutoSell':'saveAuction'));
        $offer->load($data,'');

        if (implode('',$data['active_till_parts'])!=='') {
            $offer->active_till=implode('-',[
                $data['active_till_parts']['year'],
                ($data['active_till_parts']['month']<10 ? '0':'').$data['active_till_parts']['month'],
                ($data['active_till_parts']['day']<10 ? '0':'').$data['active_till_parts']['day']
            ]);

            if($offer->active_till < date('Y-m-d')) {
                $errors[]=Yii::t('app','Das eingegebene "Aktiv bis" - Datum darf nicht in der Vergangenheit liegen');
            }
        } else {
            $offer->active_till=(new EDateTime())->modify('+6 months')->sqlDate();
        }

        if (!$data['is_active_immediately'] && implode('',$data['scheduled_dt_parts'])!=='') {

            $scheduled_date=implode('-',[
                $data['scheduled_dt_parts']['year'],
                ($data['scheduled_dt_parts']['month']<10 ? '0':'').$data['scheduled_dt_parts']['month'],
                ($data['scheduled_dt_parts']['day']<10 ? '0':'').$data['scheduled_dt_parts']['day']
            ]);

            $scheduled_time=implode(':',[
                ($data['scheduled_dt_parts']['hours']<10 ? '0':'').$data['scheduled_dt_parts']['hours'],
                ($data['scheduled_dt_parts']['minutes']<10 ? '0':'').$data['scheduled_dt_parts']['minutes'],
                '00'
            ]);

            $offer->scheduled_dt = $scheduled_date.' '.$scheduled_time;
        }


        if ($data['offerInterests'][0]['level2Interest']['id']) {
            $offer->offer_view_bonus = $data['offerInterests'][0]['level2Interest']['offer_view_bonus'];
            $offer->offer_view_total_bonus = $data['offerInterests'][0]['level2Interest']['offer_view_total_bonus'];
        } elseif ($data['offerInterests'][0]['level1Interest']['id']) {
            $offer->offer_view_bonus = $data['offerInterests'][0]['level1Interest']['offer_view_bonus'];
            $offer->offer_view_total_bonus = $data['offerInterests'][0]['level1Interest']['offer_view_total_bonus'];
        }

        $dataOffer = [];
        if ($offer->validate()) {

            if ($offer->view_bonus_total + $offer->buy_bonus <= Yii::$app->user->identity->balance) {
				if($data['type']!=Offer::TYPE_AD){
					$dataOffer=$offer->toArray([
						'id','type','allow_contact','title','description','price','delivery_days','view_bonus','buy_bonus',
						'zip','city','address','status','amount', 'show_amount', 'delivery_cost', 'active_till',
						'pay_allow_bank', 'pay_allow_paypal', 'pay_allow_jugl', 'pay_allow_pod', 'type'
					]);
				}
				if($data['type']==Offer::TYPE_AD){
					$dataOffer=$offer->toArray([
						'id','type','allow_contact','title','description','price','delivery_days','view_bonus',
						'zip','city','address','status','amount', 'show_amount', 'delivery_cost', 'active_till',
						'pay_allow_bank', 'pay_allow_paypal', 'pay_allow_jugl', 'pay_allow_pod', 'type'
					]);
				}

                $dataOffer['country']=$offer->country->country;
                $dataOffer['user']=$offer->user->getShortData(['rating', 'feedback_count', 'packet', 'impressum', 'agb']);
                $dataOffer['create_dt']=(new EDateTime($offer->create_dt))->js();

                if (count($data['offerInterests'])>0) {
                    $level1Interest=Interest::findOne(['id'=>$data['offerInterests'][0]['level1Interest']['id']]);
                    $dataOffer['level1Interest'] = $level1Interest->title;

                    $level2Interest=Interest::findOne(['id'=>$data['offerInterests'][0]['level2Interest']['id']]);
                    $dataOffer['level2Interest'] = $level2Interest->title;

                    $level3Interests=[];
                    foreach($data['offerInterests'] as $sri) {
                        $level3Interest=Interest::findOne(['id'=>$sri['level3Interest']['id']]);
                        $level3Interests[] = $level3Interest->title;
                    }
                    $dataOffer['level3Interests']=implode(', ',$level3Interests);
                }

                if(count($data['offerParamValues'])>0) {
					/*@ Jan - What is this? */
                    $dataOffer['testtest']=$data['offerParamValues'];


                    $dataOffer['paramValues'] = [];
                    foreach ($data['offerParamValues'] as $pv) {
                        if ($pv['param_value_id']) {
                            foreach ($pv['param']['values'] as $value) {
                                if ($value['id'] == $pv['param_value_id']) {
                                    $dataOffer['paramValues'][] = [
                                        'title' => $pv['param']['title'],
                                        'value' => $value['title']
                                    ];
                                }
                            }
                        }

                        if ($pv['param_value']) {
                            $dataOffer['paramValues'][] = [
                                'title' => $pv['param']['title'],
                                'value' => $pv['param_value']
                            ];
                        }

                    }
                }

                $dataOffer['images']=[];

                foreach($data['files'] as $image) {
                    $dataOffer['images'][] = \app\models\File::findOne(\app\models\File::getIdFromProtected($image['id']))->getThumbUrl('offer');
                }

                if (empty($dataOffer['images'])) {
                    $dataOffer['images']=[\app\components\Thumb::createUrl('/static/images/account/default_interest.png','offer')];
                }

                foreach($data['files'] as $image) {
                    $dataOffer['bigImages'][]=\app\models\File::findOne(\app\models\File::getIdFromProtected($image['id']))->getThumbUrl('fancybox');
                }


            } else {
                $errors[]=Yii::t('app','NOT_ENOUGH_JUGL');
            }
        } else {
            $data['$errors']=$offer->getFirstErrors();
            $errors=array_unique(array_merge($errors,array_values($data['$errors'])));
        }

        foreach($data['offerParamValues'] as $param) {
            $srpv=new OfferParamValue();
            $srpv->offer_id=$offer->id;
            $srpv->param_id=$param['param_id'];
            $srpv->param_value_id=$param['param_value_id'];
            $srpv->param_value=$param['param_value'];
            if ($srpv->param->required && $srpv->param_value_id.$srpv->param_value=='') {
                $errors[]=Yii::t('app','{param} darf nicht leer sein.',['param'=>$srpv->param->title]);
            }
        }

        if (!empty($errors)) {
            return ['offer'=>$data];
        }

        $dataOffer['preview']=true;
        return [
            'result'=>true,
            'offer'=>$dataOffer
        ];


    }

    public function actionGetCountOfferView($id) {
        $offer = Offer::findOne($id);
        return [
            'count_offer_view'=>$offer->count_offer_view
        ];
    }

}
