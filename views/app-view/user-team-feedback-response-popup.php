<div class="close" ng-click="modalService.hide()"></div>
<div class="content" ng-controller="UserTeamFeedbackResponsePopupCtrl as userTeamFeedbackResponsePopupCtrl">

    <div class="modal-feedback-box">
        <label><?= Yii::t('app', 'Ihr Antwort:')?></label>
        <textarea maxlength="2000" name="feedback" ng-model="feedback.response"></textarea>
    </div>

    <ul class="errors-list" ng-if="feedback.$allErrors">
        <li ng-repeat="error in feedback.$allErrors" ng-bind="error"></li>
    </ul>

    <div class="btn-box-modal-offer">
        <button type="button" class="btn btn-submit" ng-click="userTeamFeedbackResponsePopupCtrl.save()" ng-disabled="feedback.saving"><?= Yii::t('app', 'Absenden') ?></button>
    </div>

</div>
