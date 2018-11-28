<?php
namespace app\controllers;


use app\models\Country;
use app\models\InviteMe;
use app\models\TrollboxMessage;
use app\models\User;
use app\models\Offer;
use app\models\SearchRequest;
use Yii;
use yii\db\Query;
use app\models\InfoComment;

class ExtApiCountryController extends \app\components\ExtApiController {

	private function getCountries() {
        $query=Country::find()
            ->orderBy(['sort_order'=>SORT_ASC]);
        $countries=$query->all();

        $data=[];
        foreach($countries as $item) {
            $data[] = $item->toArray();
        }

        return [
            'results'=>$data 
        ];
    }

    public function actionInitCountries() {
        return $this->getCountries();
    }
	public function actionGetFlag() {
        return Country::getListShort();
    }

    public function actionGetCountryListNetwork() {
        return InviteMe::getCountryList();
    }

	public function actionGetCountryListOffers() {
        return Offer::getCountryList();
    }

	public function actionGetCountryListSearches() {
        return SearchRequest::getCountryList();
    }

	public function actionGetCountryListUser(){
        return User::getCountryCountList();
	}

	public function actionGetCountryListNewUser(){
        return User::getCountryCountListNewUser();
	}
	
	public function actionGetCountryListForum() {
	    return TrollboxMessage::getCountryList();
    }

	public function actionGetCountryListComments($info_id) {
        return InfoComment::getCountryList($info_id);
    }

}