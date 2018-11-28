<?php

namespace app\models;

use app\components\EDateTime;
use Yii;

class UserModifyLog extends \app\models\base\UserModifyLog
{
    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'modify_dt' => Yii::t('app','Datum'),
            'user_id' => Yii::t('app','User ID'),
            'description' => Yii::t('app','Description'),
        ];
    }



    public static function saveLog($user) {

        $data = $user->dirtyAttributes;


        if($user->oldAttributes['avatar_file_id']==$data['avatar_file_id']) {
            unset($data['avatar_file_id']);
        }

        if (checkdate($user->birthMonth,$user->birthDay,$user->birthYear)) {
            $date=new EDateTime();
            $date->setDate($user->birthYear,$user->birthMonth,$user->birthDay);
            $newBirthday=$date->sqlDate();
            if($newBirthday!=$user->birthday) {
                $data = array_merge($data, [
                    'birthday'=>Yii::$app->formatter->asDate($newBirthday, 'php:d.m.Y')
                ]);
            }
        }

        $log = '';
        $text_del = Yii::t('app', 'Gelöscht');

        if(isset($data['avatar_file_id'])) {
            $data['avatar_file_id'] ? $avatar_file_id=Yii::t('app','Profilbild geändert') : $avatar_file_id=$text_del;
            $log.=$avatar_file_id."\n";
        }

        if(isset($data['nick_name'])) {
            $nick_name = User::getEncodedAttributeLabel('nick_name').': ';
            $data['nick_name'] ? $nick_name.=$data['nick_name'] : $nick_name.=$text_del;
            $log.=$nick_name."\n";
        }

        if(isset($data['company_name'])) {
            $company_name = User::getEncodedAttributeLabel('company_name').': ';
            $data['company_name'] ? $company_name.=$data['company_name'] : $company_name.=$text_del;
            $log.=$company_name."\n";
        }

        if(isset($data['phone'])) {
            $phone = User::getEncodedAttributeLabel('phone').': ';
            $data['phone'] ? $phone.=$data['phone'] : $phone.=$text_del;
            $log.=$phone."\n";
        }

        if(isset($data['email'])) {
            $email = User::getEncodedAttributeLabel('email').': ';
            $data['email'] ? $email.=$data['email'] : $email.=$text_del;
            $log.=$email."\n";
        }

        if(isset($data['sex'])) {
            $sex = User::getEncodedAttributeLabel('sex').': ';
            $data['sex'] ? $sex.=User::getSexList()[$data['sex']] : $sex.=$text_del;
            $log.=$sex."\n";
        }

        if(isset($data['birthday'])) {
            $birthday = User::getEncodedAttributeLabel('birthday').': ';
            $data['birthday'] ? $birthday.=$data['birthday'] : $birthday.=$text_del;
            $log.=$birthday."\n";
        }

        if(isset($data['street'])) {
            $street = User::getEncodedAttributeLabel('street').': ';
            $data['street'] ? $street.=$data['street'] : $street.=$text_del;
            $log.=$street."\n";
        }

        if(isset($data['house_number'])) {
            $house_number = User::getEncodedAttributeLabel('house_number').': ';
            $data['house_number'] ? $house_number.=$data['house_number'] : $house_number.=$text_del;
            $log.=$house_number."\n";
        }

        if(isset($data['zip'])) {
            $zip = User::getEncodedAttributeLabel('zip').': ';
            $data['zip'] ? $zip.=$data['zip'] : $zip.=$text_del;
            $log.=$zip."\n";
        }

        if(isset($data['city'])) {
            $city = User::getEncodedAttributeLabel('city').': ';
            $data['city'] ? $city.=$data['city'] : $city.=$text_del;
            $log.=$city."\n";
        }

        if(isset($data['country_id'])) {
            $country = User::getEncodedAttributeLabel('country_id').': ';
            $data['country_id'] ? $country.=Country::getList()[$data['country_id']] : $country.=$text_del;
            $log.=$country."\n";
        }

        if(isset($data['profession'])) {
            $profession = User::getEncodedAttributeLabel('profession').': ';
            $data['profession'] ? $profession.=$data['profession'] : $profession.=$text_del;
            $log.=$profession."\n";
        }

        if(isset($data['marital_status'])) {
            $marital_status = User::getEncodedAttributeLabel('marital_status').': ';
            $data['marital_status'] ? $marital_status.=User::getMaritalStatusList()[$data['marital_status']] : $marital_status.=$text_del;
            $log.=$marital_status."\n";
        }

        if(isset($data['about'])) {
            $about = User::getEncodedAttributeLabel('about').': ';
            $data['about'] ? $about.= $data['about'] : $about.=$text_del;
            $log.=$about."\n";
        }

        if(isset($data['is_company_name'])) {
            $is_company_name = User::getEncodedAttributeLabel('is_company_name').': ';
            $data['is_company_name'] ? $is_company_name.= 'Ja' : $is_company_name.='Nein';
            $log.=$is_company_name."\n";
        }

        if(isset($data['company_manager'])) {
            $company_manager = User::getEncodedAttributeLabel('company_manager').': ';
            $data['company_manager'] ? $company_manager.=$data['company_manager'] : $company_manager.=$text_del;
            $log.=$company_manager."\n";
        }

        if(isset($data['impressum'])) {
            $impressum = User::getEncodedAttributeLabel('impressum').': ';
            $data['impressum'] ? $impressum.=$data['impressum'] : $impressum.=$text_del;
            $log.=$impressum."\n";
        }

        if(isset($data['agb'])) {
            $agb = User::getEncodedAttributeLabel('agb').': ';
            $data['agb'] ? $agb.=$data['agb'] : $agb.=$text_del;
            $log.=$agb."\n";
        }

        if(isset($data['paypal_email'])) {
            $paypal_email = User::getEncodedAttributeLabel('paypal_email').': ';
            $data['paypal_email'] ? $paypal_email.=$data['paypal_email'] : $paypal_email.=$text_del;
            $log.=$paypal_email."\n";
        }

        if(!empty($log)) {
            $trx=Yii::$app->db->beginTransaction();
            $model = new static();
            $model->user_id = Yii::$app->user->id;
            $model->modify_dt = (new EDateTime())->sqlDateTime();
            $model->description = $log;
            $model->save();
            $trx->commit();
        }

    }

    public static function saveLogAddReferralToParent($user, $old_user_parent, $new_user_parent) {
        $trx=Yii::$app->db->beginTransaction();
        $model = new static();
        $model->user_id = $user->id;
        $model->modify_dt = (new EDateTime())->sqlDateTime();

        $model->description = Yii::t('app','Teamwechsel von "{old_user_parent}" zu "{new_user_parent}"', [
            'old_user_parent'=>$old_user_parent->name,
            'new_user_parent'=>$new_user_parent->name
        ]);
        $model->save();

        Yii::$app->db->createCommand('update user set dt_parent_change=:dt_parent_change where id=:id',[
            ':id'=>$user->id,
            ':dt_parent_change'=>(new EDateTime())->sqlDateTime()
        ])->execute();

        $trx->commit();
    }



}
