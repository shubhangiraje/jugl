<div class="close" ng-click="modalService.hide()"></div>
<div class="content" ng-controller="SearchRequestCommentPopupCtrl as searchRequestCommentPopupCtrl">

    <div class="modal-feedback-box">
        <label><?= Yii::t('app', 'Frage hinzufügen')?>:</label>
        <textarea maxlength="2000" ng-model="comment.comment"></textarea>
    </div>

    <ul class="errors-list" ng-if="comment.$allErrors">
        <li ng-repeat="error in comment.$allErrors" ng-bind="error"></li>
    </ul>

    <div class="btn-box-modal-offer">
        <button type="button" class="btn btn-submit" ng-click="searchRequestCommentPopupCtrl.save()" ng-disabled="comment.saving"><?= Yii::t('app', 'Absenden') ?></button>
    </div>

</div>
