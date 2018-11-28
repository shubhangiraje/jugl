<?php
namespace app\controllers;

use app\models\Advertising;
use app\models\UserAdvertising;
use app\models\User;
use app\models\BalanceLog;
use app\components\Helper;
use Yii;
use yii\db\Query;
use yii\web\NotFoundHttpException;
use yii\helpers\Html;

class ExtApiAdvertisingController extends \app\components\ExtApiController {
		
	public function actionSetAdvertisingUser($advertising_id, $advertising_click_interval){
		$advertising=Advertising::find()->where('id=:advertising_id',['advertising_id'=>$advertising_id])->one();
		$ad_interval=$advertising->toArray(['click_interval']);
		
		$user =(new \yii\db\Query())
			->select('id')
			->from('advertising_user')
			->where('DATE_ADD(dt, INTERVAL :interval SECOND) > NOW()')
			->addParams([':interval'=>$ad_interval['click_interval']])
			->andWhere('user_id=:id', array(':id'=>Yii::$app->user->id))
			->andWhere('advertising_id=:ad_id', array(':ad_id'=>$advertising_id))	
			->orderBy(['dt' => SORT_DESC])
			->limit(1)
			->one();
			
		$userCheck	=(new \yii\db\Query())
			->select('id')
			->from('advertising_user')
			->andWhere('user_id=:id', array(':id'=>Yii::$app->user->id))
			->andWhere('advertising_id=:ad_id', array(':ad_id'=>$advertising_id))	
			->orderBy(['dt' => SORT_DESC])
			->limit(1)
			->one();
		if(!$user){
			if($userCheck && $ad_interval['click_interval'] == 0){
				return ['result'=>false];				
			}else{
				Yii::$app->db->createCommand("INSERT INTO advertising_user (advertising_id, user_id, status) VALUES (:ad_id,:user_id,0);",[':ad_id'=>$advertising_id,':user_id'=>Yii::$app->user->id])->execute();	
				return ['result'=>true];
			}
		}
		else{
			return ['result'=>false];	
		}
	}
	
	public function actionAcceptViewBonus($id){
		$Advertising = Advertising::find()->where(['id'=>$id])->one();
		$UserAdvertising = UserAdvertising::find()->where(['user_id'=>Yii::$app->user->id, 'advertising_id'=>$id])->orderBy(['id'=>SORT_DESC])->one();

		$model=\app\models\UserAdvertising::findOne($UserAdvertising->id);
		if ($model) { 
			$trx=Yii::$app->db->beginTransaction();
			$model->status = 1;
			if($Advertising->user_bonus != 0){
				$model->advertising_bonus = $Advertising->user_bonus;
			}
			$model->save();
			$trx->commit();
		}
		
		return $result=\app\models\UserAdvertising::accept(Yii::$app->user->id,$id);
	
	}	
}
