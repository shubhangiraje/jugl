<?php

namespace app\controllers;

use Yii;
use app\models\UserFriend;
use yii\web\ForbiddenHttpException;
use app\models\UserFriendRequest;

class ApiFriendsController extends \app\components\ApiController {

    public function getFriends($params) {
        $sort=$params['filter']['sort'];
        $statusFilter=$params['filter']['statusFilter'];
        $nameFilter=$params['filter']['nameFilter'];
        $pageNum=$params['pageNum']?:1;
        $pageCount=$params['pageCount']?:1;

        $perPage=24;

        $friendsQuery=Yii::$app->user->identity->hasMany('\app\models\UserFriend', ['user_id' => 'id'])
            ->select(['user_friend.*'])
            ->innerJoin('user','user_friend.friend_user_id=user.id')
            ->offset(($pageNum-1)*$perPage)
            ->limit($perPage*$pageCount+1);

        switch ($sort) {
            case 'alpha':
                $friendsQuery->addSelect(["TRIM(CONCAT_WS(' ',nick_name,first_name,last_name)) as name"]);
                $friendsQuery->orderBy('name asc');
                break;
            default:
                $friendsQuery->orderBy('user_friend.dt desc');
        }

        switch ($statusFilter) {
            case 'online':
                $friendsQuery->innerJoin('chat_user','(chat_user.online=1 or chat_user.online_mobile=1) and user_friend.friend_user_id=chat_user.user_id');
                break;
            default:
        }

        $searchTerms=preg_split('/[\s.,]+/',$nameFilter,-1,PREG_SPLIT_NO_EMPTY);
        foreach($searchTerms as $k=>$term) {
            $friendsQuery->andWhere("CONCAT_WS(' ',user.nick_name,user.first_name,user.last_name) like (:term_$k)",[":term_$k"=>"%$term%"]);
        }

        $friends=$friendsQuery->with('friendUser','friendUser.chatUser','friendUser.avatarFile')->all();

        $data=[
            'users'=>[],
            'hasMore'=>count($friends)>$perPage*$pageCount
        ];

        $friends=array_slice($friends,0,$perPage*$pageCount);
        foreach($friends as $friend) {
            $data['users'][]=$friend->friendUser->getShortData(['online']);
        }

        return ['friends'=>$data];
    }

    public function actionDeleteFriend() {
        $params=Yii::$app->request->getBodyParams();

        $urlState=json_decode($params['urlState'],true);
        $urlState['friends']['pageCount']=$urlState['friends']['pageNum'];
        $urlState['friends']['pageNum']=1;

        Yii::$app->user->identity->deleteFriend($params['friendId']);

        return $this->getFriends($urlState['friends']);
    }

    public function actionRequestAccept() {
        $id=Yii::$app->request->getBodyParams()['id'];
        $request=UserFriendRequest::findOne($id);
        $friendUserId=$request->user_id;

        if (!$request || $request->friend_user_id!=Yii::$app->user->id) {
            throw new ForbiddenHttpException();
        }

        $result=$request->accept();

        if ($result===true) {
            return ['redirect'=>['route'=>'userProfile','params'=>['id'=>$friendUserId]]];
        }

        return ['result'=>$result];
    }

    public function actionRequestDecline() {
        $id=Yii::$app->request->getBodyParams()['id'];
        $request=UserFriendRequest::findOne($id);
        if (!$request || $request->user_id!=Yii::$app->user->id) {
            throw new ForbiddenHttpException();
        }

        return ['result'=>$request->decline()];
    }

    public function actionFriends() {
        $params=json_decode($_REQUEST['urlState'],true);
        return $this->getFriends($params['friends']);
    }

    public function actionIndex() {
        $params=json_decode($_REQUEST['urlState'],true);
        $params['friends']['pageCount']=$params['friends']['pageNum'];
        $params['friends']['pageNum']=1;
        return $this->getFriends($params['friends']);
    }
}
