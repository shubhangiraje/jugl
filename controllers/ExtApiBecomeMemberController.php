<?php

namespace app\controllers;

use Yii;
use app\components\Helper;


class ExtApiBecomeMemberController extends \app\components\ExtApiController {

    public function actionSave() {

        $data=Yii::$app->request->getBodyParams()['user'];
        $data['facebook_id']=Yii::$app->request->getBodyParams()['fb_id'];
        $errors=[];
        $data['$allErrors']=&$errors;
		//var_dump($data);
        $model=new \app\models\RegistrationDataForm();
        $model->setScenario('becomeMember');
        $model->load($data,'');
        $trx=Yii::$app->db->beginTransaction();

        if ($model->validate()) {
            $user=new \app\models\User;
            $user->setAttributes($model->attributes);
            $now=new \app\components\EDateTime();
            $user->registration_dt=$now->sqlDateTime();
            $user->facebook_id=$data['facebook_id'];
            $user->show_in_become_member=true;
            $user->auth_key=Yii::$app->security->generateRandomString(32);
            $user->access_token=Yii::$app->security->generateRandomString(32);
            $user->encryptPwd();
            $user->status=\app\models\User::STATUS_REGISTERED;
			//$user->status=\app\models\User::STATUS_EMAIL_VALIDATION;
            $user->registered_by_become_member=1;
            $user->save();
            //$user->sendEmailValidation();

            Yii::$app->db->createCommand("
                INSERT INTO user_interest (user_id, level1_interest_id, type) 
                    SELECT :user_id, id, type FROM interest WHERE parent_id is null", [
                ':user_id'=>$user->id
            ])->execute();

            //Yii::$app->user->login($user);

        } else {
            $data['$errors']=$model->getFirstErrors();
            $errors=array_unique(array_merge($errors,array_values($data['$errors'])));
        }

        if (!empty($errors)) {
            $trx->rollBack();
            return ['user'=>$data];
        }
        
        $trx->commit();

        return [
            'result' => true
        ];
    }
	
	// For APP release 1.1.25, Facebook Login to overcome issues with registration of older app Versions, rules were changed
	public function actionSaveNew() {

        $data=Yii::$app->request->getBodyParams()['user'];
        $data['facebook_id']=Yii::$app->request->getBodyParams()['fb_id'];
        $errors=[];
        $data['$allErrors']=&$errors;
		//var_dump($data);
        $model=new \app\models\RegistrationDataForm();
        $model->setScenario('becomeMemberNew');
        $model->load($data,'');
        $trx=Yii::$app->db->beginTransaction();

        if ($model->validate()) {
            $user=new \app\models\User;
            $user->setAttributes($model->attributes);
            $now=new \app\components\EDateTime();
            $user->registration_dt=$now->sqlDateTime();
            $user->facebook_id=$data['facebook_id'];
			
			if($model->birth_day && $model->birth_month && $model->birth_year){
					$user->setBirthDay($model->birth_day);
					$user->setBirthMonth($model->birth_month);
					$user->setBirthYear($model->birth_year);	
			}
			
            $user->show_in_become_member=true;
            $user->auth_key=Yii::$app->security->generateRandomString(32);
            $user->access_token=Yii::$app->security->generateRandomString(32);
            $user->encryptPwd();
            $user->status=\app\models\User::STATUS_REGISTERED;
			//$user->status=\app\models\User::STATUS_EMAIL_VALIDATION;
            $user->registered_by_become_member=1;
            $user->save();
            //$user->sendEmailValidation();

            Yii::$app->db->createCommand("
                INSERT INTO user_interest (user_id, level1_interest_id, type) 
                    SELECT :user_id, id, type FROM interest WHERE parent_id is null", [
                ':user_id'=>$user->id
            ])->execute();

            //Yii::$app->user->login($user);

        } else {
            $data['$errors']=$model->getFirstErrors();
            $errors=array_unique(array_merge($errors,array_values($data['$errors'])));
        }

        if (!empty($errors)) {
            $trx->rollBack();
            return ['user'=>$data];
        }
        
        $trx->commit();

        return [
            'result' => true
        ];
    }
	//Function for Linking Facebook Id with Jugl Account
	public function actionSaveFacebook() {

        $data=Yii::$app->request->getBodyParams()['user'];
        $data['facebook_id']=Yii::$app->request->getBodyParams()['fb_id'];
        $errors=[];
        $data['$allErrors']=&$errors;
		
        $model=new \app\models\RegistrationDataForm();
        $model->setScenario('linkFacebook');
        $model->load($data,'');
        $trx=Yii::$app->db->beginTransaction();

        if ($model->validate()) {
            $user=\app\models\User::find()->where('email=:email',[':email'=>$model->existing_account])->one();
				Yii::$app->db->createCommand("
					UPDATE user SET facebook_id=:fb_id WHERE id=:user_id LIMIT 1;", [
					':user_id'=>$user->id,
					':fb_id'=>$data['facebook_id']
				])->execute();
        } else {
            $data['$errors']=$model->getFirstErrors();
            $errors=array_unique(array_merge($errors,array_values($data['$errors'])));
        }

        if (!empty($errors)) {
            $trx->rollBack();
            return ['user'=>$data];
        }
        
        $trx->commit();

        return [
            'result' => true
        ];
    }
	
	public function actionGetBirthdayLists(){
		return [
		'birthDayList'=>Helper::assocToRecords(Helper::getDaysList()),
        'birthMonthList'=>Helper::assocToRecords(Helper::getMonthsList()),
        'birthYearList'=>Helper::assocToRecords(Helper::getYearsList(-12,-100)),
		];
	}


}
