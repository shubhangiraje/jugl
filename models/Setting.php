<?php

namespace app\models;

use Yii;
use \yii\base\Exception;

class Setting extends \app\models\base\Setting
{
    const TYPE_FLOAT='float';
    const TYPE_INT='int';
    const TYPE_STRING='string';
    const TYPE_BOOL='bool';

    const TOKEN_DEPOSIT_TOKEN_TO_JUGL_EXCHANGE_RATE=1;
    const TOKEN_DEPOSIT_TOKEN_TO_EURO_EXCHANGE_RATE=0.01;

    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('app','Funktion'),
            'type' => Yii::t('app','Type'),
            'value' => Yii::t('app','Value'),
            'title' => Yii::t('app', 'Funktion'),
        ];
    }

    public function rules() {
        return array_merge(parent::rules(),[
            ['value','integer','enableClientValidation'=>false,'when'=>function($model) {return $model->type==static::TYPE_INT;}],
            ['value','number','enableClientValidation'=>false,'when'=>function($model) {return $model->type==static::TYPE_FLOAT;}],
        ]);
    }

    public function scenarios() {
        return [static::SCENARIO_DEFAULT=>['value']];
    }

    public function __toString() {
        return $this->title;
    }

    public static function get($name) {
        static $cache=[];

        if (!$cache[$name]) {
            $cache[$name]=static::findOne($name);
        }

        $setting=$cache[$name];

        if (!$setting) {
            throw new Exception("Setting '$name' not found");
        }

        $value=$setting->value;
        switch ($setting->type) {
            case static::TYPE_FLOAT:
                $value=floatval($value);
                break;
            case static::TYPE_INT:
                $value=intval($value);
                break;
            case static::TYPE_BOOL:
                $value=$value==1;
                break;
        }

        return $value;
    }

    public static function getDashboardForumText() {
        $lang = mb_strtoupper(Yii::$app->language);
        $setting = 'DASHBOARD_FORUM_TEXT_'.$lang;
        $res = static::findOne($setting);
        return $res->value;
    }
}
