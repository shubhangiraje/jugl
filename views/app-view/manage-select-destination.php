<div class="container">

    <div class="user-select-destination-box">
        <div class="avatar"><img ng-src="{{user.avatar}}" alt="" /></div>
        <div class="name">{{user|userName}}</div>
    </div>

    <div class="manage-network-header-box">
        <p><?= Yii::t('app', 'Wähle einen Mitglied Deines Netzwerks, in dessen Struktur <b>{{user|userName}}</b> verschoben werden soll.') ?></p>
        <div class="field-box-input">
            <input type="text" ng-model="filter.name" ng-model-options="{ updateOn: 'default blur', debounce: { default: 500, blur: 0 } }" placeholder="<?= Yii::t('app','Mitglied finden') ?>">
        </div>
    </div>

    <div class="account-box manage-network-users">
        <div class="link-back-box">
            <a href="" class="link-back" ng-click="back()"><?= Yii::t('app','Zurück') ?></a>
        </div>

        <div class="account-friends-list clearfix" scroll-load="manageSelectDestinationCtrl.loadMore" scroll-load-visible="0.7" scroll-load-has-more="log.hasMore" ng-class="{empty:log.items.length == 0}">
            <div class="account-friends-element" ng-repeat="item in log.items">
                <div class="afe-content">
                    <a ui-sref="selectDestination({move_id: user.id, id: item.id})" ng-if="item.hasChildren && filter.name==''" class="is-children"></a>
                    <a ui-sref="userProfile({id: item.id})" ng-if="item.isFriend" class="icon info"></a>
                    <a ui-sref="userProfile({id: item.id})" class="avatar"><img ng-src="{{item.avatar}}" alt="" /></a>
                    <div class="icon message" ng-click="messenger.talkWithUser(item.id)" ng-if="item.isFriend"></div>
                    <div class="padding">
                        <div class="name">{{item | userName}}  <div ng-click="updateCountry(item.id,item)" id="{{item.flag}}" class="flag flag-32 flag-{{item.flag}}"></div></div>
                        <div class="user-status" ng-class="{'online': item.online == 1 || item.online == 2, 'offline': !item.online}">{{item.online==2 ? "<?=Yii::t('app','online')?>" : (item.online==1 ? "<?=Yii::t('app','mobile')?>" : "<?=Yii::t('app','offline')?>")}}</div>
                    </div>

                    <div class="network-level" ng-if="filter.name!=''">Level {{item.level}}</div>

                    <button ng-click="manageSelectDestinationCtrl.select(item)" class="select-user-btn"><?= Yii::t('app', 'wählen') ?></button>
                </div>
            </div>
        </div>
        <div class="bottom-corner"></div>
    </div>

</div>

