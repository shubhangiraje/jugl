<?php

namespace app\controllers;

use app\components\EDateTime;
use app\models\Faq;
use Yii;

class ApiFaqController extends \app\components\ApiController {

    private function getFaqs($pageNum) {
        $perPage=20;

        $query=Faq::find()
            ->orderBy(['id'=>SORT_DESC])
            ->offset(($pageNum-1)*$perPage)
            ->limit($perPage+1);

        $news=$query->all();
        $hasMore=count($news)>$perPage;

        $data=[];
        foreach(array_slice($news,0,$perPage) as $item) {
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
            'log'=>[
                'items'=>$data,
                'hasMore'=>$hasMore
            ]
        ];
    }

    public function actionList($pageNum=1) {
        return $this->getFaqs($pageNum);
    }


}