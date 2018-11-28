<?php

namespace app\controllers;

use app\models\Invitation;
use app\models\News;
use app\models\Setting;
use app\models\User;
use app\models\UserFriend;
use app\models\UserInterest;
use Yii;
use app\models\Offer;
use app\models\SearchRequest;
use app\components\EDateTime;
use yii\db\Query;

class ExtApiDashboardController extends \app\components\ExtApiController  {

    private function getOffers($pageNum=1) {
        $perPage=10;
        $query = Offer::find()->distinct()
            ->innerJoin('offer_interest','offer_interest.offer_id=offer.id')
            ->orderBy(['id'=>SORT_DESC])->offset(($pageNum-1)*$perPage)->limit($perPage);
        $query->where('offer.active_till>=CAST(NOW() AS DATE) and view_bonus>0 and offer.status=:active_status',[':active_status'=>Offer::STATUS_ACTIVE]);

        $query->with(['user','user.chatUser','user.avatarFile','files','offerInterests','offerInterests.level1Interest','offerInterests.level2Interest','offerInterests.level3Interest']);

        Yii::$app->user->identity->addOfferSearchFilterConditions($query);
        $offers = $query->all();

        $data = [];
        foreach ($offers as $item) {
            $idata = $item->toArray(['id','type','title','price','delivery_days','view_bonus','buy_bonus','zip','city','address','relevancy', 'amount', 'show_amount', 'active_till']);
            $idata['description']=\yii\helpers\StringHelper::truncate($item->description,100);
            $idata['create_dt']=(new EDateTime($item->create_dt))->js();
            $idata['user']=$item->user->getShortData(['rating', 'feedback_count', 'packet']);

            if (count($item->offerInterests)>0) {
                $idata['level1Interest']=strval($item->offerInterests[0]->level1Interest);
                $idata['level2Interest']=strval($item->offerInterests[0]->level2Interest);

                $level3Interests=[];
                foreach($item->offerInterests as $sri) {
                    $level3Interests[]=$sri->level3Interest;
                }
                $idata['level3Interests']=implode(', ',$level3Interests);
            }

            if (count($item->files)>0) {
                $idata['image'] = $item->files[0]->getThumbUrl('searchRequestMobile');
            } else {
                $idata['image'] = \app\components\Thumb::createUrl('/static/images/account/default_interest.png','searchRequestMobile',true);
            }

            $data[] = $idata;
        }

        return $data;
    }
	private function getVideos(){
		$videos = \app\models\Video::find()->orderBy(['video_id'=>SORT_DESC])->limit(100)->all();
		$data = [];
		
     
		
		
		
        foreach ($videos as $item) {
            $idata = $item->toArray(['video_id', 'tenant_id', 'description', 'name', 'image', 'clip_id', 'language', 'cat_name', 'bonus']);
			$idata['description'] = \yii\helpers\StringHelper::truncate($item->description,40);
			/*if(strlen($data[$itemKey]['name']) > 40){
				$idata[$itemKey]['name'] = substr($data[$itemKey]['name'], 0 , 40).'...';
			}*/
			$data[] = $idata;
        }
        return $data;
	}
	
    private function getSearchRequest($pageNum=1) {
        $perPage=10;
        $level1InterestsIds=Yii::$app->user->identity->getLevel1InterestIds(\app\models\UserInterest::TYPE_SEARCH_REQUEST);

        $searchRequest=SearchRequest::findBySql("
            SELECT search_request.*
            FROM `search_request`
            WHERE exists(
                  select * from search_request_interest
                  where search_request_interest.search_request_id=search_request.id and search_request_interest.level1_interest_id in (".implode(',',$level1InterestsIds).")
              ) and search_request.active_till>=CAST(NOW() AS DATE) and search_request.status=:active_status order by id desc limit :perPage offset :offset
        ",[
            ':active_status'=>SearchRequest::STATUS_ACTIVE,
            ':offset'=>($pageNum-1)*$perPage,
            ':perPage'=>$perPage
        ])->with(['user','user.chatUser','user.avatarFile','files','searchRequestInterests','searchRequestInterests.level1Interest','searchRequestInterests.level2Interest','searchRequestInterests.level3Interest'])->all();
/*
        $query = SearchRequest::find()->distinct()
            ->innerJoin('search_request_interest','search_request_interest.search_request_id=search_request.id')
            ->leftJoin('user_interest','user_interest.user_id=:user_id and (
                user_interest.level3_interest_id=search_request_interest.level3_interest_id or
                user_interest.level2_interest_id=search_request_interest.level2_interest_id or
                user_interest.level1_interest_id=search_request_interest.level1_interest_id
                )',[':user_id'=>Yii::$app->user->id])
            ->orderBy(['id'=>SORT_DESC])
            ->limit(10);

        $query->where('search_request.active_till>=CAST(NOW() AS DATE) and search_request.status=:active_status',[);
        $searchRequest = $query->all();
*/
        $data = [];
        $ids = [];
        foreach ($searchRequest as $item) {
            $ids[] = $item->id;
            $idata = $item->toArray(['id','title','price_from','price_to','bonus','zip','city','address','relevancy']);
            $idata['description']=\yii\helpers\StringHelper::truncate($item->description,100);
            $idata['create_dt']=(new EDateTime($item->create_dt))->js();
            $idata['user']=$item->user->getShortData(['rating', 'feedback_count', 'packet']);

            if (count($item->searchRequestInterests)>0) {
                $idata['level1Interest']=strval($item->searchRequestInterests[0]->level1Interest);
                $idata['level2Interest']=strval($item->searchRequestInterests[0]->level2Interest);

                $level3Interests=[];
                foreach($item->searchRequestInterests as $sri) {
                    $level3Interests[]=$sri->level3Interest;
                }
                $idata['level3Interests']=implode(', ',$level3Interests);
            }

            if (count($item->files)>0) {
                $idata['image']=$item->files[0]->getThumbUrl('searchRequestMobile');
            } else {
                $idata['image']=\app\components\Thumb::createUrl('/static/images/account/default_interest.png','searchRequestMobile',true);
            }

            $data[] = $idata;
        }


        if(!empty($ids)) {
            $ids = implode(',',$ids);

            $countsSearchRequest = Yii::$app->db->createCommand("select sro.search_request_id,count(*) as count_total,sum(IF(status='ACCEPTED',1,0)) as count_accepted,sum(IF(status='REJECTED',1,0)) as count_rejected
            from search_request_offer sro where sro.search_request_id in (".$ids.")
                group by sro.search_request_id")->queryAll();

            $dataCounts = [];
            foreach ($countsSearchRequest as $item) {
                $dataCounts[$item['search_request_id']]=[
                    'count_total'=>$item['count_total'],
                    'count_accepted'=>$item['count_accepted'],
                    'count_rejected'=>$item['count_rejected']
                ];
            }

            foreach ($data as $key=>$value) {
                $data[$key]['count_total']=$dataCounts[$value['id']]['count_total'];
                $data[$key]['count_accepted']=$dataCounts[$value['id']]['count_accepted'];
                $data[$key]['count_rejected']=$dataCounts[$value['id']]['count_rejected'];
            }
        }

        return $data;
    }

    private function getNetworkMembers() {
        $query=Yii::$app->user->identity->hasMany('\app\models\UserReferral', ['user_id' => 'id'])
            ->select(['user_referral.*'])
            ->innerJoin('user','user_referral.referral_user_id=user.id')
            ->orderBy(['id'=>SORT_DESC]);
        $networkMembers=$query->with('referralUser','referralUser.chatUser','referralUser.avatarFile')->limit(10)->all();

        $data = [];
        foreach ($networkMembers as $item) {
            $idata = $item->referralUser->getShortData(['online']);
            $idata['registration_dt'] = (new EDateTime($item->referralUser->registration_dt))->js();
            $data[] = $idata;
        }
        return $data;
    }
	private function getNews() {

        $news = News::find()->with(['imageFile'])->orderBy(['dt'=>SORT_DESC])->limit(10)->all();
        $data = [];
        foreach ($news as $item) {
            $idata = $item->toArray();
            $idata['dt']=(new EDateTime($item->dt))->js();
			
			if(!empty($item['title_'.Yii::$app->language])) {
				$idata['title'] = $item['title_'.Yii::$app->language];
			} else {
				$idata['title'] = $item['title_de'];
			}
			
			if(!empty($item['text_'.Yii::$app->language])) {
				$idata['text'] = yii\helpers\StringHelper::truncate($item['text_'.Yii::$app->language],200);
			} else {
				$idata['text'] = yii\helpers\StringHelper::truncate($item['text_de'],200);
			}
			
            if (!empty($item->image_file_id)) {
                $idata['images']=[
                    'image'=>$item->imageFile->getThumbUrl('news'),
                    'fancybox'=>$item->imageFile->getThumbUrl('fancybox'),
                ];
            } else {
                $idata['images']=[
                    'image'=>\app\components\Thumb::createUrl('/static/images/account/default_interest.png','news',true)
                ];
            }

            $data[] = $idata;
        }

        return $data;
    }

    private function getFaqs() {
        $faqs = \app\models\Faq::find()->orderBy(['id'=>SORT_DESC])->limit(10)->all();
        $data = [];
        foreach ($faqs as $item) {
            $idata = $item->toArray();
			
			if(!empty($item['question_'.Yii::$app->language])) {
				$idata['question'] = $item['question_'.Yii::$app->language];
			} else {
				$idata['question'] = $item['question_de'];
			}
			
			if(!empty($item['response_'.Yii::$app->language])) {
				$idata['response'] = yii\helpers\StringHelper::truncate($item['response_'.Yii::$app->language],200);
			} else {
				$idata['response'] = yii\helpers\StringHelper::truncate($item['response_de'],200);
			}
			
            $data[]=$idata;
        }
        return $data;
    }

    private function getInviteMe() {
        $time=(new \app\components\EDateTime())->modify("-".\app\models\Setting::get('TEAM_CHANGE_PERIOD_DAYS')." minute");
        $invites=\app\models\User::find()->where([
            'status'=>\app\models\User::STATUS_ACTIVE,'show_in_become_member'=>1
        ])->andWhere('(registration_dt>:time or parent_id is null)',[':time'=>$time->sql()])->
        andWhere(['!=', 'id', Yii::$app->user->identity->getId()])->orderBy(['id'=>SORT_DESC])->with(['invitationWinner','invitation'])->limit(10)->all();
        $result=[];

        foreach($invites as $invite) {
            $item=$invite->toArray(['id', 'first_name','last_name','email','phone','is_company_name','company_name']);
            if ($invite->invitationWinner/* && $invite->invitation*/) {
                $item['winner']=[
                    'user_id'=>$invite->invitationWinner->user_id,
                    'userName'=>$invite->invitationWinner->secondUser->name,
                    'dt'=>(new EDateTime($invite->invitationWinner->dt))->js(),
                    'ms'=>$invite->invitationWinner->ms,
                ];
            }
            $result[]=$item;
        }

        $ids=\yii\helpers\ArrayHelper::getColumn($invites,'id');
        if (!empty($ids)) {
            $counts = Yii::$app->db->createCommand("select user_id,count(*) as cnt from user_become_member_invitation where user_id in (" . implode(',', $ids) . ") group by user_id")->queryAll();
            $counts = \yii\helpers\ArrayHelper::index($counts, 'user_id');
            foreach($result as &$r) {
                if ($counts[$r['id']] && $r['winner']) {
                    $r['winner']['count']=$counts[$r['id']]['cnt'];
                }
            }
        }

        return $result;
    }


    private function getUserInterests() {

        $user_interest = UserInterest::find()
            ->where('user_id=:user_id and level1_interest_id!=:level1_interest_id', [
            'user_id'=>Yii::$app->user->id,
            'level1_interest_id'=>685
        ])->count();

        return $user_interest;
    }

    private function getNewUserToday() {
		//@nvii-media - Internationalisierung entfernt
		/*if( Yii::$app->user->identity->country_id!=null && !empty( Yii::$app->user->identity->country_id) ){
			
			$users_count = User::find()->where(['status'=>User::STATUS_ACTIVE])
            ->andWhere('registration_dt >= CURDATE()')
			->andWhere(['country_id'=> Yii::$app->user->identity->country_id])
            ->count();
		}
		else{*/
			$users_count = User::find()->where(['status'=>User::STATUS_ACTIVE])
            ->andWhere('registration_dt >= CURDATE()')
            ->count();
		/*}*/
		
        return $users_count;
    }

    private function getCountTotalUsers() {
        $users_count = User::find()->where(['status'=>User::STATUS_ACTIVE])->count();
        return $users_count;
    }

    private function getNewUsers() {
        $result=Yii::$app->user->identity->new_network_members;
        Yii::$app->user->identity->new_network_members=0;
        Yii::$app->user->identity->save();
        return $result;
    }

    private function getCountOffers() {
        $countOffers = Offer::find()->where(['status'=>Offer::STATUS_ACTIVE])->count();
        return $countOffers;
    }

    public function actionIndex() {
        return [
            'results' => [
                'offers'=> $this->getOffers(),
                'searchRequest' => $this->getSearchRequest(),
                'networkMembers' => $this->getNetworkMembers(),
                'news' => $this->getNews(),
                'inviteMe' => $this->getInviteMe(),
                'profile'=>User::getPercentProfileData(),
                'countUserInterests'=>$this->getUserInterests(),
                'countTotalUsers'=>$this->getCountTotalUsers(),
                'countNewUserToday'=>$this->getNewUserToday(),
                'countNewUsers'=>$this->getNewUsers(),
                'trollboxMessages'=>\app\models\TrollboxMessage::getHistory(),
                'countTotalOffers'=>$this->getCountOffers(),
                'dashboardForumText'=>\app\models\Setting::getDashboardForumText(),
				'videos'=>$this->getVideos(),
                'faqs'=>$this->getFaqs()
            ]
        ];
    }

    public function actionGetOffers($pageNum) {
        return [
            'offers'=>$this->getOffers($pageNum),
        ];
    }

    public function actionGetSearchRequest($pageNum) {
        return [
            'searchRequest'=>$this->getSearchRequest($pageNum),
        ];
    }

    public function actionGetTrollbox() {
        return [
            'items'=>\app\models\TrollboxMessage::getHistory(),
        ];
    }



}
