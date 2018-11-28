<?php

namespace app\components;

use Yii;
use Yii\helpers\ArrayHelper;
use \yii\helpers\Url;

class Helper {
    public static function toLoginedHashUrl($url,$absolute=false) {
        return Url::to(['site/login','forwardAfterLogin'=>Url::to(['site/my','#'=>$url])],$absolute);
    }

    public static function assocToRecords($data) {
        $res=[];
        foreach($data as $k=>$v) {
            $res[]=['key'=>$k,'val'=>$v];
        }

        return $res;
    }

    public static function getDaysList() {
        $res=[];
        for($i=1;$i<=31;$i++) {
            $res[$i]=$i;
        }

        return $res;
    }

    public static function getYearsList($fromDelta,$toDelta) {
        $from=date('Y')+$fromDelta;
        $to=date('Y')+$toDelta;

        $res=[];

        if ($from<=$to) {
            for ($i = $from; $i <= $to; $i++) {
                $res[$i] = $i;
            }
        } else {
            for ($i = $from; $i >= $to; $i--) {
                $res[$i] = $i;
            }
        }

        return $res;
    }

    public static function getHoursList() {
        $res=[];
        for($i=1;$i<=24;$i++) {
            if($i<10) {
                $res[$i]='0'.$i;
            } else {
                $res[$i]=$i;
            }
        }

        return $res;
    }

    public static function getMinutesList() {
        $res=[];
        $i = 0;
        while($i<=60) {
            if($i<10) {
                $res[$i]='0'.$i;
            } else {
                $res[$i]=$i;
            }
            $i = $i+5;
        }

        return $res;
    }

    public static function addEmptyValue($items,$emptyKey='',$emptyVal='') {
        $res=[$emptyKey=>$emptyVal];

        foreach($items as $k=>$v) {
            $res[$k]=$v;
        }

        return $res;
    }

    public static function getYearsFromToList($from,$to) {
        $res=[];

        for($i=$from;$i<=$to;$i++) {
            $res[$i]=$i;
        }

        return $res;
    }

    public static function getMonthsList() {
        return array(
            1=>Yii::t('app','Januar'),
            2=>Yii::t('app','Februar'),
            3=>Yii::t('app','März'),
            4=>Yii::t('app','April'),
            5=>Yii::t('app','Mai'),
            6=>Yii::t('app','Juni'),
            7=>Yii::t('app','Juli'),
            8=>Yii::t('app','August'),
            9=>Yii::t('app','September'),
            10=>Yii::t('app','Oktober'),
            11=>Yii::t('app','November'),
            12=>Yii::t('app','December'),
        );
    }

    public static function getStatusList() {
        return array(
            'all'=>Yii::t('app','Alles zeigen'),
            'positive'=>Yii::t('app','Eingang'),
            'negative'=>Yii::t('app','Ausgang'),
        );
    }

    public static function interestHierarchyBreadcrumbData($model,$type=null) {
        $data=[];

        if (!$type) {
            $type=$model->type;
        }

        do {
            $data=array_merge([[
                'label'=>Yii::t('app','Interest').' "'.$model.'"',
                'url'=>['admin-interest/update','id'=>$model->id]
            ]],$data);
            $model=$model->parent;
        } while ($model);

        return array_merge([[
            'label'=>$type=='OFFER' ? Yii::t('app','Interessen für Werbung'):Yii::t('app','Interessen für Suchaufträge'),
            'url'=>['admin-interest/index', 'type'=>$type]
            ]],
        $data);
    }

    public static function paramHierarchyBreadcrumbData($model) {
        return array_merge(
            static::interestHierarchyBreadcrumbData($model->interest),
            [[
                'label'=>Yii::t('app', 'Param "'.$model.'"'),
                'url'=>['admin-param/update','id'=>$model->id]
            ]]
        );
    }

    public static function getCountriesList() {
        $countries = [];
        foreach(\app\models\Country::getList() as $key => $value) {
            $countries[] = [
                'id'=>$key,
                'country'=>$value
            ];
        }
        return $countries;
    }

    public static function formatPrice($number) {
        $number=number_format($number,2,',','.');
        $number=preg_replace('/,00$/','',$number);

        return $number;
    }

    public static function formatPriceWithSmallPart($number) {
        $number=number_format($number,5,',','.');
        $number=preg_replace('/(,\d\d\d*)0+$/','$1',$number);
        $number=preg_replace('/,00$/','',$number);

        return $number;
    }

    public static function getLangList() {
        return [
            'de'=>'de',
            'en'=>'en',
            'ru'=>'ru'
        ];
    }


}

