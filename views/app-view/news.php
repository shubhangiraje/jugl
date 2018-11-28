<div class="news container">

    <div ng-click="showInfoPopup('view-news')" ng-class="{'blink':isOneShowInfoPopup('view-news')}" class="info-popup-btn"></div>

    <div class="welcome-text">
        <h2><?=Yii::t('app','Unsere News')?></h2>
    </div>

    <div class="page-content">
        <div class="news-box" scroll-load="newsCtrl.loadMore" scroll-load-visible="0.7" scroll-load-has-more="log.hasMore">
            <div ng-repeat="item in log.items" class="news-box-item" id="news{{item.id}}">
                <div class="clearfix">
                    <div class="news-item-picture-box">
                        <a ng-if="item.images.fancybox" fancybox fancybox-force-init="true" href="{{item.images.fancybox}}">
                            <div class="news-item-picture">
                                <img ng-src="{{::item.images.image}}" alt="">
                            </div>
                        </a>
                        <div ng-if="!item.images.fancybox" class="news-item-picture">
                            <img ng-src="{{::item.images.image}}" alt="">
                        </div>
                    </div>
                    <div class="news-item-info-box">
                        <div class="news-item-title">{{::item.title}}</div>
                        <div class="news-item-dt">{{::item.dt|date:'dd.MM.yyyy'}}</div>
                        <div class="news-item-text" ng-bind-html="item.text|linky:'_blank'"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bottom-corner"></div>
    </div>


</div>