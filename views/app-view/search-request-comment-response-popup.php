<div class="close" ng-click="modalService.hide()"></div>
<div class="content" ng-controller="SearchRequestCommentResponsePopupCtrl as searchRequestCommentResponsePopupCtrl">

    <div class="modal-feedback-box">
        <label><?= Yii::t('app', 'Ihr Antwort:')?></label>
        <textarea maxlength="2000" ng-model="comment.response"></textarea>
    </div>

    <ul class="errors-list" ng-if="comment.$allErrors">
        <li ng-repeat="error in comment.$allErrors" ng-bind="error"></li>
    </ul>

    <div class="btn-box-modal-offer">
        <button type="button" class="btn btn-submit" ng-click="searchRequestCommentResponsePopupCtrl.save()" ng-disabled="comment.saving"><?= Yii::t('app', 'Absenden') ?></button>
    </div>

</div>
