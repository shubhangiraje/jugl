<div id="funds">

    <ul class="funds-stats-list">
        <li>
            <div class="stats-item balance clearfix">
                <div class="stats-item-icon">&#xe604;</div>
                <div class="stats-item-title balance-token-title">
                    <div><?= Yii::t('app','Tokenstand') ?>:</div>
                </div>
                <div class="stats-item-value">{{status.balance_token | priceFormat}}</div>
            </div>
            <div class="stats-sep clearfix">
                <div class="stats-sep-title"><?= Yii::t('app','Tokens durch eigenen Kauf') ?>:</div>
                <div class="stats-sep-value">{{status.balance_token_buyed | priceFormat }}</div>
            </div>
            <div class="stats-sep clearfix">
                <div class="stats-sep-title"><?= Yii::t('app','Tokens durch Netzwerk') ?>:</div>
                <div class="stats-sep-value">{{status.balance_token_earned | priceFormat }}</div>
            </div>
        </li>
    </ul>

    <div class="funds-transaction">
        <h3><?=Yii::t('app','Transaktions&uuml;bersicht')?></h3>
        <div class="funds-filter-box clearfix">

            <div class="funds-filter-field year">
                <label><?=Yii::t('app', 'Jahr:')?></label>
                <div class="funds-filter-select" dropdown-toggle select-click>
                    <select ng-model="fundsLogTokenCtrl.filters.year" selectpicker="{title:''}" ng-options="year.key as year.val for year in yearList"></select>
                </div>
            </div>

            <div class="funds-filter-field month">
                <label><?=Yii::t('app', 'Monat:')?></label>
                <div class="funds-filter-select" dropdown-toggle select-click>
                    <select ng-model="fundsLogTokenCtrl.filters.month" selectpicker="{title:''}" ng-options="month.key as month.val for month in monthList">
                    </select>
                </div>
            </div>

            <div class="funds-sort-field status">
                <div class="funds-filter-select" dropdown-toggle select-click>
                    <select ng-model="fundsLogTokenCtrl.filters.status" selectpicker="{title:''}" ng-options="status.key as status.val for status in statusList"></select>
                </div>
            </div>
        </div>

        <div ng-if="log.items.length > 0" class="account-info-table">
            <table responsive-table scroll-load="fundsLogTokenCtrl.loadMore" scroll-load-visible="0.7" scroll-load-has-more="log.hasMore">
                <thead>
                <tr>
                    <th><div ng-click="fundsLogTokenCtrl.setSort('dt')" ng-class="{'sort-asc':state.sort=='dt','sort-desc':state.sort=='-dt'}" class="clickable"><?=Yii::t('app','Datum')?></div></th>
                    <th><?=Yii::t('app','Transaktionstyp')?></th>
                    <th><div ng-click="fundsLogTokenCtrl.setSort('sum')" ng-class="{'sort-asc':state.sort=='sum','sort-desc':state.sort=='-sum'}" class="clickable"><?=Yii::t('app','Betrag')?></div></th>
                    <th><?=Yii::t('app','Eingang durch')?></th>
                </tr>
                </thead>
                <tbody>
                <tr ng-repeat="item in log.items">
                    <td>{{::item.dt | date:'dd.MM.yyyy HH:mm'}}</td>
                    <td>
                        <span class="funds-log-type">
                            <span ng-if="item.sum >= 0" class="plus-sum" event-text="item.type"></span>
                            <span ng-if="item.sum < 0" class="minus-sum" event-text="item.type"></span>
                        </span>
                    </td>
                    <td>
                        <span ng-if="item.sum >= 0" class="plus-sum">{{::item.sum|priceFormat:true}}</span>
                        <span ng-if="item.sum < 0" class="minus-sum">{{::item.sum|priceFormat:true}}</span>
                    </td>
                    <td>
                        <a ui-sref="userProfile({id: item.user.id})">
                            <img ng-src="{{item.user.avatarSmall}}" ng-if="item.user.avatar" class="user-avatar" alt="" />
                        </a>
                        {{::item.user | userName}}
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

    </div>

</div>
