<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "file".
 *
 * @property integer $id
 * @property string $dt
 * @property string $link
 * @property integer $size
 * @property string $name
 * @property string $ext
 *
 * @property User[] $users
 */
class File extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'file';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dt'], 'safe'],
            [['link', 'size', 'name', 'ext'], 'required'],
            [['size'], 'integer'],
            [['link', 'name', 'ext'], 'string', 'max' => 256]
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany('\app\models\User', ['avatar_file_id' => 'id']);
    }
}
