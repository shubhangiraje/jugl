<?php

namespace app\models;

use Yii;

class TokenDepositGuarantee extends \app\models\base\TokenDepositGuarantee
{
    const STATUS_ACTIVE='ACTIVE';
    const STATUS_DELETED='DELETED';
    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'title_de' => Yii::t('app','Title'),
            'title_en' => Yii::t('app','Title'),
            'title_ru' => Yii::t('app','Title'),
            'description_de' => Yii::t('app','Description'),
            'description_en' => Yii::t('app','Description'),
            'description_ru' => Yii::t('app','Description'),
            'sum_cost' => Yii::t('app','Price'),
            'sum' => Yii::t('app','Bereits festgelegt'),
            'show' => Yii::t('app','Aktiv')
        ];
    }

    public static function getList() {
        $items=static::find()->andWhere(['status'=>static::STATUS_ACTIVE])
            ->andWhere('`show`=1 and sum*:token_to_euro<sum_cost',[':token_to_euro'=>\app\models\Setting::get('TOKEN_TO_EURO_EXCHANGE_RATE')])
            ->indexBy('id')->all();

        $result=[];
        foreach($items as $item) {
            $result[$item['id']]=$item['id'];
        }

        return $result;
    }
}


\yii\base\Event::on(TokenDeposit::className(), \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE, function ($event) {
    $event->sender->beforeUpdate();
});

