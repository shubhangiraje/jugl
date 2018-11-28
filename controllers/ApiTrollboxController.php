<?php

namespace app\controllers;

use app\models\TrollboxCategory;
use app\models\TrollboxMessage;
use app\models\Country;
use app\models\File;
use app\models\TrollboxMessageVote;
use app\models\User;
use Yii;
use yii\web\NotFoundHttpException;

class ApiTrollboxController extends \app\components\ApiController
{
    public function actionEnterGroupChat() {
        $id=Yii::$app->request->getBodyParam('id');

        $model=\app\models\TrollboxMessage::findOne($id);

        if ($model) {
            // dont use nested transactions for createGroupChat, this produces error
            if (!$model->group_chat_user_id) {
                $groupChatId=\app\models\ChatUser::createGroupChat(
                    $model->type==\app\models\TrollboxMessage::TYPE_FORUM ?
                        $model->text:
                        Yii::t('app','Videoidentifikation des Users {user}',['user'=>$model->user->name])
                );

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
        $country_ids = Yii::$app->request->getBodyParam('country_ids');

        $countryIds = $country_ids ? explode(',', Yii::$app->request->getBodyParam('country_ids')) : [];

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

        $userCountryId = !empty(Yii::$app->user->identity->country_id) ? Yii::$app->user->identity->country_id : 64;
        if(Yii::$app->user->identity->is_moderator && !empty($countryIds)) {
            $model->country = count($countryIds)>1 ? $userCountryId : $countryIds[0];
        } else {
            $model->country = $userCountryId;
        }

        $model->dt=(new \app\components\EDateTime())->sqlDateTime();
        $model->setScenario('apiSave');

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

			$countries=[];
			$countrylist=Country::getList();
			$countArray=TrollboxMessage::getHistoryCountry();
			$shortnames=Country::getListShort();
			
			asort($countrylist);		
			
			foreach($countrylist as $key=>$value){
					array_push($countries,array('id'=>$key,'name'=>$value.' ('.(($countArray[$key]!=null)? $countArray[$key]: '0').') ','flag'=>$shortnames[$key]));	
			}
			$res['trollboxMessages']=TrollboxMessage::getHistory();
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


    private function getTrollboxList($country_ids,$pageNum=1,$filter='') {
        $perPage = 10;
        $query=TrollboxMessage::find()
            ->with(['user','groupChatUser'])
            ->leftJoin('user', 'trollbox_message.user_id=user.id');

        TrollboxMessage::addFilteringByVisibility($query);

        if (!Yii::$app->user->identity->is_moderator) {
            $query->andWhere(['trollbox_message.status'=>\app\models\TrollboxMessage::STATUS_ACTIVE]);
        }

        if($country_ids!='') {
            $query->andWhere(['trollbox_message.country'=>explode(',',$country_ids)]);
        }

        if($filter!=='') {
            $filter = json_decode($filter, true);
        } else {
            $filter = ['sort' => 'dt'];
        }

        $dataSort = [];
        switch ($filter['sort']) {
            case 'dt':
                $dataSort = ['trollbox_message.dt'=>SORT_DESC];
                break;
            case 'votes_up':
                $dataSort = ['trollbox_message.votes_up'=>SORT_DESC];
                break;
        }

        if ($filter['type']==TrollboxMessage::TYPE_VIDEO_IDENTIFICATION) {
            $query->andWhere('trollbox_message.type=:type_video and trollbox_message.status=:status_active and user.video_identification_status=:video_ident_status_awaiting', [
                ':type_video'=>TrollboxMessage::TYPE_VIDEO_IDENTIFICATION,
                ':status_active'=>TrollboxMessage::STATUS_ACTIVE,
                ':video_ident_status_awaiting'=>User::VIDEO_IDENTIFICATION_STATUS_AWAITING,
            ]);
        } else {
            $query->andWhere('trollbox_message.type=:type_forum or (trollbox_message.type=:type_video and trollbox_message.status=:status_active and user.video_identification_status=:video_ident_status_awaiting)', [
                ':type_forum'=>TrollboxMessage::TYPE_FORUM,
                ':type_video'=>TrollboxMessage::TYPE_VIDEO_IDENTIFICATION,
                ':status_active'=>TrollboxMessage::STATUS_ACTIVE,
                ':video_ident_status_awaiting'=>User::VIDEO_IDENTIFICATION_STATUS_AWAITING,
            ]);
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

        $data=[
            'log'=>[
                'items'=>$data,
                'hasMore'=>$hasMore
            ]
        ];

        if (Yii::$app->user->id == User::SUPERADMIN_ID) {
            $count_video_identification = TrollboxMessage::find()->joinWith(['user'])->where([
                'trollbox_message.type'=>TrollboxMessage::TYPE_VIDEO_IDENTIFICATION,
                'trollbox_message.status'=>TrollboxMessage::STATUS_ACTIVE,
                'user.video_identification_status'=>User::VIDEO_IDENTIFICATION_STATUS_AWAITING
            ])->count();
            $data['count_video_identification']=$count_video_identification;
        }

        return $data;
    }


    public function actionIndex() {
        $trollboxFilter = User::getTrollboxFilter();
        $countryList = TrollboxMessage::getCountryList();
        $forumCountry = [];
        $countryIds = [];
        $filter = '';
        $country_ids = '';


        if ($trollboxFilter) {
            $filter = $trollboxFilter['filter'];
            if(!empty($trollboxFilter['country_ids'])) {
                $country_ids = $trollboxFilter['country_ids'];
                $countryIdsArr = explode(',', $country_ids);
                foreach ($countryList as $itemCountry) {
                    if(in_array($itemCountry['id'], $countryIdsArr)) {
                        $forumCountry[] = $itemCountry;
                        $countryIds[] = $itemCountry['id'];
                    }
                }
            }
        }

        return array_merge($this->getTrollboxList($country_ids, 1, $filter), [
            'trollboxCategoryList'=>TrollboxCategory::getListFromCountry($countryIds),
            'countryList'=>$countryList,
            'dashboardForumText'=>\app\models\Setting::getDashboardForumText(),
            'forumCountry'=>$forumCountry,
            'filter'=>$filter,
            'countryIds'=>$country_ids,
            'testCountryList'=>Country::getList()
        ]);
    }

    public function actionList($country_ids='',$pageNum,$filter) {
        if($pageNum==1) {

            $trollboxFilter = [
                'country_ids'=>$country_ids,
                'filter'=>$filter
            ];

            User::saveTrollboxFilter(json_encode($trollboxFilter));

            $countryList = TrollboxMessage::getCountryList();
            $countryIds = [];

            if($country_ids) {
                foreach ($countryList as $itemCountry) {
                    if(in_array($itemCountry['id'],  explode(',', $country_ids))) {
                        $countryIds[] = $itemCountry['id'];
                    }
                }
            }

            return array_merge($this->getTrollboxList($country_ids,$pageNum,$filter), [
                'trollboxCategoryList'=>TrollboxCategory::getListFromCountry($countryIds),
            ]);

        } else {
            return $this->getTrollboxList($country_ids,$pageNum,$filter);
        }
    }

    private function getVotes($id,$pageNum=1,$type) {
        $result = [];
        $result['message_id']=$id;
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
            $result['type']=$type;
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
                    'avatar'=>$item->user->getAvatarThumbUrl('avatarMobile'),
                    'video_identification_score'=>$item->user->video_identification_score
                ]
            ];
        }

        $result['log']=[
            'items'=>$data,
            'hasMore'=>$hasMore
        ];

        return $result;
    }

    public function actionVotes($id,$type=null) {
        return $this->getVotes($id,1,$type);
    }


    public function actionVotesList($id,$pageNum,$type=null) {
        return $this->getVotes($id,$pageNum,$type);
    }

    public function actionGetMessage($id) {
        $trollboxMessage = TrollboxMessage::find()
            ->with(['file'])
            ->where(['id'=>$id])->one();

        if (!$trollboxMessage) {
            throw new \yii\web\NotFoundHttpException();
        }

        $data=$trollboxMessage->toArray(['id','text','visible_for_followers','visible_for_contacts','visible_for_all','trollbox_category_id']);

        if($trollboxMessage->file) {
            $data['file_id'] = File::getProtectedId($trollboxMessage->file->id);
            $data['image'] = $trollboxMessage->file->getThumbUrl('trollboxSmall');
        }

        return [
            'trollboxMessage'=>$data,
            'trollboxCategoryList'=>TrollboxCategory::getList()
        ];
    }

    public function actionSave() {
        $data=Yii::$app->request->getBodyParam('trollboxMessage');
        $errors=[];
        $data['$allErrors']=&$errors;

        $trx=Yii::$app->db->beginTransaction();

        $model=TrollboxMessage::findOne($data['id']);
        $model->setScenario('apiSave');
        $model->load($data,'');
        $model->file_id=\app\models\File::getIdFromProtected($data['file_id']);

        if ($model->validate()) {
            $model->save();
        } else {
            $data['$errors']=$model->getFirstErrors();
            $errors=array_unique(array_merge($errors,array_values($data['$errors'])));
        }

        if (!empty($errors)) {
            $trx->rollBack();
            return ['trollboxMessage'=>$data];
        }

        $trx->commit();

        $trollboxMessage = $model->toArray(['id','text','trollbox_category_id']);

        if($model->trollbox_category_id) {
            $trollboxMessage['trollbox_category']=$model->trollboxCategory->title;
        }

        if ($model->file_id) {
            $trollboxMessage['file']=$model->file->toArray(['id','ext','size']);
            $trollboxMessage['file']['url']=Yii::$app->request->hostInfo.$model->file->link;
            $trollboxMessage['file']['image']=$model->file->getThumbUrl('trollboxSmall');
            $trollboxMessage['file']['image_medium']=$model->file->getThumbUrl('trollboxMedium');
            $trollboxMessage['file']['image_big']=$model->file->getThumbUrl('trollboxBig');
        }

        return [
            'trollboxMessage'=>$trollboxMessage
        ];
    }

    public function actionDelete() {
        $id = Yii::$app->request->getBodyParams()['id'];

        return [
            'result'=>\app\models\TrollboxMessage::deleteMy($id)
        ];
    }


	/*nviimedia*/

	
	public function currentCountry(){
		$countryAry = Country::getList();
		$countryShortAry = Country::getListShort();
		$data = array();
		
		$data['country_id'] = Yii::$app->user->identity->country_id;
		$data['country_name'] = $countryAry[Yii::$app->user->identity->country_id];
		$data['country_shortname'] = $countryShortAry[Yii::$app->user->identity->country_id];
		return $data;
		
	}
	/*nviimedia*/


    public function actionGetVideoIdentification($user_id) {
        $trollboxMessage = TrollboxMessage::findOne(['user_id'=>$user_id, 'type'=>TrollboxMessage::TYPE_VIDEO_IDENTIFICATION, 'status'=>TrollboxMessage::STATUS_ACTIVE]);
        if (!$trollboxMessage) {
            throw new NotFoundHttpException();
        }
        return [
            'trollboxMessage'=>$trollboxMessage->getFrontInfo()
        ];
    }


}
