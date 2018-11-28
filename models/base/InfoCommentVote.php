<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "info_comment_vote".
 *
 * @property integer $info_comment_id
 * @property integer $user_id
 * @property integer $vote
 *
 * @property InfoComment $infoComment
 * @property User $user
 */
class InfoCommentVote extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'info_comment_vote';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['info_comment_id', 'user_id', 'vote'], 'required'],
            [['info_comment_id', 'user_id', 'vote'], 'integer'],
            [['info_comment_id'], 'exist', 'skipOnError' => true, 'targetClass' => InfoComment::className(), 'targetAttribute' => ['info_comment_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']]
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInfoComment()
    {
        return $this->hasOne('\app\models\InfoComment', ['id' => 'info_comment_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne('\app\models\User', ['id' => 'user_id']);
    }
}
