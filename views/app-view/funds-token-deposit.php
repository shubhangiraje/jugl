<div id="funds">
    <div class="token-deposit-container">
        <div scroll-load="fundsTokenDepositCtrl.loadMore" scroll-load-visible="0.7" scroll-load-has-more="results.hasMore">
            <ul class="funds-stats-list">

                <li ng-if="log.items.length == 0" class="result-empty-text">
                    <?= Yii::t('app', 'Leider gibt es momentan keine festgelegte Tokens.') ?>
                </li>

                <li ng-repeat="item in log.items">
                    <div class="stats-item balance clearfix">
                        <div class="stats-item-icon">&#xe604;</div>
                        <div class="stats-item-title balance-token-title">
                            <div><?= Yii::t('app','Tokens festgelegt') ?>:</div>
                        </div>
                        <div class="stats-item-value">{{item.sum|priceFormat}}</div>
                    </div>
                    <div class="stats-sep clearfix">
                        <div class="stats-sep-title"><?= Yii::t('app','Zu welchem Preis gekauft') ?>:</div>
                        <div class="stats-sep-value">{{item.buy_sum|priceFormat}} <jugl-currency ng-if="item.buy_currency=='JUGLS'"></jugl-currency><span ng-if="item.buy_currency=='EUR'">&euro;</span></div>
                    </div>
                    <div class="stats-sep clearfix">
                        <div class="stats-sep-title"><?= Yii::t('app','Zeitraum') ?>:</div>
                        <div class="stats-sep-value">{{item.period_months/12}} <?=Yii::t('app','Jahr(e)')?></div>
                    </div>
                    <div class="stats-sep clearfix">
                        <div class="stats-sep-title"><?= Yii::t('app','Zinssatz') ?>:</div>
                        <div class="stats-sep-value">{{item.contribution_percentage}}%</div>
                    </div>
                    <div class="stats-sep clearfix">
                        <div class="stats-sep-title"><?= Yii::t('app','Zinsertrag') ?>:</div>
                        <div class="stats-sep-value">{{item.percent_sum|priceFormat}}</div>
                    </div>
                    <div class="stats-sep clearfix">
                        <div class="stats-sep-title"><?= Yii::t('app','Erstellt am') ?>:</div>
                        <div class="stats-sep-value">{{item.created_at|date:"dd.MM.yyyy"}}</div>
                    </div>

                    <div class="stats-sep clearfix">
                        <div class="stats-sep-title"><?= Yii::t('app','Wird freigegeben am') ?>:</div>
                        <div class="stats-sep-value">{{item.completion_dt|date:"dd.MM.yyyy"}}</div>
                    </div>

                    <div class="stats-sep clearfix">
                        <div class="stats-sep-title">
                            <?=Yii::t('app','Festgelegte Tokens werden als')?>
                            <span ng-if="item.payout_type=='TOKENS'"><?=Yii::t('app','Tokens')?></span>
                            <span ng-if="item.payout_type=='JUGLS'"><?=Yii::t('app','Jugls')?></span>
                            <?=Yii::t('app','ausgezahlt')?>
                        </div>
                        <div class="stats-sep-value">
                            <button ng-disabled="item.saving" ng-click="fundsTokenDepositCtrl.payoutTypeToggle(item)" class="btn btn-submit"><?= Yii::t('app','Typ der Auszahlung ändern') ?></button>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
