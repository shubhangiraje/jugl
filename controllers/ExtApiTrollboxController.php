<?php

namespace app\controllers;

use app\models\TrollboxCategory;
use app\models\TrollboxMessage;
use app\models\File;
use app\models\Country;
use app\models\TrollboxMessageVote;
use Yii;
use yii\web\NotFoundHttpException;

class ExtApiTrollboxController extends \app\components\ExtApiController
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

        if (Yii::$app->user->identity->is_blocked_in_trollbox) {
            return ['trollboxMessage'=>['$allErrors'=>[Yii::t('app','Du wurdest für alle Foren von einem Moderator gesperrt')]]];
        };

        $errors=[];
        $data['$allErrors']=&$errors;

        $trx=Yii::$app->db->beginTransaction();

        $model=new TrollboxMessage();
        $model->user_id=Yii::$app->user->id;
        $model->dt=(new \app\components\EDateTime())->sqlDateTime();
        $model->setScenario('apiSave');
		$model->country=Yii::$app->user->identity->country_id;
		
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
			->leftJoin('user', 'trollbox_message.user_id=user.id')
            ->where(array('in','user.country_id',explode(',',$country_ids)))->orWhere('user.country_id IS NULL');

        TrollboxMessage::addFilteringByVisibility($query);

        if (!Yii::$app->user->identity->is_moderator) {
            $query->andWhere(['trollbox_message.status'=>\app\models\TrollboxMessage::STATUS_ACTIVE]);
        }
        $query->orderBy(['trollbox_message.is_sticky'=>SORT_DESC,'trollbox_message.dt'=>SORT_DESC]);
        $query->offset(($pageNum-1)*$perPage)
        ->limit($perPage+1);

        $filter = json_decode($filter,true);
        TrollboxMessage::addFilteringForUser($query, $filter['visibility'], $filter['category']);

        $data = [];
        $trollboxMessages=$query->all();
        $hasMore=count($trollboxMessages)>$perPage;

        foreach(array_slice($trollboxMessages,0,$perPage) as $item) {
            $data[]=$item->getFrontInfo();
        }

        return [
            'items'=>$data,
            'hasMore'=>$hasMore,
            'dashboardForumText'=>\app\models\Setting::getDashboardForumText(),
            'trollboxCategoryList'=>TrollboxCategory::getList()
        ];

    }

    private function getVotes($id,$pageNum=1) {
        $perPage = 30;
        $query=TrollboxMessageVote::find()
            ->with(['user'])
            ->where(['trollbox_message_id'=>$id]);

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
                    'avatar'=>$item->user->getAvatarThumbUrl('avatarMobile')
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

    public function actionVotes($id) {
        return $this->getVotes($id);
    }


    public function actionVotesList($id,$pageNum) {
        return $this->getVotes($id,$pageNum);
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
