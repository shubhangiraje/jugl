<?php

namespace app\models;

use Yii;


class StickUserRequestForm extends \app\components\Model {
    public $user_id;
    public $text;

    public function rules() {
        return [
            ['text','required', 'message'=>Yii::t('app', 'Bitte gib einen Grundtext ein.')],
            ['user_id','userIdValidator']
        ];
    }

    public function userIdValidator($attributes,$params) {
        $user=\app\models\User::findOne($this->user_id);

        if (!$user || !Yii::$app->user->identity->canCreateStickRequest($user)) {
            $this->addError('user_id',Yii::t('app','You already sent request to this user'));
        }
    }

    public function attributeLabels() {
        return [
            'emails'=>Yii::t('app','E-Mail addresses'),
            'text'=>Yii::t('app','Text'),
        ];
    }

    public function save() {
        $model=new UserStickToParentRequest();
        $model->user_id=Yii::$app->user->id;
        $model->referral_user_id=$this->user_id;
        $model->expires_at=(new \app\components\EDateTime())->modify('+'.Setting::get('STICK_TO_PARENT_REQUEST_RESPONSE_TIME').' HOUR')->sql();
        $model->save();
        UserEvent::addUserStickRequest($this);
    }
}