<?php

namespace app\components;


class Model extends \yii\base\Model {

    protected static function cachedAttributeLabels($classes=[]) {
        static $labels;

        if (empty($classes)) $classes=[self::className()];

        $result=[];
        foreach($classes as $class) {
            if (!isset($attributes[$class])) {
                $model=new $class;
                // get attributes labels
                $labels[$class]=$model->attributeLabels();
                // get labels for other attributes
                foreach($model->attributes as $attribute=>$value) {
                    if (!isset($labels[$class][$attribute])) {
                        $labels[$class][$attribute] = $model->getAttributeLabel($attribute);
                    }
                }
            }
            $result=array_merge($result,$labels[$class]);
        }

        return $result;
    }

    public static function getCachedAttributeLabel($attribute) {
        $labels=self::cachedAttributeLabels();
        return $labels[$attribute];
    }

    public static function getEncodedAttributeLabel($attribute) {
        return \yii\helpers\Html::encode(static::getCachedAttributeLabel($attribute));
    }

    public function __sleep() {
        // when serializing, store only attributes
        return array_keys($this->attributes);
    }
}