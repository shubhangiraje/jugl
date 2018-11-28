<div class="searches">
    <div class="container">
        <div ng-click="showInfoPopup('view-offers-index')" ng-class="{'blink':isOneShowInfoPopup('view-offers-index')}" class="info-popup-btn"></div>
        <div class="welcome-text">
            <h2><?=Yii::t('app','Kaufen / verkaufen, Interessen angeben')?></h2>
            <p><?=Yii::t('app','Ich gebe meine Interessen an (von der Zahnbürste bis zum Luxus Auto), bleibe dabei anonym und bekomme vorwiegend von Händlern, aber auch von Privatpersonen auf meinen Interessen basierende Produktangebote (Werbung). Für jede gelesene Werbung verdienst Du Jugls')?>(<span class="jugl-icon-light"></span>).</p>
        </div>

        <div class="searches-index-link-box">
            <a ui-sref="interests.index" class="btn btn-submit"><?=Yii::t('app','Suchen / Interessen angeben')?></a>
            <a ui-sref="offers.search" class="btn btn-submit">
                <?=Yii::t('app','Kaufen / Suchergebnisse / Werbung lesen')?>
                <span class="badge" ng-if="status.stat_new_offers>0">{{status.stat_new_offers>99 ? '99+':status.stat_new_offers}}</span>
            </a>
            <a ui-sref="offers.add" class="btn btn-submit"><?=Yii::t('app','Verkaufen / Werbung schalten')?></a>
            <?php /* ?><a ui-sref="offers.juglSearch" class="btn btn-submit"><?=Yii::t('app','Jugls kaufen / verkaufen')?></a><?php */?>
            <a ui-sref="offers.draft" class="btn btn-submit"><?=Yii::t('app','Entwürfe')?></a>
            <a ui-sref="offers.myRequests" class="btn btn-submit"><?=Yii::t('app','Ich habe geboten / gekauft')?></a>
            <a ui-sref="offers.myList" class="btn btn-submit">
                <?=Yii::t('app','Meine Anzeigen / Werbung verwalten')?>
                <span class="badge" ng-if="status.stat_new_offers_requests>0">{{status.stat_new_offers_requests>99 ? '99+':status.stat_new_offers_requests}}</span>
            </a>
            <a ui-sref="funds.payin" class="btn btn-submit"><?=Yii::t('app','Jugls aufladen')?></a>
        </div>

    </div>

</div>
