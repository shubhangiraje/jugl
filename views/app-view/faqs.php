<div class="faqs container">

    <div class="welcome-text">
        <h2><?=Yii::t('app','Fragen / Antworten')?></h2>
    </div>

    <div class="page-content">
        <div class="faq-box" scroll-load="faqsCtrl.loadMore" scroll-load-visible="0.7" scroll-load-has-more="log.hasMore">
            <div ng-repeat="item in log.items" class="faq-box-item" ng-class="{true: 'on', false: 'off'}[isQuestionOpen == true]" id="faq{{item.id}}">
                <div class="faq-item-question" ng-click="isQuestionOpen = !isQuestionOpen">{{::item.question}}</div>
                <div class="faq-item-response" ng-init="isQuestionOpen = false" ng-show="isQuestionOpen" ng-bind-html="item.response|linky:'_blank'"></div>
            </div>
        </div>

        <div class="bottom-corner"></div>
    </div>


</div>