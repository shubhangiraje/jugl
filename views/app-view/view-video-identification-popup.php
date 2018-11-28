<div class="close" ng-click="modalService.hideInfo()"></div>
<div class="content" ng-controller="ViewVideoIdentificationPopupCtrl as viewVideoIdentificationPopupCtrl">

    <div class="view-video-identification-box">
        <div class="trollbox-message-box clearfix">
            <div class="trollbox-message-video-verification-box">
                <div class="trollbox-message-image">

                    <div ng-if="trollboxMessage.file.ext=='mp4'" class="video-box">
                        <video video poster="{{trollboxMessage.file.image_medium}}" playsinline webkit-playsinline loop muted preload="none">
                            <source ng-src="{{trollboxMessage.file.url}}" type="video/mp4">
                        </video>
                    </div>

                    <!--<a href="{{::trollboxMessage.file.url}}" target="_blank" class="play-video"><img ng-src="{{trollboxMessage.file.image_medium}}" /></a>-->
                </div>

                <div ng-if="trollboxMessage.voted" class="video-verification-votes-text">
                    <?= Yii::t('app', 'Vielen Dank für Deine Abstimmung') ?></translate>
                </div>

                <div class="trollbox-message-info clearfix">
                    <div class="rollbox-message-info-user">
                        <div class="offer-user-box">
                            <a ui-sref="userProfile({id: trollboxMessage.user.id})">
                                <div class="offer-user-avatar"><img ng-src="{{::trollboxMessage.user.avatar}}" alt=""/></div>
                            </a>
                            <div class="offer-user-name">{{::trollboxMessage.user|userName}} <div ng-click="updateCountry(trollboxMessage.user.id,log.items)" id="{{::trollboxMessage.user.flag}}" class="flag flag-32 flag-{{trollboxMessage.user.flag}}"></div></div>
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
                            <a href="" ng-click="viewVideoIdentificationPopupCtrl.enterGroupChat(trollboxMessage.id)" class="btn btn-submit"><?= Yii::t('app','Kommentieren') ?></a>
                        </div>
                        <div ng-click="viewVideoIdentificationPopupCtrl.enterGroupChat(trollboxMessage.id)" class="message-info-total-comments"><span>{{::trollboxMessage.messagesCount|default:0}}</span><?= Yii::t('app','Kommentare insgesamt ') ?></div>
                    </div>

                    <div class="trollbox-message-info-actions trollbox-message-video-ident-actions-box">
                        <div class="video-ident-actions-wrap">
                            <div class="video-ident-actions-box" ng-if="!trollboxMessage.voted && status.id!=trollboxMessage.user.id">
                                <div>
                                    <button ng-click="viewVideoIdentificationPopupCtrl.trollboxVoteDown(trollboxMessage.id)" class="small-btn small-btn-red"><?= Yii::t('app', 'nicht echt') ?></button>
                                    <div ng-if="status.is_moderator && trollboxMessage.votes_down>0" ng-click="viewVideoIdentificationPopupCtrl.votesViewVideo(trollboxMessage.id,'down')" class="votes-view">{{trollboxMessage.votes_down}}</div>
                                </div>
                                <div>
                                    <button ng-click="viewVideoIdentificationPopupCtrl.trollboxVoteUp(trollboxMessage.id)" class="small-btn small-btn-green"><?= Yii::t('app', 'echt') ?></button>
                                    <div ng-if="status.is_moderator && trollboxMessage.votes_up>0" ng-click="viewVideoIdentificationPopupCtrl.votesViewVideo(trollboxMessage.id,'up')" class="votes-view">{{trollboxMessage.votes_up}}</div>
                                </div>
                            </div>
                            <div ng-if="trollboxMessage.count_votes>0" class="count-votes"><?= Yii::t('app', 'Stimmen') ?>: {{trollboxMessage.count_votes}}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div ng-if="trollboxMessage.messages.length>0" class="message-info-response">
                <div class="message-info-response-item" ng-repeat="message in trollboxMessage.messages">
                    <div class="message-info-response-info" ng-click="viewVideoIdentificationPopupCtrl.enterGroupChat(trollboxMessage.id)">
                        <div class="message-info-response-data clearfix">
                            <div class="message-info-response-user-info clearfix">
                                <a ui-sref="userProfile({id: message.user.id})">
                                    <div class="offer-user-avatar"><img ng-src="{{::message.user.avatar}}" alt=""/></div>
                                </a>
                                <div class="message-info-response-user">{{::message.user|userName}} <div ng-click="updateCountry(message.user.id,[log.items]); $event.stopPropagation();" id="{{::message.user.flag}}" class="flag flag-32 flag-{{message.user.flag}}"></div></div>
                            </div>
                            <div class="message-info-response-dt">{{::message.dt|date:"dd MMMM yyyy | HH:mm"}}</div>
                        </div>

                        <div ng-if="message.content_type!='IMAGE' || message.content_type!='VIDEO'" class="message-info-response-message" ng-bind-html="message.text|emoticonFix:viewVideoIdentificationPopupCtrl:true"></div>

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


    </div>


</div>