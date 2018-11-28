<?php

namespace app\models;

use Yii;
use app\models\Invitation;


class RegistrationCodeForm extends \app\components\Model
{
    public $refId;
    public $invId;
    public $code;
    

    public function rules()
    {
        return [
            [
                ['invId'],
                'invitationIdValidator'
            ],
            [
                ['refId'],
                'exist',
                'targetClass'=>'app\models\User',
                'targetAttribute'=>'id',
            ],
            /*
            [
                ['code'],
                'required',
                'when'=>function($model) {
                    return $model->refId=='' && !$model->invId;
                }
            ],
            */
            [
                ['code'],
                'exist',
                'targetClass'=>'app\models\RegistrationCode',
                'targetAttribute'=>'code',
                'filter'=>'referral_user_id is null',
                'skipOnEmpty'=>false,
                'when'=>function($model) {
                    return $model->refId=='' && !$model->invId;
                }
            ],
            [
                ['refId'],
                'freeRegistrationsLimitCheck'
            ],
        ];
    }

    public function freeRegistrationsLimitCheck($attribute,$params) {
        $parent=\app\models\User::findOne($this->refId);

        if ($parent && $parent->getRegistrationsLimit()<=$parent->free_registrations_used) {
            $this->addError('refId', Yii::t('app', 'Link nicht mehr aktiv. Bitte wende dich an deinen einladenden Kontakt'));
        }
    }

    public function invitationIdValidator($attribute,$params) {
        $invitation=Invitation::findOne($this->$attribute);
        if (!$invitation || !in_array($invitation->status,[Invitation::STATUS_OPEN,Invitation::STATUS_CLICKED])) {
            $this->addError($attribute,Yii::t('app','Der eingegebene Einladungscode ist falsch bzw. wurde bereits verwendet'));
            return;
        }

        if ($invitation->status!=Invitation::STATUS_CLICKED) {
            $invitation->status=Invitation::STATUS_CLICKED;
            $invitation->save();
        }

        $this->refId=$invitation->user_id;
    }

    public function attributeLabels() {
        return [
            'code'=>Yii::t('app','Registration code'),
        ];
    }
}
