<?php

namespace app\models;

use Yii;

class Param extends \app\models\base\Param
{
    const TYPE_LIST='LIST';
    const TYPE_TEXT='TEXT';

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

    public static function getTypeList() {
        static $items;

        if (!isset($items)) {
            $items=[
                static::TYPE_LIST=>Yii::t('app','List'),
                static::TYPE_TEXT=>Yii::t('app','Text')
            ];
        }

        return $items;
    }

    public function getParamValuesList() {
        $data=[];
        foreach($this->paramValues as $pv) {
            $data[$pv->id]=strval($pv);
        }

        return $data;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParamValues()
    {
        return $this->hasMany('\app\models\ParamValue', ['param_id' => 'id'])->orderBy('sort_order asc');
    }

    public function attributeLabels()
    {
        return [
            'title' => Yii::t('app','Title'),
            'type' => Yii::t('app','Type'),
        ];
    }
}
