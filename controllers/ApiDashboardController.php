<?php

namespace app\controllers;

use app\models\Invitation;
use app\models\InviteMe;
use app\models\News;
use app\models\Setting;
use app\models\TrollboxCategory;
use app\models\TrollboxMessage;
use app\models\User;
use app\models\UserFriend;
use app\models\UserInterest;
use app\models\Country;
use Yii;
use app\models\Offer;
use app\models\Video;
use app\models\Advertising;
use app\models\SearchRequest;
use app\components\EDateTime;
use yii\db\Query;


class ApiDashboardController extends \app\components\ApiController {

    private function getFriends($pageNum,$perPage) {
		
        $count=UserFriend::find()->andWhere(['user_friend.user_id'=>Yii::$app->user->id])
            ->count();
        $pages=floor($count/$perPage+0.01);

        $friendsQuery=Yii::$app->user->identity->hasMany('\app\models\UserFriend', ['user_id' => 'id'])
            ->select(['user_friend.*',"TRIM(CONCAT_WS(' ',nick_name,first_name,last_name)) as name"])
            ->innerJoin('user','user_friend.friend_user_id=user.id')
            ->innerJoin('chat_user','user_friend.friend_user_id=chat_user.user_id')
            ->orderBy("chat_user.online desc, chat_user.online_mobile desc, name asc")
            ->offset(($pageNum-1)*$perPage)
            ->limit($perPage);

        $friends=$friendsQuery->with('friendUser','friendUser.chatUser','friendUser.avatarFile')->all();

        $data=['pages'=>$pages,'users'=>[],'page'=>$pageNum];
        foreach($friends as $friend) {
            $data['users'][]=$friend->friendUser->getShortData(['online']);
        }

        return ['friends'=>$data];
    }

    private function getHierarchy2($baseUser) {
        $user=User::findOne($baseUser->id);
        $isCurrentUserChild=false;
        do {
            if ($user->id=Yii::$app->user->id) {
                $isCurrentUserChild=true;
                break;
            }
            $user=$user->parent;
        } while ($user);

        if (!$isCurrentUserChild) {
            throw new \yii\web\HttpException(403);
        }

        $user=User::find()->andWhere(['id'=>$baseUser->id])->with(['avatarFile','users','users.avatarFile','users.users','users.users.avatarFile','users.users.users'])->one();

        $data=$user->getShortData();
        $data['users']=[];
        foreach ($user->users as $user1) {
            $data1 = $user1->getShortData();
            $data1['users'] = [];
            foreach ($user1->users as $user2) {
                $data2=$user2->getShortData();
                if (!empty($user2->users)) $data2['users']=true;
                $data1['users'][] = $data2;
            }
            $data['users'][]=$data1;
        }

        $prevUser=$baseUser;
        if ($prevUser->parent && $prevUser->id!=Yii::$app->user->id) {
            $prevUser=$prevUser->parent;
        }
        if ($prevUser->parent && $prevUser->id!=Yii::$app->user->id) {
            $prevUser=$prevUser->parent;
        }

        return ['hierarchy'=>[
            'user'=>$data,
            'parent'=>$prevUser->id!=$baseUser->id ? $prevUser->id:null
        ]];
    }

    public function actionHierarchy() {
        $params=json_decode($_REQUEST['urlState'],true);
        return $this->getHierarchy2(User::findOne($params['hierarchy_user_id']?:Yii::$app->user->id));
    }

    public function actionFriends() {
        $params=json_decode($_REQUEST['urlState'],true);
        return $this->getFriends($params['friends_page_num']?:1,6);
    }

    private function getOffers($pageNum=1,$country_ids) {
		
        $perPage=12;
		
		$perpageAdvertising = $perPage >=(\app\models\Setting::get('ADVERTISING_OFFER_MOD_COUNT'))  ? intval(floor(($perPage/(\app\models\Setting::get('ADVERTISING_OFFER_MOD_COUNT'))))) :  \app\models\Setting::get('ADVERTISING_OFFER_MOD_COUNT') ;
		
        $query = Offer::find()->distinct()
            ->innerJoin('offer_interest','offer_interest.offer_id=offer.id')
            ->orderBy(['id'=>SORT_DESC])->offset(($pageNum-1)*$perPage)->limit($perPage);
		
		$c_ids=[];
		if($country_ids!=false && is_array($country_ids)){
			foreach($country_ids as $cids){
				
					$c_ids[]=$cids;
			}
		}
		else{
		$c_ids=0;
		}
		
		if(is_array($c_ids) && !in_array(0,$c_ids)){
			$query->where(['in','offer.country_id',$c_ids])->orWhere('offer.country_id IS NULL');	
		}
		else{
			$query->where('offer.country_id IS NULL');		
		}
		
		$query->andWhere('offer.active_till>=CAST(NOW() AS DATE) and view_bonus>0 and offer.status=:active_status',[':active_status'=>Offer::STATUS_ACTIVE]);
		
        $query->with(['user','user.chatUser','user.avatarFile','files','offerInterests','offerInterests.level1Interest','offerInterests.level2Interest','offerInterests.level3Interest']);

        Yii::$app->user->identity->addOfferSearchFilterConditions($query);
		$offers = $query->all();
		
        $data = [];
		
		$adata = [];
		$advertising  = \app\models\Advertising::getAdvertising('ADVERTISING','',$pageNum,$perpageAdvertising, Yii::$app->user->identity->country_id);
		
		if(!empty($advertising)){
			foreach ($advertising['model'] as $item1) {
				$adata[] = $item1->toArray(['advertising_display_name','link','user_bonus', 'banner', 'id', 'advertising_type', 'click_interval', 'popup_interval']);
			}	
		}
		$aryKey = 0;
		$i = 0;
		
        foreach ($offers as $item) {
			$i++;
			$z = $i;

            $idata = $item->toArray(['id','type','title','price','delivery_days','view_bonus','buy_bonus','country_id','zip','city','address','relevancy', 'amount', 'show_amount', 'active_till']);
			
			$idata['description']=\yii\helpers\StringHelper::truncate($item->description,100);
            $idata['create_dt']=(new EDateTime($item->create_dt))->js();
            $idata['user']=$item->user->getShortData(['rating', 'feedback_count', 'packet', 'country_id']);		
			$flagAry = Country::getListShort();
			$idata['user']['flag'] = $flagAry[$idata['user']['country_id']];
			
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
			
			
			if($adata){
				if($i % \app\models\Setting::get('ADVERTISING_OFFER_MOD_COUNT') == 0){
					if($adata[$aryKey]){
						$data[] = $adata[$aryKey];
						$aryKey++;
					}
				}
			}
        }
		
		if(count($offers) < $perPage){			
			foreach($adata as $k => $v){
				if($k >= $aryKey){
					$data[] = $v;
				}
			}
		}		
		
		if($advertising['rowsCount'] > count($rows)){
			$advertising  = \app\models\Advertising::getAdvertising('ADVERTISING','',$pageNum,$perpageAdvertising, Yii::$app->user->identity->country_id);
			if(!empty($advertising)){
				$hasMore = true;
				foreach ($advertising['model'] as $item1) {
					$adata[] = $item1->toArray(['advertising_display_name','link','user_bonus', 'banner', 'id', 'advertising_type', 'click_interval', 'popup_interval']);
				}	
			}else{
				$hasMore = false;
			}
		}
		
        return $data;
    }

    private function getSearchRequest($pageNum=1,$country_ids) {
        $perPage=12;
        $level1InterestsIds=Yii::$app->user->identity->getLevel1InterestIds(\app\models\UserInterest::TYPE_SEARCH_REQUEST);
		
		$c_ids=[];
		
		if($country_ids!=false && is_array($country_ids)){
			foreach($country_ids as $cids){
					$c_ids[]=$cids;
			}
		}
		else{
		$c_ids[]=null;
		}
		
		if($c_ids!=null){
		$temp=false;	
			foreach($c_ids as $check){
				if($check!=null){
				$temp=true;	
				}
			}
			if($temp){
			$add_query="and search_request.country_id IN (".implode(',',$c_ids).")";
			}
			else{
			$add_query="and search_request.country_id IS NULL";	
			}
			
			
		}
		else{
		$add_query="and search_request.country_id IS NULL";
		}
		
		
        $searchRequest=SearchRequest::findBySql("
            SELECT search_request.*
            FROM `search_request`
            WHERE exists(
                  select * from search_request_interest
                  where search_request_interest.search_request_id=search_request.id and search_request_interest.level1_interest_id in (".implode(',',$level1InterestsIds).")
              ) and search_request.active_till>=CAST(NOW() AS DATE) and search_request.status=:active_status 
			   ".$add_query."
			  
			  order by id desc limit :perPage offset :offset
			 
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
            $idata = $item->toArray(['id','title','price_from','price_to','bonus','zip','city','address','relevancy','country_id']);
            $idata['description']=\yii\helpers\StringHelper::truncate($item->description,100);
            $idata['create_dt']=(new EDateTime($item->create_dt))->js();
            $idata['user']=$item->user->getShortData(['rating', 'feedback_count', 'packet', 'country_id']);
			
			/* NVII-MEDIA - Output Flag */
			$flagAry = Country::getListShort();
			$idata['user']['flag'] = $flagAry[$idata['user']['country_id']];
			/* NVII-MEDIA - Output Flag */
			
			
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
        $networkMembers=$query->with('referralUser','referralUser.chatUser','referralUser.avatarFile')->limit(12)->all();

        $data = [];
        foreach ($networkMembers as $item) {
            $idata = $item->referralUser->getShortData(['online','country_id']);
            $idata['registration_dt'] = (new EDateTime($item->referralUser->registration_dt))->js();
			$flagAry = Country::getListShort();
			$idata['flag'] = $flagAry[$idata['country_id']];
            $data[] = $idata;
        }
        return $data;
    }

    private function getNews() {

        $news = News::find()->with(['imageFile'])->orderBy(['dt'=>SORT_DESC])->limit(12)->all();
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

    private function getInviteMe($pageNum=1,$country_ids) {
		$perPage=12;
        $time=(new \app\components\EDateTime())->modify("-".\app\models\Setting::get('TEAM_CHANGE_PERIOD_DAYS')." minute");     
		
		$c_ids=[];
		if($country_ids!=false && is_array($country_ids)){
			foreach($country_ids as $cids){
					$c_ids[]=$cids;
			}
		}
		else{
		$c_ids[]=null;
		}	
		if($c_ids!=null){
		$temp=false;	
			foreach($c_ids as $check){
				if($check!=null){
				$temp=true;	
				}
			}
			if($temp){
			$addquery="AND user.country_id IN (".implode(',',$c_ids).")";
			}
			else{
			$addquery="AND user.country_id IS NULL";	
			}	
		}
		else{
		$addquery="AND user.country_id IS NULL";
		}
		
		$invites=\app\models\User::findBySql('SELECT user.* FROM user 
		WHERE user.status=:status
		'.$addquery.'
		AND user.show_in_become_member = 1
		AND (user.registration_dt >:time)
		AND user.id!=:user_id
		ORDER BY user.id DESC 
		LIMIT :perPage 
		OFFSET :offset',
		[
			':user_id'=>Yii::$app->user->identity->getId(),
			':status'=>\app\models\User::STATUS_ACTIVE,
			':time'=>$time->sql(),
			':offset'=>($pageNum-1)*$perPage,
            ':perPage'=>$perPage    
        ])->with(['invitationWinner','invitation'])->all();

		
        $result=[];

        foreach($invites as $invite) {
            $item=$invite->toArray(['id', 'first_name','last_name','email','phone','is_company_name','company_name', 'country_id']);
			/* NVII-MEDIA - Output Flag */
			$flagAry = Country::getListShort();
			$item['flag'] = $flagAry[$invite->country_id];
			/* NVII-MEDIA - Output Flag */
			
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
		
		if( Yii::$app->user->identity->country_id!=null && !empty( Yii::$app->user->identity->country_id) ){
			$users_count = User::find()->where(['status'=>User::STATUS_ACTIVE])
            ->andWhere('registration_dt >= CURDATE()')
            ->count();
		}
		else{
			$users_count = User::find()->where(['status'=>User::STATUS_ACTIVE])
            ->andWhere('registration_dt >= CURDATE()')
            ->count();
		}
		
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

    private function getFaqs() {
        $faqs = \app\models\Faq::find()->orderBy(['id'=>SORT_DESC])->limit(12)->all();
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
			
            //$idata['response']=\yii\helpers\StringHelper::truncate($item->response,200);
            $data[] = $idata;			 
        }
        return $data;
    }
	
	/* NVII-MEDIA
	 * get videos on Dashboard  
	 */
	private function getVideos($pageNum=1){
		
		$perPage=12;
		$videos = \app\models\Video::find()->orderBy(['video_id'=>SORT_DESC])->offset(($pageNum-1)*$perPage)->limit($perPage)->all();
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
	
	private function getVideoCategories(){
		$categories = \app\models\Video::find()->groupBy('cat_name');
		$data = [];
        $cat_name = '';
		foreach ($categories as $item) {
			
			if($cat_name != $item->cat_name){
				$cat_name = $item->cat_name;
				$data[] = $item->toArray(['cat_id', 'cat_name']);
			}
        }
        return $data;
	}
	
    public function actionIndexNew() {
        if(Yii::$app->user->identity->country_id){
			$country_ids=$this->currentCountry();
		}
		else{
			$country_ids=null;
		}
		
		$data = [	
            'offers'=> null,
            'searchRequest' => null,
            'networkMembers' => $this->getNetworkMembers(),
            'news' => $this->getNews(),
            'inviteMe' => null,
            'profile'=>User::getPercentProfileData(),
            'countUserInterests'=>$this->getUserInterests(),
            'countTotalUsers'=>$this->getCountTotalUsers(),
            'countNewUserToday'=>$this->getNewUserToday(),
            'countNewUsers'=>$this->getNewUsers(),
            'trollboxMessages'=>null,
            'countTotalOffers'=>$this->getCountOffers(),
            'faqs'=>$this->getFaqs(),
			'videos'=>$this->getVideos(), //Nvii-media
			'videos_categories'=>$this->getVideoCategories(), //Nvii-media
			//'countryArrayNetwork'=>$this->getCountryListInviteMe(), //Nvii-media
			//'countryArrayOffers'=>$this->getCountryListOffers(), //Nvii-media
			//'countryArraySearches'=>$this->getCountryListSearchRequests(), //Nvii-media
			//'countryArrayForum'=>$this->getCountryListTrollbox(), //Nvii-media
			'advertisings'=>$this->getAdvertising(), //Nvii-media

            'trollboxCategoryList'=>TrollboxCategory::getList(),
            'countryArrayOffers'=>Offer::getCountryList(), //optimized
            'countryArraySearches'=>SearchRequest::getCountryList(), //optimized
            'countryArrayForum'=>TrollboxMessage::getCountryList(), //optimized
            'countryArrayNetwork'=>InviteMe::getCountryList(), //optimized
            'count_video_identification'=>TrollboxMessage::getCountVideoIdentification()
        ];

        $data['registrationFromApp']=Yii::$app->session['registrationFromApp'];
        $session['registrationFromApp']=false;

        return $data;
    }

	
	public function getAdvertising(){
		$modelForumTop  = \app\models\Advertising::getAdvertisingDashboard('FORUM_TOP', '', '', '', Yii::$app->user->identity->country_id);
		
		foreach($modelForumTop as $itemTop){
			if($itemTop->status == 1 && $itemTop->advertising_position != '' && $itemTop->id != '' && $itemTop->user_bonus != '' && $itemTop->link != ''){
				$data[$itemTop->advertising_position][$itemTop->id]['id'] = $itemTop->id;
				$data[$itemTop->advertising_position][$itemTop->id]['advertising_name'] = $itemTop->advertising_name;
				$data[$itemTop->advertising_position][$itemTop->id]['advertising_display_name'] = $itemTop->advertising_display_name;
				$data[$itemTop->advertising_position][$itemTop->id]['advertising_type'] = $itemTop->advertising_type;
				$data[$itemTop->advertising_position][$itemTop->id]['advertising_total_bonus'] = $itemTop->advertising_total_bonus;
				$data[$itemTop->advertising_position][$itemTop->id]['banner_height'] = $itemTop->banner_height;
				$data[$itemTop->advertising_position][$itemTop->id]['banner_width'] = $itemTop->banner_width;
				$data[$itemTop->advertising_position][$itemTop->id]['advertising_total_views'] = $itemTop->advertising_total_views;
				$data[$itemTop->advertising_position][$itemTop->id]['advertising_total_clicks'] = $itemTop->advertising_total_clicks;
				$data[$itemTop->advertising_position][$itemTop->id]['banner'] = $itemTop->banner;
				$data[$itemTop->advertising_position][$itemTop->id]['link'] = $itemTop->link;
				$data[$itemTop->advertising_position][$itemTop->id]['user_bonus'] = $itemTop->user_bonus;
				$data[$itemTop->advertising_position][$itemTop->id]['dt'] = $itemTop->dt;
				$data[$itemTop->advertising_position][$itemTop->id]['status'] = $itemTop->status;
				$data[$itemTop->advertising_position][$itemTop->id]['click_interval'] = $itemTop->click_interval;
				$data[$itemTop->advertising_position][$itemTop->id]['popup_interval'] = $itemTop->popup_interval;
			}
		}
		
		$modelForumBottom  = \app\models\Advertising::getAdvertisingDashboard('FORUM_BOTTOM', '', '', '', Yii::$app->user->identity->country_id);
		
		foreach($modelForumBottom as $itemBottom){
			if($itemBottom->status == 1 && $itemBottom->advertising_position != '' && $itemBottom->id != '' && $itemBottom->user_bonus != '' && $itemBottom->link != ''){
				$data[$itemBottom->advertising_position][$itemBottom->id]['id'] = $itemBottom->id;
				$data[$itemBottom->advertising_position][$itemBottom->id]['advertising_name'] = $itemBottom->advertising_name;
				$data[$itemBottom->advertising_position][$itemBottom->id]['advertising_display_name'] = $itemBottom->advertising_display_name;
				$data[$itemBottom->advertising_position][$itemBottom->id]['advertising_type'] = $itemBottom->advertising_type;
				$data[$itemBottom->advertising_position][$itemBottom->id]['advertising_total_bonus'] = $itemBottom->advertising_total_bonus;
				$data[$itemBottom->advertising_position][$itemBottom->id]['banner_height'] = $itemBottom->banner_height;
				$data[$itemBottom->advertising_position][$itemBottom->id]['banner_width'] = $itemBottom->banner_width;
				$data[$itemBottom->advertising_position][$itemBottom->id]['advertising_total_views'] = $itemBottom->advertising_total_views;
				$data[$itemBottom->advertising_position][$itemBottom->id]['advertising_total_clicks'] = $itemBottom->advertising_total_clicks;
				$data[$itemBottom->advertising_position][$itemBottom->id]['banner'] = $itemBottom->banner;
				$data[$itemBottom->advertising_position][$itemBottom->id]['link'] = $itemBottom->link;
				$data[$itemBottom->advertising_position][$itemBottom->id]['user_bonus'] = $itemBottom->user_bonus;
				$data[$itemBottom->advertising_position][$itemBottom->id]['dt'] = $itemBottom->dt;
				$data[$itemBottom->advertising_position][$itemBottom->id]['status'] = $itemBottom->status;
				$data[$itemBottom->advertising_position][$itemBottom->id]['click_interval'] = $itemBottom->click_interval;
				$data[$itemBottom->advertising_position][$itemBottom->id]['popup_interval'] = $itemBottom->popup_interval;
			}
		}
		return $data;

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
	public function actionGetInviteMe($pageNum,$country_ids=false) {

		return [
            'inviteMe'=>$this->getInviteMe($pageNum,json_decode($country_ids,true)),
        ];
    }
	
    public function actionGetOffers($pageNum,$country_ids=false) {

		return [
            'offers'=>$this->getOffers($pageNum,json_decode($country_ids,true)),
        ];
    }
	
	public function actionGetVideos($pageNum) {
        return [
            'videos'=>$this->getVideos($pageNum),
        ];
    }

    public function actionGetSearchRequest($pageNum,$country_ids=false) {
        return [
            'searchRequest'=>$this->getSearchRequest($pageNum,json_decode($country_ids,true)),
        ];
    }

    public function actionGetTrollbox($country_ids=false) {
	    $countries=json_decode($country_ids,true);
		return [
            'trollboxMessages'=>TrollboxMessage::getDashboardList($countries),
		    //'trollboxMessages'=>\app\models\TrollboxMessage::getHistory(null,null,3,$countries),
			//'countryArrayForum'=>$this->getCountryListTrollbox(),
			'dashboardForumText'=>\app\models\Setting::getDashboardForumText()
        ];
    }

    public function actionIndex() {
        $data=array_merge(
            $this->actionFriends(),
            $this->actionHierarchy()
        );

        $data['registrationFromApp']=Yii::$app->session['registrationFromApp'];
        $session['registrationFromApp']=false;

        return $data;
    }
	
	public function actionDetails($id) {
		
	}
	
	/*Country List functions*/
	
	//offers

	private function getCountryListOffers() {
	    $result=[];
		$offers=[];
		$squery=new Query;
        $squery->select(['offer.id'])
            ->from('offer')
            ->innerJoin('offer_interest','offer_interest.offer_id=offer.id')
            ->innerJoin('user','user.id=offer.user_id')
            ->leftJoin('user_interest','user_interest.user_id=:user_id and (
                user_interest.level3_interest_id=offer_interest.level3_interest_id or
                user_interest.level2_interest_id=offer_interest.level2_interest_id or
                user_interest.level1_interest_id=offer_interest.level1_interest_id
                )',[':user_id'=>Yii::$app->user->id])
            //->where('offer.active_till>=CAST(NOW() AS DATE) and offer.status=:active_status',[':active_status'=>Offer::STATUS_ACTIVE])
			->groupBy(['offer.id']);
            
		$squery->where('offer.active_till>=CAST(NOW() AS DATE) and offer.status=:active_status',[':active_status'=>Offer::STATUS_ACTIVE]);
		$squery->andWhere('offer.view_bonus>0');

		Yii::$app->user->identity->addOfferSearchFilterConditions($squery);

		$rows=$squery->all();
		$unsortedModels=Offer::find()->andWhere(['id'=>\yii\helpers\ArrayHelper::getColumn($rows,'id')])->with([
            'user',
            'user.avatarFile',
            'offerInterests',
            'offerInterests.level1Interest',
            'offerInterests.level2Interest',
            'offerInterests.level3Interest',
            'files',
            'offerMyFavorites'
        ])->indexBy('id')->all();
		
		foreach($rows as $row) {
            $model=$unsortedModels[$row['id']];
			$idata=$model->toArray(['country_id']);
			$offers[]=$idata;
		}
		
		foreach($offers as $key){
				if($key['country_id']!="" && $key['country_id']!=NULL){
					$result[$key['country_id']]=$result[$key['country_id']]+1;
				}
				else{
					$result['no_countries']=$result['no_countries']+1;
				}
		}	
		
		$data=Country::getList();		
		$countries=array();
		$shortnames=Country::getListShort();
		$countArray=$result;
		asort($data);
		foreach($data as $key=>$value){
			array_push($countries,array('id'=>$key,'name'=>$value.' ('.(($countArray['no_countries'] > 0 || $countArray[$key]!=null)? $countArray[$key]+$countArray['no_countries']: '0').') ','flag'=>$shortnames[$key]));	
		}
		
		return $countries;
    }
	//offers end

	//Search Requests

	private function getCountryListSearchRequests() {
		$result=[];
		$searches=[];
		$squery=new Query;
        $squery->select(['search_request.id',
            'relevancy'=>
                // level 3 relevancy
                '33.33*('.
                    // count of matched level3 interests
                    'SUM(IF(user_interest.level3_interest_id=search_request_interest.level3_interest_id,1,0))+'.
                    // or 1 if level1 & level2 interests matches
                    'MAX(IF(search_request_interest.level3_interest_id is null and (search_request_interest.level2_interest_id=user_interest.level2_interest_id or search_request_interest.level2_interest_id is null) and search_request_interest.level1_interest_id=user_interest.level1_interest_id ,1,0))'.
                ')/'.
                // count of level3 interests in search request or 1
                'COALESCE(NULLIF(COUNT(DISTINCT search_request_interest.level3_interest_id),0),1)+'.
                // level 2 relevancy
                '33.33*MAX(IF(user_interest.level2_interest_id=search_request_interest.level2_interest_id or (search_request_interest.level2_interest_id is null and user_interest.level1_interest_id=search_request_interest.level1_interest_id),1,0))+'.
                // level 1 relevancy
                '33.33*MAX(IF(user_interest.level1_interest_id=search_request_interest.level1_interest_id,1,0))'
        ])
            ->from('search_request')
            ->innerJoin('search_request_interest','search_request_interest.search_request_id=search_request.id')
            ->innerJoin('user','user.id=search_request.user_id')
            ->leftJoin('user_interest','user_interest.user_id=:user_id and (
                user_interest.level3_interest_id=search_request_interest.level3_interest_id or
                user_interest.level2_interest_id=search_request_interest.level2_interest_id or
                user_interest.level1_interest_id=search_request_interest.level1_interest_id
                )',[':user_id'=>Yii::$app->user->id])
            ->groupBy('search_request.id');
            
		$squery->where('search_request.active_till>=CAST(NOW() AS DATE) and search_request.status=:active_status',[':active_status'=>SearchRequest::STATUS_ACTIVE]);

		$rows=$squery->all();
		$unsortedModels=SearchRequest::find()->andWhere(['id'=>\yii\helpers\ArrayHelper::getColumn($rows,'id')])->with([
            'user',
            'user.avatarFile',
            'searchRequestInterests',
            'searchRequestInterests.level1Interest',
            'searchRequestInterests.level2Interest',
            'searchRequestInterests.level3Interest',
            'files',
            'searchRequestMyFavorites'
        ])->indexBy('id')->all();
		
		foreach($rows as $row) {
            $model=$unsortedModels[$row['id']];
			$idata=$model->toArray(['country_id']);
			$searches[]=$idata;
		}
		
		foreach($searches as $key){
				if($key['country_id']!="" && $key['country_id']!=NULL){
				$result[$key['country_id']]=$result[$key['country_id']]+1;
				}
		}
		
		
		$data=Country::getList();
		$countries=array();
		$shortnames=Country::getListShort();
		
		$countArray=$result;
		asort($data);
		foreach($data as $key=>$value){
			array_push($countries,array('id'=>$key,'name'=>$value.' ('.(($countArray[$key] !=null )? $countArray[$key]: '0').') ','flag'=>$shortnames[$key]));
		}
		return $countries;
    }
	// Search Requests end

	//Trollbox

	private function getCountryListTrollbox($limit=false) {
        if(!$limit){
		    $limit=3;
	    }

	    $data=Country::getList();
		$countries=array();
		$shortnames=Country::getListShort();
		
		
		$countArray=\app\models\TrollboxMessage::getHistoryCountry();
		//array_push($countries,array('id'=>'no_countries','name'=>'keine Zuordnung'.'('.(($countArray['no_countries']!=null)? $countArray['no_countries']: '0').')','flag'=>''));
		asort($data);
		foreach($data as $key=>$value){
			array_push($countries,array('id'=>$key,'name'=>$value.' ('.(($countArray[$key]!=null)? $countArray[$key]: '0').') ','flag'=>$shortnames[$key]));	
		}
		return $countries;
    }
	//Trollbox end
	
	//InviteMe

	private function getCountryListInviteMe() {
		
		$data=Country::getList();
		$shortnames=Country::getListShort();
		$ids=array();
		$countries=array();
		
		foreach($data as $key=>$val){
		 $ids[]=$key;
		}	
		$result=array();
		
		$time=(new \app\components\EDateTime())->modify("-".\app\models\Setting::get('TEAM_CHANGE_PERIOD_DAYS')." minute");		
		
		$counts = Yii::$app->db->createCommand("
		SELECT country_id,COUNT(country_id) AS country_counts
		FROM user 
		WHERE status='".\app\models\User::STATUS_ACTIVE."' 
		AND show_in_become_member = 1
		AND (registration_dt > '".$time->sql()."')
		AND id != ".Yii::$app->user->identity->getId()."
		GROUP BY country_id
		")->queryAll();
		
		/*$count_empty= Yii::$app->db->createCommand("
		SELECT country_id,COUNT(*) AS country_counts_empty
		FROM user 
		WHERE status='".\app\models\User::STATUS_ACTIVE."' 
		AND show_in_become_member = 1
		AND (registration_dt > '".$time->sql()."' OR  parent_id IS NULL)
		AND id != ".Yii::$app->user->identity->getId()."
		AND country_id IS NULL
		GROUP BY country_id
		")->queryAll();*/
		
		
		
		foreach($counts as $key){
				$result=$result+array($key['country_id']=>$key['country_counts']);	
		}
		
		/*foreach($count_empty as $key){
				$result=$result+array('no_country'=>$key['country_counts_empty']);	
		}*/

		$countArray=$result;
		
		//array_push($countries,array('id'=>'no_countries','name'=>'keine Zuordnung'.'('.(($countArray['no_country']!=null)? $countArray['no_country']: '0').')','flag'=>''));
		asort($data);
		
		foreach($data as $key=>$value){
		array_push($countries,array('id'=>$key,'name'=>$value.' ('.(($countArray[$key]!=null)? $countArray[$key]: '0').') ','flag'=>$shortnames[$key]));	
		}
		return $countries;
    }
	//Invite Me end
}
