<div class="close" ng-click="modalService.hide()"></div>
<div class="content" ng-controller="TrollboxMessageVisibilityCtrl as trollboxMessageVisibilityCtrl">

    <div class="trollbox-message-visibility-list">
        <div>
            <input type="checkbox" ng-true-value="1" ng-false-value="0" i-check ng-model="trollboxMessage.visible_for_followers">
            <label><?= Yii::t('app', 'An Abonnenten') ?></label>
        </div>
        <div>
            <input type="checkbox" ng-true-value="1" ng-false-value="0" i-check ng-model="trollboxMessage.visible_for_contacts">
            <label><?= Yii::t('app', 'An Kontakte') ?></label>
        </div>
        <div>
            <input type="checkbox" ng-true-value="1" ng-false-value="0" i-check ng-model="trollboxMessage.visible_for_all">
            <label><?= Yii::t('app', 'An Alle') ?></label>
        </div>

        <div class="trollbox-message-select-category">
            <label><?= Yii::t('app', 'Kategorieauswahl') ?>:</label>
            <div class="field-box-select" dropdown-toggle select-click bs-has-classes>
                <select ng-model="trollboxMessage.trollbox_category_id" selectpicker="{title:''}" ng-options="item.id as item.title for item in trollboxCategoryList">
                    <option value=""><?= Yii::t('app', 'Alle'); ?></option>
                </select>
            </div>
        </div>

        <p ng-if="status.video_identification_status=='AWAITING'"><?= Yii::t('app', 'Dein Ident Video wird gerade geprüft.') ?></p>
        <p ng-if="status.video_identification_status=='NONE'"><?= Yii::t('app', 'Um eine Video-Identifikation durchführen zu können musst Du sich in unsere {app_link} einloggen.', [
            'app_link'=>'<a ng-click="trollboxMessageVisibilityCtrl.showAppDownloadPopup()" href="">Jugl-App</a>'
        ]) ?></p>
        
        <p><?= Yii::t('app','Mit dem Absenden Deines Posts stimmst Du unseren Forumsregeln zu.') ?></p>

        <ul class="errors-list" ng-if="trollboxMessage.error">
            <li><?= Yii::t('app', 'Bitte mindestens eine Option wählen') ?></li>
        </ul>

    </div>

    <div>
        <button class="btn btn-submit" ng-click="trollboxMessageVisibilityCtrl.send()" ng-disabled="trollboxMessage.saving"><?= Yii::t('app', 'Absenden') ?></button>
    </div>

</div>