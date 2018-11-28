<?php

namespace app\controllers;

use Yii;


class ExtApiModeratorController extends \app\components\ExtApiController {

    public function actionDeleteMessage() {
        $id=Yii::$app->request->getBodyParam('id');

        return \app\components\Moderator::deleteMessage($id);
    }

    public function actionBlockUser() {
        $groupChatId=Yii::$app->request->getBodyParam('groupChatId');
        $userId=Yii::$app->request->getBodyParam('userId');

        return \app\components\Moderator::blockUser($groupChatId,$userId);
    }

    public function actionUnblockUser() {
        $groupChatId=Yii::$app->request->getBodyParam('groupChatId');
        $userId=Yii::$app->request->getBodyParam('userId');

        return \app\components\Moderator::unblockUser($groupChatId,$userId);
    }

    public function actionBlockUserInTrollbox() {
        $groupChatId=Yii::$app->request->getBodyParam('groupChatId');
        $userId=Yii::$app->request->getBodyParam('userId');

        return \app\components\Moderator::blockUserInTrollbox($groupChatId,$userId);
    }

    public function actionUnblockUserInTrollbox() {
        $userId=Yii::$app->request->getBodyParam('userId');

        if (!Yii::$app->user->identity->is_moderator) {
            return ['result'=>Yii::t('app','You are not moderator')];
        }

        $user=\app\models\User::findOne($userId);

        $trx=Yii::$app->db->beginTransaction();

        \app\components\Moderator::unblockUserInTrollbox($user);

        $trx->commit();

        return ['result'=>true];
    }

    public function actionBlockUserInTrollboxWithMessage() {
        $groupChatId=Yii::$app->request->getBodyParam('groupChatId');
        $userId=Yii::$app->request->getBodyParam('userId');

        $trx=Yii::$app->db->beginTransaction();
        $res=\app\components\Moderator::blockUserInTrollbox(-$groupChatId,$userId);
        if ($res['result']!==true) {
            return $res;
        }

        $res=\app\components\Moderator::rejectTrollboxMessage($groupChatId);
        $trx->commit();

        return $res;
    }

    public function actionAcceptTrollboxMessage() {
        $id=Yii::$app->request->getBodyParam('id');
        return \app\components\Moderator::acceptTrollboxMessage($id);
    }

    public function actionRejectTrollboxMessage() {
        $id=Yii::$app->request->getBodyParam('id');
        return \app\components\Moderator::rejectTrollboxMessage($id);
    }

    public function actionSetStickyTrollboxMessage() {
        $id=Yii::$app->request->getBodyParam('id');
        return \app\components\Moderator::setStickyTrollboxMessage($id);
    }

    public function actionUnsetStickyTrollboxMessage() {
        $id=Yii::$app->request->getBodyParam('id');
        return \app\components\Moderator::unsetStickyTrollboxMessage($id);
    }

}
