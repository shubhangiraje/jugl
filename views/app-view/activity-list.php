<div class="page-activity-list">
    <div class="container">
        <div ng-click="showInfoPopup('view-activities')" ng-class="{'blink':isOneShowInfoPopup('view-activities')}" class="info-popup-btn"></div>

        <div class="filter-activity-list-box clearfix">

            <div class="change-activity-btn-box">
                <button ng-click="activityLogCtrl.changeMode('event_follower')" ng-if="mode=='event_follower'" class="btn btn-submit">
                    <?= Yii::t('app', 'Meine Aktivitäten') ?>
                    <span class="badge" ng-if="status.new_events>0">{{status.new_events>99 ? '99+':status.new_events}}</span>
                </button>
                <button ng-click="activityLogCtrl.changeMode('event')" ng-if="mode=='event'" class="btn btn-submit">
                    <?= Yii::t('app', 'Abos Aktivitäten') ?>
                    <span class="badge" ng-if="status.new_follower_events>0">{{status.new_follower_events>99 ? '99+':status.new_follower_events}}</span>
                </button>
            </div>

            <div class="filter-activity-list" ng-if="mode=='event'">
                <div class="field-box-select" dropdown-toggle select-click>
                    <select ng-model="activityLogCtrl.filters.type" selectpicker>
                        <option value=""><?= Yii::t('app', 'Alle Aktivit&auml;ten') ?></option>
                        <!--                        <option value="FRIEND_REQUEST">--><?//= Yii::t('app', 'Kontaktanfragen') ?><!--</option>-->
                        <option value="NEW_NETWORK_MEMBER"><?= Yii::t('app', 'Wer ist neu in meinem Netzwerk?') ?></option>
                        <option value="OFFER_MY_REQUEST"><?= Yii::t('app', 'Ich habe diese Artikel gekauft');?></option>
                        <option value="OFFER_REQUEST_SOLD"><?= Yii::t('app', 'Ich habe diese Artikel verkauft') ?></option>
                        <option value="OFFER_MY_REQUEST_BET"><?= Yii::t('app', 'Ich habe auf diese Artikel geboten') ?></option>
                        <option value="OFFER_REQUEST_NEW_BET"><?= Yii::t('app', 'Gebote, die ich auf meine Artikel erhalten habe') ?></option>
                        <option value="OFFER_REQUEST_ACCEPTED"><?= Yii::t('app', 'Ich muss noch bezahlen');?></option>
                        <option value="OFFER_REQUEST_ACCEPTED_PAYED"><?= Yii::t('app', 'Ich habe schon bezahlt');?></option>
                        <option value="OFFER_REQUEST_PAYING_PAYED"><?= Yii::t('app', 'Erwarteter Geldeingang');?></option>
                        <option value="OFFER_REQUEST_PAYING_PAYED_CONFIRMED"><?= Yii::t('app', 'Bestätigter Geldeingang');?></option>
                        <option value="WAS_WIRD_MIR_ANGEBOTEN"><?= Yii::t('app', 'Was wird mir angeboten / vermittelt?');?></option>
                        <option value="SEARCH_REQUEST_MY_OFFER"><?= Yii::t('app', 'Ich habe angeboten / vermittelt ');?></option>
                        <option value="AKZEPTIERTES_ANGEBOT"><?= Yii::t('app', 'Was habe ich erfolgreich angeboten / vermittelt?');?></option>
                        <option value="ICH_WURDE_BEWERTET"><?= Yii::t('app', 'Ich wurde bewertet');?></option>
                        <option value="MY_FEEDBACKS"><?= Yii::t('app', 'Ich habe bewertet');?></option>
                        <option value="BROADCAST_MESSAGE"><?= Yii::t('app', 'Nachrichten vom Administrator');?></option>
                        <option value="TEAM_CHANGE"><?= Yii::t('app', 'Teamwechsel');?></option>
                        <option value="LIKE"><?= Yii::t('app', 'Wer hat mich geliket');?></option>
                    </select>
                </div>
            </div>

            <div class="filter-activity-list" ng-if="mode=='event_follower'">
                <div class="field-box-select" dropdown-toggle select-click>
                    <select ng-model="activityLogCtrl.filters.type" selectpicker>
                        <option value=""><?= Yii::t('app', 'Alle Aktivit&auml;ten') ?></option>
                        <option value="NEW_OFFER"><?= Yii::t('app', 'Werbung erstellt') ?></option>
                        <option value="NEW_SEARCH_REQUEST"><?= Yii::t('app', 'Suchauftrag erstellt') ?></option>
                        <option value="OFFER_BUY"><?= Yii::t('app', 'Gekauft') ?></option>
                        <option value="OFFER_BET"><?= Yii::t('app', 'Gebot abgegeben') ?></option>
                        <option value="NEW_SEARCH_REQUEST_OFFER"><?= Yii::t('app', 'Angebot auf einen Suchauftrag') ?></option>
                        <option value="NEW_REFERRAL"><?= Yii::t('app', 'Neue Mitglieder im Team') ?></option>
                        <option value="NEW_TROLLBOX_MESSAGE"><?= Yii::t('app', 'Neue Beiträge im Forum') ?></option>
                        <option value="NEW_INFO_COMMENT"><?= Yii::t('app', 'Neue Beiträge im Jugl-Wiki') ?></option>
                    </select>
                </div>
            </div>
        </div>

        <div class="activity-title">
            <span ng-if="mode=='event'"><?= Yii::t('app', 'Meine Aktivitäten') ?></span>
            <span ng-if="mode=='event_follower'"><?= Yii::t('app', 'Abos Aktivitäten')?></span>
        </div>

        <div class="activity-list-box" scroll-load="activityLogCtrl.loadMore" scroll-load-visible="0.7" scroll-load-has-more="log.hasMore">

            <div ng-repeat="event in log.items" class="activity-list-item clearfix">
                <div class="activity-dt-box">
                    <div class="activity-dt">{{::event.dt|date:dateTimeFormat}}</div>
                </div>
                <div class="activity-user-box clearfix">
                    <a href="" ng-click="activityLogCtrl.goProfile(event.user.id)" class="activity-user-avatar-box">
                        <div set-if="event.user" class="activity-user-avatar">
                            <img src="{{::event.user.avatarSmall}}" alt="default_avatar"/>
                        </div>
                    </a>
                    <div set-if="event.user" class="activity-username" ng-class="{'without-flag': !event.user.id}">{{::event.user|userName}}
                        <div set-if="event.user.id" ng-click="updateCountry(event.user.id,log.items)" id="{{event.user.flag ? event.user.flag : 'de'}}" class="flag flag-32 flag-{{event.user.flag ? event.user.flag : 'de'}}"></div>
                    </div>
                </div>

                <div class="activity-text-box">
                    <div class="activity-text" event-text="event.text"></div>
                </div>

            </div>

        </div>

    </div>
</div>


