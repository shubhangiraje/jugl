<?php

namespace app\controllers;

use app\components\EDateTime;
use Yii;
use \app\models\News;


class ApiNewsController extends \app\components\ApiController {

    private function getNews($pageNum) {
        $perPage=20;

        $query=News::find()
            ->orderBy(['dt'=>SORT_DESC])
            ->offset(($pageNum-1)*$perPage)
            ->limit($perPage+1);

        $news=$query->all();
        $hasMore=count($news)>$perPage;

        $data=[];
        foreach(array_slice($news,0,$perPage) as $item) {
            $idata = $item->toArray();
            $idata['dt']=(new EDateTime($item->dt))->js();
			
			if(!empty($item['title_'.Yii::$app->language])) {
				$idata['title'] = $item['title_'.Yii::$app->language];
			} else {
				$idata['title'] = $item['title_de'];
			}
			
			if(!empty($item['text_'.Yii::$app->language])) {
				$idata['text'] = $item['text_'.Yii::$app->language];
			} else {
				$idata['text'] = $item['text_de'];
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

        return [
            'log'=>[
                'items'=>$data,
                'hasMore'=>$hasMore
            ]
        ];
    }

    public function actionList($pageNum=1) {
        return $this->getNews($pageNum);
    }

}