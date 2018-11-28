<?php

namespace app\models;
use app\components\EDateTime;
use app\models\Country;
use Yii;

class InfoComment extends \app\models\base\InfoComment {

    const STATUS_ACTIVE='ACTIVE';
    const STATUS_REJECTED='REJECTED';
    const STATUS_DELETED='DELETED';

    public function attributeLabels() {
        return [
            'id' => Yii::t('app','ID'),
            'user_id' => Yii::t('app','Benutzer'),
            'info_id' => Yii::t('app','Info ID'),
            'dt' => Yii::t('app','Datum'),
            'comment' => Yii::t('app','Kommentar'),
            'file_id' => Yii::t('app','Abbildung'),
            'votes_up' => Yii::t('app','Votes Up'),
            'votes_down' => Yii::t('app','Votes Down'),
        ];
    }

    public static function getStatusList() {
        static $items;

        if (!isset($items)) {
            $items=[
                static::STATUS_ACTIVE=>Yii::t('app','Aktiv'),
                static::STATUS_REJECTED=>Yii::t('app','Abgelehnt'),
                static::STATUS_DELETED=>Yii::t('app','Gelöscht'),
            ];
        }

        return $items;
    }

    public function getStatusLabel() {
        return static::getStatusList()[$this->status];
    }


    public function getFrontInfo() {
        $data=$this->toArray(['id','votes_up','votes_down','comment','status']);
        $data['dt']=(new \app\components\EDateTime($this->dt))->js();

        $data['user']=[
            'id'=>$this->user->id,
			'flag'=>$this->currentCountry($this->user->country_id),
			'country_id'=>$this->user->country_id,
            'first_name'=>$this->user->first_name,
            'last_name'=>$this->user->last_name,
            'rating' => $this->user->rating,
            'is_company_name'=>$this->user->is_company_name,
            'company_name'=>$this->user->company_name,
            'feedback_count' => $this->user->feedback_count,
            'packet' => $this->user->packet,
            'avatar'=>$this->user->getAvatarThumbUrl('avatarMobile')
        ];

        if ($this->file) {
            $data['file']=$this->file->toArray(['id','ext','size']);
            $data['file']['image']=$this->file->getThumbUrl('infoCommentSmall');
            $data['file']['image_medium']=$this->file->getThumbUrl('infoCommentMedium');
            $data['file']['image_big']=$this->file->getThumbUrl('infoCommentBig');
            $data['file']['url']=Yii::$app->request->hostInfo.$this->file->link;
        }

        if (Yii::$app->user->identity->is_moderator) {
            if ($this->statusChangedUser) {
                $data['status_changed_dt']=(new EDateTime($this->status_changed_dt))->js();
                $data['statusChangedUser']=$this->statusChangedUser->name;
                $data['status_changed_user_id']=$this->status_changed_user_id;
            }
        }

        return $data;
    }

	public function currentCountry($usercountryid){
		$countryShortAry = Country::getListShort();
		return $countryShortAry[$usercountryid];
		
	}

	public function sendFollowerEvent() {
	    \app\models\UserFollowerEvent::addNewInfoComment($this);
    }

    public static function getCountryList($info_id, $is_update = false) {
        $key=__CLASS__.__FUNCTION__.$info_id.Yii::$app->language;
        $data=Yii::$app->cache->get($key);
        if ($data===false || $is_update) {
            $query = InfoComment::find()->select('COUNT(id) as count, lang as country_id')
                ->where(['info_id'=>$info_id]);

            if(!Yii::$app->user->identity->is_moderator) {
                $query->andWhere(['status'=>static::STATUS_ACTIVE]);
            }

            $countryData = $query->groupBy(['lang'])->asArray()->all();

            $countryCountData = [];
            foreach ($countryData as $item) {
                if($item['country_id']){
                    $countryCountData[$item['country_id']]=intval($item['count']);
                } else {
                    $countryCountData['no_country']=intval($item['count']);
                }
            }

            $data = [];
            foreach (Country::getList() as $country_id=>$country_name) {
                $idata['id']=$country_id;

                if($countryCountData[$country_id]) {
                    $idata['name']=$country_name.' ('.$countryCountData[$country_id].')';
                } else {
                    $idata['name']=$country_name.' (0)';
                }

                $idata['flag']=Country::getListShort()[$country_id];
                $data[]=$idata;
            }

            Yii::$app->cache->set($key,$data,300);
        }

        return $data;

    }




}

\yii\base\Event::on(InfoComment::className(), \yii\db\ActiveRecord::EVENT_AFTER_INSERT, function ($event) {
    $event->sender->sendFollowerEvent();
});

