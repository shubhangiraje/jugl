<div id="profile-settings-page">
    <div class="container">
        <div ng-click="showInfoPopup('view-settings')" ng-class="{'blink':isOneShowInfoPopup('view-settings')}" class="info-popup-btn"></div>

        <div class="welcome-text">
            <h2><?=Yii::t('app','Einstellungen')?></h2>
        </div>

        <div class="profile-update-main-data">
            <div class="profile-update-fields-box clearfix">
                <div class="profile-update-field-checkbox-box" bs-has-classes>
                    <input type="checkbox" ng-true-value="1" ng-false-value="0" i-check ng-model="data.settings.remote.setting_notification_likes"/>
                    <label><?=Yii::t('app','Benachrichtigung bei Likes')?></label>
                </div>
<!--
                <div class="profile-update-field-checkbox-box" bs-has-classes>
                    <input type="checkbox" ng-true-value="1" ng-false-value="0" i-check ng-model="data.settings.remote.setting_notification_comments"/>
                    <label><?=Yii::t('app','Benachrichtigung bei Kommentaren')?></label>
                </div>
-->
                <div class="profile-update-field-checkbox-box" bs-has-classes>
                    <input type="checkbox" ng-true-value="0" ng-false-value="1" i-check ng-model="data.settings.remote.setting_off_send_email"/>
                    <label><?=Yii::t('app','Emails an mich versenden')?></label>
                </div>

                <div class="profile-update-field-checkbox-box" bs-has-classes style="margin-bottom: 0;">
                    <input type="checkbox" ng-true-value="1" ng-false-value="0" i-check ng-model="data.settings.local.sounds"/>
                    <label><?=Yii::t('app','Alle Töne ein-/ausschalten')?></label>
                </div>
            </div>
        </div>

        <div class="profile-update-button-box">
            <button class="btn btn-submit" ng-disabled="data.saving" ng-click="settingsCtrl.save()"><?=Yii::t('app','Speichern')?></button>
        </div>
    </div>
</div>
