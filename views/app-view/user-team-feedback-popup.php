<div class="content" ng-controller="UserTeamFeedbackPopupCtrl as userTeamFeedbackPopupCtrl">
    <div class="close" ng-click="userTeamFeedbackPopupCtrl.close()"></div>

    <div class="evaluation-box">
        <label ng-if="!feedback.isNotification"><?= Yii::t('app', 'Bewerten:') ?></label>
        <label ng-if="feedback.isNotification"><?= Yii::t('app', 'Bitte bewerte Deinen Teamleader')?></label>
        <div class="evaluation">
            <input type="radio" name="evaluation" value="20" ng-model="feedback.rating"><i></i>
            <input type="radio" name="evaluation" value="40" ng-model="feedback.rating"><i></i>
            <input type="radio" name="evaluation" value="60" ng-model="feedback.rating"><i></i>
            <input type="radio" name="evaluation" value="80" ng-model="feedback.rating"><i></i>
            <input type="radio" name="evaluation" value="100" ng-model="feedback.rating"><i></i>
        </div>
    </div>

    <div class="modal-feedback-box">
        <label><?= Yii::t('app', 'Ihr Feedback:')?></label>
        <textarea maxlength="2000" name="feedback" ng-model="feedback.feedback"></textarea>
    </div>

    <ul class="errors-list" ng-if="feedback.$allErrors">
        <li ng-repeat="error in feedback.$allErrors" ng-bind="error"></li>
    </ul>

    <div class="btn-box-modal-offer">
        <button type="button" class="btn btn-submit" ng-if="feedback.isNotification" ng-click="userTeamFeedbackPopupCtrl.close()"><?= Yii::t('app', 'Jetzt nicht') ?></button>
        <button type="button" class="btn btn-submit" ng-click="userTeamFeedbackPopupCtrl.save()" ng-disabled="feedback.saving"><?= Yii::t('app', 'Absenden') ?></button>
    </div>

</div>
