<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "chat_group_num".
 *
 * @property integer $id
 */
class ChatGroupNum extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'chat_group_num';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            
        ];
    }

}
