<div class="close" ng-click="modalService.hide()"></div>
<div class="content" ng-controller="TeamChangeRequestPopup2Ctrl as teamChangeRequestPopupCtrl">

    <div class="modal-feedback-box">
        <label><span>{{userTeamRequest.user.name}}</span></label>
        <label><?= Yii::t('app', 'abwerben') ?></label>
        <textarea maxlength="2000" name="feedback" ng-model="userTeamRequest.text" placeholder="<?= Yii::t('app','Erkläre hier dem neuen Mitglied, warum Du der bessere Teamleader bist und warum es in Dein Team wechseln soll.') ?>"></textarea>
    </div>

    <ul class="errors-list" ng-if="userTeamRequest.$allErrors">
        <li ng-repeat="error in userTeamRequest.$allErrors" ng-bind="error"></li>
    </ul>

    <div class="btn-box-modal-offer">
        <button type="button" class="btn btn-submit" ng-click="modalService.hide()" ng-disabled="userTeamRequest.saving"><?= Yii::t('app', 'Abbrechen') ?></button>
        <button type="button" class="btn btn-submit" ng-click="teamChangeRequestPopupCtrl.save()" ng-disabled="userTeamRequest.saving"><?= Yii::t('app', 'Absenden') ?></button>
    </div>

</div>
