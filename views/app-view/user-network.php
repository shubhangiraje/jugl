<div class="network container clearfix">

    <h2><?=Yii::t('app', 'Netzwerk von ');?> {{user|userName}}</h2>
    <div class="expand-network-button">
        <a ui-sref="friendsInvitation.invite"><?=Yii::t('app', 'Netzwerk erweitern')?></a>
    </div>
    <div class="network-box clearfix">
        <div class="your-network">
            <div class="your-network-count"><b>{{user.network_size}}</b><?=Yii::t('app', 'Personen in Deinem Netzwerk'); ?></div>
            <div class="your-network-count"><b>{{user.network_levels}}</b><?=Yii::t('app', 'Levels in Deinem Netzwerk'); ?></div>
        </div>
        <div class="network-tree">
            <div class="network-tree-user-title">
                <div ng-if="hierarchy.parent" ng-click="UserNetworkCtrl.hierarchyShowUser(hierarchy.parent)" class="tree-prev-icon"></div>
                <div class="network-tree-user-title-avatar"><img ng-src="{{hierarchy.user.avatar}}" alt="" /></div>
                <div class="network-tree-user-title-name">{{hierarchy.user.is_company_name ? hierarchy.user.company_name : hierarchy.user.first_name}}</div>
            </div>
            <div ng-if="hierarchy.user.users.length>0" class="network-tree-users">
                <ul class="tree-users-one">
                    <li ng-repeat="user in hierarchy.user.users" ng-class="{no_friends:user.users.length==0}">
                        <div class="network-tree-user-box">
                            <div class="network-tree-user-avatar"><a ui-sref="userProfile({id: user.id})"><img ng-src="{{user.avatarSmall}}" alt="" /></a></div>
                            <div class="network-tree-user-name">{{user.is_company_name ? user.company_name : user.first_name}}</div>
                        </div>
                        <ul class="tree-users-two" ng-class="{one_friend:user.users.length==1}">
                            <li ng-repeat="user in user.users" ng-class="{no_friends:user.users.length==0}">
                                <div class="network-tree-user-box">
                                    <div class="network-tree-user-avatar"><a ui-sref="userProfile({id: user.id})"><img ng-src="{{user.avatarSmall}}" alt="" /></a></div>
                                    <div class="network-tree-user-name">{{user.is_company_name ? user.company_name : user.first_name}}</div>
                                </div>
                                <ul class="tree-users-end" ng-class="{one_friend:user.users.length==1}">
                                    <li ng-repeat="user in user.users">
                                        <div class="network-tree-user-box">
                                            <div class="network-tree-user-avatar"><a ui-sref="userProfile({id: user.id})"><img ng-src="{{user.avatarSmall}}" alt="" /></a></div>
                                            <div class="network-tree-user-name">{{user.is_company_name ? user.company_name : user.first_name}}</div>
                                        </div>
                                        <div ng-click="UserNetworkCtrl.hierarchyShowUser(user.id)" ng-if="user.users===true" class="tree-next-icon"></div>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
        <div class="bottom-corner"></div>
    </div>
</div>
