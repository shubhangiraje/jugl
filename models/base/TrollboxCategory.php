<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "trollbox_category".
 *
 * @property integer $id
 * @property string $title
 * @property integer $sort_order
 */
class TrollboxCategory extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'trollbox_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['sort_order'], 'integer'],
            [['title'], 'string', 'max' => 200]
        ];
    }

}
