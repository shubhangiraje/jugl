<div id="profile-page">
    <div class="container">
        <div ng-click="showInfoPopup('view-profile')" ng-class="{'blink':isOneShowInfoPopup('view-profile')}" class="info-popup-btn"></div>

        <div class="profile-user-info-block clearfix">
            <div class="account-column">
                <div class="profile-user-info-main">
                    <a ng-if="userInfo.id==status.id" ui-sref="profile" class="btn btn-submit btn-profile-update"><?= Yii::t('app','Profil / Daten / Kontodaten bearbeiten') ?></a>
                    <div class="avatar-box">
                        <div ng-if="userInfo.photos.length>0" class="count-user-photos">+{{userInfo.photos.length}}</div>

                        <a ng-if="userInfo.video_identification_status!='AWAITING'" fancybox fancybox-force-init="true" data-fancybox-group="user-photos" href="{{userInfo.avatar.fancybox}}">
                            <img ng-src="{{userInfo.avatar.image}}" alt="" />
                        </a>

                        <a ng-if="userInfo.video_identification_status=='AWAITING'" ng-click="userProfileCtrl.showVideoIdentification()" class="video-identification-avatar" href="">
                            <img ng-src="{{userInfo.avatar.image}}" alt="" />
                        </a>

                        <div class="myfriends-icon" ng-if="userInfo.isMyFriend"><?=Yii::t('app','Mein Kontakt')?></div>
                        <div ng-if="userInfo.photos.length>0" style="display: none">
                            <a fancybox data-fancybox-group="user-photos" ng-repeat="photo in userInfo.photos" href="{{photo}}">
                                <img ng-src="{{photo}}" alt="">
                            </a>
                        </div>
                    </div>

                    <div class="text-center">
                        <div ng-if="userInfo.packet == 'VIP'" class="user-profile-packet"><?= Yii::t('app', 'Premium') ?></div>
                        <div ng-if="userInfo.packet == 'VIP_PLUS'" class="user-profile-packet-vip-plus"></div>
                        <div ng-if="userInfo.is_company_name" class="user-profile-company"><?= Yii::t('app','Gewerblich') ?></div>
                    </div>
                    <div class="name">{{userInfo.first_name}} {{userInfo.last_name}} <div id="{{userInfo.flag}}" ng-click="updateCountry(userInfo.id,[userInfo,followers.users,friends.users,feedback.items,trollboxMessages.items,teamFeedback.items])" class="flag flag-32 flag-{{userInfo.flag}}"></div></div>
                    <div class="user-rating">
                        <div class="star-rating">
                            <span once-style="{width:(+userInfo.rating)+'%'}"></span>
                        </div>
                        <div class="user-feedback-count">({{userInfo.feedback_count}})</div>
                    </div>

                    <div ng-if="userInfo.company_name" class="logged"><b><?= Yii::t('app', 'Firma') ?>: </b> {{userInfo.company_name}}</div>
                    <div class="logged"><b><?= Yii::t('app', 'Handel abgeschlossen') ?>: </b> {{userInfo.dealsCompleted}}</div>
                    <div class="logged"><b><?=Yii::t('app','Zuletzt eingeloggt')?>:</b> {{userInfo.lastTimeWasOnline|date:'dd.MM.yyyy HH:mm'}}</div>
                    <div class="logged"><b><?=Yii::t('app','Erfolgreich eingeladen')?>: </b>{{userInfo.invitations}}</div>
                    <div class="logged"><a ui-sref="user-network({id: userInfo.id})"><b><?=Yii::t('app','Insgesamt im Netzwerk')?>: </b>{{userInfo.network_size}} <span ng-if="userInfo.network_size>0"class="icon-eye"></span></a></div>
                    <div class="logged"><b><?=Yii::t('app','Netzwerktiefe (Level)')?>: </b> {{userInfo.network_levels}}</div>
                    <div class="logged"><b><?=Yii::t('app',' Mitglied seit')?>: </b> {{userInfo.registration_dt|date:'dd.MM.yyyy'}}</div>
                    <div ng-if="userInfo.packet=='VIP_PLUS' && userInfo.id==status.id" class="logged"><b><?=Yii::t('app','Anzahl der Mitglieder, die vom Abwerben verhindert werden können')?>:</b> {{status.availableStickRequestsCount}}</div>
                    <div class="user-profile-validation-status">
                        <div class="logged">
                            <b><?= Yii::t('app', 'Identitätsprüfungen') ?>:</b>
                            <p><?= Yii::t('app', 'Zur Verifizierung auf die Buttons klicken') ?></p>
                        </div>
                        <ul>
                            <li><div><span class="icon-checked"></span><?= Yii::t('app', 'E-mail adresse') ?></div></li>
                            <li>
                                <div ng-if="userInfo.validation_phone_status=='VALIDATED'"><span class="icon-checked"></span><?= Yii::t('app', 'Telefonnummer') ?></div>
                                <div ng-if="userInfo.id!=status.id && userInfo.validation_phone_status!='VALIDATED'"><span class="icon-no-checked"></span><?= Yii::t('app', 'Telefonnummer') ?></div>
                                <button ng-if="userInfo.id==status.id && userInfo.validation_phone_status!='VALIDATED'" ng-click="userProfileCtrl.gotoValidationPhone()" class="btn btn-submit"><?= Yii::t('app', 'Telefonnummer') ?></button>
                            </li>
                            <li>
                                <div ng-if="userInfo.validation_status=='SUCCESS'"><span class="icon-checked"></span><?= Yii::t('app', 'Pass / Ausweis') ?></div>
                                <div ng-if="userInfo.id!=status.id && userInfo.validation_status!='SUCCESS'"><span class="icon-no-checked"></span><?= Yii::t('app', 'Pass / Ausweis') ?></div>
                                <button ng-if="userInfo.id==status.id && userInfo.validation_status!='SUCCESS'" ng-click="userProfileCtrl.gotoValidationPassport()" class="btn btn-submit"><?= Yii::t('app', 'Pass / Ausweis') ?></button>
                            </li>

                            <li>
                                <div ng-if="userInfo.video_identification_status=='ACCEPTED_AUTO' || userInfo.video_identification_status=='ACCEPTED_MANUAL'"><span class="icon-checked"></span><?= Yii::t('app', 'Video Ident.') ?></div>
                                <div ng-if="userInfo.id!=status.id && (userInfo.video_identification_status!='ACCEPTED_AUTO' || userInfo.video_identification_status!='ACCEPTED_MANUAL')"><span class="icon-no-checked"></span><?= Yii::t('app', 'Video Ident.') ?></div>
                                <button ng-if="userInfo.id==status.id && (userInfo.video_identification_status!='ACCEPTED_AUTO' || userInfo.video_identification_status!='ACCEPTED_MANUAL')" ng-click="userProfileCtrl.showAppDownloadPopup()" class="btn btn-submit"><?= Yii::t('app', 'Video Ident.') ?></button>
                            </li>

							<?php if (Yii::$app->user->identity->access_translator==1) { ?>
							<li>
								<a href="http://translationtool.jugl.net/" target="_blank">
									<button ng-if="userInfo.id==status.id && userInfo.validation_status!='SUCCESS'" class="btn btn-submit"><?= Yii::t('app', '   Übersetzer    ') ?></button>
								</a>
							</li>
							<?php } ?>
                        </ul>
                    </div>

                    <div class="status">
                        <div class="online" ng-if="userInfo.isOnline==2"><?=Yii::t('app','online')?></div>
                        <div class="online" ng-if="userInfo.isOnline==1"><?=Yii::t('app','mobile')?></div>
                        <div class="offline" ng-if="!userInfo.isOnline"><?=Yii::t('app','offline')?></div>
                    </div>

                    <div class="buttons">
                        <button class="friend-del" ng-if="userInfo.isMyFriend" ng-disabled="userInfo.requesting" ng-click="userProfileCtrl.deleteFromFriends(userInfo.id);"><?=Yii::t('app','Kontakt entfernen')?></button>
                        <button class="friend" ng-if="userInfo.friendRequestSend" ng-disabled="userInfo.requesting" ng-click="userProfileCtrl.addToFriends(userInfo.id);"><?=Yii::t('app','Anfrage erneut senden')?></button>
                        <br />
                        <div ng-if="userInfo.id!=status.id">
                            <button ng-if="userInfo.isMyFriendBlocked" class="user-blocked" ><?=Yii::t('app','Du wurdest gesperrt')?></button>
                            <button ng-if="!userInfo.isMyFriendBlocked" class="message" ng-click="messenger.talkWithUser(userInfo.id)"><?=Yii::t('app','Nachricht schreiben')?></button>
                            <br/>
                        </div>
                        <button class="friend-block" ng-if="!userInfo.ignored && status.id!=userInfo.id" ng-disabled="userInfo.requesting" ng-click="userProfileCtrl.addToIgnoreList(userInfo.id);"><?=Yii::t('app','Vorübergehend sperren')?></button>
                        <button class="friend" ng-if="userInfo.ignored" ng-disabled="userInfo.requesting" ng-click="userProfileCtrl.delFromIgnoreList(userInfo.id);"><?=Yii::t('app','Entsperren')?></button>

                        <div class="clearfix"></div>
                        <button class="btn btn-submit" ng-click="userProfileCtrl.subscribe()" ng-if="status.id!=userInfo.id && !userInfo.isMyFollow"><?= Yii::t('app', 'Abonnieren') ?></button>
                        <button class="btn btn-submit" ng-click="userProfileCtrl.unsubscribe()" ng-if="status.id!=userInfo.id && userInfo.isMyFollow"><?= Yii::t('app', 'Abonnement beenden')?></button>
                        <div class="clearfix"></div>

                    </div>
                </div>
            </div>
            <div class="account-column">
                <div class="account-box user-profile-info-box">
                    <h2><?=Yii::t('app','Persönliche Daten')?></h2>
                    <div class="profile-user-info">
                        <div class="data" ng-if="userInfo.nick_name">
                            <b><?=Yii::t('app','Nickname')?>:</b> {{userInfo.nick_name}}
                        </div>
                        <div class="data" ng-if="userInfo.sex">
                            <b><?=Yii::t('app','Geschlecht')?>:</b> {{userInfo.sex}}
                        </div>
                        <div class="data" ng-if="userInfo.marital_status">
                            <b><?=Yii::t('app','Familienstand')?>:</b> {{userInfo.marital_status}}
                        </div>
                        <div class="data" ng-if="userInfo.age">
                            <b><?=Yii::t('app','Alter')?>:</b> {{userInfo.age}}
                        </div>
                        <div class="data" ng-if="userInfo.birthday">
                            <b><?=Yii::t('app','Geboren am')?>:</b> {{userInfo.birthday}}
                        </div>
                        <div class="data" ng-if="userInfo.zip_city">
                            <b><?=Yii::t('app','PLZ / Ort')?>:</b> {{userInfo.zip_city}}
                        </div>
                        <div class="data" ng-if="userInfo.profession">
                            <b><?=Yii::t('app','Beruf')?>:</b> {{userInfo.profession}}
                        </div>
                        <div class="data" ng-if="userInfo.street_house_number">
                            <b><?=Yii::t('app','Straße / Haus-Nr.')?>:</b> {{userInfo.street_house_number}}
                        </div>
                        <div class="data" ng-if="userInfo.company_name">
                            <b><?=Yii::t('app','Firmenname')?>:</b> {{userInfo.company_name}}
                        </div>
                        <div class="data about" ng-if="userInfo.about">
                            <b><?=Yii::t('app','Über')?> {{userInfo | userName}}:</b>
                            {{userInfo.about}}
                        </div>
                    </div>
                    <div class="bottom-corner"></div>
                </div>

                <div class="account-box user-profile-change-network-box" id="teamleader-feedback">
                    <h2>
                        <span ng-if="userInfo.teamChangeFinishTime"><?=Yii::t('app','Teamwechsel')?></span>
                        <span ng-if="!userInfo.teamChangeFinishTime"><?=Yii::t('app','Team')?></span>
                    </h2>

                    <div class="profile-user-info">

                        <div ng-if="userInfo.canCreateStickRequest && userInfo.teamChangeFinishTime && userInfo.registered_by_become_member">
                            <button class="friend-network-change-block btn btn-submit" ng-click="userProfileCtrl.networkChangeBlock()"><?=Yii::t('app','Abwerben verhindern')?></button>
                        </div>

                        <div ng-if="userInfo.teamChangeFinishTime" server-countdown="userInfo.teamChangeFinishTime" server-countdown-with-days="true" class="countdown-change-network"></div>

                        <button ng-click="userProfileCtrl.requestTeamChange2()" ng-if="!userInfo.block_parent_team_requests && (!userInfo.parent || userInfo.parent.id!=status.id) && userInfo.id!=status.id && userInfo.teamChangeFinishTime && !userInfo.request_sent2" class="btn btn-submit"><?= Yii::t('app','Abwerben') ?></button>
                        <button ng-if="!userInfo.block_parent_team_requests && (!userInfo.parent || userInfo.parent.id!=status.id) && userInfo.id!=status.id && userInfo.teamChangeFinishTime && userInfo.request_sent2" class="btn btn-submit"><?= Yii::t('app','Bereits eingeladen') ?></button>
                        <a ng-if="userInfo.teamChangeFinishTime && userInfo.id==status.id" ui-sref="team-change-user-search" class="btn btn-submit"><?= Yii::t('app', 'Wechseln') ?></a>

                        <div ng-if="userInfo.parent">
                            <div class="offer-user-box clearfix">
                                <a ui-sref="userProfile({id: userInfo.id})">
                                    <div class="offer-user-avatar"><img ng-src="{{userInfo.avatar.image}}" alt=""/></div>
                                </a>
                             <div class="offer-user-name">{{::userInfo|userName}} <div ng-click="updateCountry(userInfo.id,[userInfo,followers.users,friends.users,feedback.items,trollboxMessages.items,teamFeedback.items])" id="{{::userInfo.flag}}" class=" flag flag-32 flag-{{userInfo.flag}}"></div></div>
                                <div class="offer-user-rating">
                                    <div class="star-rating">
                                        <span once-style="{width:(+userInfo.rating)+'%'}"></span>
                                    </div>
                                    <div class="user-feedback-count">({{::userInfo.feedback_count}})</div>
                                    <div ng-if="userInfo.packet=='VIP'" class="user-packet">&nbsp;</div>
                                    <div ng-if="userInfo.packet=='VIP_PLUS'" class="user-packet-vip-plus">&nbsp;</div>
                                </div>
                            </div>

                            <div class="text-change-network"><?= Yii::t('app','ist im Team von')?></div>

                            <div class="offer-user-box clearfix">
                                <a ui-sref="userProfile({id: userInfo.parent.id})">
                                    <div class="offer-user-avatar"><img ng-src="{{::userInfo.parent.avatarUrl}}" alt=""/></div>
                                </a>
                               <div class="offer-user-name">{{::userInfo.parent|userName}} <div ng-click="updateCountry(userInfo.parent.id,[userInfo,followers.users,friends.users,feedback.items,trollboxMessages.items,teamFeedback.items])" id="{{::userInfo.parent.flag}}" class="flag flag-32 flag-{{userInfo.parent.flag}}"></div></div>
                                <div class="offer-user-rating">
                                    <div class="star-rating">
                                        <span once-style="{width:(+userInfo.parent.rating)+'%'}"></span>
                                    </div>
                                    <div class="user-feedback-count">({{::userInfo.parent.feedback_count}})</div>
                                    <div ng-if="userInfo.parent.packet=='VIP'" class="user-packet">&nbsp;</div>
                                    <div ng-if="userInfo.parent.packet=='VIP_PLUS'" class="user-packet-vip-plus">&nbsp;</div>
                                </div>
                            </div>
                        </div>

                        <button ng-click="userProfileCtrl.requestTeamChange()" ng-if="status.parent_id!=userInfo.id && status.teamChangeFinishTime && userInfo.id!=status.id && !userInfo.teamChangeFinishTime && !userInfo.request_sent" class="btn btn-submit"><?= Yii::t('app','Komm in mein Team!') ?></button>
                        <button ng-if="status.parent_id!=userInfo.id && status.teamChangeFinishTime && userInfo.id!=status.id && !userInfo.teamChangeFinishTime && userInfo.request_sent" class="btn btn-submit"><?= Yii::t('app','Teamwechsel bereits beantragt') ?></button>
                        <button ng-click="userProfileCtrl.teamFeedback()" ng-if="userInfo.id==status.id && !userInfo.teamChangeFinishTime" class="btn btn-submit"><?= Yii::t('app','Teamleading bewerten') ?></button>

                    </div>
                    <div class="bottom-corner"></div>
                </div>

            </div>
        </div>

        <div ng-if="teamFeedback.items.length>0 || feedback.items.length>0" class="account-box">
            <h2><?=Yii::t('app','Bewertungen')?></h2>
            <div class="feedback-profile-box" id="teamleader-feedbacks">
                <div ng-if="teamFeedback.items.length>0" ng-click="collapseFeedback.feedbackUser=collapseFeedback.feedbackUser?0:1" ng-class="{'open':collapseFeedback.feedbackUser}" class="feedback-collapse-title">
                    <span class="icon-collapse"></span>
                    <?= Yii::t('app','Teamleaderbewertungen anzeigen') ?>
                    <div class="user-feedback-rating-box">
                        <div class="star-rating">
                            <span once-style="{width:(+userInfo.team_rating)+'%'}"></span>
                        </div>
                        <div class="feedback-count">({{::userInfo.team_feedback_count}})</div>
                    </div>
                </div>

                <ul ng-if="collapseFeedback.feedbackUser" class="feedback-list" scroll-load="userProfileCtrl.loadMoreTeamFeedback" scroll-load-visible="0.7" scroll-load-has-more="teamFeedback.hasMore" ng-class="{empty:teamFeedback.items.length == 0}">
                    <li ng-repeat="item in teamFeedback.items">
                        <div class="feedback-user">
                            <div class="offer-user-box clearfix">
                                <a ui-sref="userProfile({id: item.user.id})">
                                    <div class="offer-user-avatar"><img ng-src="{{::item.user.avatarSmall}}" alt=""/></div>
                                </a>
                               <div class="offer-user-name">{{::item.user|userName}} <div ng-click="updateCountry(item.user.id,[userInfo,followers.users,friends.users,feedback.items,trollboxMessages.items,teamFeedback.items])" id="{{::item.user.flag}}" class="flag flag-32 flag-{{::item.user.flag}}"></div></div>
                                <div class="offer-user-rating">
                                    <div class="star-rating">
                                        <span once-style="{width:(+item.user.rating)+'%'}"></span>
                                    </div>
                                    <div class="user-feedback-count">({{::item.user.feedback_count}})</div>
                                    <div ng-if="item.user.packet=='VIP'" class="user-packet">&nbsp;</div>
                                    <div ng-if="item.user.packet=='VIP_PLUS'" class="user-packet-vip-plus">&nbsp;</div>
                                </div>
                            </div>
                        </div>
                        <div class="feedback-text">
                            <p ng-bind-html="item.feedback|linky:'_blank'"></p>

                            <ul ng-if="item.response" class="feedback-response-box">
                                <li>
                                    <div class="feedback-user">
                                        <div class="offer-user-box clearfix">
                                            <a ui-sref="userProfile({id: userInfo.id})">
                                                <div class="offer-user-avatar"><img ng-src="{{::userInfo.avatar.image}}" alt=""/></div>
                                            </a>
                                       <div class="offer-user-name">{{::userInfo|userName}} <div ng-click="updateCountry(userInfo.id,[userInfo,followers.users,friends.users,feedback.items,trollboxMessages.items,teamFeedback.items])" id="{{::userInfo.flag}}" class="flag flag-32 flag-{{userInfo.flag}}"></div></div>
                                            <div class="offer-user-rating">
                                                <div class="feedback-response-dt">{{item.response_dt|date:"dd.MM.yyyy"}}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="feedback-text">
                                        <p ng-bind-html="item.response|linky:'_blank'"></p>
                                    </div>
                                </li>
                            </ul>
                            <br/>
                            <button ng-if="userInfo.id==status.id" type="button" class="btn btn-submit" ng-click="userProfileCtrl.teamFeedbackResponse(item)"><?= Yii::t('app','Antworten') ?></button>
                        </div>
                        <div class="feedback-date-and-rating clearfix">
                            <div class="star-rating">
                                <span once-style="{width:(+item.rating)+'%'}"></span>
                            </div>
                            <div class="feedback-date">{{::item.create_dt|date:"dd.MM.yyyy"}}</div>
                        </div>
                    </li>
                </ul>

                <div ng-if="feedback.items.length>0" ng-click="collapseFeedback.feedbackDeals=collapseFeedback.feedbackDeals?0:1" ng-class="{'open':collapseFeedback.feedbackDeals}" class="feedback-collapse-title">
                    <span class="icon-collapse"></span>
                    <?= Yii::t('app','Handelsbewertung') ?>

                    <div class="user-feedback-rating-box">
                        <div class="star-rating">
                            <span once-style="{width:(+userInfo.rating)+'%'}"></span>
                        </div>
                        <div class="feedback-count">({{::userInfo.feedback_count}})</div>
                    </div>
                </div>

                <ul ng-if="collapseFeedback.feedbackDeals" class="feedback-list" scroll-load="userProfileCtrl.loadMoreFeedback" scroll-load-visible="0.7" scroll-load-has-more="feedback.hasMore" ng-class="{empty:feedback.items.length == 0}">
                    <li ng-repeat="item in feedback.items">
                        <div class="feedback-user">
                            <div class="offer-user-box clearfix">
                                <a ui-sref="userProfile({id: item.user.id})">
                                    <div class="offer-user-avatar"><img ng-src="{{::item.user.avatarSmall}}" alt=""/></div>
                                </a>
                             <div class="offer-user-name">{{::item.user|userName}} <div ng-click="updateCountry(item.user.id,[userInfo,followers.users,friends.users,feedback.items,trollboxMessages.items,teamFeedback.items])" id="{{::userInfo.flag}}" id="{{::item.user.flag}}" class="flag flag-32 flag-{{item.user.flag}}"></div></div>
                                <div class="offer-user-rating">
                                    <div class="star-rating">
                                        <span once-style="{width:(+item.user.rating)+'%'}"></span>
                                    </div>
                                    <div class="user-feedback-count">({{::item.user.feedback_count}})</div>
                                    <div ng-if="item.user.packet=='VIP'" class="user-packet">&nbsp;</div>
                                    <div ng-if="item.user.packet=='VIP_PLUS'" class="user-packet-vip-plus">&nbsp;</div>
                                </div>
                            </div>
                        </div>
                        <div class="feedback-text">
                            <p ng-bind-html="item.feedback|linky"></p>
                            <ul ng-if="item.response" class="feedback-response-box">
                                <li>
                                    <div class="feedback-user">
                                        <div class="offer-user-box clearfix">
                                            <a ui-sref="userProfile({id: userInfo.id})">
                                                <div class="offer-user-avatar"><img ng-src="{{::userInfo.avatar.image}}" alt=""/></div>
                                            </a>
                                           <div class="offer-user-name">{{::userInfo|userName}} <div ng-click="updateCountry(userInfo.id,[userInfo,followers.users,friends.users,feedback.items,trollboxMessages.items,teamFeedback.items])" id="{{::userInfo.flag}}" class="flag flag-32 flag-{{userInfo.flag}}"></div></div>
                                            <div class="offer-user-rating">
                                                <div class="feedback-response-dt">{{item.response_dt|date:"dd.MM.yyyy"}}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="feedback-text">
                                        <p ng-bind-html="item.response|linky:'_blank'"></p>
                                    </div>
                                </li>
                            </ul>
                            <br/>
                            <button ng-if="userInfo.id==status.id" type="button" class="btn btn-submit" ng-click="userProfileCtrl.feedbackResponse(item)"><?= Yii::t('app','Antworten') ?></button>
                        </div>
                        <div class="feedback-date-and-rating clearfix">
                            <div class="star-rating">
                                <span once-style="{width:(+item.rating)+'%'}"></span>
                            </div>
                            <div class="feedback-date">{{::item.create_dt|date:"dd.MM.yyyy"}}</div>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="bottom-corner"></div>
        </div>

        <div ng-if="friends.users.length>0" class="account-box">
            <h2><?=Yii::t('app','Kontakte & Abonnenten von')?> {{userInfo | userName}}</h2>

            <div ng-if="friends.users.length>0">
                <div class="contact-collapse-title">
                    <div ng-click="collapse.friends=collapse.friends?0:1" ng-class="{'open':collapse.friends}" class="collapse-title">
                        <span class="icon-collapse"></span><?= Yii::t('app','Kontakte') ?>
                        <span class="count-elem">{{friends.count}}</span>
                    </div>
                </div>

                <div ng-if="collapse.friends" class="account-friends-list clearfix" scroll-load="userProfileCtrl.loadMore" scroll-load-visible="0.7" scroll-load-has-more="friends.hasMore" ng-class="{empty:friends.users.length == 0}">
                    <div class="account-friends-element" ng-repeat="user in friends.users">
                        <div class="afe-content">
                            <div ng-if="userInfo.id==status.id" class="change-fried-and-follow-box clearfix">
                                <div class="change-friend-box">
                                    <input ng-change="userProfileCtrl.changeFriend(user)" type="checkbox" value="user.isFriend" i-check ng-model="user.isFriend">
                                    <label><?= Yii::t('app', 'Kontakt') ?></label>
                                </div>
                                <div class="change-follow-box">
                                    <label><?= Yii::t('app', 'Abonniert') ?></label>
                                    <input ng-change="userProfileCtrl.changeSubscribe(user)" type="checkbox" value="user.isFollow" i-check ng-model="user.isFollow">
                                </div>
                            </div>

                            <a ui-sref="userProfile({id: user.id})" ng-if="user.isFriend" class="icon info"></a>
                            <a ui-sref="userProfile({id: user.id})" class="avatar"><img ng-src="{{user.avatar}}" alt="" /></a>
                            <div class="icon message" ng-click="messenger.talkWithUser(user.id)" ng-if="user.isFriend"></div>
                            <div class="padding">
                                <div class="name">{{user | userName}} <div ng-click="updateCountry(user.id,[userInfo,followers.users,friends.users,feedback.items,trollboxMessages.items,teamFeedback.items])" id="{{user.flag}}" class="flag flag-32 flag-{{user.flag}}"></div></div>
                                <div class="place" ng-bind="user.address"></div>
                                <div class="user-status" ng-class="{'online': user.online == 1 || user.online == 2, 'offline': !user.online}">{{user.online==2 ? "<?=Yii::t('app','online')?>" : (user.online==1 ? "<?=Yii::t('app','mobile')?>" : "<?=Yii::t('app','offline')?>")}}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div ng-if="followers.users.length>0">
                <div class="contact-collapse-title">
                    <div ng-click="collapse.followers=collapse.followers?0:1" ng-class="{'open':collapse.followers}" class="collapse-title">
                        <span class="icon-collapse"></span><?= Yii::t('app','Abonnenten') ?>
                        <span class="count-elem">{{followers.count}}</span>
                    </div>
                </div>

                <div ng-if="collapse.followers" class="account-friends-list clearfix" scroll-load="userProfileCtrl.loadMoreFollowers" scroll-load-visible="0.7" scroll-load-has-more="followers.hasMore" ng-class="{empty:followers.users.length == 0}">
                    <div class="account-friends-element" ng-repeat="user in followers.users">
                        <div class="afe-content">
                            <a ui-sref="userProfile({id: user.id})" ng-if="user.isFriend" class="icon info"></a>
                            <a ui-sref="userProfile({id: user.id})" class="avatar"><img ng-src="{{user.avatar}}" alt="" /></a>
                            <div class="icon message" ng-click="messenger.talkWithUser(user.id)" ng-if="user.isFriend"></div>
                            <div class="padding">
                                <div class="name">{{user | userName}} <div ng-click="updateCountry(user.id,[userInfo,followers.users,friends.users,feedback.items,trollboxMessages.items,teamFeedback.items])" id="{{user.flag}}" class="flag flag-32 flag-{{user.flag}}"></div></div>
                                <div class="place" ng-bind="user.address"></div>
                                <div class="user-status" ng-class="{'online': user.online == 1 || user.online == 2, 'offline': !user.online}">{{user.online==2 ? "<?=Yii::t('app','online')?>" : (user.online==1 ? "<?=Yii::t('app','mobile')?>" : "<?=Yii::t('app','offline')?>")}}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div ng-if="trollboxMessages.items.length>0" id="mainTrollboxMessages">
                <div class="contact-collapse-title">
                    <div ng-click="collapse.trollboxMessages=collapse.trollboxMessages?0:1" ng-class="{'open':collapse.trollboxMessages}" class="collapse-title">
                        <span class="icon-collapse"></span><?= Yii::t('app','Forumsbeiträge') ?>
                        <span class="count-elem">{{trollboxMessages.count}}</span>
                    </div>
                </div>

                <div ng-if="collapse.trollboxMessages" scroll-load="userProfileCtrl.loadMoreTrollboxMessages" scroll-load-visible="0.7" scroll-load-has-more="trollboxMessages.hasMore" class="user-profile-trollbox-messages-box">
                    <div ng-repeat="trollboxMessage in trollboxMessages.items" class="dashboard-forum-message trollbox-message-box" ng-class="{'trollbox-messages-deleted': trollboxMessage.status=='DELETED' || trollboxMessage.status=='REJECTED'}">

                        <div class="trollbox-message-status" ng-if="trollboxMessage.status=='DELETED' || trollboxMessage.status=='REJECTED'">
                            <span ng-if="trollboxMessage.status=='DELETED'" class="trollbox-message-status-deleted"><?= Yii::t('app', 'Gelöscht') ?></span>
                            <span ng-if="trollboxMessage.status=='REJECTED'" class="trollbox-message-status-rejected"><?= Yii::t('app', 'Blockiert') ?></span>
                        </div>

                        <div ng-if="trollboxMessage.file" class="trollbox-message-image">
                            <div ng-if="trollboxMessage.file.ext=='mp4'" class="video-box">
                                <video video poster="{{trollboxMessage.file.image_medium}}" playsinline webkit-playsinline loop muted preload="none">
                                    <source ng-src="{{trollboxMessage.file.url}}" type="video/mp4">
                                </video>
                            </div>
                            <a ng-if="trollboxMessage.file.ext!='mp4'" href="{{trollboxMessage.file.image_big}}" fancybox ><img ng-src="{{trollboxMessage.file.image_medium}}" /></a>
                        </div>

                        <div class="trollbox-message-text" ng-bind-html="trollboxMessage.text | emoticonFix:userProfileCtrl:true"></div>

                        <div class="trollbox-message-info clearfix">
                            <div class="rollbox-message-info-user">
                                <div class="offer-user-box">
                                    <a ui-sref="userProfile({id: trollboxMessage.user.id})">
                                        <div class="offer-user-avatar"><img ng-src="{{::trollboxMessage.user.avatar}}" alt=""/></div>
                                    </a>
                                    <div class="offer-user-name">{{::trollboxMessage.user|userName}} <div ng-click="updateCountry(trollboxMessage.user.id,[userInfo,followers.users,friends.users,feedback.items,trollboxMessages.items,teamFeedback.items])" id="{{::trollboxMessage.user.flag}}" class="flag flag-32 flag-{{trollboxMessage.user.flag}}"></div></div>
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
                                <div ng-if="trollboxMessage.status!='DELETED'" class="message-comment-bnt-box">
                                    <a href="" ng-click="userProfileCtrl.enterGroupChat(trollboxMessage.id)" class="btn btn-submit"><?= Yii::t('app','Kommentieren') ?></a>
                                </div>
                                <div class="message-info-total-comments"><span>{{::trollboxMessage.messagesCount|default:0}}</span><?= Yii::t('app','Kommentare insgesamt ') ?></div>
                            </div>

                            <div class="trollbox-message-info-actions">
                                <div ng-if="status.is_moderator && trollboxMessage.status!='DELETED'" class="message-actions-box">
                                    <button ng-click="userProfileCtrl.trollboxRejectMessage(trollboxMessage)" set-if="trollboxMessage.status=='ACTIVE' || trollboxMessage.status=='AWAITING_ACTIVATION'" class="btn-reject-message">{{::trollboxMessage.status=='AWAITING_ACTIVATION' ? '<?=Yii::t('app', 'Ablehnen')?>':'<?=Yii::t('app', 'Blockieren')?>'}}</button>
                                    <button ng-click="userProfileCtrl.trollboxAcceptMessage(trollboxMessage)" set-if="trollboxMessage.status=='REJECTED' || trollboxMessage.status=='AWAITING_ACTIVATION'" class="btn-accept-message">{{::trollboxMessage.status=='AWAITING_ACTIVATION' ? '<?=Yii::t('app', 'Freigeben')?>':'<?=Yii::t('app', 'Entblocken')?>'}}</button>
                                    <button ng-click="userProfileCtrl.trollboxBlockUser(trollboxMessage)" ng-if="!trollboxMessage.user.is_blocked_in_trollbox" class="btn-block-user"><?=Yii::t('app', 'Für alle Foren sperren')?></button>
                                    <button ng-click="userProfileCtrl.trollboxUnblockUser(trollboxMessage)" ng-if="trollboxMessage.user.is_blocked_in_trollbox" class="btn-accept-message"><?=Yii::t('app', 'Für alle Foren entsperren')?></button>
                                    <button ng-click="userProfileCtrl.trollboxSetStickyTrollboxMessage(trollboxMessage)" set-if="!trollboxMessage.is_sticky" class="btn-accept-message"><?=Yii::t('app', 'Beitrag anpinnen')?></button>
                                    <button ng-click="userProfileCtrl.trollboxUnsetStickyTrollboxMessage(trollboxMessage)" set-if="trollboxMessage.is_sticky" class="btn-accept-message"><?=Yii::t('app', 'Beitrag entpinnen')?></button>

                                    <div set-if="trollboxMessage.status=='REJECTED' && trollboxMessage.status_changed_dt && trollboxMessage.status!='DELETED'" class="message-reject">
                                        <div class="dt-message-reject">{{::trollboxMessage.status_changed_dt|date : 'dd.MM.yyyy - HH:mm'}} <?= Yii::t('app','Uhr') ?> {{::trollboxMessage.user.id==trollboxMessage.status_changed_user_id ? '<?=Yii::t('app','Nutzer')?>':'<?= Yii::t('app','Moderator') ?>'}}</div>
                                        <div class="user-message-reject">{{::trollboxMessage.statusChangedUser}}</div>
                                    </div>
                                    <div set-if="trollboxMessage.status=='ACTIVE' && trollboxMessage.status_changed_dt && trollboxMessage.status!='DELETED'" class="message-accept">
                                        <div class="dt-message-accept">{{::trollboxMessage.status_changed_dt|date : 'dd.MM.yyyy - HH:mm'}} <?= Yii::t('app','Uhr') ?> {{::trollboxMessage.user.id==trollboxMessage.status_changed_user_id ? '<?= Yii::t('app','Nutzer') ?>':'<?= Yii::t('app','Moderator') ?>'}}</div>
                                        <div class="user-message-accept">{{::trollboxMessage.statusChangedUser}}</div>
                                    </div>
                                </div>
                                <div class="message-info-votes">
                                    <div class="negative" ng-click="userProfileCtrl.trollboxVoteDown(trollboxMessage)">{{::trollboxMessage.votes_down}}</div>
                                    <div class="positive" ng-click="userProfileCtrl.trollboxVoteUp(trollboxMessage)">{{::trollboxMessage.votes_up}}</div>
                                    <div class="votes-view" ng-click="(trollboxMessage.votes_down+trollboxMessage.votes_up)>0 ? userProfileCtrl.votesView(trollboxMessage.id):null" ng-class="{'no-votes':(trollboxMessage.votes_down+trollboxMessage.votes_up)===0}"></div>
                                </div>
                                <div ng-if="trollboxMessage.trollbox_category_id" class="trollbox-message-category">{{trollboxMessage.trollbox_category}}</div>
                            </div>
                        </div>

                        <div ng-if="userInfo.id==status.id && trollboxMessage.status!='DELETED'" class="trollbox-main-btn-box">
                            <button class="btn btn-submit" ng-click="userProfileCtrl.trollboxMessageUpdate(trollboxMessage.id)"><?= Yii::t('app','Bearbeiten') ?></button>
                            <button class="btn btn-submit" ng-click="userProfileCtrl.trollboxMessageDelete(trollboxMessage.id)"><?= Yii::t('app','Löschen') ?></button>
                        </div>

                    </div>
                </div>


            </div>


            <div class="bottom-corner"></div>
        </div>
    </div>

</div>
