<?php

namespace app\components;

use Yii;
use yii\base\Behavior;
use yii\helpers\Html;
use yii\db\BaseActiveRecord;


class ModelSortableBehavior extends Behavior {

    public function move($pos, $where=[], $whereParams=[]) {
        $model = $this->owner;
        if (!$model)
            return;

        if ($pos==='up' || $pos==='down') {
            $query = $this->owner->find()->where($where,$whereParams);
            if ($pos==='up') {
                $query->andWhere('sort_order<:sort_order', [':sort_order' => $model->sort_order]);
                $query->orderBy('sort_order desc');
            } else {
                $query->andWhere('sort_order>:sort_order', [':sort_order' => $model->sort_order]);
                $query->orderBy('sort_order asc');
            }
            $model2 = $query->one();

            if (!$model2)
                return;

            $trx = Yii::$app->db->beginTransaction();

            $tmp = $model2->sort_order;
            $model2->sort_order = $model->sort_order;
            $model->sort_order = $tmp;

            $model->save();
            $model2->save();

            $trx->commit();
        }
    }

    public function events()
    {
        return [
                BaseActiveRecord::EVENT_AFTER_INSERT => 'afterInsert'
        ];
    }

    public function afterInsert($event) {
        $this->owner->sort_order=$this->owner->id;
        $this->owner->save();
    }

    public static function actionColumnSortingButtons() {
        return [
            'moveUp' => function($url, $model, $key) {
                preg_match('%^/([^/]+)/%', $url, $m);
                $controller = $m[1];

                return Html::a(
                    '<span class="glyphicon glyphicon-arrow-up"></span>',
                    ["$controller/move",'id'=>$model->id,'pos'=>'up','pjaxForcePost'=>1],
                    [
                        'title' => Yii::t('app', 'Move Up'),
                    ]
                );
            },
            'moveDown' => function($url, $model, $key) {
                preg_match('%^/([^/]+)/%',$url,$m);
                $controller=$m[1];

                return Html::a(
                    '<span class="glyphicon glyphicon-arrow-down"></span>',
                    ["$controller/move",'id'=>$model->id,'pos'=>'down','pjaxForcePost'=>1],
                    [
                        'title' => Yii::t('app', 'Move Down'),
                    ]
                );
            },
        ];
    }
}