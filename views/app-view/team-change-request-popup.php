<div class="close" ng-click="modalService.hide()"></div>
<div class="content" ng-controller="TeamChangeRequestPopupCtrl as teamChangeRequestPopupCtrl">

    <div class="modal-feedback-box">
        <label><?= Yii::t('app', 'Teamanfrage an') ?></label>
        <label><span>{{userTeamRequest.user.name}}</span></label>
        <textarea maxlength="2000" name="feedback" ng-model="userTeamRequest.text" placeholder="Schreibe hier einen Text, warum Du in das Team aufgenommen werden willst"></textarea>
    </div>

    <ul class="errors-list" ng-if="userTeamRequest.$allErrors">
        <li ng-repeat="error in userTeamRequest.$allErrors" ng-bind="error"></li>
    </ul>

    <div class="btn-box-modal-offer">
        <button type="button" class="btn btn-submit" ng-click="modalService.hide()" ng-disabled="userTeamRequest.saving"><?= Yii::t('app', 'Abbrechen') ?></button>
        <button type="button" class="btn btn-submit" ng-click="teamChangeRequestPopupCtrl.save()" ng-disabled="userTeamRequest.saving"><?= Yii::t('app', 'Absenden') ?></button>
    </div>

</div>
