<div class="close" ng-click="modalService.hide()"></div>
<div class="content" ng-controller="SearchRequestOfferDetailsRejectCtrl as searchRequestOfferDetailsRejectCtrl">

    <div class="reject-box">
        <h1><?= Yii::t('app', 'Ablehnungsgrund'); ?></h1>

        <ul class="reject-list">
            <li>
                <input type="radio" value="OFFER_NOT_FIT" i-check ng-model="reject.reject_reason"/>
                <label><?= Yii::t('app', 'Das Angebot passt nicht'); ?></label>
            </li>
            <li>
                <input type="radio" value="CHANGED_MY_MIND" i-check ng-model="reject.reject_reason"/>
                <label><?= Yii::t('app', 'Ich habe mich anders entschieden'); ?></label>
            </li>
            <li>
                <input type="radio" value="OFFER_IS_EXPENSIVE" i-check ng-model="reject.reject_reason"/>
                <label><?= Yii::t('app', 'Das Angebot ist zu teuer'); ?></label>
            </li>
            <li>
                <input type="radio" value="OTHERS" i-check ng-model="reject.reject_reason"/>
                <label><?= Yii::t('app', 'Sonstiges'); ?></label>
            </li>
        </ul>

        <div class="reject-comment">
            <textarea name="comment" ng-model="reject.reject_comment"></textarea>
        </div>

        <ul class="errors-list" ng-if="reject.$allErrors">
            <li ng-repeat="error in reject.$allErrors" ng-bind="error"></li>
        </ul>

        <div class="reject-btn">
            <button type="button" class="btn btn-submit" ng-click="searchRequestOfferDetailsRejectCtrl.save()" ng-disabled="reject.saving" ><?= Yii::t('app', 'Absenden') ?></button>
        </div>

    </div>

</div>
