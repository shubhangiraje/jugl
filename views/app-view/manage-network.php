<div class="container">

    <div ng-click="showInfoPopup('view-manage-network')" ng-class="{'blink':isOneShowInfoPopup('view-manage-network')}" class="info-popup-btn"></div>

    <div class="welcome-text">
        <h2><?=Yii::t('app','Netzwerk verwalten')?></h2>
    </div>

    <div class="manage-network-header-box">
        <p><?= Yii::t('app', 'Hier kannst Du dein eigenes Netzwerk verwalten, indem Du Mitglieder aus der ersten Ebene in die tieferen Ebenen verschiebst. ') ?></p>
        <div class="field-box-input">
            <input type="text" ng-model="filter.name" ng-model-options="{ updateOn: 'default blur', debounce: { default: 500, blur: 0 } }" placeholder="<?= Yii::t('app','Mitglied finden') ?>">
        </div>
    </div>

    <div class="account-box manage-network-users">
        <div class="account-friends-list clearfix" scroll-load="manageNetworkCtrl.loadMore" scroll-load-visible="0.7" scroll-load-has-more="log.hasMore" ng-class="{empty:log.items.length == 0}">
            <div class="account-friends-element" ng-repeat="user in log.items">
                <div class="afe-content">
                    <a ui-sref="userProfile({id: user.id})" ng-if="user.isFriend" class="icon info"></a>
                    <a ui-sref="userProfile({id: user.id})" class="avatar"><img ng-src="{{user.avatar}}" alt="" /></a>
                    <div class="icon message" ng-click="messenger.talkWithUser(user.id)" ng-if="user.isFriend"></div>
                    <div class="padding">
                        <div class="name">{{user | userName}}  <div ng-click="updateCountry(user.id,user)" id="{{user.flag}}" class="flag flag-32 flag-{{user.flag}}"></div></div>
                        <div class="user-status" ng-class="{'online': user.online == 1 || user.online == 2, 'offline': !user.online}">{{user.online==2 ? "<?=Yii::t('app','online')?>" : (user.online==1 ? "<?=Yii::t('app','mobile')?>" : "<?=Yii::t('app','offline')?>")}}</div>
                    </div>
                    <a ui-sref="selectDestination({move_id: user.id})" class="select-user-btn"><?= Yii::t('app', 'wählen') ?></a>
                </div>
            </div>
        </div>
        <div class="bottom-corner"></div>
    </div>

</div>

