<?php

use app\components\NgAsset;
use yii\helpers\Url;
use \yii\helpers\Json;
use app\models\User;

NgAsset::register($this);

$config=[
    'chat'=>[
        'authorizationKey'=>Yii::$app->user->identity->chatAuthorizationKey,
        'connect'=>Yii::$app->params['chat']['connect']
    ],
    //'redirectToRegistrationPayment'=>Yii::$app->user->identity->status==User::STATUS_AWAITING_MEMBERSHIP_PAYMENT,
    'showAppPopup'=>in_array(Yii::$app->user->identity->status,[User::STATUS_REGISTERED]),
    'forceFillupProfile'=>in_array(Yii::$app->user->identity->status,[User::STATUS_LOGINED]),
    'language'=>Yii::$app->language,
    'useFacebookPixel'=>Yii::$app->params['useFacebookPixel'],
    'buyTokenUri'=>Yii::$app->params['buyTokenSite'].Yii::$app->params['buyTokenUrl'].'?PHPSESSID='.session_id()
];

?>
<script>
    var config=<?=Json::encode($config)?>;
    var infoViewedViews=<?=Json::encode($infoViewedViews)?>
</script>

<div ng-controller="AccountPanelCtrl as accountPanelCtrl" class="account-panel" >

    <h2><?=Yii::t('app','Navigation')?></h2>
    <div account-panel-close class="close"></div>
	 <div class="account-panel-box">
        <a ng-click="showInfoPopup('view-earn-money')" class="account-panel-link icon-searches"><?=Yii::t('app','Wie funktioniert Jugl.net?')?></a>
		</div>
    <div class="account-panel-box">
        <a ui-sref="forum" ui-sref-active="active" class="account-panel-link icon-forum"><?=Yii::t('app','Jugl Forum')?></a>
        <a ui-sref="earn-money" ui-sref-active="active" class="account-panel-link icon-earn-money"><?=Yii::t('app','Geld verdienen')?></a>
        <a ui-sref="friendsInvitation.invite" ui-sref-active="active" class="account-panel-link invite"><?=Yii::t('app','Freunde einladen / Netzwerk aufbauen')?></a>
        <a ui-sref="offers.index" ui-sref-active="active" class="account-panel-link icon-offers-add">
            <?=Yii::t('app','Kaufen / verkaufen, Interessen angeben')?>
            <span class="badge" ng-if="status.stat_new_offers>0 || status.stat_new_offers_requests>0">{{status.stat_new_offers + status.stat_new_offers_requests>99 ? '99+':status.stat_new_offers + status.stat_new_offers_requests}}</span>
        </a>
        <a ui-sref="searches.index" ui-sref-active="active" class="account-panel-link icon-searches">
            <?=Yii::t('app','Suchauftrag erstellen / recherchieren / vermitteln / Interessen angeben')?>
            <span class="badge" ng-if="status.stat_new_search_requests>0 || status.stat_new_search_requests_offers>0">
                {{status.stat_new_search_requests + status.stat_new_search_requests_offers>99 ? '99+':status.stat_new_search_requests + status.stat_new_search_requests_offers}}
            </span>
        </a>
    </div>
    <div class="account-panel-box">
        <a ui-sref="functions" ui-sref-active="active" class="account-panel-link icon-functions"><?=Yii::t('app','Alle Funktionen')?></a>
        <a ui-sref="dealsCompleted" ui-sref-active="active" class="account-panel-link icon-offers-completed">
            <?=Yii::t('app','Gesch&auml;fte & Bewertungen')?>
            <span class="badge" ng-if="status.stat_awaiting_feedbacks>0">
                {{status.stat_awaiting_feedbacks>99 ? '99+':status.stat_awaiting_feedbacks}}
            </span>
        </a>
        <a ui-sref="favorites" ui-sref-active="active" class="account-panel-link icon-favorites"><?=Yii::t('app','Mein Merkzettel')?></a>
    </div>
    <div class="account-panel-box">
        <a ui-sref="funds" ui-sref-active="active" class="account-panel-link funds"><?=Yii::t('app','Mein Konto')?></a>
        <a ui-sref="userSearch" ui-sref-active="active" class="account-panel-link search"><?=Yii::t('app','Mitglieder suchen')?></a>
        <a ui-sref="userProfile({id: status.id})" ui-sref-active="active" class="account-panel-link icon-profile"><?=Yii::t('app','Mein Profil')?></a>
        <a ng-if="status.packet == 'VIP_PLUS'" ui-sref="manageNetwork" ui-sref-active="active" class="account-panel-link icon-manage-network"><?=Yii::t('app','Netzwerk verwalten')?></a>
        <a ui-sref="profileSettings" ui-sref-active="active" class="account-panel-link icon-settings"><?=Yii::t('app','Einstellungen')?></a>
        <?php /* <a ng-click="accountPanelCtrl.deleteProfile()" class="account-panel-link icon-profile-delete"><?=Yii::t('app','Profil löschen')?></a> */ ?>
        <?php /* <a ui-sref="help" ui-sref-active="active" class="account-panel-link help"><?=Yii::t('app','Hilfe')?></a> */ ?>
    </div>
</div>


<header ng-controller="ToolbarCtrl as toolbarCtrl" ng-cloack>
    <div class="header-top">
        <div class="container clearfix">
            <a href="/" class="logo"><img src="/static/images/account/account-small-logo.png" alt="jugl.net" /></a>
            <div class="header-lang">
                <div class="lang <?= Yii::$app->language ?>" ng-click="toolbarCtrl.langPopup()" ></div>
            </div>
            <div class="header-greeting" ng-if="status.first_name && status.last_name"><a ui-sref="dashboard"><?=Yii::t('app','Willkommen, ')?> {{status | userName}}</a></div>
            <div class="header-separator"></div>
            <div class="logout"><a href="<?=Url::to(['site/logout'])?>"><?=Yii::t('app','Logout')?></a></div>
			<div ng-click="showInfoPopup('view-earn-money')" class="border-btn-white how-it-works-button"><?= Yii::t('app', 'So funktioniert Jugl') ?> <span ng-class="{'blink':isOneShowInfoPopup('view-earn-money')}" class="info-icon"></span></div>
            <div class="header-right">
                <?php /* <div class="header-help"><a ui-sref="help" ui-sref-active="active"><?=Yii::t('app','Hilfe')?></a></div> */ ?>
                <div class="header-forum"><a ui-sref="forum" ui-sref-active="active"><?=Yii::t('app','zum Jugl-Forum')?></a></div>
                <div class="header-functions"><a ui-sref="dashboard"><?=Yii::t('app','zur Startseite')?></a></div>
                <div class="header-friends-invite"><a ui-sref="friendsInvitation.invite" ui-sref-active="active"><?=Yii::t('app','Freunde einladen')?></a></div>
            </div>
        </div>
    </div>

    <div class="header-panel">
        <div class="container clearfix mobile-panel">
            <div class="header-panel-element nav">
                <div class="icon"><span class="badge" ng-if="status.stat_new_offers + status.stat_new_offers_requests + status.stat_new_search_requests + status.stat_new_search_requests_offers+status.stat_awaiting_feedbacks>0">{{status.stat_new_offers + status.stat_new_offers_requests + status.stat_new_search_requests + status.stat_new_search_requests_offers+status.stat_awaiting_feedbacks>99 ? 99+'+':status.stat_new_offers + status.stat_new_offers_requests + status.stat_new_search_requests + status.stat_new_search_requests_offers+status.stat_awaiting_feedbacks}}</span></div>
                <a class="link" account-panel></a>
                <div class="title"><?=Yii::t('app','Navigation')?></div>
            </div>

            <div ng-if="status.first_name && status.last_name" class="user-info-wrapper">
                <div class="user-info">
                    <img ng-src="{{status.avatarFile.thumbs.avatarSmall}}" alt="{{status | userName}}" />
                    <div class="name" user-info-menu>{{status | userName}}</div>
                    <div class="user-info-menu">
                        <ul>
                            <li><a ui-sref="userProfile({id: status.id})" ui-sref-active="active" class="profile"><?=Yii::t('app','Mein Profil')?></a></li>
                            <li><a href="<?=Url::to('site/logout');?>" class="logout"><?=Yii::t('app','Abmelden')?></a></li>
                            <li class="user-info-menu-packet" ng-if="status.packet=='VIP_PLUS' || status.packet=='VIP' || status.packet=='STANDART'">
                                <div>
                                    <span class="user-info-menu-packet-text"><?=Yii::t('app','Ihre Mitgliedschaft')?>: </span>
                                    <span class="user-info-menu-packet-current" ng-if="status.packet=='VIP_PLUS'"><?= Yii::t('app', 'PremiumPlus') ?></span>
                                    <span class="user-info-menu-packet-current" ng-if="status.packet=='VIP'"><?= Yii::t('app', 'Premium') ?></span>
                                    <span class="user-info-menu-packet-current" ng-if="status.packet=='STANDART'"><?= Yii::t('app', 'Standard') ?></span>
                                </div>
                                <a class="user-info-menu-packet-update-btn" ng-if="status.packet!='VIP_PLUS'" ui-sref="packetUpgrade"><?=Yii::t('app', 'Upgrade') ?></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="header-panel-element nav">
                <div class="icon"></div>
                <a class="link" mobile-show-panel></a>
                <div class="title"><?=Yii::t('app','Kurzinfos')?></div>
            </div>
        </div>

        <div class="container clearfix main">
            <div class="header-panel-element nav">
                <div class="title"><?=Yii::t('app','Navigation')?></div>
                <div class="icon"><span class="badge" ng-if="status.stat_new_offers + status.stat_new_offers_requests + status.stat_new_search_requests + status.stat_new_search_requests_offers+status.stat_awaiting_feedbacks>0">{{status.stat_new_offers + status.stat_new_offers_requests + status.stat_new_search_requests + status.stat_new_search_requests_offers+status.stat_awaiting_feedbacks>99 ? 99+'+':status.stat_new_offers + status.stat_new_offers_requests + status.stat_new_search_requests + status.stat_new_search_requests_offers+status.stat_awaiting_feedbacks}}</span></div>
                <a class="link" account-panel></a>
            </div>

            <div class="header-panel-element news">
                <div class="title"><?=Yii::t('app','Nachrichten')?></div>
                <div class="amount" ng-bind="messenger.user.unreaded_chat_messages"></div>
                <div class="icon"></div>
                <a class="link" ng-click="messenger.showChat(true)"></a>
            </div>

            <div class="header-panel-element activities">
                <div class="title"><?=Yii::t('app','Aktivitäten')?></div>
                <div class="icon"><span class="badge" ng-if="status.new_events + status.new_follower_events>0">{{status.new_events + status.new_follower_events>99 ? 99+'+':status.new_events + status.new_follower_events}}</span></div>
                <a ui-sref="activityList" class="link"></a>
            </div>

            <div class="header-panel-element balance">
                <div class="title"><?=Yii::t('app','Kontostand')?></div>
                <div class="amount"></div>
                <div class="summ">{{status.balance | priceFormat}} <jugl-currency></jugl-currency></div>
                <a ui-sref="funds.log" class="link"></a>
            </div>

            <div class="header-panel-element network">
                <div class="title"><?=Yii::t('app','Dein Netzwerk')?></div>
                <div class="amount"></div>
                <div class="summ" ng-bind="status.network_size"></div>
                <a ui-sref="network" class="link"></a>
            </div>

            <div class="header-panel-element new-members">
                <div class="title"><?=Yii::t('app','Neu im Netzwerk')?></div>
                <div class="amount"></div>
                <div class="summ" ng-bind="status.new_network_members"></div>
                <a ui-sref="networkMembers" class="link"></a>
            </div>

            <div ng-if="status.first_name && status.last_name" class="user-info-wrapper">
                <div class="user-info">
                    <img ng-src="{{status.avatarFile.thumbs.avatarSmall}}" alt="{{status | userName}}" />
                    <div class="name" user-info-menu>{{status | userName}}</div>
                    <div class="user-info-menu">
                        <ul>
                            <li><a ui-sref="userProfile({id: status.id})" ui-sref-active="active" class="profile"><?=Yii::t('app','Mein Profil')?></a></li>
                            <li><a href="<?=Url::to('site/logout');?>" class="logout"><?=Yii::t('app','Abmelden')?></a></li>
                            <li class="user-info-menu-packet" ng-if="status.packet=='VIP_PLUS' || status.packet=='VIP' || status.packet=='STANDART'">
                                <div>
                                    <span class="user-info-menu-packet-text"><?=Yii::t('app','Ihre Mitgliedschaft')?>: </span>
                                    <span class="user-info-menu-packet-current" ng-if="status.packet=='VIP'"><?= Yii::t('app', 'Premium') ?></span>
                                    <span class="user-info-menu-packet-current" ng-if="status.packet=='VIP_PLUS'"><?= Yii::t('app', 'PremiumPlus') ?></span>
                                    <span class="user-info-menu-packet-current" ng-if="status.packet=='STANDART'"><?= Yii::t('app', 'Standard') ?></span>
                                </div>
                                <a class="user-info-menu-packet-update-btn" ng-if="status.packet!='VIP_PLUS'" ui-sref="packetUpgrade"><?=Yii::t('app', 'Upgrade') ?></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div ng-if="status.packet=='VIP_PLUS' || status.packet=='VIP' || status.packet=='STANDART'" class="header-panel-bottom">
        <div class="container">
            <div class="header-panel-packet">
                <span class="header-panel-packet-text"><?=Yii::t('app','Ihre Mitgliedschaft')?>: </span>
                <span class="header-panel-packet-current" ng-if="status.packet=='VIP'"><?= Yii::t('app', 'Premium') ?></span>
                <span class="header-panel-packet-current" ng-if="status.packet=='VIP_PLUS'"><?= Yii::t('app', 'PremiumPlus') ?></span>
                <span class="header-panel-packet-current" ng-if="status.packet=='STANDART'"><?= Yii::t('app', 'Standard') ?></span>
                <a class="header-panel-packet-update-btn" ng-if="status.packet!='VIP_PLUS'" ui-sref="packetUpgrade"><span><?=Yii::t('app', 'Upgrade') ?></span></a>
            </div>
        </div>
    </div>

    <div ng-if="status.packet===''" class="header-panel-bottom">
        <div class="container">
            <div class="header-panel-packet">
                <a class="header-panel-packet-update-btn" ui-sref="packetUpgrade"><span><?=Yii::t('app', 'Premium-upgrade hier klicken') ?></span></a>
            </div>
        </div>
    </div>

</header>

<div ui-view></div>

<div messenger-window-resize ng-controller="MessengerCtrl as messengerCtrl">
    <div class="account-messenger" ng-if="messenger.canShowChat" messenger-popup ng-class="{showed: messenger.showChat(), compact: messenger.compactMode}">
        <div class="close" ng-click="messenger.showChat(false)"></div>
        <div class="reduce" ng-click="messenger.rollupChat()"></div>
        <div class="increase" ng-click="messenger.expandChat()"></div>

        <div ng-if="!messenger.compactMode" class="messenger-columns clearfix">
            <div ng-if="messenger.conversation" class="messenger-conversation-column">
                <a ng-click="messenger.users[messenger.conversation.user_id].is_group_chat ? null:$state.go('userProfile',{id:messenger.conversation.user_id})" class="messenger-owner-info">
                    <img ng-src="{{(messenger.users[messenger.conversation.user_id] | default : messenger.conversation.user).avatar_url}}" alt="{{messenger.users[messenger.conversation.user_id] | default : messenger.conversation.user | userName}}" />
                    <span ng-bind-html="messenger.users[messenger.conversation.user_id] | default : messenger.conversation.user | userName | emoticonFix:messengerCtrl"></span>
                </a>

                <div class="messenger-conversation">
                    <div scroll-pane scroll-config="{stickToBottom: true, contentWidth: '0', autoReinitialise: true}" scroll-chat-bottom init-scroll-bottom="true" track-element=".messenger-conversation-message" id="messenger-conversation-scroll" class="messenger-conversation-inner">
                        <div>
                            <div ng-repeat="message in (log=(messenger.conversation.log | orderBy:['dt','id']))"
                                 class="messenger-conversation-message"
                                 ng-init="isOutgoing = message.user ? message.user.id==status.id:message.type.indexOf('OUTGOING')==0"
                                 once-class="{'visible-only-for-moderator': message.visible_only_for_moderator, out: isOutgoing, in: !isOutgoing, 'new-day': ( (message.dt | date : 'dd.MM.yyyy') != (log[ $index - 1 ].dt | date : 'dd.MM.yyyy') )}"
                                 data-message-date="{{::message.dt | date : 'EEEE, d MMMM yyyy'}}">

                                <div ng-if="message.content_type=='TEXT' && message.type=='SYSTEM'" class="system-message">
                                    <div class="system-message-body">{{::message.text}}</div>
                                </div>

                                <div ng-if="message.type!='SYSTEM'" class="body" ng-class="{'message-unread': message.type=='INCOMING_UNREADED', 'moderator-message': message.user.is_moderator}" >

                                    <div set-if="message.type=='INCOMING_UNREADED'" class="message-unread-text"><?= Yii::t('app', 'Neu') ?></div>

                                    <div set-if="!isOutgoing && message.user" class="message-user-name">{{::message.user | userName}}</div>

                                    <a ui-sref="userProfile({id: status.id})" class="avatar" set-if="isOutgoing">
                                        <img ng-src="{{::status.avatarFile.thumbs.avatar}}" alt="{{::status | userName}}" />
                                        <span set-if="messenger.users[messenger.conversation.user_id].is_group_chat && status.is_moderator" class="type-moderator"><?= Yii::t('app', 'Moderator') ?></span>
                                        <span set-if="messenger.users[messenger.conversation.user_id].is_group_chat && status.is_moderator" class="type-moderator-icon"></span>
                                    </a>
                                    <a ui-sref="userProfile({id: message.user ? message.user.id:messenger.conversation.user_id})" class="avatar" set-if="!isOutgoing">
                                        <img ng-src="{{::message.user ? message.user.avatar_small_url:messenger.users[messenger.conversation.user_id].avatar_small_url}}" alt="{{::(message.user ? message.user:messenger.users[messenger.conversation.user_id]) | userName}}" />
                                        <span set-if="messenger.users[messenger.conversation.user_id].is_group_chat && message.user.is_moderator" class="type-moderator"><?= Yii::t('app', 'Moderator') ?></span>
                                        <span set-if="messenger.users[messenger.conversation.user_id].is_group_chat && message.user.is_moderator" class="type-moderator-icon"></span>
                                    </a>
                                    <div set-if="message.content_type=='TEXT'" class="body-text" once-html="message.text | emoticonFix:messengerCtrl:true"></div>

                                    <a set-if="message.content_type=='IMAGE'" href="{{::message.file.url}}" target="_blank" fancybox><img ng-src="{{::message.file.thumbUrl}}"/></a>

                                    <div set-if="message.content_type=='GEOLOCATION'">Ich bin gerade hier:<br/><a href="http://www.google.com/maps/place/{{message.extra.lattitude}},{{message.extra.longitude}}" target="_blank"><img src="/static/images/account/map.jpg" width="110" height="110" style="margin-top: 5px;"></a></div>
                                    <a set-if="message.content_type=='VIDEO'" href="{{::message.file.url}}" target="_blank"><img src="/static/images/account/video.png"/></a>

                                    <div set-if="message.content_type=='AUDIO'" audio-player="message">
                                        <div class="audio-message">
                                            <div class="audio-message-box">
                                                <div class="audio-message-controls clearfix">
                                                    <div class="audio-message-button-box">
                                                        <button class="audio-message-button play"></button>
                                                    </div>
                                                    <div class="audio-message-progress-box">
                                                        <div class="audio-message-time-line">
                                                            <div class="audio-message-play-head"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="audio-message-time"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div set-if="message.content_type=='CONTACT'">
                                        <div class="message-contact-box">
                                            <div class="message-contact-user">
                                                {{::message.extra.contact.name.formatted}}
                                            </div>
                                            <ul class="message-contact-list" >
                                                <li ng-repeat="item in message.extra.contact.phoneNumbers" class="icon-contact-phone">
                                                    <div class="message-contact-list-value">{{::item.value}}</div>
                                                    <div class="message-contact-list-type">{{::item.type}}</div>
                                                </li>
                                                <li ng-repeat="item in message.extra.contact.emails" class="icon-contact-email">
                                                    <div class="message-contact-list-value break">{{::item.value}}</div>
                                                    <div class="message-contact-list-type">{{::item.type}}</div>
                                                </li>
                                                <li ng-repeat="item in message.extra.contact.addresses" class="icon-contact-address">
                                                    <div class="message-contact-list-value">{{::item.formatted}}</div>
                                                    <div class="message-contact-list-type">{{::item.type}}</div>
                                                </li>
                                                <li ng-if="message.extra.contact.birthday" class="icon-contact-birthday">
                                                    <div class="message-contact-list-value">{{::message.extra.contact.birthday|birthdayFormat}}</div>
                                                    <div class="message-contact-list-type"><translate>Geburtstag</translate></div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="message-status-info clearfix">
                                        <div class="delete" ng-click="messengerCtrl.deleteMessage(message)"><?=Yii::t('app','Löschen')?></div>
                                        <div class="deliveried" set-if="message.type=='OUTGOING_UNDELIVERED'"><span class="icon-undelivered"></span></div>
                                        <div class="deliveried" set-if="message.type=='OUTGOING_UNREADED'"><span class="icon-unreaded"></span></div>
                                        <div class="readed" set-if="message.type=='OUTGOING_READED'"><span class="icon-readed"></span></div>
                                        <div class="time">{{::message.dt | date : 'HH:mm'}}</div>
                                    </div>

                                    <div set-if="!isOutgoing && messenger.users[messenger.conversation.user_id].is_group_chat && status.is_moderator" class="message-groupchat-actions clearfix">
                                        <div ng-click="messenger.moderatorDeleteMessage(message.id)" set-if="!message.visible_only_for_moderator" class="groupchat-delete-message"><?= Yii::t('app','Nachricht entfernen') ?></div>
                                        <div ng-click="messenger.moderatorBlockUser(message.user.id)" set-if="!message.user.is_blocked_in_trollbox && !message.user.is_blocked_in_this_chat" class="groupchat-block-user"><?= Yii::t('app','Benutzer sperren') ?></div>
                                        <div ng-click="messenger.moderatorUnblockUser(message.user.id)" set-if="!message.user.is_blocked_in_trollbox && message.user.is_blocked_in_this_chat" class="groupchat-unblock-user"><?= Yii::t('app','Benutzer entsperren') ?></div>
                                        <div ng-click="messenger.moderatorBlockUserInTrollbox(message.user.id)" set-if="!message.user.is_blocked_in_trollbox" class="groupchat-block-user"><?= Yii::t('app','Für alle Foren sperren') ?></translate></div>
                                        <div ng-click="messenger.moderatorUnblockUserInTrollbox(message.user.id)" set-if="message.user.is_blocked_in_trollbox" class="groupchat-unblock-user"><?= Yii::t('app','Für alle Foren entsperren') ?></translate></div>
                                    </div>

                                </div>



                            </div>
                            <div ng-if="messenger.decision_needed_ids.indexOf(messenger.conversation.user_id)!==-1" class="decision-needed-box">
                                <div class="decision-needed-text"><?= Yii::t('app', 'Der Absender ist nicht in deiner Kontaktliste') ?></div>
                                <div class="decision-needed-buttons">
                                    <button ng-click="messenger.decisionAddToFriends()"><?= Yii::t('app', 'Kontakt annehmen'); ?></button>
                                    <!--<button ng-click="messenger.decisionSkip()"><?= Yii::t('app', 'Kontakt ablehnen'); ?></button>-->
                                    <button ng-click="messenger.decisionSpam()"><?= Yii::t('app', 'Spam melden'); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="messenger-conversation-form clearfix" ng-controller="ConversationCtrl as conversationCtrl">
                    <div class="upload-image">
                        <input nv-file-select filters="imageFilter,recipientIgnoreFilter" uploader="chatUploader" options="chatUploadOptions" type="file" />
                    </div>
                    <div class="smiles" emoticons-tooltip emoticons-list="messengerCtrl.emoticonsList" message-text="messenger.conversation.message.text">
                        <div class="emoticons-tooltip">
                            <span ng-repeat="(emoticon,text) in conversationCtrl.emoticonsList" ng-bind="emoticon" class="emoticon"></span>
                        </div>
                    </div>
                    <textarea ng-readonly="messenger.conversation.message.sending || chatUploader.isUploading" messenger-textarea placeholder="<?=Yii::t('app','Deine Nachricht...')?>" ng-model="messenger.conversation.message.text" maxlength="2000" ></textarea>
                    <button ng-click="conversationCtrl.sendMessage()" ng-disabled="messenger.conversation.message.sending || chatUploader.isUploading"><?=Yii::t('app','Senden')?></button>
                </div>

            </div>
            <div class="messenger-contacts-column">
                <div ng-if="!messenger.compactMode" class="clearfix"><div class="title"><?=Yii::t('app','Messenger / Kontakte')?></div></div>

                <div class="messenger-contacts-tabs-content">
                    <div class="messenger-conversations-filter">
                        <b><?=Yii::t('app','Status:')?></b>
                        <input type="radio" i-check ng-model="messengerCtrl.filterStatus" value="1" /><label><?=Yii::t('app','online')?></label>
                        <input type="radio" i-check ng-model="messengerCtrl.filterStatus" value="" /><label><?=Yii::t('app','alle')?></label>
                    </div>

                    <div class="messenger-conversations-filter-btn-group clearfix">
                        <button type="button" ng-class="{'active':messengerCtrl.displayChats=='chats'}" ng-click="messengerCtrl.displayChats='chats'"><?= Yii::t('app', 'Chats') ?><span class="badge" ng-if="messenger.user.unreaded_chat_messages>0">{{messenger.user.unreaded_chat_messages>99 ? '99+':messenger.user.unreaded_chat_messages}}</span></button>
                        <button type="button" ng-class="{'active':messengerCtrl.displayChats=='forumChats'}" ng-click="messengerCtrl.displayChats='forumChats'"><?= Yii::t('app', 'Forumchats') ?><span class="badge" ng-if="messenger.user.unreaded_group_chat_messages>0">{{messenger.user.unreaded_group_chat_messages>99 ? '99+':messenger.user.unreaded_group_chat_messages}}</span></button>
                    </div>

                    <div messenger-contacts-resize scroll-pane scroll-config="{contentWidth: '0'}" track-element=".messenger-contact-element" id="messenger-contacts-list" class="messenger-contacts-list" ng-if="messenger.users">
                        <div ng-repeat="user in messenger.users | objectToList | filter : userStatusFilter(false) | orderBy : userOrderDt:true | limitTo: 500"
                             ng-init="conversation=userConversations[user.id]"
                             ng-click="messenger.openConversation(user.id)"
                             class="messenger-contact-element">
                            <div class="clearfix">
                                <a class="avatar"><img ng-src="{{user.avatar_small_url}}" alt="{{user | userName}}" /></a>
                                <div class="messenger-contact-info" ng-class="{'has-unreaded-messages': conversation.unreaded_messages > 0}">
                                    <div class="user" data-unreaded-messages="{{conversation.unreaded_messages}}">

									<!--<div id="{{user.flag}}" class="img-thumbnail flag flag-icon flag-icon-{{user.flag}}"> </div>-->
									<span ng-bind-html="user | userName | emoticonFix:messengerCtrl"></span>

                                    <div class="messenger-contact-status" ng-class="{online: user.status==1 || user.status==2, offline: user.status!=1 && user.status!=2}">
                                        <div ng-if="user.status==2"><?=Yii::t('app','online')?></div>
                                        <div ng-if="user.status==1"><?=Yii::t('app','mobile')?></div>
                                        <div ng-if="user.status!=1 && user.status!=2"><?=Yii::t('app','offline')?></div>
                                    </div>
                                </div>
                                <div class="delete" ng-click="messengerCtrl.deleteConversationMessages(user.id, $event)"></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div ng-if="messenger.compactMode" class="conversations-list">
            <div ng-repeat="conversation in messenger.conversations | orderBy : rawProperty('message.dt') : true | limitTo: 700"
                 class="conversation-element"
                 ng-class="{'has-unreaded-messages': conversation.unreaded_messages>0}"
                 ng-init="user=messenger.users[conversation.user_id]"
                 ng-click="messenger.openConversation(user.id)">
                    <a class="avatar" data-unreaded-messages="{{conversation.unreaded_messages}}"><img ng-src="{{user.avatar_small_url}}" alt="{{user | userName}}" /></a>
                    <div class="user">{{user | userName}}</div>
            </div>
        </div>

    </div>
</div>

<div class="modal-box" ng-if="modalService.isShow" ng-class="{'show-modal':modalService.isShow, 'no-tranparent':modalService.data.noTransparentBackground}">
    <div class="modal-window" ng-if="!modalService.data.template">
        <div class="close" ng-if="!modalService.loading" ng-click="modalService.hide()"></div>
        <div class="content" ng-bind-html="modalService.data.message"></div>
        <div class="buttons" ng-if="modalService.data.buttons.length > 0 && !modalService.loading">
            <div ng-repeat="button in modalService.data.buttons"
                 ng-class="button.class"
                 ng-bind="button.caption"
                 ng-click="button.onClick()">
            </div>
        </div>
        <div ng-if="modalService.loading" class="loader-box">
            <div class="spinner"></div>
        </div>
    </div>
    <div class="modal-window" ng-class="modalService.data.classes" ng-if="modalService.data.template" ng-include="modalService.data.template"></div>
</div>

<div class="modal-box modal-info-box" ng-if="modalService.isShowInfo" ng-class="{'show-modal':modalService.isShowInfo}">
    <div class="modal-window" ng-if="!modalService.dataInfo.template">
        <div class="close" ng-click="modalService.hideInfo()"></div>
        <div class="content" ng-bind-html="modalService.dataInfo.message"></div>
        <div class="buttons" ng-if="modalService.dataInfo.buttons.length > 0">
            <div ng-repeat="button in modalService.dataInfo.buttons"
                 ng-class="button.class"
                 ng-bind="button.caption"
                 ng-click="button.onClick()">
            </div>
        </div>
    </div>
    <div class="modal-window" ng-class="modalService.dataInfo.classes" ng-if="modalService.dataInfo.template" ng-include="modalService.dataInfo.template"></div>
</div>


<div class="balloon-wrap" balloon-message>
    <div ng-repeat="balloon in balloons" class="balloon-box clearfix">
        <div class="balloon-user-avatar">
            <a ui-sref="userProfile({id: balloon.user.id})">
                <img ng-src="{{balloon.user.avatar_small_url}}" alt="{{balloon.user | userName}}" />
            </a>
        </div>
        <div class="balloon-info">
            <div class="balloon-username">{{balloon.user | userName}}</div>
            <div class="balloon-message">{{balloon.text}}</div>
            <button ng-click="messenger.talkWithUser(balloon.user.id)" class="balloonShowBtn" type="button"><?= Yii::t('app', 'Anzeigen') ?></button>
        </div>
        <div class="balloon-close" ng-click="balloons.splice($index, 1);"></div>
    </div>
</div>
