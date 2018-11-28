<?php

namespace app\controllers;

use app\models\base\Interest;
use Yii;
use app\models\UserInterest;
use yii\helpers\ArrayHelper;


class ApiUserInterestsController extends \app\components\ApiController {

    private function getUserInterests($type=null) {

        if (!$type) {
            $type='OFFER';
        }

        $user_interest = UserInterest::find()
            ->select(['level1_interest_id'])
            ->with(['level1Interest','level1Interest.file'])
            ->where(['user_id' => Yii::$app->user->id,'type'=>$type])
            ->groupBy(['level1_interest_id'])
            ->all();

        $queryCountLevel2 = ArrayHelper::index(Yii::$app->db->createCommand("SELECT level1_interest_id, COUNT(*) as count_level2
                                                                                FROM user_interest
                                                                                  WHERE user_id=:user_id AND level2_interest_id is not null AND level3_interest_id is null
                                                                                    GROUP BY level1_interest_id",[':user_id'=>Yii::$app->user->id])->queryAll(),'level1_interest_id');

        $queryCountLevel3 = ArrayHelper::index(Yii::$app->db->createCommand("SELECT level1_interest_id, COUNT(*) as count_level3
                                                                                FROM user_interest
                                                                                  WHERE user_id=:user_id AND level3_interest_id is not null
                                                                                    GROUP BY level1_interest_id",[':user_id'=>Yii::$app->user->id])->queryAll(),'level1_interest_id' );

        $data = [];

        foreach ($user_interest as $item) {
            $count_level2 = $queryCountLevel2[$item->level1_interest_id]['count_level2'];
            $count_level3 = $queryCountLevel3[$item->level1_interest_id]['count_level3'];

            $item->level1Interest->file ? $thumb = $item->level1Interest->file->getThumbUrl('interest') : $thumb = '/static/images/account/default_interest.png';

            $data[] = [
                'interest_id' => $item->level1_interest_id,
                'interest_title' => $item->level1Interest->title,
                'interest_sort' => $item->level1Interest->sort_order,
                'interest_img' => $thumb,
                'count_level2' => $count_level2,
                'count_level3' => $count_level3
            ];
        }

        return [
            'interests'=>$data
        ];
    }


    private function getUserInterestsUpdate($id) {

        $interests = UserInterest::find()
            ->where(['user_id'=>Yii::$app->user->id, 'level1_interest_id'=>$id])
            ->with(['level1Interest','level1Interest.file','level2Interest','level2Interest.file', 'level3Interest'])
            ->all();

        $data =[];
        $dataLevel2 = [];
        $dataLevel3 = [];

        foreach ($interests as $item) {

            $item->level1Interest->file ? $thumb = $item->level1Interest->file->getThumbUrl('interest') : $thumb = \app\components\Thumb::createUrl('/static/images/account/default_interest.png','interest');
            $item->level2Interest->file ? $thumb2 = $item->level2Interest->file->getThumbUrl('interestSmall') : $thumb2 = \app\components\Thumb::createUrl('/static/images/account/default_interest.png','interestSmall');

            $data = [
                'interest_id'=>$item->level1_interest_id,
                'interest_title'=>$item->level1Interest->title,
                'interest_img' =>$thumb,
            ];

            if($item->level2_interest_id != NULL) {
                if ($item->level3_interest_id != NULL) {
                    $dataLevel3[$item->level2_interest_id][] = [
                        'interest_id' => $item->level3_interest_id,
                        'interest_title' => $item->level3Interest->title,
                        'interest_sort' => $item->level3Interest->sort_order,
                    ];
                    $dataLevel2[$item->level2_interest_id] = [
                        'interest_id' => $item->level2_interest_id,
                        'interest_title' => $item->level2Interest->title,
                        'interest_img' => $thumb2,
                        'interest_sort' => $item->level2Interest->sort_order,
                        'level3_interests' => $dataLevel3[$item->level2_interest_id]
                    ];
                } else {
                    $dataLevel2[$item->level2_interest_id] = [
                        'interest_id' => $item->level2_interest_id,
                        'interest_title' => $item->level2Interest->title,
                        'interest_img' => $thumb2,
                        'interest_sort' => $item->level2Interest->sort_order,
                    ];
                }
            }
        }

        $data['level2_interests'] = array_values($dataLevel2);
        return ['interests'=>$data];

    }


    public function saveInterest($level1Id, $level2Id = NULL, $level3Id = NULL) {
        $type=Interest::findOne($level1Id)->type;
        if(!$level3Id) {
            $user_interest = new UserInterest();
            $user_interest->user_id = Yii::$app->user->id;
            $user_interest->level1_interest_id = $level1Id;
            $user_interest->level2_interest_id = $level2Id;
            $user_interest->type=$type;
            $user_interest->save();
        } else {
            for($i = 0; $i<count($level3Id); $i++) {
                $user_interest = new UserInterest();
                $user_interest->user_id = Yii::$app->user->id;
                $user_interest->type=$type;
                $user_interest->level1_interest_id = $level1Id;
                $user_interest->level2_interest_id = $level2Id;
                $user_interest->level3_interest_id = $level3Id[$i];
                $user_interest->save();
            }
        }
    }


    public function saveInterestLevel1($level1Id) {
        $is_level1 = UserInterest::findOne(['user_id'=>Yii::$app->user->id, 'level1_interest_id'=>$level1Id]);
        if(!$is_level1) {
            $this->saveInterest($level1Id);
        } else {
            UserInterest::deleteAll(['user_id'=>Yii::$app->user->id, 'level1_interest_id'=>$level1Id]);
            $this->saveInterest($level1Id);
        }
    }

    public function saveInterestLevel2($level1Id, $level2Id) {
        $is_level1 = UserInterest::findOne(['user_id'=>Yii::$app->user->id, 'level1_interest_id'=>$level1Id]);
        $is_level2 = UserInterest::findOne(['user_id'=>Yii::$app->user->id, 'level1_interest_id'=>$level1Id, 'level2_interest_id'=>$level2Id]);
        $is_level2_null = UserInterest::findOne(['user_id'=>Yii::$app->user->id, 'level1_interest_id'=>$level1Id, 'level2_interest_id'=>NULL]);

        if($is_level1 and $is_level2_null) {
            UserInterest::deleteAll([
                'user_id'=>Yii::$app->user->id,
                'level1_interest_id'=>$level1Id,
            ]);
            $this->saveInterest($level1Id, $level2Id);
        } else if($is_level1 and !$is_level2) {
            $this->saveInterest($level1Id, $level2Id);
        } else if($is_level1 and $is_level2) {
            UserInterest::deleteAll([
                'user_id'=>Yii::$app->user->id,
                'level1_interest_id'=>$level1Id,
                'level2_interest_id'=>$level2Id,
            ]);
            $this->saveInterest($level1Id, $level2Id);
        } else {
            $this->saveInterest($level1Id, $level2Id);
        }
    }


    public function saveInterestLevel3($level1Id, $level2Id, $level3Id) {
        $is_level2 = UserInterest::findOne([
            'user_id'=>Yii::$app->user->id,
            'level1_interest_id'=>$level1Id,
            'level2_interest_id'=>$level2Id,
        ]);

        if($is_level2) {
            UserInterest::deleteAll([
                'user_id'=>Yii::$app->user->id,
                'level1_interest_id'=>$level1Id,
                'level2_interest_id'=>$level2Id,
            ]);
            $this->saveInterest($level1Id, $level2Id, $level3Id);
        } else {
            $this->saveInterest($level1Id, $level2Id, $level3Id);
        }
    }


    public function deleteInterest($interestId) {
        $params = [
            'user_id'=>Yii::$app->user->id,
            'level1_interest_id'=>$interestId
        ];

        UserInterest::deleteAll('(user_id=:user_id and level1_interest_id=:level1_interest_id)',$params);
    }


    public function deleteLevel2Interest($interestLevel1Id, $interestLevel2Id) {
        $params = [
            'user_id'=>Yii::$app->user->id,
            'level1_interest_id'=>$interestLevel1Id,
            'level2_interest_id'=>$interestLevel2Id,
        ];

        UserInterest::deleteAll('(user_id=:user_id and level1_interest_id=:level1_interest_id and level2_interest_id=:level2_interest_id)',$params);
    }


    public function deleteLevel3Interest($interestLevel1Id, $interestLevel2Id, $interestLevel3Id) {
        $params = [
            'user_id'=>Yii::$app->user->id,
            'level1_interest_id'=>$interestLevel1Id,
            'level2_interest_id'=>$interestLevel2Id,
            'level3_interest_id'=>$interestLevel3Id
        ];

        UserInterest::deleteAll('(user_id=:user_id and level1_interest_id=:level1_interest_id and level2_interest_id=:level2_interest_id and level3_interest_id=:level3_interest_id)',$params);
    }


    public function actionIndex() {
        return  $this->getUserInterests($_REQUEST['type']);
    }


    public function actionUpdate($id) {
        return  $this->getUserInterestsUpdate($id);
    }

    public function actionSaveLevel1Interest() {
        $params = Yii::$app->request->getBodyParams();
        $transaction = Yii::$app->db->beginTransaction();
        $this->saveInterestLevel1($params['level1Id']);
        $transaction->commit();
        return ['result'=>true];
    }

    public function actionSaveLevel2Interest() {
        $params = Yii::$app->request->getBodyParams();
        $transaction=Yii::$app->db->beginTransaction();

        $level3_interest_id = array();

        if(!empty($params['level3Interests'])) {
            foreach($params['level3Interests'] as $key => $value) {
                if($value === true)
                    $level3_interest_id[] = $key;
            };
        }

        $this->saveInterestLevel2($params['level1Id'], $params['level2Id']);
        $this->saveInterestLevel3($params['level1Id'], $params['level2Id'], $level3_interest_id);

        $transaction->commit();

        return ['result'=>true];
    }


    public function actionDeleteInterest() {
        $params = Yii::$app->request->getBodyParams();
        $transaction=Yii::$app->db->beginTransaction();
        $model=Interest::findOne($params['interestId']);
        $this->deleteInterest($params['interestId']);
        $transaction->commit();
        return $this->getUserInterests($model->type);
    }


    public function actionDeleteLevel2Interest() {
        $params = Yii::$app->request->getBodyParams();
        $transaction=Yii::$app->db->beginTransaction();
        $interestLevel1Id = $params['interestLevel1Id'];
        $interestLevel2Id = $params['interestLevel2Id'];

        $this->deleteLevel2Interest($interestLevel1Id, $interestLevel2Id);

        $countLevel2 = UserInterest::find()
            ->where([
                'user_id' => Yii::$app->user->id,
                'level1_interest_id'=>$interestLevel1Id,
            ])
            ->count();

        if($countLevel2 == 0) {
            $this->saveInterestLevel1($interestLevel1Id);
        }

        $transaction->commit();
        return $this->getUserInterestsUpdate($interestLevel1Id);

    }

    public function actionDeleteLevel3Interest() {
        $params = Yii::$app->request->getBodyParams();
        $transaction=Yii::$app->db->beginTransaction();
        $interestLevel1Id = $params['interestLevel1Id'];
        $interestLevel2Id = $params['interestLevel2Id'];
        $interestLevel3Id = $params['interestLevel3Id'];

        $this->deleteLevel3Interest($interestLevel1Id, $interestLevel2Id, $interestLevel3Id);

        $countLevel3 = UserInterest::find()
            ->where([
                'user_id' => Yii::$app->user->id,
                'level1_interest_id'=>$interestLevel1Id,
                'level2_interest_id'=>$interestLevel2Id,
            ])
            ->count();

        if($countLevel3 == 0) {
            $this->saveInterestLevel2($interestLevel1Id,$interestLevel2Id);
        }

        $transaction->commit();
        return $this->getUserInterestsUpdate($interestLevel1Id);

    }

}
