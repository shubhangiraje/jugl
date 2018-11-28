<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "news".
 *
 * @property integer $id
 * @property integer $image_file_id
 * @property string $dt
 * @property string $title_de
 * @property string $title_en
 * @property string $title_ru
 * @property string $text_de
 * @property string $text_en
 * @property string $text_ru
 *
 * @property File $imageFile
 */
class News extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'news';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title_de', 'text_de'], 'required'],
            [['image_file_id'], 'integer'],
            [['dt'], 'safe'],
            [['text_de','text_en','text_ru'], 'string'],
            [['title_de','title_en','title_ru'], 'string', 'max' => 256]
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImageFile()
    {
        return $this->hasOne('\app\models\File', ['id' => 'image_file_id']);
    }
}
