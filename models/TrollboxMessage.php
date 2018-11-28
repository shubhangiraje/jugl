<?php

namespace app\models;

use app\components\EDateTime;
use app\components\Moderator;
use app\models\Country;
use Yii;

class TrollboxMessage extends \app\models\base\TrollboxMessage
{
    const STATUS_ACTIVE='ACTIVE';
    const STATUS_REJECTED='REJECTED';
    const STATUS_AWAITING_ACTIVATION='AWAITING_ACTIVATION';
    const STATUS_DELETED='DELETED';

    const FILTER_ALL='ALL';
    const FILTER_CONTACTS='CONTACTS';
    const FILTER_FOLLOWING='FOLLOWING';
    const FILTER_MAIN='MAIN';

    const TYPE_FORUM='FORUM';
    const TYPE_VIDEO_IDENTIFICATION='VIDEO_IDENTIFICATION';

    public function attributeLabels() {
        return [
            'user_id' => Yii::t('app','Benutzer'),
            'dt' => Yii::t('app','Datum'),
            'text' => Yii::t('app','Text'),
            'file_id' => Yii::t('app','Abbildung'),
            'votes_up' => Yii::t('app','Erhaltene Likes'),
            'votes_down' => Yii::t('app','Vergebene Likes'),
            'trollbox_category_id'=> Yii::t('app','Kategorien'),
            'visible_for_all'=>Yii::t('app','Alle'),
            'visible_for_followers'=>Yii::t('app','Abos'),
            'visible_for_contacts'=>Yii::t('app','Kontakte'),
            'device_uuid'=>Yii::t('app', 'Device ID')
        ];
    }

    public function rules() {
        return array_merge(parent::rules(),[
            [['text'], 'required', 'on'=>'apiSave'],
            [['text'], 'string', 'min' => 100, 'on'=>'apiSave'],
            [['visible_for_all', 'visible_for_followers', 'visible_for_contacts'], 'validateVisibility', 'on'=>'apiSave']
        ]);
    }

    public static function getStatusList() {
        static $items;

        if (!isset($items)) {
            $items=[
                static::STATUS_ACTIVE=>Yii::t('app','Aktiv'),
                static::STATUS_REJECTED=>Yii::t('app','Abgelehnt'),
                static::STATUS_AWAITING_ACTIVATION=>Yii::t('app','Neu'),
                static::STATUS_DELETED=>Yii::t('app','Gelöscht'),
            ];
        }

        return $items;
    }

    public function getStatusLabel() {
        return static::getStatusList()[$this->status];
    }

    public static function getVisibilityList() {
        static $items;

        if (!isset($items)) {
            $items=[
                static::FILTER_ALL=>Yii::t('app','Alle'),
                static::FILTER_CONTACTS=>Yii::t('app','Kontakte'),
                static::FILTER_FOLLOWING=>Yii::t('app','Abos')
            ];
        }

        return $items;
    }

	public function getCountryLabel() {
        return Country::getList($this->country);
    }

    public function getGroupChatUser()
    {
        return $this->hasOne('\app\models\ChatUser', ['user_id' => 'group_chat_user_id']);
    }

    public static function addFilteringByVisibility($query) {
        if (!Yii::$app->user->identity->is_moderator) {
            // add filtering by visibility
            $query->leftJoin('user_friend ufrv','ufrv.user_id=trollbox_message.user_id and ufrv.friend_user_id=:my_user_id');
            $query->leftJoin('user_follower uflv','uflv.user_id=trollbox_message.user_id and uflv.follower_user_id=:my_user_id');
            $query->andWhere("
		    trollbox_message.user_id=:my_user_id or 
		    trollbox_message.visible_for_all=1 or
		    trollbox_message.visible_for_contacts=1 and ufrv.user_id is not null or
		    trollbox_message.visible_for_followers=1 and uflv.user_id is not null
		",[':my_user_id'=>Yii::$app->user->id]);
        }
    }

    public static function addFilteringForUser($query,$filterVisibility, $filterCategory = null, $filterPeriod = null) {
        // add filtering from user
        switch ($filterVisibility) {
            case static::FILTER_MAIN:
                $query->andWhere('trollbox_message.user_id=:my_user_id', [':my_user_id'=>Yii::$app->user->id]);
                break;
            case static::FILTER_CONTACTS:
                $query->innerJoin('user_friend ufr','ufr.user_id=:my_user_id and trollbox_message.user_id=ufr.friend_user_id', [':my_user_id'=>Yii::$app->user->id]);
                break;
            case static::FILTER_FOLLOWING:
                $query->innerJoin('user_follower ufl','ufl.follower_user_id=:my_user_id and trollbox_message.user_id=ufl.user_id', [':my_user_id'=>Yii::$app->user->id]);
                break;
        }

        if($filterCategory!='') {
            $query->andWhere('trollbox_message.trollbox_category_id=:trollbox_category_id', [':trollbox_category_id'=>$filterCategory]);
        }

        if($filterPeriod!='') {
            switch ($filterPeriod) {
                case 'TODAY':
                    $query->andWhere('trollbox_message.dt>=:dt', [':dt'=>(new EDateTime())->sqlDate()]);
                    break;
                case 'WEEK':
                    $query->andWhere('trollbox_message.dt>=:dt', [':dt'=>(new EDateTime())->modify('-7 day')->sqlDate()]);
                    break;
                case 'MONTH':
                    $query->andWhere('trollbox_message.dt>=:dt', [':dt'=>(new EDateTime())->modify('-1 month')->sqlDate()]);
                    break;
            }
        }

    }


    public static function getDashboardList($country_ids=[]) {
        $query = static::find()
            ->with(array_merge(['user','groupChatUser'],Yii::$app->user->identity->is_moderator ? ['statusChangedUser']:[]))
            ->where(['type'=>TrollboxMessage::TYPE_FORUM])
            ->limit(3)
            ->orderBy('trollbox_message.is_sticky desc, trollbox_message.dt desc');

        if (!Yii::$app->user->identity->is_moderator) {
            $query->andWhere(['trollbox_message.status'=>static::STATUS_ACTIVE]);
        }

        if(!empty($country_ids)) {
            $query->andWhere(['trollbox_message.country'=>$country_ids]);
        }

        $models=$query->all();

        $data=[];
        foreach($models as $model) {
            $data[]=$model->getFrontInfo();
        }

        return $data;
    }




    public static function getHistory($lessThanId=null,$greaterThanId=null,$limit=3,$country_ids=null,$filter=null) {

		$c_ids=array();
		
		if($country_ids){
			foreach($country_ids as $cids){
					$c_ids[]=$cids;
			}
		}
		else{
			if($country_ids){
				foreach($country_ids as $cids){	
					$c_ids[]=$cids;
				}
			}
			else{
				$country_ids=false;
			}
		}

		
        $query=static::find()
            ->andFilterCompare('trollbox_message.id',$lessThanId,'<')
            ->andFilterCompare('trollbox_message.id',$greaterThanId,'>');

		if($country_ids!=null && is_array($country_ids)){
		$query->andWhere(['in','trollbox_message.country',$c_ids]);
		}

        static::addFilteringByVisibility($query);
        static::addFilteringForUser($query, $filter);

        $query->with(array_merge(['user','groupChatUser'],Yii::$app->user->identity->is_moderator ? ['statusChangedUser']:[]))
            ->limit($limit)->orderBy('trollbox_message.is_sticky desc, trollbox_message.dt desc');

        if (!Yii::$app->user->identity->is_moderator) {
            $query->andWhere(['trollbox_message.status'=>static::STATUS_ACTIVE]);
        }

        $models=$query->all();

        $data=[];
        foreach($models as $model) {
            $data[]=$model->getFrontInfo();
        }
		
        return $data;
    }

    public function getFrontInfo() {
        $data=$this->toArray(['id','votes_up','votes_down','text','trollbox_category_id','status','type']);
        $data['messagesCount']=$this->groupChatUser->group_chat_messages_count;
        $data['dt']=(new \app\components\EDateTime($this->dt))->js();

        if (Yii::$app->user->identity->is_moderator || $this->user_id == Yii::$app->user->id) {
            if ($this->statusChangedUser) {
                $data['status_changed_dt']=(new EDateTime($this->status_changed_dt))->js();
                $data['statusChangedUser']=$this->statusChangedUser->name;
                $data['status_changed_user_id']=$this->status_changed_user_id;
            }
        }
		
		/* NVII-MEDIA - Output Flag */
		$flagAry = Country::getListShort();
		$flag = $flagAry[$this->user->country_id];
		/* NVII-MEDIA - Output Flag */

        $data['user']=[
            'id'=>$this->user->id,
            'first_name'=>$this->user->first_name,
            'last_name'=>$this->user->last_name,
            'rating' => $this->user->rating,
            'is_company_name' => $this->user->is_company_name,
            'company_name' => $this->user->company_name,
            'feedback_count' => $this->user->feedback_count,
            'packet' => $this->user->packet,
            'avatar'=>$this->user->getAvatarThumbUrl('avatarMobile'),
			'country_id' => $this->user->country_id,
			'flag' => $flag
        ];

        if($this->trollbox_category_id) {
            $data['trollbox_category'] = $this->trollboxCategory->title;
        }

        if (Yii::$app->user->identity->is_moderator) {
            $data['user']['is_blocked_in_trollbox']=$this->user->is_blocked_in_trollbox;
            $data['is_sticky']=$this->is_sticky;
        }

        if ($this->file) {
            $data['file']=$this->file->toArray(['id','ext','size']);
            $data['file']['url']=Yii::$app->request->hostInfo.$this->file->link;
            $data['file']['image']=$this->file->getThumbUrl('trollboxSmall');
            $data['file']['image_medium']=$this->file->getThumbUrl('trollboxMedium');
            $data['file']['image_big']=$this->file->getThumbUrl('trollboxBig');
        }

        $data['count_votes']=$this->votes_up + $this->votes_down;

        $data['voted']=false;
        if (TrollboxMessageVote::findOne(['user_id'=>Yii::$app->user->id, 'trollbox_message_id'=>$this->id])) {
            $data['voted']=true;
        }

        $chatMessages=\app\models\ChatMessage::find()
            ->where([
                'user_id'=>$this->group_chat_user_id,
                'second_user_id'=>$this->group_chat_user_id,
                'deleted'=>0
            ])
            ->with([
                'chatFiles',
                'senderUser',
                'senderUser.avatarFile',
            ])
            ->limit(2)->orderBy('id desc')->all();

        $data['messages']=[];

        foreach($chatMessages as $msgKey => $msgVal) {
            /* NVII-MEDIA - Output Flag */
            $flagAry = Country::getListShort();
            $flag = $flagAry[$msgVal->senderUser->country_id];
            /* NVII-MEDIA - Output Flag */

		    $messageData=[
                'text'=>$msgVal->text,
                'content_type'=>$msgVal->content_type,
                'dt'=>(new \app\components\EDateTime($msgVal->dt))->js(),
                'user'=>$msgVal->senderUser->getShortData(['rating', 'feedback_count', 'packet', 'country_id'])
            ];

		    if ($msgVal->extra) {
		        $messageData['extra']=json_decode($msgVal->extra,true);
            }

            if (count($msgVal->chatFiles)>0) {
		        $file=$msgVal->chatFiles[0];
		        $messageData['file']=$file->toArray(['ext','name','size']);
		        $messageData['file']['url']=Yii::$app->request->getHostInfo().$file->link;
                $messageData['file']['thumb_url']=$file->getThumbUrl('trollboxMedium');
            }

            $data['messages'][]=$messageData;

			/* NVII-MEDIA - Output Flag */
			$data['messages'][$msgKey]['user']['flag'] = $flag;
			/* NVII-MEDIA - Output Flag */
        }
        return $data;
    }
	
	public static function getHistoryCountry($lessThanId=null,$greaterThanId=null,$limit=null) {

        $query=static::find()->select('country')->all();

        $data=[];
        foreach($query as $model) {
            if($model['country']!="" && $model['country']!=NULL){
					$data[$model['country']]=$data[$model['country']]+1;
				}
				else{
					$data['no_countries']=$data['no_countries']+1;
			}
        }

        return $data;
    }

    public static function getCountryList() {
        $key=__CLASS__.__FUNCTION__.json_encode([boolval(Yii::$app->user->identity->is_moderator),Yii::$app->language]);
        $data=Yii::$app->cache->get($key);

        if ($data===false) {
            $query = static::find()->select(['COUNT(id) as count', 'country as country_id']);
            if (!Yii::$app->user->identity->is_moderator) {
                $query->andWhere(['status' => static::STATUS_ACTIVE]);
            }
            $countryList = $query->groupBy(['country'])->asArray()->all();

            $countryCountData = [];
            foreach ($countryList as $item) {
                $countryCountData[$item['country_id']] = intval($item['count']);
            }

            $data = [];
            foreach (Country::getList() as $country_id => $country_name) {
                $idata['id'] = $country_id;

                if ($countryCountData[$country_id]) {
                    $idata['name'] = $country_name . ' (' . $countryCountData[$country_id] . ')';
                } else {
                    $idata['name'] = $country_name . ' (0)';
                }

                $idata['flag'] = Country::getListShort()[$country_id];
                $data[] = $idata;
            }

            Yii::$app->cache->set($key,$data,300);
        }

        return $data;
    }



    public function getFrontInfoCountry() {
        return $this->user->country_id;
    }

    public function scenarios() {
        $scenarios=parent::scenarios();
        $scenarios['apiSave']=['text','visible_for_all','visible_for_followers','visible_for_contacts','trollbox_category_id'];
        return $scenarios;
    }

    public function validateVisibility() {
        if ($this->visible_for_all!=1 && $this->visible_for_contacts!=1 && $this->visible_for_followers!=1) {
            $this->addError('visible_for_all', Yii::t('app','Bitte mindestens eine Option wählen'));
        }
    }

    public function sendFollowerEvent() {
        if ($this->status==static::STATUS_ACTIVE) {
            \app\models\UserFollowerEvent::addNewTrollboxMessage($this);
        }
    }

    public static function deleteMy($id) {
        $model=static::findOne($id);

        if (!$model || $model->status==static::STATUS_DELETED) {
            return Yii::t('app',"Forum doesn't exist");
        }

        $trx=Yii::$app->db->beginTransaction();

        $model->status=static::STATUS_DELETED;
        $model->save();

        Yii::$app->db->createCommand('update chat_message set deleted=1 where second_user_id=:group_chat_id and user_id!=:group_chat_id',[
            ':group_chat_id'=>$model->group_chat_user_id,
        ])->execute();

        Yii::$app->db->createCommand('update chat_user set group_chat_messages_count=(select count(*) from chat_message where user_id=:id and second_user_id=:id and deleted=0) where user_id=:id',[
            ':id'=>$model->group_chat_user_id
        ])->execute();

        Yii::$app->db->createCommand('delete from chat_user_contact where second_user_id=:id',[
            ':id'=>$model->group_chat_user_id
        ])->execute();

        Yii::$app->db->createCommand('delete from chat_conversation where second_user_id=:id',[
            ':id'=>$model->group_chat_user_id
        ])->execute();
        \app\components\Moderator::fixGroupChatConversations($model->group_chat_user_id);

        \app\components\Moderator::updateInitInfo($model->group_chat_user_id);

        $trx->commit();

        return true;
    }

    public static function checkNewMessageLimits() {
        $packet=Yii::$app->user->identity->packet=='' ? \app\models\User::PACKET_STANDART:Yii::$app->user->identity->packet;

        if(Yii::$app->user->identity->trollbox_messages_limit_per_day>0) {
            $dailyLimit=Yii::$app->user->identity->trollbox_messages_limit_per_day;
        } else {
            $dailyLimit=\app\models\Setting::get('TROLLBOX_'.$packet.'_MESSAGES_PER_DAY');
        }

        $todayMessages=\app\models\TrollboxMessage::find()
            ->andWhere(['user_id'=>Yii::$app->user->id])
            ->andWhere('dt>=:dt',[':dt'=>(new EDateTime())->modify('-1 day')->sqlDateTime()])->count();

        return $dailyLimit<=$todayMessages ? Yii::t('app','Du kannst nur {limit,plural,=1{# Beitrag} other{# Beiträge}} pro 24 Std. posten',['limit'=>$dailyLimit]):true;
    }

    public function setVideoIdentStatus($status) {
        $scoreMultiplier=1;

        $wasRejected=$this->user->video_identification_status==\app\models\User::VIDEO_IDENTIFICATION_STATUS_REJECTED;
        $wasAccepted=in_array($this->user->video_identification_status,[\app\models\User::VIDEO_IDENTIFICATION_STATUS_ACCEPTED_AUTO,\app\models\User::VIDEO_IDENTIFICATION_STATUS_ACCEPTED_MANUAL]);
        $willBeRejected=$status==\app\models\User::VIDEO_IDENTIFICATION_STATUS_REJECTED;
        $willBeAccepted=in_array($status,[\app\models\User::VIDEO_IDENTIFICATION_STATUS_ACCEPTED_AUTO,\app\models\User::VIDEO_IDENTIFICATION_STATUS_ACCEPTED_MANUAL]);

        if ($willBeRejected==$wasRejected && $willBeAccepted==$wasAccepted) {
            $scoreMultiplier=0;
        }

        if (($wasRejected && $willBeAccepted) || ($wasAccepted && $willBeRejected)) {
            $scoreMultiplier=2;
        }

        if (($wasRejected || $wasAccepted) && !$willBeAccepted && !$willBeRejected) {
            $scoreMultiplier=-1;
        }

        $this->user->video_identification_status = $status;
        $this->user->save();

        Yii::$app->db->createCommand("
              update trollbox_message_vote
              join user on (user.id=trollbox_message_vote.user_id) 
              set user.video_identification_score=user.video_identification_score+IF(:correct_vote=trollbox_message_vote.vote,:score_match,:score_unmatch)
              where trollbox_message_vote.trollbox_message_id=:trollbox_message_id
              ",[
            ':trollbox_message_id'=>$this->id,
            ':score_match'=>\app\models\Setting::get('VIDEOIDENT_SCORE_PLUS_IF_VOTE_MATCH')*$scoreMultiplier,
            ':score_unmatch'=>-\app\models\Setting::get('VIDEOIDENT_SCORE_MINUS_IF_VOTE_UNMATCH')*$scoreMultiplier,
            ':correct_vote'=>$willBeAccepted ? 1:-1
        ])->execute();

        $umatchedUserIds=Yii::$app->db->createCommand("
              select user.id
              from trollbox_message_vote
              join user on (user.id=trollbox_message_vote.user_id) 
              where trollbox_message_vote.trollbox_message_id=:trollbox_message_id and trollbox_message_vote.vote!=:correct_vote
              ",[
            ':trollbox_message_id'=>$this->id,
            ':correct_vote'=>$willBeAccepted ? 1:-1
        ])->queryColumn();

        if ($willBeAccepted) {
            UserEvent::addSystemMessage($this->user_id, Yii::t('app', 'Wir gratulieren! Dein Videoident wurde soeben von unserem Community erfolgreich verifiziert.'));
        }

        if ($willBeRejected) {
            UserEvent::addSystemMessage($this->user_id, Yii::t('app', 'Es tut uns leid, Dein Videoident wurde von unserem Community abgelehnt.'));
        }

        UserEvent::addVideoIdentUnmatchMessage($this,$umatchedUserIds);
    }

    public static function getCountVideoIdentification() {
        $count = TrollboxMessage::find()->joinWith(['user'])->where([
            'trollbox_message.type'=>TrollboxMessage::TYPE_VIDEO_IDENTIFICATION,
            'trollbox_message.status'=>TrollboxMessage::STATUS_ACTIVE,
            'user.video_identification_status'=>User::VIDEO_IDENTIFICATION_STATUS_AWAITING
        ])->count();
        return $count;
    }



}

\yii\base\Event::on(TrollboxMessage::className(), \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE, function ($event) {
    if ($event->sender->group_chat_user_id && $event->sender->oldAttributes['text']!==$event->sender->text) {
        $event->sender->groupChatUser->updateGroupChatTitle($event->sender->text);
        $event->sender->groupChatUser->save();
    }

    if ($event->sender->oldAttributes['status']!=$event->sender->status) {
        $event->sender->sendFollowerEvent();
    }
});

\yii\base\Event::on(TrollboxMessage::className(), \yii\db\ActiveRecord::EVENT_AFTER_INSERT, function ($event) {
    $event->sender->device_uuid = Yii::$app->request->getHeaders()->get('X-Ext-Api-Auth-Device-Uuid');
    $event->sender->save();
    $event->sender->sendFollowerEvent();
});
