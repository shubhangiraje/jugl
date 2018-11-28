<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\KnownDevice;


class KnownDeviceSearch extends KnownDevice {

    public function rules() {
        return [
            [['id'], 'integer'],
            [['device_uuid'], 'safe'],
        ];
    }

    public function scenarios() {
        return Model::scenarios();
    }

    public function search($params) {
        $query = KnownDevice::find()
            ->innerJoin('known_device kd','kd.device_uuid=known_device.device_uuid and kd.id<>known_device.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder'=>['known_device.id'=>SORT_DESC]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'known_device.device_uuid', $this->device_uuid]);

        return $dataProvider;

    }
}