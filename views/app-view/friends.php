<div id="friends-page">
    <div class="container">
        <h1><?=Yii::t('app','Meine Kontakte')?></h1>
        <div class="filter-box clearfix">
            <div class="fields-group">
                <b class="fields-group-title"><?=Yii::t('app','Sortieren:')?></b>
                <div class="fields-group-field">
                    <input type="radio" value="dt" i-check ng-model="filter.sort" />
                    <label><?=Yii::t('app','nach Datum')?></label>
                </div>
                <div class="fields-group-field">
                    <input type="radio" value="alpha" i-check ng-model="filter.sort" />
                    <label><?=Yii::t('app','alphabetisch')?></label>
                </div>
            </div>
            <div class="fields-group">
                <b class="fields-group-title"><?=Yii::t('app','Status:')?></b>
                <div class="fields-group-field">
                    <input type="radio" value="online" i-check ng-model="filter.statusFilter" />
                    <label><?=Yii::t('app','online')?></label>
                </div>
                <div class="fields-group-field">
                    <input type="radio" value="all" i-check ng-model="filter.statusFilter" />
                    <label><?=Yii::t('app',' alle')?></label>
                </div>
            </div>
            <div class="fields-search">
                <input type="text" class="friends-search" ng-model="filter.nameFilter" ng-model-options="{ updateOn: 'default blur', debounce: { default: 500, blur: 0 } }" placeholder="<?=Yii::t('app','Nach Namen suchen...')?>" />
            </div>
        </div>
        <div class="account-box">
            <div class="account-friends-list clearfix" scroll-load="friendsCtrl.loadMore" scroll-load-visible="0.7" scroll-load-has-more="friends.hasMore" ng-class="{empty:friends.users.length == 0}">
                <div class="account-friends-element" ng-repeat="user in friends.users">
                    <?php
                        $myFriendsPage = true;
                        include('user-box.php');
                    ?>
                </div>
            </div>
            <div class="bottom-corner"></div>
        </div>
    </div>
</div>
