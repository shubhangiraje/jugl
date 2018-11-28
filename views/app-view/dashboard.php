<div class="dashboard container clearfix">
    <div class="my-net-column account-column">
        <div class="invite-friends-box">
            <h2><?=Yii::t('app','Freunde einladen & Geld verdienen')?></h2>
            <?=Yii::t('app','Für jeden Freund, der sich durch Deine Einladung registriert, bekommst Du 100 Jugls (1€). Wenn Deine Freunde wiederum Freunde einladen, erhältst du jeweils 29% von deren Gewinn und wenn diese wiederum Freunde einladen, das Gleiche usw. usw. usw.')?>
            <a ui-sref="friendsInvitation.invite"><?=Yii::t('app','Jetzt Freunde & Bekannte einladen')?></a>
            <div class="bottom-corner"></div>
        </div>

        <div class="account-box">
            <h2 class="network"><?=Yii::t('app','Mein Netzwerk')?></h2>

            <div class="network-tree">
                <div class="network-tree-user-title">
                    <div ng-if="hierarchy.parent" ng-click="dashboardCtrl.hierarchyShowUser(hierarchy.parent)" class="tree-prev-icon"></div>
                    <div class="network-tree-user-title-avatar"><img ng-src="{{hierarchy.user.avatar}}" alt="" /></div>
                    <div class="network-tree-user-title-name">{{hierarchy.user.first_name}}</div>
                </div>
                <div ng-if="hierarchy.user.users.length>0" class="network-tree-users">
                    <ul class="tree-users-one">
                        <li ng-repeat="user in hierarchy.user.users" ng-class="{no_friends:user.users.length==0}">
                            <div class="network-tree-user-box">
                                <div class="network-tree-user-avatar"><a ui-sref="userProfile({id: user.id})"><img ng-src="{{user.avatarSmall}}" alt="" /></a></div>
                                <div class="network-tree-user-name">{{user.first_name}}</div>
                            </div>
                            <ul class="tree-users-end" ng-class="{one_friend:user.users.length==1}">
                                <li ng-repeat="user in user.users" ng-class="{no_friends:user.users.length==0}">
                                    <div class="network-tree-user-box">
                                        <div class="network-tree-user-avatar"><a ui-sref="userProfile({id: user.id})"><img ng-src="{{user.avatarSmall}}" alt="" /></a></div>
                                        <div class="network-tree-user-name">{{user.first_name}}</div>
                                    </div>
                                    <div ng-click="dashboardCtrl.hierarchyShowUser(user.id)" ng-if="user.users===true" class="tree-next-icon"></div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="bottom-corner"></div>
        </div>
    </div>

    <div class="my-friends-column account-column">
        <div class="account-box">
            <h2 class="men"><?=Yii::t('app','Meine Kontakte')?></h2>
            <div class="account-friends-list clearfix" ng-class="{empty:friends.users.length == 0}">
                <div class="account-friends-element" ng-repeat="user in friends.users">
                    <?php include('user-box.php'); ?>
                </div>
                <div class="account-friends-element invite-friend">
                    <div class="afe-content">
                        <div class="circle"><div class="plus"></div></div>
                        <div class="padding">
                            <div class="invite-title"><?=Yii::t('app','Freunde einladen')?></div>
                        </div>
                        <a ui-sref="friendsInvitation.invite" class="link"></a>
                    </div>
                </div>
            </div>
            <div ng-if="friends.pages > 1" class="friends-list-navigation">
                <span class="prev" ng-click="friends.page < 2 || dashboardCtrl.friendsNavigationClick(-1)" ng-class="{disabled:friends.page < 2}"></span>
                <span class="next" ng-click="friends.page >= friends.pages || dashboardCtrl.friendsNavigationClick(1)" ng-class="{disabled:friends.page >= friends.pages}"></span>
            </div>
            <div class="bottom-corner"></div>
        </div>
    </div>
</div>
