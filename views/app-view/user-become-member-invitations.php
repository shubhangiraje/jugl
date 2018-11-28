<div class="container">
    <div class="welcome-text">
        <h2><?=Yii::t('app','Gewinner')?></h2>
    </div>


    <ul class="user-become-member-invitations-list">
        <li class="clearfix" ng-repeat="item in items">
            <div class="found-user-box clearfix">
                <a ui-sref="userProfile({id: item.user.id})">
                    <div class="found-user-avatar">
                        <img ng-src="{{::item.user.avatarSmall}}">
                    </div>
                </a>
                <div class="found-user-name ng-binding">{{::item.user|userName}}</div>
                <div class="offer-user-rating">
                    <div class="star-rating">
                        <span once-style="{width:(+item.user.rating)+'%'}"></span>
                    </div>
                    <div class="user-feedback-count ng-binding">({{::item.user.feedback_count}})</div>
                    <div ng-if="item.user.packet=='VIP'" class="user-packet ng-scope">&nbsp;</div>
                    <div ng-if="item.user.packet=='VIP_PLUS'" class="user-packet-vip-plus ng-scope">&nbsp;</div>
                </div>
            </div>
            <div class="user-become-member-winner-dt">
                <div>{{::item.dt|date:'dd.MM.yyyy'}}</div>
                <div>{{::item.dt|date:'HH:mm:ss'}}.{{::item.ms}} <?= Yii::t('app', 'Uhr')?></div>
            </div>
        </li>
    </ul>


</div>