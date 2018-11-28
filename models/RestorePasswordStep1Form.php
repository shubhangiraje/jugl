<?php

namespace app\models;

use Yii;


class RestorePasswordStep1Form extends \app\components\Model
{
	public $email;
    public $verifyCode;

	/**
	 * @return array the validation rules.
	 */
	public function rules()
	{
		return [
            [['email','verifyCode'], 'required'],
            ['verifyCode', 'captcha'],
			[['email'], 'exist','skipOnError'=>true,'targetClass'=>'app\models\User','targetAttribute'=>'email','message'=>Yii::t('app', 'Es existiert kein Mitglied mit dieser E-Mail-Adresse.')],
		];
	}

    public function attributeLabels() {
        return [
            'email'=>Yii::t('app','E-Mail'),
            'verifyCode'=>Yii::t('app','Code eingeben')
        ];
    }

    public function sendRestoreCode($data) {
        if ($this->load($data) && $this->validate()) {
            $trx=Yii::$app->db->beginTransaction();

            $code=AccessCode::generateCode(AccessCode::TYPE_RESTORE_PASSWORD,$this->email);
            $link=\yii\helpers\Url::toRoute(['site/restore-password-step2','object'=>$this->email,'code'=>$code],true);

            $res=Yii::$app->mailer->sendEmail($this->email,'restore-password-code',['link'=>$link,'model'=>$this]);
            if ($res) {
                $trx->commit();
            } else {
                $this->addError('email',Yii::t('app','An error occured while sending E-Mail'));
            }

            return $res;
        }

        return false;
    }
}
