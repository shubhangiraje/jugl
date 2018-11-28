<?php

namespace app\models;

use Yii;

class BalanceTokenLog extends \app\models\base\BalanceTokenLog
{
    const TYPE_PAYOUT='PAYOUT';
    const TYPE_PAYIN='PAYIN';
    const TYPE_OUT='OUT';
    const TYPE_EVE='EVE';
    const TYPE_IN='IN';
   // const TYPE_INPUT='IN';
    const TYPE_IN_REF='IN_REF';
    const TYPE_IN_REF_REF='IN_REF_REF';
    const TYPE_IN_REG_REF='IN_REG_REF';
    const TYPE_IN_REG_REF_REF='IN_REG_REF_REF';

    public $stat_count;
    public $stat_sum_plus;
    public $stat_sum_minus;

    static function getTypeList() {
        static $items;
        if (!isset($items)) {
            $items = [
               // static::TYPE_PAYOUT => Yii::t('app', 'Auszahlung'),
               // static::TYPE_PAYIN => Yii::t('app', 'Einzahlung'),
                static::TYPE_EVE => Yii::t('app', 'Alles zeigen'),
                static::TYPE_IN => Yii::t('app','Eingänge'),
                static::TYPE_OUT => Yii::t('app', 'Ausgänge'),


              //  static::TYPE_IN => Yii::t('app', 'Eingang'),
               // static::TYPE_IN_REF => Yii::t('app', 'Eingang'),
                // static::TYPE_IN_REF_REF => Yii::t('app', 'Eingang'),
                // static::TYPE_IN_REG_REF => Yii::t('app', 'Eingang'),
                // static::TYPE_IN_REG_REF_REF => Yii::t('app', 'Eingang')
            ];
        }
        return $items;
    }

    public function gettypeLabel() {
        return $this->getTypeList()[$this->type];
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(),[
            'stat_count'=>Yii::t('app','Transaktionen durch'),
            'stat_sum_plus'=>Yii::t('app','Eingang durch'),
            'stat_sum_minus'=>Yii::t('app','Ausgang durch'),
        ]);
    }
    
   public function commentData()
    {
        $query =(new \yii\db\Query())
            ->select(['comment'])
            ->from('balance_token_log')
            ->all();
            $data = array();
        foreach ($query as $comment_array) {
            array_push($data, $comment_array['comment']);
        }    
        return $data;
    }
}
