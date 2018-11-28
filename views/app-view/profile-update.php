<?php

use app\models\User;

?>

<div id="profile-page">
    <div class="container clearfix">
        <div ng-click="showInfoPopup('view-profile')" ng-class="{'blink':isOneShowInfoPopup('view-profile')}" class="info-popup-btn"></div>
        <div ng-show="isWelcome" class="profile-page-welcome">
            <p><?= Yii::t('app', 'Als nächstes vervollständige Deine Profildaten bei Jugl.net, damit Deine Freunde und andere Nutzer mehr über dich erfahren können:'); ?></p>
        </div>

        <div class="btn-save-profile-top">
            <button class="btn btn-submit" ng-if="!isWelcome" ng-disabled="userProfile.saving" ng-click="profileUpdateCtrl.save()"><?=Yii::t('app','Speichern')?></button>
        </div>

        <div ng-show="!isWelcome" class="profile-page-welcome">
            <h1><?=Yii::t('app','Mein Profil')?></h1>
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

                    <div ng-if="status.packet=='VIP_PLUS' || status.packet=='VIP' || status.packet=='STANDART'" class="profile-update-packet">
                        <div>
                            <span class="profile-update-packet-text"><?=Yii::t('app','Ihre Mitgliedschaft')?>: </span>
                            <span class="profile-update-packet-current" ng-if="status.packet=='VIP'"><?= Yii::t('app', 'Premium') ?></span>
                            <span class="profile-update-packet-current" ng-if="status.packet=='VIP_PLUS'"><?= Yii::t('app', 'PremiumPlus') ?></span>
                            <span class="profile-update-packet-current" ng-if="status.packet=='STANDART'"><?= Yii::t('app', 'Standard') ?></span>
                        </div>
                        <a class="profile-update-packet-update-btn btn btn-submit" ng-if="status.packet=='STANDART'" ui-sref="packetUpgrade"><span><?=Yii::t('app', 'Upgrade') ?></span></a>
                    </div>

                    <div class="profile-user-photos">
                        <div class="fields-box-image">
                            <div ng-repeat="photo in user.photos" class="preview-upload-image">
                                <img ng-src="{{photo.thumbs.photoSmall}}" />
                                <button ng-click="profileUpdateCtrl.deleteFile(photo.id)" class="btn-del-image"></button>
                            </div>
                            <div ng-if="user.photos.length<30" class="box-input-file" ng-if="uploader.queue.length != uploader.queueLimit">
                                <div class="spinner" ng-if="uploader.isUploading"></div>
                                <span class="icon-input-file" ng-if="!uploader.isUploading"></span>
                                <input type="file" nv-file-select filters="imageFilter,queueLimit" uploader="uploader" options="fileUploadOptions" multiple />
                            </div>
                        </div>
                    </div>

                </div>
            </div>


            <div ng-show="!isWelcome" class="profile-update-main-data">
                <h2><?=Yii::t('app','Persönliche Daten:')?></h2>
                <div class="profile-update-fields-box">
                    <div class="clearfix">
                        <div class="profile-update-field-column-left">
                            <div class="profile-update-field-input-box" bs-has-classes>
                                <input type="text" ng-model="user.first_name" disabled placeholder="<?=User::getEncodedAttributeLabel('first_name')?>" />
                            </div>
                            <div class="profile-update-field-input-box" bs-has-classes>
                                <input type="text" ng-model="user.last_name" disabled placeholder="<?=User::getEncodedAttributeLabel('last_name')?>" />
                            </div>

                            <div ng-if="!user.is_company_name" class="profile-update-field-input-box" bs-has-classes>
                                <input ng-model-options="{updateOn: 'default blur', debounce: { 'default': 5000, 'blur': 0 }}" type="text" ng-model="user.nick_name" placeholder="<?=User::getEncodedAttributeLabel('nick_name')?>" />
                            </div>

                            <div ng-if="user.is_company_name" class="profile-update-field-input-box" bs-has-classes>
                                <input type="text" ng-model="user.company_manager" placeholder="<?=User::getEncodedAttributeLabel('company_manager')?>" />
                            </div>

                            <div class="profile-update-field-checkbox-box" bs-has-classes>
                                <input type="checkbox" ng-true-value="1" ng-false-value="0" i-check ng-model="user.is_company_name">
                                <label><?=User::getEncodedAttributeLabel('is_company_name')?></label>
                            </div>

                            <div class="profile-update-field-input-box" bs-has-classes>
                                <input type="text" ng-model="user.company_name" placeholder="<?=User::getEncodedAttributeLabel('company_name')?>" />
                            </div>

                            <div class="profile-update-field-input-box" bs-has-classes>
                                <input ng-model-options="{updateOn: 'default blur', debounce: { 'default': 5000, 'blur': 0 }}" type="text" ng-model="user.phone" placeholder="<?=User::getEncodedAttributeLabel('phone')?>" />
                            </div>
                        </div>
                        <div class="profile-update-field-column-right">
                            <div class="profile-update-field-input-box" bs-has-classes>
                                <input type="text" ng-model="user.email" placeholder="<?=User::getEncodedAttributeLabel('email')?>" />
                            </div>
                            <div class="profile-update-field-input-box" bs-has-classes>
                                <input type="password" ng-model="user.oldPassword" placeholder="<?=User::getEncodedAttributeLabel('oldPassword')?>" />
                            </div>
                            <div class="profile-update-field-input-box" bs-has-classes>
                                <input type="password" ng-model="user.newPassword" placeholder="<?=User::getEncodedAttributeLabel('newPassword')?>" />
                            </div>
                            <div class="profile-update-field-input-box" bs-has-classes>
                                <input type="password" ng-model="user.newPasswordRepeat" placeholder="<?=User::getEncodedAttributeLabel('newPasswordRepeat')?>" />
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

            <div class="profile-update-others-data">
                <h2><?=Yii::t('app','Ergänzende Daten:')?></h2>

                <div class="profile-update-fields-box">
                    <div class="profile-update-line titles-line clearfix">
                        <div class="profile-update-line-visibility" style="margin-top: 0;">
                            <h3><?=Yii::t('app','Deine persönliche Daten')?></h3>
                        </div>
                        <div class="profile-update-line-fields clearfix">
                            <h3><?=Yii::t('app','Sichtbarkeit für andere')?></h3>
                        </div>
                    </div>

                    <div class="profile-update-line clearfix">
                        <div class="profile-update-line-fields clearfix">
                            <div class="profile-update-line-field birth">
                                <div class="profile-update-paddings" bs-has-classes>
                                    <label><?=Yii::t('app','Geburtstag')?>:</label>
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
                        <div class="profile-update-line-visibility" bs-has-classes>
                            <div class="block"><input type="radio" value="all" i-check ng-model="user.visibility_birthday"/><label class="all"></label><span class="title"><?=Yii::t('app','Für alle sichtbar')?></span></div>
                            <div class="block"><input type="radio" value="friends" i-check ng-model="user.visibility_birthday"/><label class="friends"></label><span class="title"><?=Yii::t('app','Nur für Kontakte sichtbar')?></span></div>
                            <div class="block"><input type="radio" value="none" i-check ng-model="user.visibility_birthday"/><label class="none"></label><span class="title"><?=Yii::t('app','Für niemanden sichtbar')?></span></div>
                        </div>
                    </div>

                    <div class="profile-update-line clearfix">
                        <div class="profile-update-line-fields clearfix">
                            <div class="profile-update-line-field third2" bs-has-classes><input ng-model-options="{updateOn: 'default blur', debounce: { 'default': 5000, 'blur': 0 }}" type="text" ng-model="user.street" placeholder="<?=User::getEncodedAttributeLabel('street')?>" /></div>
                            <div class="profile-update-line-field third" bs-has-classes><input ng-model-options="{updateOn: 'default blur', debounce: { 'default': 5000, 'blur': 0 }}" type="text" ng-model="user.house_number" placeholder="<?=User::getEncodedAttributeLabel('house_number')?>"/></div>
                        </div>
                        <div class="profile-update-line-visibility" bs-has-classes>
                            <div class="block"><input type="radio" value="all" i-check ng-model="user.visibility_address1"/><label class="all"></label><span class="title"><?=Yii::t('app','Für alle sichtbar')?></span></div>
                            <div class="block"><input type="radio" value="friends" i-check ng-model="user.visibility_address1"/><label class="friends"></label><span class="title"><?=Yii::t('app','Nur für Kontakte sichtbar')?></span></div>
                            <div class="block"><input type="radio" value="none" i-check ng-model="user.visibility_address1"/><label class="none"></label><span class="title"><?=Yii::t('app','Für niemanden sichtbar')?></span></div>
                        </div>
                    </div>

                    <div class="profile-update-line clearfix">
                        <div class="profile-update-line-fields clearfix">
                            <div class="profile-update-line-field profile-zip" bs-has-classes><input ng-model-options="{updateOn: 'default blur', debounce: { 'default': 5000, 'blur': 0 }}" type="text" ng-model="user.zip" placeholder="<?=User::getEncodedAttributeLabel('zip')?>" /></div>
                            <div class="profile-update-line-field profile-city" bs-has-classes><input type="text" ng-model="user.city" placeholder="<?=User::getEncodedAttributeLabel('city')?>"/></div>
                            <div class="profile-update-line-field profile-country" bs-has-classes>
                                <div class="field-box-select" dropdown-toggle select-click>
                                    <select ng-model="user.country_id" selectpicker="{title:''}" ng-disabled="!user.is_moderator && !user.allow_country_change" ng-options="item.id as item.country for item in countries">
                                        <option value=""><?=User::getEncodedAttributeLabel('country_id')?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="profile-update-line-visibility" bs-has-classes>
                            <div class="block"><input type="radio" value="all" i-check ng-model="user.visibility_address2"/><label class="all"></label><span class="title"><?=Yii::t('app','Für alle sichtbar')?></span></div>
                            <div class="block"><input type="radio" value="friends" i-check ng-model="user.visibility_address2"/><label class="friends"></label><span class="title"><?=Yii::t('app','Nur für Kontakte sichtbar')?></span></div>
                            <div class="block"><input type="radio" value="none" i-check ng-model="user.visibility_address2"/><label class="none"></label><span class="title"><?=Yii::t('app','Für niemanden sichtbar')?></span></div>
                        </div>
                    </div>

                    <div class="profile-update-line clearfix">
                        <div class="profile-update-line-fields clearfix">
                            <div class="profile-update-line-field" bs-has-classes><input ng-model-options="{updateOn: 'default blur', debounce: { 'default': 5000, 'blur': 0 }}" type="text" ng-model="user.profession" placeholder="<?=User::getEncodedAttributeLabel('profession')?>" /></div>
                        </div>
                        <div class="profile-update-line-visibility" bs-has-classes>
                            <div class="block"><input type="radio" value="all" i-check ng-model="user.visibility_profession"/><label class="all"></label><span class="title"><?=Yii::t('app','Für alle sichtbar')?></span></div>
                            <div class="block"><input type="radio" value="friends" i-check ng-model="user.visibility_profession"/><label class="friends"></label><span class="title"><?=Yii::t('app','Nur für Kontakte sichtbar')?></span></div>
                            <div class="block"><input type="radio" value="none" i-check ng-model="user.visibility_profession"/><label class="none"></label><span class="title"><?=Yii::t('app','Für niemanden sichtbar')?></span></div>
                        </div>
                    </div>

                    <div class="profile-update-line clearfix">
                        <div class="profile-update-line-fields clearfix">
                            <div class="profile-update-line-field" bs-has-classes>
                                <label><?=User::getEncodedAttributeLabel('marital_status')?>:</label>
                                <div class="profile-update-select" dropdown-toggle select-click>
                                    <select ng-model="user.marital_status" selectpicker="{title:''}" ng-options="item.value as item.name for item in maritalStatuses">
                                        <option value=""><?= Yii::t('app', 'Bitte wählen') ?></option>
                                    </select>

                                </div>
                            </div>
                        </div>
                        <div class="profile-update-line-visibility" bs-has-classes>
                            <div class="block"><input type="radio" value="all" i-check ng-model="user.visibility_marital_status"/><label class="all"></label><span class="title"><?=Yii::t('app','Für alle sichtbar')?></span></div>
                            <div class="block"><input type="radio" value="friends" i-check ng-model="user.visibility_marital_status"/><label class="friends"></label><span class="title"><?=Yii::t('app','Nur für Kontakte sichtbar')?></span></div>
                            <div class="block"><input type="radio" value="none" i-check ng-model="user.visibility_marital_status"/><label class="none"></label><span class="title"><?=Yii::t('app','Für niemanden sichtbar')?></span></div>
                        </div>
                    </div>

                    <div class="profile-update-line clearfix">
                        <div class="profile-update-line-fields clearfix">
                            <div class="profile-update-line-field" bs-has-classes><textarea ng-model-options="{updateOn: 'default blur', debounce: { 'default': 5000, 'blur': 0 }}" ng-model="user.about" placeholder="<?=User::getEncodedAttributeLabel('about')?>"></textarea></div>
                        </div>
                        <div class="profile-update-line-visibility with-textarea" bs-has-classes>
                            <div class="block"><input type="radio" value="all" i-check ng-model="user.visibility_about"/><label class="all"></label><span class="title"><?=Yii::t('app','Für alle sichtbar')?></span></div>
                            <div class="block"><input type="radio" value="friends" i-check ng-model="user.visibility_about"/><label class="friends"></label><span class="title"><?=Yii::t('app','Nur für Kontakte sichtbar')?></span></div>
                            <div class="block"><input type="radio" value="none" i-check ng-model="user.visibility_about"/><label class="none"></label><span class="title"><?=Yii::t('app','Für niemanden sichtbar')?></span></div>
                        </div>
                    </div>

                    <div class="profile-update-visibility-legend">
                        <div class="block">
                            <span class="label all"></span>
                            <span class="title"><?=Yii::t('app','Für alle sichtbar')?></span>
                        </div>
                        <div class="block">
                            <span class="label friends"></span>
                            <span class="title"><?=Yii::t('app','Nur für Kontakte sichtbar')?></span>
                        </div>
                        <div class="block">
                            <span class="label none"></span>
                            <span class="title"><?=Yii::t('app','Für niemanden sichtbar')?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div ng-if="status.validation_phone_status!='VALIDATED' && status.parent_registration_bonus==0" class="text-center">
                <a href="" ng-click="profileUpdateCtrl.deleteProfile()" class="btn btn-submit"><?=Yii::t('app','Profil löschen')?></a>
            </div>

            <div ng-show="!isWelcome" class="profile-update-pay-data">
                <h2><?=Yii::t('app','Meine Zahlungsinformationen')?>:</h2>

                <div class="profile-update-fields-box">

                    <div class="profile-update-bank-details">
                        <h3><?=Yii::t('app','Meine Bankverbindung')?>:</h3>
                        <div ng-repeat="bankData in user.bankDatas" class="bank-details-data">
                            <div class="profile-update-line-field">
                                <input ng-model-options="{updateOn: 'default blur', debounce: { 'default': 5000, 'blur': 0 }}" type="text" placeholder="<?=Yii::t('app','IBAN')?>" ng-model="bankData.iban"/>
                            </div>
                            <div class="profile-update-line-field">
                                <input ng-model-options="{updateOn: 'default blur', debounce: { 'default': 5000, 'blur': 0 }}" type="text" placeholder="<?=Yii::t('app','BIC')?>" ng-model="bankData.bic"/>
                            </div>
                            <div class="profile-update-line-field">
                                <input ng-model-options="{updateOn: 'default blur', debounce: { 'default': 5000, 'blur': 0 }}" type="text" placeholder="<?=Yii::t('app','Kontoinhaber')?>" ng-model="bankData.owner"/>
                            </div>
                        </div>

                        <div class="add-bank-details">
                            <button ng-click="user.bankDatas.push({})"><?=Yii::t('app','Weitere Bankverbindung erstellen')?></button>
                        </div>

                        <h3><?=Yii::t('app','Mein Paypal')?>:</h3>
                        <div class="paypal-img"><img src="/static/images/account/paypal.jpg" alt="paypal"></div>
                        <div class="profile-update-line-field">
                            <input ng-model="user.paypal_email" ng-model-options="{updateOn: 'default blur', debounce: { 'default': 5000, 'blur': 0 }}" type="text" placeholder="<?=Yii::t('app','E-mail')?>" />
                        </div>


                    </div>

                </div>
            </div>


            <div class="profile-update-validaton-data" id="validationPhone" ng-if="user.validation_phone_status!='VALIDATED'">
                <h2><?=Yii::t('app','Daten Bestätigen')?>:</h2>
                <div class="profile-update-fields-box">
                    <div class="profile-update-fields-box-text">
                        <p><?= Yii::t('app', 'Damit derjenige, der Dich zu Jugl.net eingeladen hat, seinen Einladungsbonus erhält, gib hier Deine Handynummer an. Wir senden Dir einen Bestätigungscode per SMS zu, den Du unten in das Feld einträgst.') ?></p>
                    </div>
                    <div ng-if="!userProfile.validationCode.validation_code_send" class="profile-update-validaton-fields">
                        <label><?= Yii::t('app','Hier Handynummer eingeben') ?>:</label>
                        <div class="profile-update-line-field">
                            <input type="text" placeholder="<?=Yii::t('app','z.B. +4917612345678')?>" ng-model="user.validation_phone"/>
                        </div>

                        <ul class="errors-list" ng-if="userProfile.validationPhone.$allErrors">
                            <li ng-repeat="error in userProfile.validationPhone.$allErrors" ng-bind="error"></li>
                        </ul>

                        <div class="text-center">
                            <button class="btn btn-submit" ng-disabled="sendingSms" ng-if="user.validation_phone_status=='NOT_VALIDATED'" ng-click="profileUpdateCtrl.sendValidationPhone()">
								<span><?= Yii::t('app','Absenden') ?></span>
							</button>
							<button class="btn btn-submit" ng-disabled="sendingSms" ng-if="user.validation_phone_status=='SEND_CODE' && new_code_interval == 0" ng-click="profileUpdateCtrl.sendValidationPhone()">
								<span><?= Yii::t('app','Erneut zusenden') ?></span>
							</button>
							<button class="btn btn-submit" ng-disabled="code_clicked" ng-if="user.validation_phone_status=='SEND_CODE' && new_code_interval > 0"  ng-click="profileUpdateCtrl.sendValidationPhone()">
								<span><?= Yii::t('app','Bitte Warte') ?> {{new_code_interval}} <?= Yii::t('app','Sek.') ?></span>
                            </button>
                        </div>

                        <div ng-if="user.validation_phone_status=='SEND_CODE'">
                            <div class="profile-update-line-field">
                                <input type="text" placeholder="<?=Yii::t('app','Hier Code eingeben')?>" ng-model="user.validation_code_form"/>
                            </div>
                            <ul class="errors-list" ng-if="userProfile.validationCode.$allErrors">
                                <li ng-repeat="error in userProfile.validationCode.$allErrors" ng-bind="error"></li>
                            </ul>
                            <div class="text-center">
                                <button class="btn btn-submit" ng-click="profileUpdateCtrl.sendValidationCode()"><?= Yii::t('app','Code absenden') ?></button>
                            </div>
                        </div>
                    </div>

                    <div ng-if="userProfile.validationCode.validation_code_send" class="success-sent"><?= Yii::t('app', 'Die Bestätigung wurde erfolgreich abgeschlossen') ?></div>
                </div>
            </div>

            <ul class="errors-list" ng-if="user.$allErrors">
                <li ng-repeat="error in user.$allErrors" ng-bind="error"></li>
            </ul>

            <div class="profile-update-button-box">
                <button class="btn btn-submit" ng-if="isWelcome" ng-disabled="userProfile.saving" ng-click="profileUpdateCtrl.save()"><?=Yii::t('app','Weiter')?></button>
                <button class="btn btn-submit" ng-if="!isWelcome" ng-disabled="userProfile.saving" ng-click="profileUpdateCtrl.save()"><?=Yii::t('app','Speichern')?></button>
            </div>

        </form>
    </div>
</div>
