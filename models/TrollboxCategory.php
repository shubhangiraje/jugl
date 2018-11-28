<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

class TrollboxCategory extends \app\models\base\TrollboxCategory
{
    /**
    * @inheritdoc
    */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app','ID'),
            'title' => Yii::t('app','Title'),
            'sort_order' => Yii::t('app','Sort Order'),
        ];
    }

    public function behaviors() {
        return [
            'sortable' => [
                'class' => \app\components\ModelSortableBehavior::className(),
            ],
        ];
    }

    public static function getList() {
        $categories = static::find()
            ->orderBy(['sort_order'=>SORT_ASC])
            ->all();

        $data = [];
        foreach ($categories as $category) {
            $idata['title']=$category->title;
            $idata['id']=intval($category->id);
            $data[]=$idata;
        }
        return $data;
    }

    public static function getListFromCountry($country_ids) {
        $categories = Yii::$app->db->createCommand('
            SELECT trollbox_category.id ,trollbox_category.title, tmp.count_message
            FROM trollbox_category
            LEFT JOIN (
               SELECT trollbox_category_id, COUNT(id) as count_message
               FROM trollbox_message 
               WHERE trollbox_message.trollbox_category_id IS NOT NULL
                  AND trollbox_message.status=:status
                  '.(!empty($country_ids)?'AND trollbox_message.country IN ('.implode(',',$country_ids).')':'').'
               GROUP by trollbox_message.trollbox_category_id
            ) as tmp ON tmp.trollbox_category_id = trollbox_category.id
            ORDER BY trollbox_category.sort_order ASC
        ', [
            ':status'=>TrollboxMessage::STATUS_ACTIVE
        ])->queryAll();

        $data = [];
        foreach ($categories as $category) {
            $idata['title']=$category['title'];
            $idata['id']=intval($category['id']);
            $idata['count_message']=intval($category['count_message']);
            $data[]=$idata;
        }
        return $data;
    }


    public static function getFrontList() {
        $categories = static::find()
            ->orderBy(['sort_order'=>SORT_ASC])
            ->all();

        $data = ArrayHelper::map($categories, 'id', 'title');
        return $data;
    }


}
