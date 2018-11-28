<?php

namespace app\controllers;

use Yii;
use yii\helpers\Url;
use app\models\InviteByEmailForm;
use app\models\InviteBySMSForm;
use app\models\Invitation;

class ApiFriendsInvitationInviteController extends \app\components\ApiController {

    public function actionInviteByEmail() {
        $data=Yii::$app->request->getBodyParams()['inviteByEmail'];

        $errors=[];
        $data['$allErrors']=&$errors;

        $form=new InviteByEmailForm();
        $form->load($data,'');

        if ($form->validate()) {
            $result=Invitation::Invite(Invitation::TYPE_EMAIL,$form->emailsAsArray,$form->text);
            $data['result']=$result;
        } else {
            $data['$errors']=$form->getFirstErrors();
            $errors=array_unique(array_merge($errors,array_values($data['$errors'])));
        }

        return [
            'inviteByEmail'=>$data
        ];
    }

    public function actionInviteBySms() {
        $data=Yii::$app->request->getBodyParams()['inviteBySMS'];

        $errors=[];
        $data['$allErrors']=&$errors;

        $form=new InviteBySMSForm();
        $form->load($data,'');

        if ($form->validate()) {
            $result=Invitation::Invite(Invitation::TYPE_SMS,$form->phonesAsArray, $form->text);
            $data['result']=$result;
        } else {
            $data['$errors']=$form->getFirstErrors();
            $errors=array_unique(array_merge($errors,array_values($data['$errors'])));
        }

        return [
            'inviteBySMS'=>$data
        ];
    }

    public function actionIndex() {
        return [
            'refLink'=>Url::to(['registration/index','refId'=>Yii::$app->user->id],true)
        ];
    }
}