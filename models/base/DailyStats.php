<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "daily_stats".
 *
 * @property string $dt
 * @property integer $packet_upgrades
 */
class DailyStats extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'daily_stats';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dt'], 'required'],
            [['dt'], 'safe'],
            [['packet_upgrades'], 'integer']
        ];
    }

}
