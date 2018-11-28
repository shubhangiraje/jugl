<div class="close" ng-click="modalService.hide()"></div>
<div class="content" ng-controller="UserCountryUpdatePopupCtrl as userCountryUpdatePopupCtrl">

    <div class="user-country-update-box">
        <div class="field-box-select" dropdown-toggle select-click>
            <label><?= Yii::t('app', 'Land') ?>:</label>
            <select ng-model="userCountryUpdateForm.country_id" selectpicker="{title:''}" ng-options="item.id as item.country for item in countries"></select>
        </div>

        <button type="button" class="btn btn-submit" ng-click="userCountryUpdatePopupCtrl.save()" ng-disabled="userCountryUpdateForm.saving"><?= Yii::t('app', 'Speichern') ?></button>
    </div>

</div>
