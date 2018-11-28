<?php

namespace app\models;

use Yii;

class DefaultText extends \app\models\base\DefaultText {

    const SEARCH_REQUEST_DELETE = 'SEARCH_REQUEST_DELETE';
    const OFFER_DELETE = 'OFFER_DELETE';
    const INTERESTS_UPDATE = 'INTERESTS_UPDATE';
    const OFFER_VALIDATION_ACCEPTED = 'OFFER_VALIDATION_ACCEPTED';
    const OFFER_VALIDATION_REJECTED = 'OFFER_VALIDATION_REJECTED';
    const SEARCH_REQUEST_VALIDATION_ACCEPTED = 'SEARCH_REQUEST_VALIDATION_ACCEPTED';
    const SEARCH_REQUEST_VALIDATION_REJECTED = 'SEARCH_REQUEST_VALIDATION_REJECTED';

    //PLACEHOLDERS
    const PLACEHOLDER_SEARCH_REQUEST_DELETE = '{SUCHAUFTRAG}';
    const PLACEHOLDER_OFFER_DELETE = '{WERBUNG}';

    public $default_text_id;
    public $default_text_edit;


    public function attributeLabels() {
        return [
            'id' => Yii::t('app','ID'),
            'text' => Yii::t('app','Text'),
            'category' => Yii::t('app','Category'),
            'default_text_id' => Yii::t('app','Freigabegrund'),
            'default_text_edit' => Yii::t('app','Individuelle Nachricht')
        ];
    }

    public function rules() {
        return array_merge(parent::rules(),[
            ['default_text_id', 'required', 'on'=>'default-text'],
//            ['default_text_edit', 'required', 'when' => function ($model) { return !$model->default_text_id; },
//                'whenClient' => "function (attribute, value) {
//                    return attribute.\$form.find('select[name=\"DefaultText[default_text_id]\"]').val() == '';
//                }",
//            'on'=>'default-text-edit'],
            ['default_text_edit', 'required', 'on'=>'default-text-edit']
        ]);
    }

    public function scenarios() {
        $scenarios=parent::scenarios();
        $scenarios['default-text']=['default_text_id'];
        $scenarios['default-text-edit']=['default_text_id', 'default_text_edit'];
        return $scenarios;
    }

    public static function getCategoryList() {
        static $items;
        if (!isset($items)) {
            $items=[
                static::SEARCH_REQUEST_DELETE => Yii::t('app', 'Suchaufträge löschen'),
                static::OFFER_DELETE => Yii::t('app', 'Werbung löschen'),
                static::INTERESTS_UPDATE => Yii::t('app', 'Kategorie ändern'),
                static::OFFER_VALIDATION_ACCEPTED => Yii::t('app', 'Werbung freigegeben'),
                static::OFFER_VALIDATION_REJECTED => Yii::t('app', 'Werbung abgelehnt'),
                static::SEARCH_REQUEST_VALIDATION_ACCEPTED => Yii::t('app', 'Suchauftrag freigegeben'),
                static::SEARCH_REQUEST_VALIDATION_REJECTED => Yii::t('app', 'Suchauftrag abgelehnt'),
            ];
        }
        return $items;
    }

    public function getLabel() {
        return $this->getCategoryList()[$this->category];
    }

    public static function getCategoryLabel($category) {
        return static::getCategoryList()[$category];
    }

    public static function getDefaultTextList($category) {
        $texts = static::find()->where(['category'=>$category])->orderBy(['id'=>SORT_DESC])->all();
        $data = [];
        foreach ($texts as $item) {
            $data[$item->id] = $item->text;
        }
        return $data;
    }


}
