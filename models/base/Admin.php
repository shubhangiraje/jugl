<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "admin".
 *
 * @property integer $id
 * @property string $type
 * @property string $email
 * @property string $password
 * @property string $first_name
 * @property string $last_name
 * @property string $status
 * @property string $access_token
 * @property string $auth_key
 * @property integer $failed_logins
 * @property integer $access_dashboard
 * @property integer $access_user_view
 * @property integer $access_user_update
 * @property integer $access_user_validation
 * @property integer $access_payouts
 * @property integer $access_interests
 * @property integer $access_search_request_view
 * @property integer $access_search_request_validate
 * @property integer $access_search_request_update
 * @property integer $access_offer_view
 * @property integer $access_offer_validate
 * @property integer $access_offer_update
 * @property integer $access_settings
 * @property integer $access_broadcast
 * @property integer $access_news
 *
 * @property AdminActionLog[] $adminActionLogs
 * @property AdminSessionLog[] $adminSessionLogs
 * @property BalanceLogMod[] $balanceLogMods
 */
class Admin extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'admin';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'email', 'password', 'first_name', 'last_name', 'status', 'access_token', 'auth_key'], 'required'],
            [['type', 'status'], 'string'],
            [['failed_logins', 'access_dashboard', 'access_user_view', 'access_user_update', 'access_user_validation', 'access_payouts', 'access_interests', 'access_search_request_view', 'access_search_request_validate', 'access_search_request_update', 'access_offer_view', 'access_offer_validate', 'access_offer_update', 'access_settings', 'access_broadcast', 'access_news', 'access_translator'], 'integer'],
            [['email'], 'string', 'max' => 128],
            [['password', 'first_name', 'last_name'], 'string', 'max' => 64],
            [['access_token', 'auth_key'], 'string', 'max' => 32],
            [['email'], 'unique']
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdminActionLogs()
    {
        return $this->hasMany('\app\models\AdminActionLog', ['admin_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdminSessionLogs()
    {
        return $this->hasMany('\app\models\AdminSessionLog', ['admin_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBalanceLogMods()
    {
        return $this->hasMany('\app\models\BalanceLogMod', ['admin_id' => 'id']);
    }
}
