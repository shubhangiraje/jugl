<div class="close" ng-click="modalService.hide()"></div>
<div class="content" ng-controller="OfferHistoryViewPopupCtrl as offerHistoryViewPopupCtrl">
    <div scroll-pane scroll-config="{contentWidth: '0', autoReinitialise: true, showArrows: false}" auto-height-popup id="offer-history-view-scroll">

        <div class="offer-history-view">

            <div class="offer-history-view-user-box clearfix">
                <div class="found-user-avatar"><img ng-src="{{log.user.avatarSmall}}" alt=""/></div>
                <div class="found-user-name blurDetails">{{log.user|userName}}</div>
                <div class="found-user-city blurDetails">{{log.user.city}}</div>
            </div>

            <ul scroll-load="offerHistoryViewPopupCtrl.loadMore" scroll-load-visible="0.7" scroll-load-has-more="log.hasMore">
                <li ng-repeat="item in log.items" class="clearfix">
                    <div class="offer-history-view-create-dt">{{::item.create_dt|date:dateTimeFormat}}</div>
                    <div class="offer-history-view-duration">{{::item.duration|secondsToTime}}</div>
                </li>
            </ul>
        </div>

    </div>
</div>