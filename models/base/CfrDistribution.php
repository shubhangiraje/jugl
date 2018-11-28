<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "cfr_distribution".
 *
 * @property integer $id
 * @property string $dt
 * @property integer $votes_count
 * @property string $jugl_sum
 * @property string $jugl_sum_fact
 *
 * @property CfrDistributionUser[] $cfrDistributionUsers
 */
class CfrDistribution extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cfr_distribution';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dt', 'votes_count', 'jugl_sum', 'jugl_sum_fact'], 'required'],
            [['dt'], 'safe'],
            [['votes_count'], 'integer'],
            [['jugl_sum', 'jugl_sum_fact'], 'number']
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCfrDistributionUsers()
    {
        return $this->hasMany('\app\models\CfrDistributionUser', ['cfr_distribution_id' => 'id']);
    }
}
