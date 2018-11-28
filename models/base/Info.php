<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "info".
 *
 * @property integer $id
 * @property string $view
 * @property string $title_de
 * @property string $title_en
 * @property string $title_ru
 * @property string $description_de
 * @property string $description_en
 * @property string $description_ru
 *
 * @property InfoComment[] $infoComments
 */
class Info extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['view', 'description_de'], 'required'],
            [['description_de', 'description_en', 'description_ru'], 'string'],
            [['view'], 'string', 'max' => 64],
            [['title_de', 'title_en', 'title_ru'], 'string', 'max' => 256]
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInfoComments()
    {
        return $this->hasMany('\app\models\InfoComment', ['info_id' => 'id']);
    }
}
