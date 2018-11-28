<div class="close" ng-click="modalService.hide()"></div>
<div class="content" ng-controller="SearchRequestOfferDetailsFeedbackCtrl as searchRequestOfferDetailsFeedbackCtrl">

    <div class="evaluation-box">
        <label><?= Yii::t('app', 'Bewerten:') ?></label>
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
        <textarea name="feedback" ng-model="feedback.feedback"></textarea>
    </div>

    <ul class="errors-list" ng-if="feedback.$allErrors">
        <li ng-repeat="error in feedback.$allErrors" ng-bind="error"></li>
    </ul>

    <div class="btn-box-modal-offer">
        <button type="button" class="btn btn-submit" ng-click="searchRequestOfferDetailsFeedbackCtrl.save()" ng-disabled="feedback.saving"><?= Yii::t('app', 'Handel abschlie&szlig;en') ?></button>
    </div>

</div>
