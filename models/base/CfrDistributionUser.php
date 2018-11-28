<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "cfr_distribution_user".
 *
 * @property integer $id
 * @property integer $cfr_distribution_id
 * @property integer $user_id
 * @property integer $votes_count
 * @property string $jugl_sum
 * @property integer $processed
 *
 * @property CfrDistribution $cfrDistribution
 * @property User $user
 */
class CfrDistributionUser extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cfr_distribution_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'cfr_distribution_id', 'user_id', 'votes_count', 'jugl_sum'], 'required'],
            [['id', 'cfr_distribution_id', 'user_id', 'votes_count', 'processed'], 'integer'],
            [['jugl_sum'], 'number'],
            [['cfr_distribution_id'], 'exist', 'skipOnError' => true, 'targetClass' => CfrDistribution::className(), 'targetAttribute' => ['cfr_distribution_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']]
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCfrDistribution()
    {
        return $this->hasOne('\app\models\CfrDistribution', ['id' => 'cfr_distribution_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne('\app\models\User', ['id' => 'user_id']);
    }
}
