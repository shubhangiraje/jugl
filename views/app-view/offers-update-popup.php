<div class="close" ng-click="modalService.hide()"></div>
<div class="content" ng-controller="OfferUpdateCtrl as offerUpdateCtrl" ng-init="offerUpdateForm=modalService.data.offerUpdateForm">
    <div id="modal-offer-update-popup">
        <div class="modal-update-popup">
            <div ng-if="!offerUpdateForm.without_bonus" class="modal-update-field">
                <label><?= \app\models\OfferUpdateForm::getEncodedAttributeLabel('view_bonus_total')?>:</label>
                <input ng-model="offerUpdateForm.view_bonus_total" type="text" />
            </div>
            <div ng-if="!offerUpdateForm.without_bonus" class="modal-update-field">
                <label><?= \app\models\OfferUpdateForm::getEncodedAttributeLabel('view_bonus')?>:</label>
                <input ng-model="offerUpdateForm.view_bonus" type="text" />
            </div>

            <div class="modal-update-field">
                <div class="modal-update-field-date">
                    <label><?=Yii::t('app','Anzeige Aktiv bis')?>:</label>
                    <div class="field-box-select select-day" dropdown-toggle select-click bs-has-classes>
                        <select ng-model="offerUpdateForm.active_till_day" selectpicker="{title:''}" ng-options="item.key as item.val for item in modalService.data.birthDayList">
                            <option value=""><?= Yii::t('app', 'Day'); ?></option>
                        </select>
                    </div>
                    <div class="field-box-select select-month" dropdown-toggle select-click bs-has-classes>
                        <select ng-model="offerUpdateForm.active_till_month" selectpicker="{title:''}" ng-options="item.key as item.val for item in modalService.data.birthMonthList">
                            <option value=""><?= Yii::t('app', 'Monat'); ?></option>
                        </select>
                    </div>
                    <div class="field-box-select select-year" dropdown-toggle select-click bs-has-classes>
                        <select ng-model="offerUpdateForm.active_till_year" selectpicker="{title:''}" ng-options="item.key as item.val for item in modalService.data.birthYearList">
                            <option value=""><?= Yii::t('app', 'Jahr'); ?></option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="modal-feedback-box">
                <label><?= Yii::t('app', 'Kommentar')?>:</label>
                <textarea maxlength="2000" name="feedback" ng-model="offerUpdateForm.comment"></textarea>
            </div>

        </div>

        <ul class="errors-list" ng-if="offerUpdateForm.$allErrors">
            <li ng-repeat="error in offerUpdateForm.$allErrors">
                <span ng-if="error!='NOT_ENOUGH_JUGL'">{{::error}}</span>
                <span ng-if="error=='NOT_ENOUGH_JUGL'"><?= Yii::t('app', 'Du hast leider nicht genung Jugls auf Deinem Konto.')?>&nbsp;<a href="" ng-click="offerUpdateCtrl.goPayIn()"><?= Yii::t('app', 'Jetzt Jugls aufladen')?></a></span>
            </li>
        </ul>

        <div class="btn-box-modal-offer">
            <button type="button" class="btn btn-submit" ng-click="offerUpdateCtrl.save()" ng-disabled="offerUpdateForm.saving"><?= Yii::t('app', 'Speichern') ?></button>
        </div>
    </div>

</div>
