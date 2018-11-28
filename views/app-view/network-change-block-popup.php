<div class="close" ng-click="modalService.hide()"></div>
<div class="content" ng-controller="NetworkChangeBlockPopupCtrl as networkChangeBlockPopupCtrl">

    <h3><?= Yii::t('app','Bitte Grund eingeben') ?></h3>

    <div class="modal-feedback-box">
        <textarea maxlength="2000" name="feedback" ng-model="stickUserRequest.text"></textarea>
    </div>

    <ul class="errors-list" ng-if="stickUserRequest.$allErrors">
        <li ng-repeat="error in stickUserRequest.$allErrors" ng-bind="error"></li>
    </ul>

    <div class="btn-box-modal-offer">
        <button type="button" class="btn btn-submit" ng-click="networkChangeBlockPopupCtrl.send()" ng-disabled="feedback.saving"><?= Yii::t('app', 'Absenden') ?></button>
    </div>

</div>