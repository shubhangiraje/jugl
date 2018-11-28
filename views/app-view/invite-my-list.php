<div class="invite-my-users container">
    <div ng-click="showInfoPopup('view-invite-my-list')" ng-class="{'blink':isOneShowInfoPopup('view-invite-my-list')}" class="info-popup-btn"></div>
    <div class="welcome-text">
        <h2 class="trollbox-heading"><?=Yii::t('app','Netzwerk aufbauen')?></h2>
        <div class="box">
			 <h2 class="detail-search-filter"> 
			 <a ng-click="refreshUserList()" class="invite-my-refresh-btn" href="">Aktualisieren</a>
			 <span class="disNone"><?=Yii::t('app','Netzwerk aufbauen')?></span>
			
				<div class="searches-filter-list">
					<div class="field-box-select filter-select">
                        <multiselect ng-model="currentCountry" labels="labels"
                            options="countryList" id-prop="id" display-prop="name" show-select-all="true" show-unselect-all="true" show-search="true" >
                        </multiselect>
                    </div>
				</div>
			</h2>
		</div>
    </div>

    <div class="invite-my-list-box">

        <?php /*
        <div class="adsense-box">
            <adsense ad-client="ca-pub-5908295137597899" ad-slot="123456789" inline-style="display:inline-block;width:728px;height:90px" ad-format="auto"></adsense>
        </div>
        */ ?>

        <div class="box-text text-center clearfix" ng-show="log.items.length==0"><p><?= Yii::t('app','Für die ausgewählten Länder existieren aktuell keine neuen Mitglieder.') ?></p></div>

        <div ng-if="state.loading" class="loader-box">
            <div class="spinner"></div>
        </div>

        <div class="clearfix" scroll-load="inviteMyListCtrl.loadMore" scroll-load-visible="0.7" scroll-load-has-more="log.hasMore" ng-if="log.items.length>0">
            <div class="invite-my-item-box" ng-repeat="user in log.items" ng-if="!user.winner && user_status.delay_invited_member <= 0">
                <div class="invite-my-box">
                    <div class="invite-my-user">
                        <a ui-sref="userProfile({id: user.id})">{{::user|userName}} <div ng-click="updateCountry(user.id,log.items)"  id="{{::user.flag}}" class="flag flag-32 flag-{{user.flag}}"></div></a>
                    </div>
                    <a ng-click="inviteMyListCtrl.invite(user)" href="" class="invite-my-btn"><?= Yii::t('app','Einladen') ?></a>
                </div>
            </div>
            <div class="invite-my-item-box" ng-repeat="user in log.items" ng-if="user.winner">
                <div class="invite-my-box">
                    <div class="invite-my-user">
                        <a ui-sref="userProfile({id: user.id})">{{::user|userName}} <div id="{{::user.flag}}" class="flag flag-32 flag-{{::user.flag}}"></div></a>
                    </div>
                    <a ng-if="!user.winner" ng-click="inviteMyListCtrl.invite(user)" href="" class="invite-my-btn"><?= Yii::t('app','Einladen') ?></a>
                    <div ng-if="user.winner" class="invite-winner-box">
                        <div class="invite-winner-text"><?= Yii::t('app','eingeladen') ?>:</div>
                        <div class="invite-winner-dt">{{::user.winner.dt|date:'dd.MM.yyyy HH:mm'}} <?= Yii::t('app','Uhr') ?></div>
                        <div class="invite-winner-user">{{::user.winner.userName}}</div>
                        <a ui-sref="user-become-member-invitations({id: user.id})">{{::user.winner.count}}</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>