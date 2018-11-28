<div class="searches">
    <div class="container">

        <div ng-click="showInfoPopup('view-searches-index')" ng-class="{'blink':isOneShowInfoPopup('view-searches-index')}" class="info-popup-btn"></div>

        <div class="welcome-text">
            <h2><?=Yii::t('app','Suchauftrag erstellen / recherchieren / vermitteln')?></h2>
            <p><?=Yii::t('app','Vermittlung, Recherche, Assistenz. Egal was Du oder andere suchen. Du bekommst dafür Vermittlungsprovision oder profitierst von den Angeboten.')?></p>
        </div>

        <div class="searches-index-link-box">
            <a ui-sref="searches.search" class="btn btn-submit">
                <?=Yii::t('app','Was suchen andere')?>
                <span class="badge" ng-if="status.stat_new_search_requests>0">{{status.stat_new_search_requests>99 ? '99+':status.stat_new_search_requests}}</span>
            </a>
            <a ui-sref="interests-searches.index" class="btn btn-submit"><?=Yii::t('app','Interessen angeben')?></a>
            <a ui-sref="searches.add" class="btn btn-submit"><?=Yii::t('app','Suchauftrag erstellen')?></a>
            <a ui-sref="searches.draft" class="btn btn-submit"><?=Yii::t('app','Entwürfe')?></a>
            <a ui-sref="searches.myList" class="btn btn-submit">
                <?=Yii::t('app','Was wird mir angeboten')?>
                <span class="badge" ng-if="status.stat_new_search_requests_offers>0">{{status.stat_new_search_requests_offers>99 ? '99+':status.stat_new_search_requests_offers}}</span>
            </a>
            <a ui-sref="searches.myOffers" class="btn btn-submit"><?=Yii::t('app','Was habe ich anderen vermittelt / angeboten ?')?></a>
        </div>


    </div>

</div>
