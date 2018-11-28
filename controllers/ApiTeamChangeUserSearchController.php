<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\components\EDateTime;


class ApiTeamChangeUserSearchController extends \app\components\ApiController {

    private function addTermsConditions($query,$field,$search) {
        $terms=preg_split('/[\s.,]+/',$search,-1,PREG_SPLIT_NO_EMPTY);
        foreach($terms as $term) {
            $query->andFilterWhere(['like', $field, $term]);
        }

        return count($terms);
    }

    public function getUsers($params) {
        $sex=$params['filter']['sex'];
        $name=$params['filter']['name'];
        $zipcity=$params['filter']['zipcity'];
        $ageFrom=$params['filter']['ageFrom'];
        $ageTo=$params['filter']['ageTo'];
        $single=boolval($params['filter']['single']);
        $rating=boolval($params['filter']['rating']);
        $returnResults=trim($sex.$name.$zipcity.$ageFrom.$ageTo.$single.$rating)!='';

        $pageNum=$params['pageNum']?:1;
        $pageCount=$params['pageCount']?:1;

        $perPage=50;

        $usersQuery=User::find()
            ->where('user.id!=:user_id and status=:status_active',[':user_id'=>Yii::$app->user->id,':status_active'=>User::STATUS_ACTIVE]);

        $data = [];
        if ($returnResults) {
            $usersQuery->andFilterWhere(['sex'=>$sex]);
            if (Yii::$app->user->identity->parent) {
                $usersQuery->andWhere('id!=:parent_id', [':parent_id' => Yii::$app->user->identity->parent_id]);
            }
            $this->addTermsConditions($usersQuery,"CONCAT_WS(' ',user.nick_name,user.first_name,user.last_name,user.company_name)",$name);
            $this->addTermsConditions($usersQuery,"CONCAT_WS(' ',user.zip,user.city)",$zipcity);

            if ($ageFrom!='') {
                $dateTo=(new EDateTime())->modify("-".intval($ageFrom)." year");
                $usersQuery->andWhere('birthday<:date_to1',[
                    ':date_to1'=>$dateTo->sqlDate(),
                ]);
            }

            if ($ageTo!='') {
                $dateTo=(new EDateTime())->modify("-".intval($ageTo)." year");
                $usersQuery->andWhere('birthday>:date_to2',[
                    ':date_to2'=>$dateTo->sqlDate()
                ]);
            }

            if ($single) {
                $usersQuery->andWhere('marital_status=:marital_status_single',[
                    ':marital_status_single'=>\app\models\User::MARTIAL_STATUS_SINGLE
                ]);
            }

            if($rating) {
                $usersQuery->orderBy(['rating'=>SORT_DESC]);
            }

            $searchUserCount = $usersQuery->/*groupBy(['user.id'])->*/count();

            $users=$usersQuery->offset(($pageNum-1)*$perPage)
                ->limit($perPage+1)
                ->with('chatUser','avatarFile')->all();

            $data=[
                'items'=>[],
                'hasMore'=>count($users)>$perPage,
                'searchUserCount' => $searchUserCount
            ];

            $users=array_slice($users,0,$perPage);
            foreach($users as $user) {
                $userData=$user->getShortData(['rating','feedback_count','team_rating']);
                $userData['registration_dt']=(new EDateTime($user->registration_dt))->jsDate();
                $data['items'][]=$userData;
            }

            $uti=\app\models\UserTeamRequest::find()->andWhere([
                'user_id'=>Yii::$app->user->id,
                'type'=>\app\models\UserTeamRequest::TYPE_REFERRAL_TO_PARENT,
                'second_user_id'=>\yii\helpers\ArrayHelper::getColumn($data['items'],'id')
            ])->indexBy('second_user_id')->asArray()->all();

            foreach($data['items'] as &$item) {
                if ($uti[$item['id']]) {
                    $item['invitation_sent']=true;
                }
            }
        }

        return $data;
    }

    public function actionUsers() {
        $params=json_decode($_REQUEST['urlState'],true);
        return $this->getUsers(
            $params['users']
        );
    }

    public function actionIndex() {
        $params=json_decode($_REQUEST['urlState'],true);
        $params['users']['pageCount']=$params['users']['pageNum'];
        $params['users']['pageNum']=1;
        return $this->getUsers($params['users']);
    }

}
