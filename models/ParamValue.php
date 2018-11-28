<?php

namespace app\models;

use Yii;

class ParamValue extends \app\models\base\ParamValue
{
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

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'param_id' => Yii::t('app','Param ID'),
            'title' => Yii::t('app','Title'),
            'sort_order' => Yii::t('app','Sort Order'),
        ];
    }
}
