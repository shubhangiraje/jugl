<?php

namespace app\models;

use app\components\EDateTime;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TrollboxMessage;

/**
 * UserSearch represents the model behind the search form about `app\modules\foms\models\User`.
 */
class TrollboxMessageSearch extends TrollboxMessage
{
    public $create_dt_from;
    public $create_dt_to;
    public $user_name;
    public $visible;
    public $video_identification_status;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['create_dt_from','create_dt_to','country','status','trollbox_category_id','user_name','text','visible','video_identification_status','device_uuid'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params, $type = TrollboxMessage::TYPE_FORUM)
    {
        $query = TrollboxMessageSearch::find()
            ->joinWith(['user','trollboxCategory'])
            ->where(['type'=>$type]);

        if ($type==TrollboxMessage::TYPE_VIDEO_IDENTIFICATION) {
            $query->andWhere(['trollbox_message.status'=>TrollboxMessage::STATUS_ACTIVE]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes'=>[
					'country',
                    'id',
                    'status',
                    'dt',
                    'is_sticky',
                    'votes_up',
                    'votes_down',
                    'device_uuid'
                ],
                'defaultOrder'=>['is_sticky'=>SORT_DESC,'id'=>SORT_DESC]
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'trollbox_message.status' => $this->status,
        ]);
		
		$query->andFilterWhere([
            'trollbox_message.country' => $this->country,
        ]);

        if ($this->create_dt_from!='') {
            $query->andWhere('dt>=:dt_from',[
                ':dt_from'=>(new EDateTime($this->create_dt_from))->sqlDate()
            ]);
        }

        if ($this->create_dt_to!='') {
            $query->andWhere('dt<=:dt_to',[
                ':dt_to'=>(new EDateTime($this->create_dt_to))->modify('+1 day')->sqlDate()
            ]);
        }

        if ($this->user_name!='') {
            $query->andWhere('user.first_name like(:name) or user.last_Name like(:name) or user.company_name like(:name)',[':name'=>'%'.$this->user_name.'%']);
        }

        $query->andFilterWhere(['like', 'text', $this->text])
              ->andFilterWhere(['like', 'device_uuid', $this->device_uuid]);

        $query->andFilterWhere([
            'trollbox_message.trollbox_category_id' => $this->trollbox_category_id,
        ]);

        if ($this->visible!='') {
            switch ($this->visible) {
                case TrollboxMessage::FILTER_ALL:
                    $query->andFilterWhere(['trollbox_message.visible_for_all' => 1]);
                    break;
                case TrollboxMessage::FILTER_FOLLOWING:
                    $query->andFilterWhere(['trollbox_message.visible_for_followers' => 1]);
                    break;
                case TrollboxMessage::FILTER_CONTACTS:
                    $query->andFilterWhere(['trollbox_message.visible_for_contacts' => 1]);
                    break;
            }
        }

        if ($type==TrollboxMessage::TYPE_VIDEO_IDENTIFICATION) {
            if ($this->video_identification_status!='') {
                $query->andWhere('user.video_identification_status=:video_identification_status',[':video_identification_status'=>$this->video_identification_status]);
            }
        }


        return $dataProvider;
    }
}