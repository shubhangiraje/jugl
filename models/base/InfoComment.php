<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "info_comment".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $info_id
 * @property string $dt
 * @property string $comment
 * @property integer $file_id
 * @property integer $votes_up
 * @property integer $votes_down
 * @property integer $lang
 * @property string $status
 * @property string $status_changed_dt
 * @property integer $status_changed_user_id
 *
 * @property User $user
 * @property Info $info
 * @property File $file
 * @property User $statusChangedUser
 * @property InfoCommentVote[] $infoCommentVotes
 * @property User[] $users
 */
class InfoComment extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'info_comment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'info_id', 'comment', 'lang'], 'required'],
            [['user_id', 'info_id', 'file_id', 'votes_up', 'votes_down', 'lang', 'status_changed_user_id'], 'integer'],
            [['dt', 'status_changed_dt'], 'safe'],
            [['comment', 'status'], 'string'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['info_id'], 'exist', 'skipOnError' => true, 'targetClass' => Info::className(), 'targetAttribute' => ['info_id' => 'id']],
            [['file_id'], 'exist', 'skipOnError' => true, 'targetClass' => File::className(), 'targetAttribute' => ['file_id' => 'id']],
            [['status_changed_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['status_changed_user_id' => 'id']]
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
    public function getInfo()
    {
        return $this->hasOne('\app\models\Info', ['id' => 'info_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFile()
    {
        return $this->hasOne('\app\models\File', ['id' => 'file_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatusChangedUser()
    {
        return $this->hasOne('\app\models\User', ['id' => 'status_changed_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInfoCommentVotes()
    {
        return $this->hasMany('\app\models\InfoCommentVote', ['info_comment_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany('\app\models\User', ['id' => 'user_id'])->viaTable('info_comment_vote', ['info_comment_id' => 'id']);
    }
}
