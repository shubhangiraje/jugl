<?php

namespace app\models;

use Yii;

class UserBecomeMemberInvitation extends \app\models\base\UserBecomeMemberInvitation
{
    public function getFormattedMs() {
        $hundredths=floor($this->ms/10);

        return sprintf("%'.02d\n", $hundredths);
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'user_id' => Yii::t('app','User ID'),
            'second_user_id' => Yii::t('app','Second User ID'),
            'dt' => Yii::t('app','Dt'),
            'ms' => Yii::t('app','Ms'),
        ];
    }
	public function getUserInvitedToday($userId){
		return $this->find()->where('is_winner=:is_winner', array(':is_winner'=>1))->andWhere('dt >= :dt',[':dt' => date('Y-m-d')])->andWhere('second_user_id = :user',[':user' => $userId])->count();
	}										  
}
