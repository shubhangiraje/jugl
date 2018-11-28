<?php

namespace app\models;

use Yii;

class Interest extends \app\models\base\Interest
{
    const COMMON_INTEREST_ID=685;
    const COMMON_INTEREST_ID2=685000;
    const TYPE_OFFER='OFFER';
    const TYPE_SEARCH_REQUEST='SEARCH_REQUEST';

    public function behaviors()
    {
        return [
            'sortable' => [
                'class' => \app\components\ModelSortableBehavior::className(),
            ],
        ];
    }


    public function __toString() {
        return $this->title;
    }

    public static function getLevel1List($type) {
        $items=[];
        foreach(static::find()->where('parent_id is null')->andWhere(['type'=>$type])->orderBy('title')->all() as $model) {
            $items[$model->id]=$model->title;
        }
        return $items;
    }

    public function getLevel() {
        $level=0;

        $object=$this;

        do {
            $level++;
            $object=$object->parent;
        } while ($object);

        return $level;
    }

    public function attributeLabels()
    {
        return [
            'title' => Yii::t('app','Title'),
            'file_id' => Yii::t('app','Image'),
            'offer_view_bonus' => Yii::t('app','Werbebonus pro User'),
			'search_request_bonus' => Yii::t('app','Vermittlungsbonus'),
            'offer_view_total_bonus' => Yii::t('app','Min. Budget für Werbeaktion '),
        ];
    }

    public function getShortData() {
        return $this->toArray(['id','title','offer_view_bonus','offer_view_total_bonus','search_request_bonus']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParams()
    {
        return $this->hasMany('\app\models\Param', ['interest_id' => 'id'])->orderBy('sort_order asc');
    }

    public function getParentsParams() {
        $params=[];

        $interest=$this->parent;
        while ($interest) {
            foreach($interest->params as $param) {
                $params[$param->id]=strval($param->title);
            }

            $interest=$interest->parent;
        }

        asort($params);
        return $params;
    }


}
