<?php
//commit -  neu
namespace app\controllers;

use Yii;
use app\models\Advertising;
use app\components\AdminController;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;

class AdminAdvertisingController extends AdminController
{
    public function actionIndex()
    {

		$query = Advertising::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder'=>['id'=>SORT_DESC]
            ]
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new Advertising();
		$isModel = new \app\models\InterestSelection();
        $isModel->type=\app\models\UserInterest::TYPE_SEARCH_REQUEST;
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
				$isModel->loadFromAdvertising($model);
				$isModel->load(Yii::$app->request->post());
				if($isModel->validate() && $model->validate()){
					$model->save();
					$isModel->saveForAdvertising($model);
				}
                return $this->redirect(['index']);
            }else{
			}
        }

        return $this->render('create', [
            'model' => $model,
			'isModel' => $isModel
        ]);
    }

	
    public function actionDelete($id)
    {
		$delteModel = \app\models\AdvertisingInterest::deleteAdvertisingInterest($id);
        try {
            $this->findModel($id)->delete();
        } catch (\yii\base\Exception $e) {
            return $this->pjaxRefreshAlert(Yii::t('app',"Can't delete this item, it is use by another item(s)"));
        }
        return $this->pjaxRefresh();
    }
	
	public function actionUpdate($id) {
        $model = $this->findModel($id);
		$isModel = new \app\models\InterestSelection();
        $isModel->type=\app\models\UserInterest::TYPE_SEARCH_REQUEST;
        $isModel->loadFromAdvertising($model);
		if ($model->load(Yii::$app->request->post())){
			$isModel->load(Yii::$app->request->post());
            if ($model->validate() && $isModel->validate()) {
				$trx=Yii::$app->db->beginTransaction();
				$model->save();
				$isModel->saveForAdvertising($model);
				$trx->commit();
                return $this->redirect(['index']);
			}
		}
		return $this->render('update', [
            'model' => $model,
            'isModel' => $isModel
		]);
    }

	public function actionInterestNestedLevel2() {
        $level1_id=$_REQUEST['depdrop_all_params']['level1-id'];

        $items=\app\models\InterestSelection::getNestedLevelList($level1_id);

        $data=['output'=>[],'selected'=>''];
        foreach($items as $k=>$v) {
            $data['output'][]=['id'=>$k,'name'=>$v];
        }

        echo json_encode($data);
    }

    public function actionInterestNestedLevel3() {
        $level2_id=$_REQUEST['depdrop_all_params']['level2-id'];

        $items=\app\models\InterestSelection::getNestedLevelList($level2_id);

        $data=['output'=>[],'selected'=>''];
        foreach($items as $k=>$v) {
            $data['output'][]=['id'=>$k,'name'=>$v];
        }

        echo json_encode($data);
    }
	
    protected function findModel($id)
    {
        if (($model = Advertising::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
	
	public function actionStatistics(){
		
		$model['advertising_data'] = array();
		$advertising_model = Yii::$app->db->createCommand('SELECT id, advertising_name, advertising_total_clicks, advertising_total_bonus, advertising_total_views FROM advertising WHERE status = 1')->query();
		
		foreach($advertising_model as $key => $val){
			$advertising_clicks_today = Yii::$app->db->createCommand('SELECT COUNT(id) AS advertising_clicks_today FROM advertising_user WHERE DATE(dt)=DATE(CURRENT_DATE) AND advertising_id = '.$val['id'])->queryScalar();	
			$advertising_earnings_today = floatval(($val['advertising_total_clicks'] != 0 ? ($advertising_clicks_today * $val['advertising_total_bonus']) / $val['advertising_total_clicks'] : $val['advertising_total_bonus'] / $val['advertising_total_views']));

			$advertising_clicks_yesterday = Yii::$app->db->createCommand('SELECT COUNT(id) AS advertising_clicks_yesterday FROM advertising_user WHERE DATE(dt)=DATE(CURRENT_DATE-INTERVAL 1 DAY) AND advertising_id = '.$val['id'])->queryScalar();	
			$advertising_earnings_yesterday = floatval(($val['advertising_total_clicks'] != 0 ? ($advertising_clicks_yesterday * $val['advertising_total_bonus']) / $val['advertising_total_clicks'] : $val['advertising_total_bonus'] / $val['advertising_total_views']));		

			$advertising_outgoings_yesterday_result = Yii::$app->db->createCommand('SELECT COUNT(id) AS advertising_clicks_yesterday FROM advertising_user WHERE DATE(dt)=DATE(CURRENT_DATE-INTERVAL 1 DAY) AND status = 1 AND advertising_id = '.$val['id'])->queryScalar();	
			$advertising_outgoings_yesterday = floatval(($val['advertising_total_clicks'] != 0 ? ($advertising_outgoings_yesterday_result * $val['advertising_total_bonus']) / $val['advertising_total_clicks'] : $val['advertising_total_bonus'] / $val['advertising_total_views']));		
			
			$advertising_earnings_total_result = Yii::$app->db->createCommand('SELECT COUNT(id) AS advertising_clicks_today FROM advertising_user WHERE advertising_id = '.$val['id'])->queryScalar();	
			$advertising_earnings_total = floatval(($val['advertising_total_clicks'] != 0 ? ($advertising_earnings_total_result * $val['advertising_total_bonus']) / $val['advertising_total_clicks'] : $val['advertising_total_bonus'] / $val['advertising_total_views']));
			
			$advertising_outgoings_total_result = Yii::$app->db->createCommand('SELECT COUNT(id) AS advertising_clicks_today FROM advertising_user WHERE status = 1 AND advertising_id = '.$val['id'])->queryScalar();	
			$advertising_outgoings_total = floatval(($val['advertising_total_clicks'] != 0 ? ($advertising_outgoings_total_result * $val['advertising_total_bonus']) / $val['advertising_total_clicks'] : $val['advertising_total_bonus'] / $val['advertising_total_views']));

			$model['advertising_data'][$key] = $val;
			$model['advertising_data'][$key]['advertising_clicks_today'] = $advertising_clicks_today;
			$model['advertising_data'][$key]['advertising_earnings_today'] = number_format($advertising_earnings_today, 2, '.', ' ');
			$model['advertising_data'][$key]['advertising_clicks_yesterday'] = $advertising_clicks_yesterday;
			$model['advertising_data'][$key]['advertising_earnings_yesterday'] = number_format($advertising_earnings_yesterday, 2, '.', ' ');
			$model['advertising_data'][$key]['advertising_outgoings_yesterday'] = number_format($advertising_outgoings_yesterday, 2, '.', ' ');
			$model['advertising_data'][$key]['advertising_earnings_total'] = number_format($advertising_earnings_total, 2, '.', ' ');
			$model['advertising_data'][$key]['advertising_outgoings_total'] = number_format($advertising_outgoings_total, 2, '.', ' ');
		}
		
		
		$advertising_clicks_all = Yii::$app->db->createCommand('
		SELECT COUNT(id)
		FROM advertising_user
		WHERE status = 1')->queryScalar();

		$advertising_outgoings_all = Yii::$app->db->createCommand('
		SELECT SUM(advertising_bonus)
		FROM advertising_user
		WHERE status = 1')->queryScalar();
		
		$model['advertising_all']['advertising_clicks_all'] = $advertising_clicks_all;
		$model['advertising_all']['advertising_outgoings_all_jugl'] = number_format($advertising_outgoings_all, 2, '.', ' ');
		$model['advertising_all']['advertising_outgoings_all_eur'] = number_format($advertising_outgoings_all / 100, 2, '.', ' ');
		 return $this->render('statistics', [
            'model' => $model,
        ]);
	}
}