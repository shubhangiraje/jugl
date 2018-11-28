<div class="dashboard container clearfix">
    <div class="container">
        <div class="box" ng-if="status.teamChangeFinishTime">
            <div class="box-text text-center box-text-buttons">
                <div class="dashboard-team-change-countdown" server-countdown="status.teamChangeFinishTime" server-countdown-with-days="true"></div>
                <a ui-sref="team-change-user-search" class="btn btn-submit"><?=Yii::t('app','Teamwechsel')?></a>
            </div>
        </div>
<?php /*
        <div class="text-center">
            <a class="btn btn-submit" href="{{buyTokenUri}}"><?= Yii::t('app', 'Tokens kaufen') ?></a>
            <a class="btn btn-submit" href="{{buyTokenDepositUri}}"><?= Yii::t('app', 'Tokens festlegen') ?></a>
        </div>

        <br>
*/ ?>

        <div ng-if="count_video_identification>0" class="count-video-identification-box">
            <a ng-click="dashboardCtrl.gotoVideoIdentificationList()" class="btn btn-submit" href=""><?= Yii::t('app', 'Neue Videoidents') ?>: {{count_video_identification}}</a>
        </div>

        <div class="box">
            <h2><?=Yii::t('app','Netzwerk aufbauen')?>
			    <!--Nvii-->
			     
				<div class="searches-filter-list">
					<div class="field-box-select filter-select">
						<multiselect ng-model="networkCountry" labels="labels"
						 options="countryArrayNetwork" ng-change="changedNetworkCountry()"  id-prop="id" display-prop="name" show-select-all="true" show-unselect-all="true" show-search="true" >
						</multiselect>
					</div>
				</div>
				
               
                <div ng-click="showInfoPopup('view-invite-my-list')" ng-mouseover="showInfoPopup('view-invite-my-list')" class="info-icon"></div>
            </h2>
            <div class="box-text has-dashboard-carousel clearfix">
                <div class="box-text text-center clearfix" ng-show="!inviteMe ||inviteMe.length<=0"><p><?= Yii::t('app','Für die ausgewählten Länder existieren aktuell keine neuen Mitglieder.') ?></p></div>
				
				<div  class="dashboard-network dashboard-carousel" delay="user_status.delay_invited_member" change="networkCountry" dashboard-carousel-after-slide="dashboardCtrl.inviteMeSlideCallback" >
                    <div class="dashboard-carousel-container">
                        <div class="dashboard-invite-my dashboard-carousel-list">
                            <div ng-repeat="itemInviteUser in inviteMe" class="dashboard-carousel-item" ng-if="!itemInviteUser.winner && user_status.delay_invited_member <= 0">
                                <div class="invite-my-box">
                                    <div class="invite-my-user">
                                        <a ui-sref="userProfile({id: itemInviteUser.id})">{{itemInviteUser|userName}} <div class="full-width-flags"><div id="{{itemInviteUser.flag}}" class="flag flag-32 flag-{{itemInviteUser.flag}}"></div></div></a>
                                    </div>
                                    <a ng-if="!itemInviteUser.winner" href="" ng-click="dashboardCtrl.invite(itemInviteUser)" class="invite-my-btn"><?= Yii::t('app','Einladen') ?></a>
                                    <div ng-if="itemInviteUser.winner" class="invite-winner-box">
                                        <div class="invite-winner-text"><?= Yii::t('app','eingeladen') ?>:</div>
                                        <div class="invite-winner-dt">{{::itemInviteUser.winner.dt|date:'dd.MM.yyyy HH:mm'}} <?= Yii::t('app','Uhr') ?></div>
                                        <div class="invite-winner-user">{{::itemInviteUser.winner.userName}}</div>
                                        <a ui-sref="user-become-member-invitations({id: itemInviteUser.id})">{{::itemInviteUser.winner.count}}</a>
                                    </div>
                                </div>
                            </div>
                            <div ng-repeat="itemInviteUser in inviteMe" class="dashboard-carousel-item" ng-if="itemInviteUser.winner">
                                <div class="invite-my-box">
                                    <div class="invite-my-user">
                                       <a ui-sref="userProfile({id: itemInviteUser.id})">{{itemInviteUser|userName}} <div class="full-width-flags"><div id="{{itemInviteUser.flag}}" class="flag flag-32 flag-{{itemInviteUser.flag}}"></div></div></a>
                                    </div>
                                    <a ng-if="!itemInviteUser.winner" href="" ng-click="dashboardCtrl.invite(itemInviteUser)" class="invite-my-btn"><?= Yii::t('app','Einladen') ?></a>
                                    <div ng-if="itemInviteUser.winner" class="invite-winner-box">
                                        <div class="invite-winner-text"><?= Yii::t('app','eingeladen') ?>:</div>
                                        <div class="invite-winner-dt">{{::itemInviteUser.winner.dt|date:'dd.MM.yyyy HH:mm'}} <?= Yii::t('app','Uhr') ?></div>
                                        <div class="invite-winner-user">{{::itemInviteUser.winner.userName}}</div>
                                        <a ui-sref="user-become-member-invitations({id: itemInviteUser.id})">{{::itemInviteUser.winner.count}}</a>
                                    </div>
                                </div>
                            </div>							
                        </div>
                    </div>
                    <div class="dashboard-carousel-nav-prev"></div>
                    <div class="dashboard-carousel-nav-next"></div>
                </div>
                <div class="text-center">
                    <a ui-sref="invite-my-list" class="btn btn-save"><?=Yii::t('app','Alle zeigen')?></a>
                </div>
                <div class="bottom-corner"></div>
            </div>	
			
        </div>
		

		<div class="box" ng-if="videos.length>0">
            <h2>
                <?=Yii::t('app','Videos')?> 
                <div ng-click="showInfoPopup('view-new-videos')" ng-mouseover="showInfoPopup('view-new-videos')" class="info-icon"></div>
            </h2>
			<div class="box-text has-dashboard-carousel clearfix">
                <div class="dashboard-carousel" dashboard-carousel-after-slide="dashboardCtrl.videosSlideCallback">
                    <div class="dashboard-carousel-container">
                        <div class="dashboard-videos dashboard-carousel-list">
                            <div ng-repeat="itemVideos in videos" class="dashboard-carousel-item">
								<div class="dashboard-videos-item">
									<a ui-sref="videos.details({id: itemVideos.video_id})" class="dashboard-videos-image">
										<img ng-src="{{itemVideos.image}}" />
                                    </a>
									<div class="dashboard-videos-info">
                                        <div class="videos-title">
                                            <a ui-sref="videos.details({id: itemVideos.video_id})">{{itemVideos.name}}</a>
                                        </div>
										<ul class="videos-category">
										<li class="ng-binding">{{itemVideos.cat_name}}</li>
										</ul>
										<div ng-if="itemVideos.bonus" class="dashboard-videos-param promotion-bonus"><?= Yii::t('app', 'Bonus') ?>: <span class="dashboard-videos-value">{{itemVideos.bonus|priceFormat}} <jugl-currency></jugl-currency></span><br>Falls Werbung vorhanden</div>
									</div>
								</div>
                            </div>
                        </div>
                    </div>
                    <div class="dashboard-carousel-nav-prev"></div>
                    <div class="dashboard-carousel-nav-next"></div>
                </div>
                <div class="bottom-corner"></div>
            </div>
			<!-- <div  class="box-text text-center box-text-buttons clearfix">
					<a ng-repeat="itemVideosCat in videos_categories" ui-sref="video.search" class="btn btn-submit" href="#/video/search">{{itemVideosCat.cat_name}}</a>
			</div>-->
		</div>

        <div class="box">
            <h2>
			   <?=Yii::t('app','Neueste werbung')?>
               <!--Nvii-->
				<div class="searches-filter-list">
					<div class="field-box-select filter-select">
						<multiselect ng-change="changedOfferCountry()" ng-model="offerCountry" labels="labels"
						 options="countryArrayOffers" id-prop="id" display-prop="name" show-select-all="true" show-unselect-all="true" show-search="true" >
						</multiselect>
					</div>
				</div>
				
                <div ng-click="showInfoPopup('view-offers-search')" class="info-icon"></div>
            </h2>

            <div ng-show="!offers.length" class="box-text clearfix">
                <div class="summary">
                    <?=Yii::t('app','Hier steht noch keine Werbung mit Werbebonus, weil Du noch keine Interessenskategorien ausgewählt hast. Bitte klicke dafür auf den Button "Wofür interessierst Du Dich?"')?>
                </div>
                <div class="text">
                    <?=Yii::t('app','Leider haben wir keine auf Deinen Interessen basierenden Angebote mit Werbebonus gefunden. Klicke bitte auf den Button "Inserate ohne Werbebonus durchsuchen". Dort gibt es weitere Angebote ohne Werbebonus.')?>
                </div>
                <div class="text-center">
                    <a ui-sref="offers.search" class="btn btn-save"><?=Yii::t('app','Alle Werbebonusanzeigen')?></a>
                </div>
                <div class="bottom-corner"></div>
            </div>
			
            <div ng-show="offers.length>0" class="box-text has-dashboard-carousel clearfix">
                <div class="dashboard-offers dashboard-carousel" change="offers" reload="offerCountry" dashboard-carousel-after-slide="dashboardCtrl.offersSlideCallback" >
                    <div class="dashboard-carousel-container">
                        <div class="dashboard-offers dashboard-carousel-list">
						<!--Nvii-->	
                         <div ng-repeat="offer in offers" class="dashboard-carousel-item">
							<div class="dashboard-offers-item">
									
                                    <a ui-sref="offers.details({id:offer.id})" class="dashboard-offer-image" ng-if="!offer.advertising_type">
                                        <img ng-src="{{::offer.image}}" />
                                    </a>
                                    <div ng-click="showInfoPopup('info-offer-type')" class="dashboard-offer-type" ng-if="!offer.advertising_type">
                                        <span set-if="offer.type == 'AUCTION'"><?=Yii::t('app','Bieterverfahren')?></span>
                                        <span set-if="offer.type == 'AD'"><?=Yii::t('app','Keine Kaufmöglichkeit')?></span>
                                        <span set-if="offer.type == 'AUTOSELL'"><?=Yii::t('app','Sofortkauf')?></span>
                                    </div>
                                    <div class="dashboard-offer-info">
										<div class="advertising_banner" ng-if="offer.advertising_display_name && offer.advertising_type == 'BANNER' && offer.id && offer.user_bonus && offer.link && offer.banner">
											<div id="advertising-{{offer.id}}" a-data-id="{{offer.id}}" a-data-user-bonus="{{offer.user_bonus}}" ng-click="dashboardCtrl.setAdvertising({{offer.id}}, {{offer.user_bonus}}, {{offer.click_interval}}, {{offer.popup_interval}})">
												<a href="{{offer.link}}" target="_blank" title="{{offer.advertising_display_name}}"><img src ="{{offer.banner}}" alt="{{offer.advertising_display_name}}"></a>
											</div>
                                        </div>
										<div class="offer-title">
                                            <a ui-sref="offers.details({id:offer.id})">{{::offer.title}}</a>
                                        </div>
                                        <ul class="offer-category" ng-if="!offer.advertising_type">
                                            <li>{{::offer.level1Interest}}</li>&nbsp;
                                            <li ng-if="offer.level2Interest">{{::offer.level2Interest}}</li>&nbsp;
                                            <li ng-if="offer.level3Interests">{{::offer.level3Interests}}</li>
                                        </ul>

                                        <div class="dashboard-offer-info-box" ng-if="offer.type != 'AD'  && !offer.advertising_type">
                                            <div ng-if="offer.type == 'AUTOSELL'" class="dashboard-offer-param"><?= Yii::t('app', 'Preis') ?>: <span class="dashboard-offer-value">{{::offer.price|priceFormat}} &euro;</span></div>
                                            <div ng-if="offer.type == 'AUCTION'" class="dashboard-offer-param"><?= Yii::t('app', 'Preisvorstellung') ?>: <span class="dashboard-offer-value">{{::offer.price|priceFormat}} &euro;</span></div>
                                            <div class="dashboard-offer-param">{{::offer.zip}} {{::offer.city}}</div>
                                            <div ng-if="offer.view_bonus" class="dashboard-offer-param promotion-bonus"><?= Yii::t('app', 'Werbebonus') ?>: <span class="dashboard-offer-value">{{::offer.view_bonus|priceFormat}} <jugl-currency></jugl-currency></span></div>
                                            <div ng-if="offer.buy_bonus" class="dashboard-offer-param buy-bonus"><?= Yii::t('app', 'Kaufbonus') ?>: <span class="dashboard-offer-value">{{::offer.buy_bonus|priceFormat}} <jugl-currency></jugl-currency></span></div>
                                            <div class="dashboard-offer-param" ng-if="offer.show_amount == 1"><?= Yii::t('app', 'St&uuml;ckzahl') ?>: <b>{{::offer.amount}}</b></div>
                                            <div class="dashboard-offer-param"><?= Yii::t('app', ' Aktiv bis') ?>: <b>{{::offer.active_till|date:"dd.MM.yyyy"}}</b></div>
                                            <div class="dashboard-offer-date">{{::offer.create_dt|date:"dd.MM.yyyy"}}</div>
                                            <div class="found-user-box">
                                                <a ui-sref="userProfile({id: offer.user.id})">
                                                    <div class="found-user-avatar"><img ng-src="{{::offer.user.avatarSmall}}" alt=""/></div>
                                                </a>
                                             <div class="found-user-name">{{::offer.user|userName}} <div class="full-width-flags"><div ng-click="updateCountry(offer.user.id,[offers,searchRequest,trollboxMessages])" id="{{::offer.user.flag}}" class="flag flag-32 flag-{{offer.user.flag}}"></div></div></div>
                                                <div class="offer-user-rating"> 
                                                    <div class="star-rating">
                                                        <span once-style="{width:(+offer.user.rating)+'%'}"></span>
                                                    </div>
                                                    <div class="user-feedback-count">({{::offer.user.feedback_count}})</div>
                                                    <div ng-if="offer.user.packet=='VIP'" class="user-packet">&nbsp;</div>
                                                    <div ng-if="offer.user.packet=='VIP_PLUS'" class="user-packet-vip-plus">&nbsp;</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="dashboard-offer-info-box" ng-if="offer.type == 'AD'  && !offer.advertising_type">
                                            <div ng-if="offer.view_bonus" class="dashboard-offer-param promotion-bonus"><?= Yii::t('app', 'Werbebonus') ?>: <span class="dashboard-offer-value">{{::offer.view_bonus|priceFormat}} <jugl-currency></jugl-currency></span></div>
                                            <div class="dashboard-offer-param"><?= Yii::t('app', ' Aktiv bis') ?>: <b>{{::offer.active_till|date:"dd.MM.yyyy"}}</b></div>
                                            <div class="dashboard-offer-date">{{::offer.create_dt|date:"dd.MM.yyyy"}}</div>
                                            <div class="found-user-box">
                                                <a ui-sref="userProfile({id: offer.user.id})">
                                                    <div class="found-user-avatar"><img ng-src="{{::offer.user.avatarSmall}}" alt=""/></div>
                                                </a>
                                                <div class="found-user-name">{{::offer.user|userName}}</div>
                                                <div class="offer-user-rating">
                                                    <div class="star-rating">
                                                        <span once-style="{width:(+offer.user.rating)+'%'}"></span>
                                                    </div>
                                                    <div class="user-feedback-count">({{::offer.user.feedback_count}})</div>
                                                    <div ng-if="offer.user.packet=='VIP'" class="user-packet">&nbsp;</div>
                                                    <div ng-if="offer.user.packet=='VIP_PLUS'" class="user-packet-vip-plus">&nbsp;</div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>	
                        </div>
                    </div>
                    <div class="dashboard-carousel-nav-prev"></div>
                    <div class="dashboard-carousel-nav-next"></div>
                </div>
                <div class="bottom-corner"></div>
            </div>
			<div class="box-text text-center clearfix" ng-show="filteredOfferFilter.length==0"><p><?= Yii::t('app','Für die ausgewählten Länder existieren aktuell keine neuen Werbungen.') ?></p></div>
            <div class="box-text text-center box-text-buttons clearfix">
                <a ui-sref="offers.advancedSearch" class="btn btn-submit"><?=Yii::t('app','Inserate ohne Werbebonus durchsuchen')?></a>
                <a ui-sref="interests.index" class="btn btn-submit"><?=Yii::t('app','Für was interessierts Du Dich?')?></a>
                <a ui-sref="offers.search" class="btn btn-submit"><?=Yii::t('app','Inserate mit Werbebonus')?></a>
                <a ui-sref="offers.add" class="btn btn-submit"><?=Yii::t('app','Anzeige aufgeben')?></a>
            </div>

        </div>

        <div class="box">
            <h2><?=Yii::t('app','Aufträge')?>
			<!--Nvii-->
				<div class="searches-filter-list">
					<div class="field-box-select filter-select">
					   <multiselect ng-change="changedSearchRequestsCountry()" ng-model="searchesCountry" labels="labels"
						 options="countryArraySearches" id-prop="id" display-prop="name" show-select-all="true" show-unselect-all="true" show-search="true" >
						</multiselect>
					</div>
				</div>
				
                <div ng-click="showInfoPopup('view-searches-search')" class="info-icon"></div>
            </h2>

            <div ng-show="searchRequest.length < 1" class="box-text clearfix">
                <div class="summary">
                    <?=Yii::t('app','Hier stehen noch keine Suchaufträge, weil Du noch nicht ausgewählt hast, in welchen Interessenskategorien Du für andere Mitglieder recherchieren mochtest. Bitte klicke dazu auf den Button “Womit kennst Du Dich aus?”')?>
                </div>
                <div class="text">
                    <?=Yii::t('app','Leider gibt es derzeit keine Suchaufträge in den von Dir angegebenen Interessensgebieten.')?>
                </div>
                <div class="text-center">
                    <a ui-sref="searches.search" class="btn btn-save"><?=Yii::t('app','Alle Suchaufträge anzeigen')?></a>
                </div>
                <div class="bottom-corner"></div>
            </div>

            <div ng-show="searchRequest.length > 0" class="box-text has-dashboard-carousel clearfix">
                <div class="dashboard-searchrequests dashboard-carousel" change="searchesCountry" dashboard-carousel-after-slide="dashboardCtrl.searchRequestsSlideCallback">
                    <div class="dashboard-carousel-container">
                        <div class="dashboard-searchrequests dashboard-carousel-list">
                            <div ng-repeat="item in searchRequest" class="dashboard-carousel-item">
                                <div class="dashboard-searchrequests-item">
                                    <a ui-sref="searches.details({id:item.id})" class="dashboard-searchrequest-image text-center">
                                        <img ng-src="{{::item.image}}" />
                                    </a>

                                    <div class="dashboard-searchrequest-category clearfix">
                                        <span>{{::item.level1Interest}}</span>&nbsp;
                                        <span set-if="item.level2Interest">{{::item.level2Interest}}</span>&nbsp;
                                        <span set-if="item.level3Interests">{{::item.level3Interests}}</span>
                                    </div>

                                    <div class="dashboard-searchrequest-title text-center"><a ui-sref="searches.details({id:item.id})">{{::item.title}}</a></div>

                                    <div set-if="item.price_to" class="dashboard-searchrequest-price"><?= Yii::t('app','Preis') ?>: {{::item.price_from|priceFormat}} - {{::item.price_to|priceFormat}} &euro;</div>
                                    <div set-if="!item.price_to" class="dashboard-searchrequest-price"><?= Yii::t('app','Preis') ?>: {{::item.price_from|priceFormat}} &euro;</div>


                                    <div set-if="item.bonus" class="dashboard-searchrequest-param">
                                        <div class="dashboard-searchrequest-text">
                                            <?=Yii::t('app','Für die Vermittlung eines passenden Angebots zahle ich:')?>
                                            <span class="dashboard-searchrequest-value">{{::item.bonus|priceFormat}} <span class="icon-jugl"></span></span>
                                        </div>
                                    </div>

                                    <div class="dashboard-searchrequest-date">{{::item.create_dt|date:"dd.MM.yyyy"}}</div>

                                    <div class="found-user-box clearfix">
                                        <a ui-sref="userProfile({id: item.user.id})">
                                            <div class="found-user-avatar"><img ng-src="{{::item.user.avatarSmall}}" alt=""/></div>
                                        </a>
                                       <div class="found-user-name">{{::item.user|userName}} <div class="full-width-flags"><div ng-click="updateCountry(item.user.id,[offers,searchRequest,trollboxMessages])" id="{{::item.user.flag}}" class="flag flag-32 flag-{{item.user.flag}}"></div></div></div>
                                        <div class="offer-user-rating">
                                            <div class="star-rating">
                                                <span once-style="{width:(+item.user.rating)+'%'}"></span>
                                            </div>
                                            <div class="user-feedback-count">({{::item.user.feedback_count}})</div>
                                            <div ng-if="item.user.packet=='VIP'" class="user-packet">&nbsp;</div>
                                            <div ng-if="item.user.packet=='VIP_PLUS'" class="user-packet-vip-plus">&nbsp;</div>
                                        </div>
                                    </div>

                                    <div class="search-request-item-statistic">
                                        <div class="clearfix">
                                            <span class="param"><?= Yii::t('app', 'Angebote'); ?></span>
                                            <span class="value">{{::item.count_total|default:0}}
                                                <a ng-if="item.count_total>0" ui-sref="searches.offersList({id: item.id})" class="icon-eye"></a>
                                            </span>
                                        </div>
                                        <div class="clearfix">
                                            <span class="param"><?= Yii::t('app', 'Abgelehnt'); ?></span>
                                            <span class="value">{{::item.count_rejected|default:0}}
                                                <a ng-if="item.count_rejected>0" ui-sref="searches.offersList({id: item.id})" class="icon-eye"></a>
                                            </span>
                                        </div>
                                        <div class="clearfix">
                                            <span class="param"><?= Yii::t('app', 'Angenommen'); ?></span>
                                            <span class="value">{{::item.count_accepted|default:0}}
                                                <a ng-if="item.count_accepted>0" ui-sref="searches.offersList({id: item.id})" class="icon-eye"></a>
                                            </span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="dashboard-carousel-nav-prev"></div>
                    <div class="dashboard-carousel-nav-next"></div>
                </div>
                <div class="bottom-corner"></div>
            </div>
            <div class="box-text text-center box-text-buttons clearfix">
                <div class="dashboard-search-request-text">
                    <p><span><?= Yii::t('app','Wir sind {{countTotalUsers|priceFormat}} Helfer stark!') ?></span></p>
                    <p><?= Yii::t('app','Welchen Auftrag können wir für Dich erledigen?') ?></p>
                    <p><?= Yii::t('app','Jede erdenkliche Dienstleistung und Hilfe ist möglich!') ?></p>
                </div>

                <a ui-sref="searches.add" class="btn btn-submit"><?=Yii::t('app','Auftrag erstellen')?></a>
                <a ui-sref="interests-searches.index" class="btn btn-submit"><?=Yii::t('app','Aufträge durchsuchen')?></a>
            </div>
        </div>

        <div class="box">
            <h2>
                <?=Yii::t('app','Mitgliederzahl')?> <span class="count">{{countTotalUsers|priceFormat}}</span>
                <!-- <div ng-click="showInfoPopup('view-activities')" class="info-icon"></div> -->
            </h2>
            <div class="box-text clearfix">
                <div class="today-count">
                    <?=Yii::t('app','heute neu:')?> <span class="count">{{countNewUserToday|priceFormat}}</span>
                </div>
                <div class="text-center">
                    <a ui-sref="new-users" class="btn btn-save"><?=Yii::t('app','Neue Mitglieder anzeigen')?></a>
                </div>
                <div class="bottom-corner"></div>
            </div>
        </div>

        <div class="box" set-if="networkMembers.length > 0">
            <h2>
                <?=Yii::t('app','Neu in deinem Netzwerk')?> <span class="count">{{countNewUsers}}</span>
                <a class="link-new-network-members" ui-sref="new-network-members"><?= Yii::t('app','Alle anzeigen') ?></a>
                <div ng-click="showInfoPopup('view-network')" class="info-icon"></div>
            </h2>
            <div class="box-text has-dashboard-carousel clearfix">
                <div class="dashboard-carousel">
                    <div class="dashboard-carousel-container">
                        <div class="dashboard-network dashboard-carousel-list">
                            <div ng-repeat="user in networkMembers" class="dashboard-carousel-item">
                                <?php include('user-box.php'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="dashboard-carousel-nav-prev"></div>
                    <div class="dashboard-carousel-nav-next"></div>
                </div>
                <div class="text-center box-text-buttons">
                    <a ui-sref="network" class="btn btn-save"><?=Yii::t('app','Netzwerk anzeigen')?></a>
                    <a ui-sref="friendsInvitation.invite" class="btn btn-submit"><?=Yii::t('app','Noch mehr Freunde einladen')?></a>
                </div>
                <div class="bottom-corner"></div>
            </div>
        </div>

        <div class="box">
            <h2>
                <?=Yii::t('app','Jugl Forum')?>
                <!--Nvii-->
				<div class="searches-filter-list">
					<div class="field-box-select filter-select">
                        <multiselect ng-change="changedTrollboxCountry()" ng-model="forumCountry" labels="labels"
                             options="countryArrayForum" id-prop="id" display-prop="name" show-select-all="true" show-unselect-all="true" show-search="true" >
                        </multiselect>
					</div>
				</div>

                <div ng-click="showInfoPopup('view-forum')" class="info-icon"></div>

                <div class="trollbox-filter-box">
                    <div class="trollbox-filter-title"><?= Yii::t('app', 'anzeigen') ?>:</div>
                    <div class="trollbox-filter-btns">
                        <button ng-click="dashboardCtrl.gotoForum('')" class="btn btn-submit active"><?= Yii::t('app', 'Alle') ?></button>
                        <button ng-click="dashboardCtrl.gotoForum('MAIN')" class="btn btn-submit"><?= Yii::t('app', 'Meine') ?></button>
                        <button ng-click="dashboardCtrl.gotoForum('FOLLOWING')" class="btn btn-submit"><?= Yii::t('app', 'Abos') ?></button>
                        <button ng-click="dashboardCtrl.gotoForum('CONTACTS')" class="btn btn-submit"><?= Yii::t('app', 'Kontakte');?></button>
                    </div>
                </div>

            </h2>

            <div class="box-text clearfix trollbox-create-message-box">
                <div class="summary" ng-bind-html="dashboardForumText"></div>
                <div class="dashboard-forum-form trollbox-form">

                    <div class="forum-new-message-box">
                        <div class="forum-new-message-image">
                            <div class="preview-upload-image" ng-if="trollbox.newMessage.image">
                                <img ng-src="{{trollbox.newMessage.image}}"/>
                                <button ng-click="dashboardCtrl.deleteTrollboxImage()" class="btn-del-image"></button>
                            </div>
                            <div class="box-input-file" ng-if="!trollbox.newMessage.image">
                                <div class="spinner" ng-if="uploader.isUploading"></div>
                                <input type="file" nv-file-select filters="imageVideoFilter,queueLimit" uploader="uploader" options="fileUploadOptions" />
                            </div>
                        </div>
                        <div class="forum-new-message-image-notification"><?=Yii::t('app','Bild / Video hochladen')?></div>
                        <div class="forum-new-message-smiles">
                            <div class="smiles" emoticons-tooltip emoticon-forum="true" emoticons-list="dashboardCtrl.emoticonsList" message-text="trollbox.newMessage.text">
                                <div class="emoticons-tooltip">
                                    <span ng-repeat="(emoticon,text) in dashboardCtrl.emoticonsList" ng-bind="emoticon" class="emoticon"></span>
                                </div>
                            </div>
                        </div>
                        <div class="forum-new-message-send">
                            <div class="btn btn-submit" ng-click="dashboardCtrl.trollboxSendMessage()"><?=Yii::t('app','Absenden')?></div>
                        </div>
                    </div>

                    <textarea placeholder="<?=Yii::t('app','Text eingeben')?>" maxlength="2500" ng-model="trollbox.newMessage.text"></textarea>

                    <ul class="errors-list" ng-if="trollbox.newMessage.$allErrors">
                        <li ng-repeat="error in trollbox.newMessage.$allErrors">{{::error}}</li>
                    </ul>
                </div>

                <div class="forum-update-btn-box">
                    <button ng-click="dashboardCtrl.updateTrollbox()" class="btn btn-submit" type="button"><?= Yii::t('app', 'Chats aktualisieren') ?></button>
                </div>

                <div ng-if="trollbox.loading" class="loader-box trollbox-loader">
                    <div class="spinner"></div>
                </div>

                <div class="dashboard-forum-messages">
				
				<div ng-repeat="advertising in advertisings.FORUM_TOP" class="advertising-cnt" ng-if="advertising.advertising_type == 'BANNER'" style="display:block;">
					<div id="advertising-{{::advertising.id}}" class="dashboard-forum-message trollbox-message-box clearfix" style="width:100%" a-data-id="{{::advertising.id}}" a-data-user-bonus="{{::advertising.user_bonus}}" class="advertising_script" ng-click="dashboardCtrl.setAdvertising({{::advertising.id}}, {{::advertising.user_bonus}}, {{::advertising.click_interval}}, {{::advertising.popup_interval}})">
						<a href="{{::advertising.link}}" target="_blank" title="{{::advertising.advertising_display_name}}"><img src ="{{::advertising.banner}}" alt="{{::advertising.advertising_display_name}}"></a>
					</div>
				</div>

                    <div ng-repeat="trollboxMessage in trollboxMessages" class="dashboard-forum-message trollbox-message-box clearfix">
                        <div ng-if="trollboxMessage.file" class="trollbox-message-image">
                            <div ng-if="trollboxMessage.file.ext=='mp4'" class="video-box">
                                <video video poster="{{trollboxMessage.file.image_medium}}" playsinline webkit-playsinline loop muted preload="none">
                                    <source ng-src="{{trollboxMessage.file.url}}" type="video/mp4">
                                </video>
                            </div>
                            <a ng-if="trollboxMessage.file.ext!='mp4'" href="{{::trollboxMessage.file.image_big}}" fancybox ><img ng-src="{{trollboxMessage.file.image_medium}}" /></a>
                        </div>

                        <div class="trollbox-message-text" ng-bind-html="trollboxMessage.text|emoticonFix:dashboardCtrl:true"></div>

                        <div class="trollbox-message-info clearfix">
                            <div class="rollbox-message-info-user">
                                <div class="offer-user-box">
                                    <a ui-sref="userProfile({id: trollboxMessage.user.id})">
                                        <div class="offer-user-avatar"><img ng-src="{{::trollboxMessage.user.avatar}}" alt=""/></div>
                                    </a>
                                    <div class="offer-user-name">{{::trollboxMessage.user|userName}} <div ng-click="updateCountry(trollboxMessage.user.id,[offers,searchRequest,trollboxMessages])" id="{{::trollboxMessage.user.flag}}" class="flag flag-32 flag-{{trollboxMessage.user.flag}}"></div></div>

                                    <div class="offer-user-rating">
                                        <div class="star-rating">
                                            <span once-style="{width:(+trollboxMessage.user.rating)+'%'}"></span>
                                        </div>
                                        <div class="user-feedback-count">({{::trollboxMessage.user.feedback_count}})</div>
                                        <div ng-if="trollboxMessage.user.packet=='VIP'" class="user-packet">&nbsp;</div>
                                        <div ng-if="trollboxMessage.user.packet=='VIP_PLUS'" class="user-packet-vip-plus">&nbsp;</div>
                                    </div>
                                </div>
                                <div class="forum-message-dt">
                                    <span>{{trollboxMessage.dt|date : 'dd.MM.yyyy HH:mm'}}</span>
                                </div>
                            </div>
                            <div class="trollbox-message-info-total-comments">
                                <div class="message-comment-bnt-box">
                                    <a href="" ng-click="dashboardCtrl.enterGroupChat(trollboxMessage.id)" class="btn btn-submit"><?= Yii::t('app','Kommentieren') ?></a>
                                </div>
                                <div ng-click="dashboardCtrl.enterGroupChat(trollboxMessage.id)" class="message-info-total-comments"><span>{{::trollboxMessage.messagesCount|default:0}}</span><?= Yii::t('app','Kommentare insgesamt ') ?></div>
                            </div>

                            <div class="trollbox-message-info-actions">
                                <div ng-if="status.is_moderator" class="message-actions-box">
                                    <button ng-click="dashboardCtrl.trollboxRejectMessage(trollboxMessage)" set-if="trollboxMessage.status=='ACTIVE' || trollboxMessage.status=='AWAITING_ACTIVATION'" class="btn-reject-message">{{::trollboxMessage.status=='AWAITING_ACTIVATION' ? '<?=Yii::t('app', 'Ablehnen')?>':'<?=Yii::t('app', 'Blockieren')?>'}}</button>
                                    <button ng-click="dashboardCtrl.trollboxAcceptMessage(trollboxMessage)" set-if="trollboxMessage.status=='REJECTED' || trollboxMessage.status=='AWAITING_ACTIVATION'" class="btn-accept-message">{{::trollboxMessage.status=='AWAITING_ACTIVATION' ? '<?=Yii::t('app', 'Freigeben')?>':'<?=Yii::t('app', 'Entblocken')?>'}}</button>
                                    <button ng-click="dashboardCtrl.trollboxBlockUser(trollboxMessage)" ng-if="!trollboxMessage.user.is_blocked_in_trollbox" class="btn-block-user"><?=Yii::t('app', 'Für alle Foren sperren')?></button>
                                    <button ng-click="dashboardCtrl.trollboxUnblockUser(trollboxMessage)" ng-if="trollboxMessage.user.is_blocked_in_trollbox" class="btn-accept-message"><?=Yii::t('app', 'Für alle Foren entsperren')?></button>
                                    <button ng-click="dashboardCtrl.trollboxSetStickyTrollboxMessage(trollboxMessage)" set-if="!trollboxMessage.is_sticky" class="btn-accept-message"><?=Yii::t('app', 'Beitrag anpinnen')?></button>
                                    <button ng-click="dashboardCtrl.trollboxUnsetStickyTrollboxMessage(trollboxMessage)" set-if="trollboxMessage.is_sticky" class="btn-accept-message"><?=Yii::t('app', 'Beitrag entpinnen')?></button>
                                    <div set-if="trollboxMessage.status=='REJECTED' && trollboxMessage.status_changed_dt" class="message-reject">
                                        <div class="dt-message-reject">{{::trollboxMessage.status_changed_dt|date : 'dd.MM.yyyy - HH:mm'}} <?= Yii::t('app','Uhr') ?> {{::trollboxMessage.user.id==trollboxMessage.status_changed_user_id ? '<?=Yii::t('app','Nutzer')?>':'<?= Yii::t('app','Moderator') ?>'}}</div>
                                        <div class="user-message-reject">{{::trollboxMessage.statusChangedUser}}</div>
                                    </div>
                                    <div set-if="trollboxMessage.status=='ACTIVE' && trollboxMessage.status_changed_dt" class="message-accept">
                                        <div class="dt-message-accept">{{::trollboxMessage.status_changed_dt|date : 'dd.MM.yyyy - HH:mm'}} <?= Yii::t('app','Uhr') ?> {{::trollboxMessage.user.id==trollboxMessage.status_changed_user_id ? '<?=Yii::t('app','Nutzer')?>':'<?= Yii::t('app','Moderator') ?>'}}</div>
                                        <div class="user-message-accept">{{::trollboxMessage.statusChangedUser}}</div>
                                    </div>
                                </div>
                                <div class="message-info-votes">
                                    <div class="negative" ng-click="dashboardCtrl.trollboxVoteDown(trollboxMessage.id)">{{::trollboxMessage.votes_down}}</div>
                                    <div class="positive" ng-click="dashboardCtrl.trollboxVoteUp(trollboxMessage.id)">{{::trollboxMessage.votes_up}}</div>
                                    <div class="votes-view" ng-click="(trollboxMessage.votes_down+trollboxMessage.votes_up)>0 ? dashboardCtrl.votesView(trollboxMessage.id):null" ng-class="{'no-votes':(trollboxMessage.votes_down+trollboxMessage.votes_up)===0}"></div>
                                </div>
                                <div ng-if="trollboxMessage.trollbox_category_id" class="trollbox-message-category">{{trollboxMessage.trollbox_category}}</div>
                            </div>
                        </div>

                        <div ng-if="trollboxMessage.messages.length>0" class="message-info-response">
                            <div class="message-info-response-item" ng-repeat="message in trollboxMessage.messages">
                                <div class="message-info-response-info" ng-click="dashboardCtrl.enterGroupChat(trollboxMessage.id)">
                                    <div class="message-info-response-data clearfix">
                                        <div class="message-info-response-user-info clearfix">
                                            <a ui-sref="userProfile({id: message.user.id})">
                                                <div class="offer-user-avatar"><img ng-src="{{::message.user.avatar}}" alt=""/></div>
                                            </a>
                                            <div class="message-info-response-user">{{::message.user|userName}} <div ng-click="updateCountry(message.user.id,[offers,searchRequest,trollboxMessages])" id="{{::message.user.flag}}" class="flag flag-32 flag-{{message.user.flag}}"></div></div>
                                        </div>
                                        <div class="message-info-response-dt">{{::message.dt|date:"dd MMMM yyyy | HH:mm"}}</div>
                                    </div>

                                    <div ng-if="message.content_type!='IMAGE' || message.content_type!='VIDEO'" class="message-info-response-message" ng-bind-html="message.text|emoticonFix:dashboardCtrl:true"></div>

                                    <div ng-if="message.content_type=='IMAGE'" class="message-info-response-message-picture">
                                        <img ng-src="{{::message.file.thumb_url}}">
                                    </div>

                                    <div ng-if="message.content_type=='VIDEO'" class="message-info-response-message-picture play-video">
                                        <img ng-src="{{::message.file.thumb_url}}" alt="">
                                    </div>

                                </div>
                            </div>
                        </div>
					</div>
					<div class="box-text text-center clearfix" ng-show="trollboxMessages.length==0"><p><?= Yii::t('app','Für die ausgewählten Länder existieren aktuell keine neuen Forumbeiträge.') ?></p></div>			

					<div ng-repeat="advertising in advertisings.FORUM_BOTTOM" class="advertising-cnt" ng-if="advertising.advertising_type == 'BANNER'" style="display:block;">
						<div id="advertising-{{::advertising.id}}" class="dashboard-forum-message trollbox-message-box clearfix" a-data-id="{{::advertising.id}}" a-data-user-bonus="{{::advertising.user_bonus}}" ng-click="dashboardCtrl.setAdvertising({{::advertising.id}}, {{::advertising.user_bonus}}, {{::advertising.click_interval}}, {{::advertising.popup_interval}})">
							<a href="{{::advertising.link}}" target="_blank" title="{{::advertising.advertising_display_name}}"><img src ="{{::advertising.banner}}" alt="{{::advertising.advertising_display_name}}"></a>
						</div>
					</div>
					
					
                </div>
                <div class="dashboard-forum-btn-box">
                    <a ui-sref="forum" class="btn btn-submit"><?= Yii::t('app','Alle Gruppenchats anzeigen') ?></a>
                </div>
                <div class="bottom-corner"></div>
            </div>
        </div>


        <div class="box">
            <h2>
                <?=Yii::t('app','Mein profil / Meine daten')?>
                <div ng-click="showInfoPopup('view-profile')" class="info-icon"></div>
            </h2>
            <div class="box-text clearfix">
                <div class="dashboard-profile-percent">
                    <div class="dashboard-profile-percent-label"><?=Yii::t('app','Profil ausgefüllt zu:')?> <span>{{profile.percent}}%</span></div>
                    <div class="dashboard-profile-percent-line"><span once-style="{width:(+profile.percent)+'%'}"></span></div>
                    <a ui-sref="profile" class="dashboard-profile-link"></a>
                </div>
                <div class="bottom-corner"></div>
            </div>
        </div>

        <div class="box" set-if="news.length > 0">
            <h2>
                <?=Yii::t('app','Unsere News')?>
                <div ng-click="showInfoPopup('view-news')" class="info-icon"></div>
            </h2>
            <div class="box-text has-dashboard-carousel clearfix">
                <div class="dashboard-news dashboard-carousel">
                    <div class="dashboard-carousel-container">
                        <div class="dashboard-news dashboard-carousel-list">
                            <div ng-repeat="newsItem in news" class="dashboard-carousel-item">
                                <div class="dashboard-news-item">
                                    <div class="dashboard-news-item-head">
                                        <div class="left">
                                            <a ng-if="newsItem.images.fancybox" fancybox fancybox-force-init="true" href="{{newsItem.images.fancybox}}"><img ng-src="{{::newsItem.images.image}}" /></a>
                                            <img ng-if="!newsItem.images.fancybox" ng-src="{{::newsItem.images.image}}" alt="">
                                        </div>
                                        <div class="right">
                                            <span class="title">{{::newsItem.title}}</span><br/>
                                            <span class="date">{{::newsItem.dt|date:"dd.MM.yyyy"}}</span>
                                        </div>
                                    </div>
                                    <div class="dashboard-news-item-text" ng-bind-html="newsItem.text"></div>
                                    <!--<button ng-click="dashboardCtrl.readMoreNews(newsItem.id)" class="btn btn-submit dashboard-news-read-btn"><?= Yii::t('app','Weiterlesen') ?></button>-->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="dashboard-carousel-nav-prev"></div>
                    <div class="dashboard-carousel-nav-next"></div>
                </div>

                <div class="text-center">
                    <a ui-sref="news" class="btn btn-save"><?=Yii::t('app','Alle zeigen')?></a>
                </div>

                <div class="bottom-corner"></div>
            </div>
        </div>

        <div class="box" set-if="faqs.length > 0">
            <h2><?=Yii::t('app','Fragen / Antworten')?></h2>
            <div class="box-text has-dashboard-carousel clearfix">
                <div class="dashboard-carousel">
                    <div class="dashboard-carousel-container">
                        <div class="dashboard-faqs dashboard-carousel-list">
                            <div ng-repeat="faqItem in faqs" class="dashboard-carousel-item">
                                <div class="dashboard-faq-item">
                                    <div class="dashboard-faq-question">{{::faqItem.question}}</div>
                                    <div class="dashboard-faq-response" ng-bind-html="faqItem.response"></div>
                                    <!--<button ng-click="dashboardCtrl.readMoreFaq(faqItem.id)" class="btn btn-submit dashboard-faq-read-btn"><?= Yii::t('app','Weiterlesen') ?></button>-->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="dashboard-carousel-nav-prev"></div>
                    <div class="dashboard-carousel-nav-next"></div>
                </div>

                <div class="text-center">
                    <a ui-sref="faqs" class="btn btn-save"><?=Yii::t('app','Alle zeigen')?></a>
                </div>

                <div class="bottom-corner"></div>
            </div>
        </div>

        <div class="text-center dashboard-bottom-buttons">
            <a ui-sref="offers.search" class="btn btn-submit"><?=Yii::t('app','Anzeigen mit Werbebonus')?></a><br/>
            <a ui-sref="offers.add" class="btn btn-submit"><?=Yii::t('app','Verkaufen / Werbung schalten')?></a><br/>
            <a ui-sref="offers.myRequests" class="btn btn-submit"><?=Yii::t('app','Ich habe geboten / gekauft')?></a><br/>
            <a ui-sref="offers.myList" class="btn btn-submit"><?=Yii::t('app','Meine Anzeigen / Werbung verwalten')?></a><br/>

            <a ui-sref="searches.search" class="btn btn-submit"><?=Yii::t('app','Was suchen andere?')?></a><br/>
            <a ui-sref="searches.add" class="btn btn-submit"><?=Yii::t('app','Suchauftrag erstellen')?></a><br/>
            <a ui-sref="searches.myList" class="btn btn-submit"><?=Yii::t('app','Was wird mir angeboten / vermittelt?')?></a><br/>
            <a ui-sref="searches.myOffers" class="btn btn-submit"><?=Yii::t('app','Was habe ich anderen vermittelt / angeboten?')?></a><br/>

            <a ui-sref="userSearch" class="btn btn-submit"><?= Yii::t('app','Mitglieder suchen') ?></a><br/>
            <a ui-sref="favorites" class="btn btn-submit"><?= Yii::t('app','Merkzettel') ?></a><br/>
            <a ui-sref="dealsCompleted" class="btn btn-submit"><?= Yii::t('app','Geschäfte & Bewertungen') ?></a><br/>

            <a ui-sref="funds.payin" class="btn btn-submit"><?=Yii::t('app','Jugl aufladen')?></a><br/>
            <a ui-sref="funds.payout" class="btn btn-submit"><?=Yii::t('app','Jugl einlösen')?></a><br/>

            <a ng-click="showInfoPopup('view-earn-money')" class="btn btn-submit"><?=Yii::t('app','So funktioniert Jugl')?></a><br/>
            <a ui-sref="earn-money" class="btn btn-submit"><?=Yii::t('app','Wie kann ich Geld verdienen?')?></a>
        </div>
    </div>
</div>
