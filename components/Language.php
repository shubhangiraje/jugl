<?php

namespace app\components;
use Yii;

class Language {

    const DEFAULT_LANGUAGE = 'de';

    public static function getList() {
        return Yii::$app->params['languages'];
    }

    public static function getLanguage() {
        $data = [];

        if (($list = strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']))) {
            if (preg_match_all('/([a-z]{1,8}(?:-[a-z]{1,8})?)(?:;q=([0-9.]+))?/', $list, $list)) {
                $data = array_combine($list[1], $list[2]);
                foreach ($data as $n => $v)
                    $data[$n] = $v ? $v : 1;
                arsort($data, SORT_NUMERIC);
            }
        }

        reset($data);
        $lang = key($data);

        if (stristr($lang, '-')) {
            list($lang) = explode('-', $lang);
        }

        $result = in_array($lang, static::getList()) ? $lang : static::DEFAULT_LANGUAGE;
        return $result;
    }

    public static function setLanguage() {
        if(!isset(Yii::$app->session['language'])) {
            Yii::$app->session['language'] = static::getLanguage();
        }
        Yii::$app->language = Yii::$app->session['language'];
    }
}