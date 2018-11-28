<div class="container new-users">
    <div class="welcome-text">
        <h2><?=Yii::t('app','Neu in deinem Netzwerk')?></h2>
    </div>

    <div class="account-box">
        <div class="account-friends-list clearfix" ng-if="log.items.length==0">
            <div class="result-empty-text"><?=Yii::t('app','Keine Benutzer gefunden')?></div>
        </div>
        <div class="account-friends-list clearfix" scroll-load="newNetworkMembersCtrl.loadMore" scroll-load-visible="0.7" scroll-load-has-more="log.hasMore" ng-if="log.items.length>0">
            <div class="account-friends-element" ng-repeat="user in log.items">
                <?php include('user-box.php'); ?>
            </div>
        </div>
        <div class="bottom-corner"></div>
    </div>

</div>