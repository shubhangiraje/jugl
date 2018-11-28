<div class="close" ng-click="modalService.hide()"></div>
<div class="content" ng-controller="SpamReportPopupCtrl as spamReportPopupCtrl">

    <div class="modal-feedback-box">
        <label><?= Yii::t('app', 'Grund:')?></label>
        <textarea name="comment" ng-model="spamReport.comment"></textarea>
    </div>

    <ul class="errors-list" ng-if="spamReport.$allErrors">
        <li ng-repeat="error in spamReport.$allErrors" ng-bind="error"></li>
    </ul>

    <div class="btn-box-modal-offer">
        <button type="button" class="btn btn-submit" ng-click="spamReportPopupCtrl.save()" ng-disabled="spamReport.saving"><?= Yii::t('app', 'Absenden') ?></button>
    </div>

</div>
