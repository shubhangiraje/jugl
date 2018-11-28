<?php

namespace app\components;

use Yii;
use yii\helpers\Html;


class ActionColumn extends \yii\grid\ActionColumn {
    public $options = ['style'=>'width:120px'];

    public function init() {
        $this->buttons['delete']= function($url, $model, $key) {
            $params=[
                'title' => Yii::t('app', 'Delete'),
                'onclick' => 'if (!confirm("'.Yii::t('app','Do you really want to delete this item?').'")) {event.preventDefault();event.stopPropagation();}',
            ];

            if (!$this->grid->pjax) {
                $params['data-method']='post';
            }

            return Html::a(
                '<span class="glyphicon glyphicon-trash"></span>',
                $url.'&pjaxForcePost=1',
                $params
            );
        };

        $this->buttons['update']= function($url, $model, $key) {
            return Html::a(
                '<i class="glyphicon glyphicon-pencil"></i>',

                $url.(property_exists($model,"type")? '&type='.$model->type : ''),

                [
                    'title' => Yii::t('app', 'Update'),
                    'data-pjax' => 0
                ]
            );
        };

        parent::init();
    }
}