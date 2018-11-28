<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "user_interest".
 *
 * @property integer $user_id
 * @property integer $level1_interest_id
 * @property integer $level2_interest_id
 * @property integer $level3_interest_id
 * @property string $type
 *
 * @property User $user
 * @property Interest $level1Interest
 * @property Interest $level2Interest
 * @property Interest $level3Interest
 */
class UserInterest extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_interest';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'level1_interest_id', 'type'], 'required'],
            [['user_id', 'level1_interest_id', 'level2_interest_id', 'level3_interest_id'], 'integer'],
            [['type'], 'string'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['level1_interest_id'], 'exist', 'skipOnError' => true, 'targetClass' => Interest::className(), 'targetAttribute' => ['level1_interest_id' => 'id']],
            [['level2_interest_id'], 'exist', 'skipOnError' => true, 'targetClass' => Interest::className(), 'targetAttribute' => ['level2_interest_id' => 'id']],
            [['level3_interest_id'], 'exist', 'skipOnError' => true, 'targetClass' => Interest::className(), 'targetAttribute' => ['level3_interest_id' => 'id']]
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne('\app\models\User', ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLevel1Interest()
    {
        return $this->hasOne('\app\models\Interest', ['id' => 'level1_interest_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLevel2Interest()
    {
        return $this->hasOne('\app\models\Interest', ['id' => 'level2_interest_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLevel3Interest()
    {
        return $this->hasOne('\app\models\Interest', ['id' => 'level3_interest_id']);
    }
}
