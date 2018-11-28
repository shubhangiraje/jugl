<div class="close" ng-click="modalService.hide()"></div>
<div class="content" ng-controller="OfferRequestPaymentComplaintPopupCtrl as offerRequestPaymentComplaintPopupCtrl">

    <div class="modal-bonus-box">
        <div class="modal-feedback-box">
            <label><?= Yii::t('app', 'Mahnungstexte:')?></label>
            <textarea ng-model="paymentComplaint.text"></textarea>
        </div>

    </div>

    <div class="btn-box-modal-bonus buttons">
        <button type="button" class="btn btn-submit" ng-disabled="data.saving" ng-click="modalService.hide()"><?= Yii::t('app', 'Abbrechen') ?></button>
        <button type="button" class="btn btn-submit" ng-disabled="data.saving" ng-click="offerRequestPaymentComplaintPopupCtrl.save()"><?= Yii::t('app', 'Absenden') ?></button>
    </div>

</div>
