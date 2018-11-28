<?php

namespace app\models;

use Yii;
use app\components\EDateTime;

class SearchRequestComment extends \app\models\base\SearchRequestComment {

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'search_request_id' => Yii::t('app','Search Request ID'),
            'user_id' => Yii::t('app','Benutzer'),
            'comment' => Yii::t('app','Kommentar'),
            'create_dt' => Yii::t('app','Datum'),
            'response' => Yii::t('app','Antwort'),
            'response_dt' => Yii::t('app','Antwort Datum'),
        ];
    }

    public function rules() {
        return array_merge(parent::rules(),[
            ['response','required','on'=>'response-update','message'=>Yii::t('app','Geben Sie bitte Antwort')]
        ]);
    }

    public function scenarios() {
        $scenarios = parent::scenarios();
        $scenarios['response-update'] = ['response'];
        return $scenarios;
    }


    public static function getComments($searchRequestId, $pageNum=1) {
        $perPage = 10;
        $query=SearchRequestComment::find()
            ->where(['search_request_id' => $searchRequestId])
            ->with(['user'])
            ->orderBy(['id'=>SORT_DESC]);

        $query->offset(($pageNum-1)*$perPage)
            ->limit($perPage+1);

        $data = [];
        $comments=$query->all();
        $hasMore=count($comments)>$perPage;

        foreach(array_slice($comments,0,$perPage) as $item) {
            $data[] = [
                'id' => $item->id,
                'user'=> $item->user->getShortData(['rating','feedback_count', 'packet']),
                'comment' => $item->comment,
                'response' => $item->response,
                'response_dt' =>(new EDateTime($item->response_dt))->js(),
                'create_dt' => (new EDateTime($item->create_dt))->js()
            ];
        }

        return [
            'items'=>$data,
            'hasMore'=>$hasMore
        ];
    }



}
