<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "annecy_reward".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $dt
 * @property string $credits
 * @property string $campaign_title
 * @property string $click_id
 *
 * @property User $user
 */
class AnnecyReward extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'annecy_reward';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['dt'], 'safe'],
            [['credits'], 'number'],
            [['campaign_title', 'click_id'], 'string', 'max' => 256],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']]
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne('\app\models\User', ['id' => 'user_id']);
    }
}
