<?php

namespace app\controllers;

use Yii;
use app\models\Interest;
use app\models\UserInterest;


class ExtApiInterestsController extends \app\components\ExtApiController {


    private function getInterestsLevelData($parent_id = NULL, $type = NULL) {

        $interests = Interest::findOne(['id'=> $parent_id]);
        $levelParentId = $interests->parent_id;

        $conditions=['parent_id'=>$parent_id];

        if ($type) {
            $conditions['type']=$type;
        }

        if (!$type && !$parent_id) {
            $conditions['type']='OFFER';
        }

        $interests = Interest::find()
            ->with(['file'])
            ->where($conditions)
            ->all();

        $data = [];

        foreach ($interests as $item) {
            $thumb = $item->file->link ? $thumb = $item->file->getThumbUrl('interestMobile') : $thumb = \app\components\Thumb::createUrl('/static/images/account/default_interest.png','interestMobile',true);

            $idata = [
                'interest_id' => $item->id,
                'interest_title' => $item->title,
                'interest_sort'=>$item->sort_order,
            ];

            if(!$levelParentId) {
                $idata['interest_img'] = $thumb;
                $idata['isChildInterests'] = boolval($item->interests);
            }

            $data[] = $idata;
        }

        return $data;
    }


    private function getInterestLevel3Selected($parent_id) {
        $user_interest = UserInterest::find()
            ->select(['level3_interest_id'])
            ->where(['user_id'=>Yii::$app->user->id,'level2_interest_id'=>$parent_id])
            ->groupBy(['level3_interest_id'])
            ->all();

        $level3Interests = [];

        foreach ($user_interest as $item) {
            if($item->level3_interest_id != NULL)
                $level3Interests[$item->level3_interest_id] = true;
        }

        return $level3Interests;
    }

    private function getIterestLevel($parent_id) {
        $interests = Interest::findOne(['id'=> $parent_id]);
        $levelParentId = $interests->parent_id;

        $parentInterests = Interest::findOne(['id'=> $levelParentId]);

        if($parentInterests) {
            return [
                'level1_id' => $parentInterests->id,
                'level1_title' => $parentInterests->title,
                'level2_id' => $interests->id,
                'level2_title' => $interests->title,
            ];

        } else {
            return [
                'level1_id'=> $interests->id,
                'level1_title'=> $interests->title,
            ];
        }

    }


    public function actionAddStep1() {
        return [
            'interests' => $this->getInterestsLevelData(null,$_REQUEST['type'])
        ];
    }


    public function actionAddStep2($parent_id) {
        return [
            'interests' => $this->getInterestsLevelData($parent_id),
            'level_interests' => $this->getIterestLevel($parent_id)
        ];
    }


    public function actionAddStep3($parent_id) {
        return [
            'interests' => $this->getInterestsLevelData($parent_id),
            'level_interests' => $this->getIterestLevel($parent_id),
            'level3Interests' => $this->getInterestLevel3Selected($parent_id)
        ];
    }

    public function actionSearchesAddStep3($parent_id) {
        return [
            'interests' => $this->getInterestsLevelData($parent_id),
            'level_interests' => $this->getIterestLevel($parent_id),
        ];
    }


}
