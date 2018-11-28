<?php

namespace app\controllers;


use app\models\Country;
use app\models\InviteMe;
use app\models\TrollboxMessage;
use app\models\User;
use app\models\Offer;
use app\models\SearchRequest;
use \app\models\Info;
use app\models\InfoComment;
use app\models\InfoCommentVote;
use Yii;
use yii\db\Query;



class ApiCountryController extends \app\components\ApiController {

    public function actionGetCountryListNetwork() {
        return InviteMe::getCountryList();
    }

	public function actionGetCountryListOffers() {
        return Offer::getCountryList();
    }

	public function actionGetCountryListSearches() {
        return SearchRequest::getCountryList();
    }
	
	public function actionGetCountryListUser() {
        return User::getCountryCountList();
	}

	public function actionGetCountryListNewUser() {
        return User::getCountryCountListNewUser();
	}

	public function actionGetCountryListForum() {
        return TrollboxMessage::getCountryList();
    }
	
	public function actionGetCountryListComments($info_id) {
        return InfoComment::getCountryList($info_id);
    }

}
