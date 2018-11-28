<div class="afe-content">
<!--    --><?php //if(!empty($myFriendsPage)): ?><!--<div class="delete" ng-click="friendsCtrl.deleteFromFriends(user.id)"></div>--><?php //endif; ?>
    <a ui-sref="userProfile({id: user.id})" ng-if="user.isFriend" class="icon info"></a>
    <a ui-sref="userProfile({id: user.id})" class="avatar"><img ng-src="{{user.avatar}}" alt="" /></a>
    <div class="icon message" ng-click="messenger.talkWithUser(user.id)" ng-if="user.isFriend"></div>
    <div class="padding">
        <div class="name">{{user | userName}}  <div ng-click="updateCountry(user.id,user)" id="{{user.flag}}" class="flag flag-32 flag-{{user.flag}}"></div></div>
        <div class="place" ng-bind="user.address"></div>
        <div class="user-status" ng-class="{'online': user.online == 1 || user.online == 2, 'offline': !user.online}">{{user.online==2 ? "<?=Yii::t('app','online')?>" : (user.online==1 ? "<?=Yii::t('app','mobile')?>" : "<?=Yii::t('app','offline')?>")}}</div>
    </div>
</div>
