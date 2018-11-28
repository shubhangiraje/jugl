<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "zip_coords".
 *
 * @property integer $country_id
 * @property string $zip
 * @property string $lattitude
 * @property string $longitude
 *
 * @property Country $country
 */
class ZipCoords extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'zip_coords';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['country_id', 'zip', 'lattitude', 'longitude'], 'required'],
            [['country_id'], 'integer'],
            [['lattitude', 'longitude'], 'number'],
            [['zip'], 'string', 'max' => 16]
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne('\app\models\Country', ['id' => 'country_id']);
    }
}
