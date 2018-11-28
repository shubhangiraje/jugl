<?php

use app\models\User;

?>

<div id="profile-page" class="profile-fillup">
    <div class="container clearfix">


        <div class="profile-page-welcome">
            <h1><?=Yii::t('app','Mein Profil')?></h1>
            <p><?=Yii::t('app','Du hast jetzt die Gelegenheit, einmalig Deinen Namen und Deine Daten zu ändern. Es ist wichtig, dass Du den richtigen Namen in Deinem Profil angegeben hast, sonst kannst Du Deine Jugl-Punkte nicht einlösen. Jugl.net verkauft keine Daten oder Email-Adressen an Dritte. Keine lästige Werbung per Email.')?></p>
			
        </div>
        <form novalidate>
            <div class="profile-update-avatar">
                <h2><?=Yii::t('app','Dein Profilbild:')?></h2>
                <div class="profile-update-fields-box">
                    <img class="profile-avatar" ng-src="{{user.avatarFile.thumbs.avatarBig | default : '/static/images/account/default_avatar.png'}}" /><br />
                    <div class="profile-update-upload">
                        <?=Yii::t('app','Profilbild hochladen')?>
                        <input type="file" nv-file-select filters="imageFilter" uploader="uploader" options="avatarUploadOptions"/>
                    </div>
                </div>
            </div>


            <div class="profile-update-main-data">
                <h2><?=Yii::t('app','Persönliche Daten:')?></h2>
                <div class="profile-update-fields-box">
                    <div class="clearfix">
                        <div class="profile-update-field-column-left">
                            <div class="profile-update-field-input-box" bs-has-classes>
                                <input type="text" ng-model="user.first_name" placeholder="<?=User::getEncodedAttributeLabel('first_name')?>" />
                            </div>

                            <div class="profile-update-field-input-box" bs-has-classes>
                                <input type="text" ng-model="user.last_name" placeholder="<?=User::getEncodedAttributeLabel('last_name')?>" />
                            </div>

                            <div class="profile-fillup-birthday-field-box">
                                <label><?=Yii::t('app','Geburtstag')?>:</label>
                                <div class="profile-fillup-birthday-box clearfix">
                                    <div class="profile-update-select birthDay" dropdown-toggle select-click>
                                        <select ng-model="user.birthDay" selectpicker ng-options="item.key as item.val for item in birthDayList"></select>
                                    </div>
                                    <div class="profile-update-select birthMonth" dropdown-toggle select-click>
                                        <select ng-model="user.birthMonth" selectpicker ng-options="item.key as item.val for item in birthMonthList"></select>
                                    </div>
                                    <div class="profile-update-select birthYear" dropdown-toggle select-click>
                                        <select ng-model="user.birthYear" selectpicker ng-options="item.key as item.val for item in birthYearList"></select>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="profile-update-field-column-right">

                            <div class="profile-update-field-input-box" bs-has-classes>
                                <input type="text" ng-model="user.city" placeholder="<?=User::getEncodedAttributeLabel('city')?>" />
                            </div>

                            <div class="profile-update-field-input-box" bs-has-classes>
                                <input type="text" ng-model="user.street" placeholder="<?=User::getEncodedAttributeLabel('street')?>" />
                            </div>

                            <div class="profile-update-field-input-box" bs-has-classes>
                                <input type="text" ng-model="user.house_number" placeholder="<?=User::getEncodedAttributeLabel('house_number')?>" />
                            </div>

                            <div class="profile-update-field-checkbox-box" bs-has-classes>
                                <input type="checkbox" ng-true-value="1" ng-false-value="0" i-check ng-model="user.is_company_name"/>
                                <label><?=User::getEncodedAttributeLabel('is_company_name')?></label>
                            </div>

                            <div ng-if="user.is_company_name" class="profile-update-field-input-box" bs-has-classes>
                                <input type="text" ng-model="user.company_manager" placeholder="<?=User::getEncodedAttributeLabel('company_manager')?>" />
                            </div>

                            <div class="profile-update-field-input-box" bs-has-classes>
                                <input type="text" ng-model="user.company_name" placeholder="<?=User::getEncodedAttributeLabel('company_name')?>" />
                            </div>

                            <div class="profile-update-field-radio-box" bs-has-classes>
                                <div class="radio" ng-repeat="(id,name) in sexes">
                                    <input type="radio" value="{{id}}" i-check ng-model="user.sex"/><label>{{name}}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div ng-if="user.is_company_name" class="profile-update-field-textarea-box" bs-has-classes>
                        <label><?=User::getEncodedAttributeLabel('impressum')?>:</label>
                        <textarea ng-model="user.impressum" rows="5"></textarea>
                    </div>

                    <div ng-if="user.is_company_name" class="profile-update-field-textarea-box" bs-has-classes>
                        <label><?=User::getEncodedAttributeLabel('agb')?>:</label>
                        <textarea ng-model="user.agb" rows="5"></textarea>
                    </div>

                </div>

            </div>

            <ul class="errors-list" ng-if="user.$allErrors">
                <li ng-repeat="error in user.$allErrors" ng-bind="error"></li>
            </ul>

            <div class="profile-update-button-box">
                <button class="btn btn-submit" ng-disabled="user.saving" ng-click="profileFillupCtrl.save()"><?=Yii::t('app','Weiter')?></button>
            </div>
			<div class="profile-update-button-box">
                <button class="btn btn-submit" ng-disabled="user.saving" ng-click="profileFillupCtrl.later()"><?=Yii::t('app','Später erinnern')?></button>
            </div>
        </form>
    </div>
</div>
