<?php

namespace app\models;

use Yii;

class UserStickToParentRequest extends \app\models\base\UserStickToParentRequest
{
    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'user_id' => Yii::t('app','User ID'),
            'referral_user_id' => Yii::t('app','Referral User ID'),
            'expires_at' => Yii::t('app','Expires At'),
        ];
    }

    public static function processExpired() {
        foreach(static::find()->where('expires_at<=NOW() and completed=0')->with(['referralUser'])->all() as $model) {
            $trx=Yii::$app->db->beginTransaction();

            $model->completed=1;
            $model->save();
            \app\models\UserEvent::addStickParentRequestExpired($model->referralUser);

            $trx->commit();
        }
    }
}
