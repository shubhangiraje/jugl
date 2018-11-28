<div class="close" ng-click="modalService.hide()"></div>
<div class="content" ng-controller="OfferDetailsRequestCtrl as offerDetailsRequestCtrl">

    <div class="modal-feedback-box">
        <label style="text-align: left"><?= Yii::t('app', 'Kommentar an den Anbieter verfassen:')?></label>
        <textarea name="feedback" ng-model="offerRequest.description"></textarea>
    </div>

    <ul class="errors-list" ng-if="offerRequest.$allErrors">
        <li ng-repeat="error in offerRequest.$allErrors" ng-bind="error"></li>
    </ul>

    <div class="btn-box-modal-offer">
        <button type="button" class="btn btn-submit" ng-click="offerDetailsRequestCtrl.save()" ng-disabled="offerRequest.saving"><?= Yii::t('app', 'Abschicken') ?></button>
    </div>

</div>
