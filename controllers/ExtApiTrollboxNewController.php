<?php

namespace app\controllers;

use app\models\TrollboxCategory;
use app\models\TrollboxMessage;
use app\models\File;
use app\models\Country;
use app\models\TrollboxMessageVote;
use Yii;

class ExtApiTrollboxNewController extends \app\components\ExtApiController
{
    public function actionEnterGroupChat() {
        $id=Yii::$app->request->getBodyParam('id');

        $model=\app\models\TrollboxMessage::findOne($id);

        if ($model) {
            // dont use nested transactions for createGroupChat, this produces error
            if (!$model->group_chat_user_id) {
                $groupChatId=\app\models\ChatUser::createGroupChat($model->text);

                $trx=Yii::$app->db->beginTransaction();

                $model->lockForUpdate();

                if (!$model->group_chat_user_id) {
                    $model->group_chat_user_id=$groupChatId;
                    $model->save();
                }

                $trx->commit();
            }

            $trx=Yii::$app->db->beginTransaction();

            $alreadyJoined=Yii::$app->db->createCommand("select user_id from chat_user_contact where user_id=:user_id and second_user_id=:chat_user_id for update",[
                ":chat_user_id"=>$model->group_chat_user_id,
                ":user_id"=>Yii::$app->user->id
            ])->queryScalar();

            if (!$alreadyJoined) {
                $model->groupChatUser->joinUserToGroupChat(Yii::$app->user->identity);
            }

            $trx->commit();
            return ['result'=>true,'groupChatId'=>$model->group_chat_user_id];
        }

        return ['result'=>false];
    }

    public function actionSendMessage() {
        $data=Yii::$app->request->getBodyParam('trollboxMessage');
        $country_id=Yii::$app->request->getBodyParam('country_id');

		if (Yii::$app->user->identity->is_blocked_in_trollbox) {
            return ['trollboxMessage'=>['$allErrors'=>[Yii::t('app','Du wurdest für alle Foren von einem Moderator gesperrt')]]];
        };

        $errors=[];
        $data['$allErrors']=&$errors;

        $res=TrollboxMessage::checkNewMessageLimits();
        if ($res!==true) {
            $errors[]=$res;
            return ['trollboxMessage'=>$data];
        }

        $trx=Yii::$app->db->beginTransaction();

        $model=new TrollboxMessage();
        $model->user_id=Yii::$app->user->id;
        $model->dt=(new \app\components\EDateTime())->sqlDateTime();
        $model->setScenario('apiSave');

        if(empty($country_id)) {
            $country_id = Yii::$app->user->identity->country_id;
        }

		$model->country=Yii::$app->user->identity->is_moderator ? $country_id : Yii::$app->user->identity->country_id;
		
        $model->load($data,'');
        $model->file_id=\app\models\File::getIdFromProtected($data['file_id']);

        if ($model->validate()) {
            $packet=in_array(Yii::$app->user->identity->packet,[\app\models\User::PACKET_VIP,\app\models\User::PACKET_VIP_PLUS]) ? \app\models\User::PACKET_VIP:\app\models\User::PACKET_STANDART;
            $requiredValidation=\app\models\Setting::get('VALIDATE_TROLLBOX_MESSAGE_'.$packet);
            $model->status=$requiredValidation ? TrollboxMessage::STATUS_AWAITING_ACTIVATION:TrollboxMessage::STATUS_ACTIVE;
            $model->save();
        } else {
            $data['$errors']=$model->getFirstErrors();
            $errors=array_unique(array_merge($errors,array_values($data['$errors'])));
        }

        $res=['trollboxMessage'=>$data];
        if ($model->status==TrollboxMessage::STATUS_AWAITING_ACTIVATION) {
            $res['message']=Yii::t('app','Dein Gruppenchat wurde erstellt und wird durch Moderatoren überprüft. Sobald Dein Chat freigegeben wird, erscheint dieser hier auf der Startseite.');
        }

        if (empty($errors)) {
            
			$res['trollboxMessages']=TrollboxMessage::getHistory();
			
			$countries=[];
			$countrylist=Country::getList();
			$countArray=TrollboxMessage::getHistoryCountry();
			$shortnames=Country::getListShort();
			
			asort($countrylist);		
			
			foreach($countrylist as $key=>$value){
					array_push($countries,array('id'=>$key,'name'=>$value.' ('.(($countArray[$key]!=null)? $countArray[$key]: '0').') ','flag'=>$shortnames[$key]));	
			}

            $res['countryArrayTrollbox']=$countries;
            $trx->commit();
        }

        //\app\components\ChatServer::broadcast(['type'=>'trollboxNewMessage','status'=>true,'id'=>$model->id]);

        return $res;
    }

    public function actionVoteMessage() {
        $id=Yii::$app->request->getBodyParam('id');
        $vote=Yii::$app->request->getBodyParam('vote')>0 ? 1:-1;

        $trx=Yii::$app->db->beginTransaction();

        $model=\app\models\TrollboxMessageVote::findOne(['trollbox_message_id'=>$id,'user_id'=>Yii::$app->user->id]);

        if (!$model) {
            $model=new \app\models\TrollboxMessageVote();
            $model->user_id=Yii::$app->user->id;
            $model->trollbox_message_id=$id;
            $model->vote=$vote;
			$model->dt=(new \app\components\EDateTime)->sqlDateTime();
														  
            $model->save();

            \app\models\TrollboxMessage::updateAllCounters(['votes_up'=>$vote>0 ? 1:0,'votes_down'=>$vote<0 ? 1:0],['id'=>$id]);
        } else {
            $votes_up=$model->vote==1 ? -1:0;
            $votes_down=$model->vote==-1 ? -1:0;
            $model->vote=$vote;
            $model->save();
            if ($model->vote>0) {
                $votes_up++;
            } else {
                $votes_down++;
            }

            \app\models\TrollboxMessage::updateAllCounters(['votes_up'=>$votes_up,'votes_down'=>$votes_down],['id'=>$id]);
            //return ['result'=>Yii::t('app','Du hast bereits eine Stimme für diese Nachricht abgegeben')];
        }

        $model=\app\models\TrollboxMessage::findOne($id);

        if ($model && $model->user_id==Yii::$app->user->id) {
            return ['result'=>Yii::t('app','Du kannst keine Stimme für Deine eigene Nachricht abgeben')];
        }

        $trx->commit();

        return ['result'=>Yii::t('app','Deine Stimme wurde abgegeben'),'message'=>$model->getFrontInfo()];
    }

    public function actionGetNewMessages() {
        return [
            'messages'=>\app\models\TrollboxMessage::getHistory(null,Yii::$app->request->getBodyParam('lastKnownId'))
        ];
    }

    public function actionGetHistory() {
        return [
            'messages'=>\app\models\TrollboxMessage::getHistory()
        ];
    }

    public function actionList($pageNum=1,$country_ids=false,$filter) {
        $perPage = 10;
        $query=TrollboxMessage::find()
            ->with(['user','groupChatUser'])
			->leftJoin('user', 'trollbox_message.user_id=user.id');
		
		if ($country_ids) {
		$countries=explode(',',$country_ids);	
		
			if($countries[0]){
				$query->where(['trollbox_message.country'=>$countries[0]]);
			}
			else{
				$query->where('trollbox_message.id IS NULL');
			}
	
        }
		else{
			$query->where('trollbox_message.id IS NULL');
		}
			
		TrollboxMessage::addFilteringByVisibility($query);
		
        if (!Yii::$app->user->identity->is_moderator) {
            $query->andWhere(['trollbox_message.status'=>\app\models\TrollboxMessage::STATUS_ACTIVE]);
        }

		$filter = json_decode($filter,true);

        $dataSort = [];
        switch ($filter['sort']) {
            case 'dt':
                $dataSort = ['trollbox_message.dt'=>SORT_DESC];
                break;
            case 'votes_up':
                $dataSort = ['trollbox_message.votes_up'=>SORT_DESC];
                break;
        }

        $dataSort = array_merge(['trollbox_message.is_sticky'=>SORT_DESC], $dataSort);

        $query->orderBy($dataSort);
        $query->offset(($pageNum-1)*$perPage)
            ->limit($perPage+1);

        TrollboxMessage::addFilteringForUser($query, $filter['visibility'], $filter['category'], $filter['period']);
        $data = [];
        $trollboxMessages=$query->all();
        $hasMore=count($trollboxMessages)>$perPage;

        foreach(array_slice($trollboxMessages,0,$perPage) as $item) {
            $data[]=$item->getFrontInfo();
        }

        $country_id = $country_ids ? $country_ids : $this->currentCountry()['country_id'];

        return [
			'currentCountry'=>$this->currentCountry(),
            'items'=>$data,
            'hasMore'=>$hasMore,
            'dashboardForumText'=>\app\models\Setting::getDashboardForumText(),
            'trollboxCategoryList'=>TrollboxCategory::getListFromCountry([$country_id])
        ];

    }
	public function currentCountry(){
		$countryAry = Country::getList();
		$countryShortAry = Country::getListShort();
		$data = array();
		
		$data['country_id'] = Yii::$app->user->identity->country_id;
		$data['country_name'] = $countryAry[Yii::$app->user->identity->country_id];
		$data['country_shortname'] = $countryShortAry[Yii::$app->user->identity->country_id];
		return $data;
		
	}

	private function getVotes($id,$pageNum=1,$type=null) {
        $perPage = 30;
        $query=TrollboxMessageVote::find()
            ->with(['user'])
            ->where(['trollbox_message_id'=>$id]);

        if ($type) {
            switch ($type) {
                case 'up':
                    $query->andWhere(['vote'=>1]);
                    break;
                case 'down':
                    $query->andWhere(['vote'=>-1]);
                    break;
            }
        }

        $query->offset(($pageNum-1)*$perPage)
            ->limit($perPage+1);

        $data = [];
        $votes=$query->all();
        $hasMore=count($votes)>$perPage;

        foreach(array_slice($votes,0,$perPage) as $item) {
            $data[] = [
                'trollbox_message_id'=>$item->trollbox_message_id,
                'vote'=>$item->vote,
                'user'=>[
                    'id'=>$item->user->id,
                    'first_name'=>$item->user->first_name,
                    'last_name'=>$item->user->last_name,
                    'is_company_name'=>$item->user->is_company_name,
                    'company_name'=>$item->user->company_name,
                    'rating'=>$item->user->rating,
                    'feedback_count'=>$item->user->feedback_count,
                    'avatar'=>$item->user->getAvatarThumbUrl('avatarMobile'),
                    'video_identification_score'=>$item->user->video_identification_score
                ]
            ];
        }

        return [
            'log'=>[
                'items'=>$data,
                'hasMore'=>$hasMore
            ],
            'message_id'=>$id
        ];
    }

    public function actionVotes($id,$type=null) {
        return $this->getVotes($id,1,$type);
    }


    public function actionVotesList($id,$pageNum,$type=null) {
        return $this->getVotes($id,$pageNum,$type);
    }										   

}
