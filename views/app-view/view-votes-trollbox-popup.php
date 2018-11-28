<div class="close" ng-click="modalService.hide()"></div>
<div class="content" ng-controller="VotesTrollboxPopupCtrl as votesTrollboxPopupCtrl">
    <div scroll-pane scroll-config="{contentWidth: '0', autoReinitialise: true, showArrows: false}" auto-height-popup id="votes-view-scroll">
        <div scroll-load="votesTrollboxPopupCtrl.loadMore" scroll-load-visible="0.7" scroll-load-has-more="log.hasMore" class="votes-view-box clearfix">
            <div ng-repeat="item in log.items" class="vote-user-box">
                <div class="vote-user">
                    <div ng-click="votesTrollboxPopupCtrl.goProfile(item.user.id)" class="pointer user-avatar-small">
                        <img ng-src="{{::item.user.avatar}}" alt=""/>
                    </div>
                    <div class="user-full-name">
                        <span ng-if="item.user.is_company_name">{{::item.user.company_name}}</span>
                        <span ng-if="!item.user.is_company_name">{{::item.user.first_name}}<br>{{::item.user.last_name}}</span>
                    </div>
                    <div ng-if="item.vote<0 && !type" class="vote negative"></div>
                    <div ng-if="item.vote>0 && !type" class="vote positive"></div>
                    <div ng-if="type" class="video-identification-score">{{item.user.video_identification_score}}</div>
                </div>
            </div>
        </div>
    </div>
</div>