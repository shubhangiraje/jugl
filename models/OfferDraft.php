<?php

namespace app\models;

use Yii;

class OfferDraft extends \app\models\base\OfferDraft
{
    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'user_id' => Yii::t('app','User ID'),
            'data' => Yii::t('app','Data'),
            'create_dt' => Yii::t('app','Create Dt')
        ];
    }

    public static function deleteDraft($id) {
        $model = static::findOne($id);
        if(!$model) {
            throw new NotFoundHttpException();
        }
        $model->delete();
        return true;
    }

}
