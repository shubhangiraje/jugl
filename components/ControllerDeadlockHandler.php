<?php

namespace app\components;

use Yii;


trait ControllerDeadlockHandler {
    public function runAction($id, $params = [], $deadlockRetry=0)
    {
        try {
            return parent::runAction($id, $params);
        } catch (\Exception $e) {
            if ($e instanceof  \yii\db\Exception && $e->errorInfo[0]=='40001' && $e->errorInfo[1]=='1213' && $deadlockRetry<10) {
                // rollback transaction
                while (Yii::$app->db->getTransaction() && Yii::$app->db->getTransaction()->getIsActive()) {
                    Yii::$app->db->getTransaction()->rollBack();
                }

                $deadlockRetry++;
                \app\components\SLogger::log("DEADLOCK RETRY $deadlockRetry");
                usleep(rand(50*1000,250*1000)*$deadlockRetry);

                if ($deadlockRetry>10) {
                    Yii::error("deadock detected, retry $deadlockRetry");
                }
                $this->runAction($id,$params,$deadlockRetry);
            } else {
                throw $e;
            }
        }
    }
}