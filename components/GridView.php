<?php

namespace app\components;


class GridView extends \kartik\grid\GridView {
    public $hover=true;
    public $pjax=true;
    public $responsiveWrap = false;
    public $pjaxSettings=[
        'loadingCssClass'=>false,
        'options'=>[
          'timeout'=>10000,
        ]
    ];
    public $export=false;
    public $options=['class' => 'grid-view admin-table'];

    protected function beginPjax() {
        if (!$this->pjax) {
            return;
        }

        if (empty($this->pjaxSettings['options']['id'])) {
            $this->pjaxSettings['options']['id'] = $this->options['id'] . '-pjax';
            $this->pjaxSettings['linkSelector'] = '#' . $this->options['id'] . '-pjax a:not([data-pjax=0])';
        }

        $view = $this->getView();
        $id=$this->pjaxSettings['options']['id'];
        $container = 'jQuery("#' . $id . '")';
        $view->registerJs("{$container}.on('pjax:beforeSend', function(event,xhr,settings){if (settings.url.match(/^[^#]*[?&]pjaxForcePost=/)) settings.type='POST';});");

        parent::beginPjax();
    }
}