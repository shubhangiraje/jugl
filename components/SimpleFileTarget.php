<?php

namespace app\components;

use Yii;

class SimpleFileTarget extends \yii\log\FileTarget {

    private function formatTime($time) {
        $ms=substr(number_format($time-floor($time),3),1);
        return date('Y-m-d H:i:s',$time).$ms;
    }

    public function formatMessage($message)
    {
        return $this->formatTime($message[3]).' ['.\Yii::$app->request->getUserIP().'] '.$message[0];
    }

    public function getContextMessage()
    {
        return '';
    }
}

