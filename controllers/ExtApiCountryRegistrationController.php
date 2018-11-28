<?php
namespace app\controllers;


use app\models\Country;
use app\models\User;
use app\models\Offer;
use app\models\SearchRequest;
use Yii;
use yii\db\Query;
use app\models\InfoComment;

class ExtApiCountryRegistrationController extends \app\components\ExtApiController {

	private function getCountries() {
       
        $countries=Country::getList();
		
        $data=[];
        foreach($countries as $k=>$v) {
            $data[] = array("id"=>$k,"country"=>$v);
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

    public function actionGetList() {
        $countries = [];
        foreach(Country::getList() as $key => $value) {
            $countries[] = ['id'=>$key, 'country'=>$value];
        }

        return [
            'countries'=>$countries,
            'country_id'=>Country::getId()
        ];
    }


    

}