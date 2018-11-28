<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "country".
 *
 * @property integer $id
 * @property string $country
 * @property integer $sort_order
 *
 * @property Offer[] $offers
 * @property Offer[] $offers0
 * @property SearchRequest[] $searchRequests
 * @property User[] $users
 * @property ZipCoords[] $zipCoords
 */
class Country extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'country';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['country'], 'required'],
            [['sort_order'], 'integer'],
            [['country'], 'string', 'max' => 50]
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOffers()
    {
        return $this->hasMany('\app\models\Offer', ['uf_country_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOffers0()
    {
        return $this->hasMany('\app\models\Offer', ['country_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSearchRequests()
    {
        return $this->hasMany('\app\models\SearchRequest', ['country_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany('\app\models\User', ['country_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getZipCoords()
    {
        return $this->hasMany('\app\models\ZipCoords', ['country_id' => 'id']);
    }
}
