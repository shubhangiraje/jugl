<?php

namespace app\controllers;

use Yii;
use app\models\Faq;

class ExtApiFaqController extends \app\components\ExtApiController {

    private function getFaqs($pageNum=1) {
        $perPage=20;

        $query=Faq::find()
            ->orderBy(['id'=>SORT_DESC])
            ->offset(($pageNum-1)*$perPage)
            ->limit($perPage+1);

        $faqs=$query->all();
        $hasMore=count($faqs)>$perPage;

        $data=[];
        foreach(array_slice($faqs,0,$perPage) as $item) {
            $idata = $item->toArray();
			
			if(!empty($item['question_'.Yii::$app->language])) {
				$idata['question'] = $item['question_'.Yii::$app->language];
			} else {
				$idata['question'] = $item['question_de'];
			}
			
			if(!empty($item['response_'.Yii::$app->language])) {
				$idata['response'] = $item['response_'.Yii::$app->language];
			} else {
				$idata['response'] = $item['response_de'];
			}
			
			$data[]=$idata;
        }

        return [
            'results'=>[
                'items'=>$data,
                'hasMore'=>$hasMore
            ]
        ];
    }

    public function actionList($pageNum) {
        return $this->getFaqs($pageNum);
    }

}