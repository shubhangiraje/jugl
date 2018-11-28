<div class="container trollbox">
    <div ng-click="showInfoPopup('view-forum')" ng-class="{'blink':isOneShowInfoPopup('view-forum')}" class="info-popup-btn"></div>
    <div class="welcome-text">
	    <h2 class="trollbox-heading"><?=Yii::t('app','Jugl Forum')?></h2>

        <div class="trollbox-filter-type">
            <button ng-click="trollboxCtrl.changeFilterType('')" ng-class="{'active': filter.type==''}" type="button"><?= Yii::t('app', 'Forum') ?></button>
            <button ng-click="trollboxCtrl.changeFilterType('VIDEO_IDENTIFICATION')" ng-class="{'active': filter.type=='VIDEO_IDENTIFICATION'}" type="button">
                <?= Yii::t('app', 'Verifizierungsvideos') ?>
                <span class="badge" ng-if="status.id==68 && count_video_identification>0">{{count_video_identification>99 ? '99+':count_video_identification}}</span>
            </button>
        </div>

        <div class="trollbox-header-box clearfix">
            <div class="searches-filter-list">
                <div class="field-box-select filter-select">
                    <multiselect ng-model="forumCountry" labels="labels"
                         options="countryList" id-prop="id" display-prop="name" show-select-all="true" show-unselect-all="true" show-search="true" >
                    </multiselect>
                </div>
            </div>

            <div ng-if="filter.type==''">
                <div class="trollbox-filter-visibility-box">
                    <div class="trollbox-filter-title"><?= Yii::t('app', 'anzeigen') ?>:</div>
                    <div class="trollbox-filter-btns">
                        <button ng-click="trollboxCtrl.changeFilterVisibility('')" ng-class="{'active': filter.visibility==''}" class="btn btn-submit"><?= Yii::t('app', 'Alle') ?></button>
                        <button ng-click="trollboxCtrl.changeFilterVisibility('MAIN')" ng-class="{'active': filter.visibility=='MAIN'}" class="btn btn-submit"><?= Yii::t('app', 'Meine') ?></button>
                        <button ng-click="trollboxCtrl.changeFilterVisibility('FOLLOWING')" ng-class="{'active': filter.visibility=='FOLLOWING'}" class="btn btn-submit"><?= Yii::t('app', 'Abos') ?></button>
                        <button ng-click="trollboxCtrl.changeFilterVisibility('CONTACTS')" ng-class="{'active': filter.visibility=='CONTACTS'}" class="btn btn-submit"><?= Yii::t('app', 'Kontakte');?></button>
                    </div>
                </div>

                <div class="trollbox-others-filter-box">
                    <div ng-if="!isUpdatedCategoryList" class="trollbox-filter-category field-box-select" dropdown-toggle select-click bs-has-classes>
                        <label><?= Yii::t('app','Kategorien') ?>:</label>
                        <select ng-model="filter.category" selectpicker="{title:''}" ng-options="item.id as item.title for item in categoryList">
                            <option value=""><?= Yii::t('app', 'Alle'); ?></option>
                        </select>
                    </div>

                    <div class="trollbox-filter-date field-box-select" dropdown-toggle select-click bs-has-classes>
                        <label><?= Yii::t('app','Zeitraum wählen') ?>:</label>
                        <select ng-model="filter.period" selectpicker="{title:''}">
                            <option value=""><?= Yii::t('app', 'Alle'); ?></option>
                            <option value="TODAY"><?= Yii::t('app', 'Heute'); ?></option>
                            <option value="WEEK"><?= Yii::t('app', 'Woche'); ?></option>
                            <option value="MONTH"><?= Yii::t('app', 'Monat'); ?></option>
                        </select>
                    </div>

                    <div class="trollbox-sort-box field-box-select" dropdown-toggle select-click bs-has-classes>
                        <label><?= Yii::t('app','Sortieren') ?>:</label>
                        <select ng-model="filter.sort" selectpicker>
                            <option value="dt"><?= Yii::t('app', 'Datum'); ?></option>
                            <option value="votes_up"><?= Yii::t('app', 'Beliebteste'); ?></option>
                        </select>
                    </div>

                </div>
            </div>

            <div ng-if="filter.type=='VIDEO_IDENTIFICATION'" class="video-identification-filters">
                <div class="trollbox-filter-date field-box-select" dropdown-toggle select-click bs-has-classes>
                    <label><?= Yii::t('app','Zeitraum wählen') ?>:</label>
                    <select ng-model="filter.period" selectpicker="{title:''}">
                        <option value=""><?= Yii::t('app', 'Alle'); ?></option>
                        <option value="TODAY"><?= Yii::t('app', 'Heute'); ?></option>
                        <option value="WEEK"><?= Yii::t('app', 'Woche'); ?></option>
                        <option value="MONTH"><?= Yii::t('app', 'Monat'); ?></option>
                    </select>
                </div>
                <div class="trollbox-sort-box field-box-select" dropdown-toggle select-click bs-has-classes>
                    <label><?= Yii::t('app','Sortieren') ?>:</label>
                    <select ng-model="filter.sort" selectpicker>
                        <option value="dt"><?= Yii::t('app', 'Datum'); ?></option>
                        <option value="votes_up"><?= Yii::t('app', 'Beliebteste'); ?></option>
                    </select>
                </div>
            </div>


            <div class="trollbox-reset-btn-box">
                <button ng-click="trollboxCtrl.resetFilter()" class="btn btn-submit"><?= Yii::t('app', 'Filter zurücksetzen') ?></button>
            </div>

        </div>
    </div>

    <div class="trollbox-box">
        <div ng-if="filter.type!='VIDEO_IDENTIFICATION'" class="trollbox-create-message-box">
            <div class="summary" ng-bind-html="dashboardForumText"></div>
            <div class="dashboard-forum-form trollbox-form">

                <div class="forum-new-message-box">
                    <div class="forum-new-message-image">
                        <div class="preview-upload-image" ng-if="trollbox.newMessage.image">
                            <img ng-src="{{trollbox.newMessage.image}}"/>
                            <button ng-click="trollboxCtrl.deleteTrollboxImage()" class="btn-del-image"></button>
                        </div>
                        <div class="box-input-file" ng-if="!trollbox.newMessage.image">
                            <div class="spinner" ng-if="uploader.isUploading"></div>
                            <input type="file" nv-file-select filters="imageVideoFilter,queueLimit" uploader="uploader" options="fileUploadOptions" />
                        </div>
                    </div>
                    <div class="forum-new-message-image-notification"><?=Yii::t('app','Bild / Video hochladen')?></div>
                    <div class="forum-new-message-smiles">
                        <div class="smiles" emoticons-tooltip emoticon-forum="true" emoticons-list="trollboxCtrl.emoticonsList" message-text="trollbox.newMessage.text">
                            <div class="emoticons-tooltip">
                                <span ng-repeat="(emoticon,text) in trollboxCtrl.emoticonsList" ng-bind="emoticon" class="emoticon"></span>
                            </div>
                        </div>
                    </div>
                    <div class="forum-new-message-send">
                        <div class="btn btn-submit" ng-click="trollboxCtrl.trollboxSendMessage()"><?=Yii::t('app','Absenden')?></div>
                    </div>
                </div>

                <textarea placeholder="<?=Yii::t('app','Text eingeben')?>" maxlength="2500" ng-model="trollbox.newMessage.text"></textarea>

                <ul class="errors-list" ng-if="trollbox.newMessage.$allErrors">
                    <li ng-repeat="error in trollbox.newMessage.$allErrors">{{::error}}</li>
                </ul>
            </div>
        </div>

        <div class="forum-update-btn-box">
            <button ng-disabled="set_timer > 0" ng-click="trollboxCtrl.updateTrollbox()" class="btn btn-submit" type="button"><?= Yii::t('app', 'Chats aktualisieren') ?></button>
        </div>

        <div ng-if="state.loading" class="loader-box trollbox-loader">
            <div class="spinner"></div>
        </div>

        <div class="trollbox-list" scroll-load="trollboxCtrl.loadMore" scroll-load-visible="0.7" scroll-load-has-more="log.hasMore" ng-if="log.items.length>0">
            <div ng-repeat="trollboxMessage in log.items" class="dashboard-forum-message trollbox-message-box clearfix">

                <div ng-if="trollboxMessage.type=='FORUM'" class="trollbox-message-normal-box">
                    <div ng-show="trollboxMessage.file" class="trollbox-message-image">
                        <!--<a ng-if="trollboxMessage.file.ext=='mp4'" href="{{::trollboxMessage.file.url}}" target="_blank" class="play-video">
                            <img ng-src="{{trollboxMessage.file.image_medium}}" />
                        </a>-->

                        <div ng-if="trollboxMessage.file.ext=='mp4'" class="video-box">
                            <video video poster="{{trollboxMessage.file.image_medium}}" playsinline webkit-playsinline loop muted preload="none">
                                <source ng-src="{{trollboxMessage.file.url}}" type="video/mp4">
                            </video>
                        </div>

                        <a ng-if="trollboxMessage.file.ext!='mp4'" href="{{::trollboxMessage.file.image_big}}" fancybox ><img ng-src="{{trollboxMessage.file.image_medium}}" /></a>
                    </div>

                    <div class="trollbox-message-text" ng-bind-html="trollboxMessage.text|emoticonFix:trollboxCtrl:true"></div>

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
                                <a href="" ng-click="trollboxCtrl.enterGroupChat(trollboxMessage.id)" class="btn btn-submit"><?= Yii::t('app','Kommentieren') ?></a>
                            </div>
                            <div ng-click="trollboxCtrl.enterGroupChat(trollboxMessage.id)" class="message-info-total-comments"><span>{{::trollboxMessage.messagesCount|default:0}}</span><?= Yii::t('app','Kommentare insgesamt ') ?></div>
                        </div>

                        <div class="trollbox-message-info-actions">
                            <div ng-if="status.is_moderator" class="message-actions-box">
                                <button ng-click="trollboxCtrl.trollboxRejectMessage(trollboxMessage)" set-if="trollboxMessage.status=='ACTIVE' || trollboxMessage.status=='AWAITING_ACTIVATION'" class="btn-reject-message">{{::trollboxMessage.status=='AWAITING_ACTIVATION' ? '<?=Yii::t('app', 'Ablehnen')?>':'<?=Yii::t('app', 'Blockieren')?>'}}</button>
                                <button ng-click="trollboxCtrl.trollboxAcceptMessage(trollboxMessage)" set-if="trollboxMessage.status=='REJECTED' || trollboxMessage.status=='AWAITING_ACTIVATION'" class="btn-accept-message">{{::trollboxMessage.status=='AWAITING_ACTIVATION' ? '<?=Yii::t('app', 'Freigeben')?>':'<?=Yii::t('app', 'Entblocken')?>'}}</button>
                                <button ng-click="trollboxCtrl.trollboxBlockUser(trollboxMessage)" ng-if="!trollboxMessage.user.is_blocked_in_trollbox" class="btn-block-user"><?=Yii::t('app', 'Für alle Foren sperren')?></button>
                                <button ng-click="trollboxCtrl.trollboxUnblockUser(trollboxMessage)" ng-if="trollboxMessage.user.is_blocked_in_trollbox" class="btn-accept-message"><?=Yii::t('app', 'Für alle Foren entsperren')?></button>
                                <button ng-click="trollboxCtrl.trollboxSetStickyTrollboxMessage(trollboxMessage)" set-if="!trollboxMessage.is_sticky" class="btn-accept-message"><?=Yii::t('app', 'Beitrag anpinnen')?></button>
                                <button ng-click="trollboxCtrl.trollboxUnsetStickyTrollboxMessage(trollboxMessage)" set-if="trollboxMessage.is_sticky" class="btn-accept-message"><?=Yii::t('app', 'Beitrag entpinnen')?></button>

                                <div set-if="trollboxMessage.status=='REJECTED' && trollboxMessage.status_changed_dt" class="message-reject">
                                    <div class="dt-message-reject">{{::trollboxMessage.status_changed_dt|date : 'dd.MM.yyyy - HH:mm'}} <?= Yii::t('app','Uhr') ?> {{::trollboxMessage.user.id==trollboxMessage.status_changed_user_id ? '<?=Yii::t('app','Nutzer')?>':'<?= Yii::t('app','Moderator') ?>'}}</div>
                                    <div class="user-message-reject">{{::trollboxMessage.statusChangedUser}}</div>
                                </div>
                                <div set-if="trollboxMessage.status=='ACTIVE' && trollboxMessage.status_changed_dt" class="message-accept">
                                    <div class="dt-message-accept">{{::trollboxMessage.status_changed_dt|date : 'dd.MM.yyyy - HH:mm'}} <?= Yii::t('app','Uhr') ?> {{::trollboxMessage.user.id==trollboxMessage.status_changed_user_id ? '<?= Yii::t('app','Nutzer') ?>':'<?= Yii::t('app','Moderator') ?>'}}</div>
                                    <div class="user-message-accept">{{::trollboxMessage.statusChangedUser}}</div>
                                </div>
                            </div>
                            <div class="message-info-votes">
                                <div class="negative" ng-click="trollboxCtrl.trollboxVoteDown(trollboxMessage.id)">{{::trollboxMessage.votes_down}}</div>
                                <div class="positive" ng-click="trollboxCtrl.trollboxVoteUp(trollboxMessage.id)">{{::trollboxMessage.votes_up}}</div>
                                <div class="votes-view" ng-click="(trollboxMessage.votes_down+trollboxMessage.votes_up)>0 ? trollboxCtrl.votesView(trollboxMessage.id):null" ng-class="{'no-votes':(trollboxMessage.votes_down+trollboxMessage.votes_up)===0}"></div>
                            </div>
                            <div ng-if="trollboxMessage.trollbox_category_id" class="trollbox-message-category">
                                {{trollboxMessage.trollbox_category}}
                            </div>
                        </div>
                    </div>
                </div>


                <div ng-if="trollboxMessage.type=='VIDEO_IDENTIFICATION'" class="trollbox-message-video-verification-box">
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
                                <a href="" ng-click="trollboxCtrl.enterGroupChat(trollboxMessage.id)" class="btn btn-submit"><?= Yii::t('app','Kommentieren') ?></a>
                            </div>
                            <div ng-click="trollboxCtrl.enterGroupChat(trollboxMessage.id)" class="message-info-total-comments"><span>{{::trollboxMessage.messagesCount|default:0}}</span><?= Yii::t('app','Kommentare insgesamt ') ?></div>
                        </div>

                        <div class="trollbox-message-info-actions trollbox-message-video-ident-actions-box">
                            <div class="video-ident-actions-wrap">
                                <div class="video-ident-actions-box" ng-if="!trollboxMessage.voted && status.id!=trollboxMessage.user.id">
                                    <div>
                                        <button ng-click="trollboxCtrl.trollboxVoteDown(trollboxMessage.id)" class="small-btn small-btn-red"><?= Yii::t('app', 'nicht echt') ?></button>
                                        <div ng-if="status.is_moderator && trollboxMessage.votes_down>0" ng-click="trollboxCtrl.votesViewVideo(trollboxMessage.id,'down')" class="votes-view">{{trollboxMessage.votes_down}}</div>
                                    </div>
                                    <div>
                                        <button ng-click="trollboxCtrl.trollboxVoteUp(trollboxMessage.id)" class="small-btn small-btn-green"><?= Yii::t('app', 'echt') ?></button>
                                        <div ng-if="status.is_moderator && trollboxMessage.votes_up>0" ng-click="trollboxCtrl.votesViewVideo(trollboxMessage.id,'up')" class="votes-view">{{trollboxMessage.votes_up}}</div>
                                    </div>
                                </div>
                                <div ng-if="trollboxMessage.count_votes>0" class="count-votes"><?= Yii::t('app', 'Stimmen') ?>: {{trollboxMessage.count_votes}}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div ng-if="trollboxMessage.messages.length>0" class="message-info-response">
                    <div class="message-info-response-item" ng-repeat="message in trollboxMessage.messages">
                        <div class="message-info-response-info" ng-click="trollboxCtrl.enterGroupChat(trollboxMessage.id)">
                            <div class="message-info-response-data clearfix">
                                <div class="message-info-response-user-info clearfix">
                                    <a ui-sref="userProfile({id: message.user.id})">
                                        <div class="offer-user-avatar"><img ng-src="{{::message.user.avatar}}" alt=""/></div>
                                    </a>
                                    <div class="message-info-response-user">{{::message.user|userName}} <div ng-click="updateCountry(message.user.id,[log.items]); $event.stopPropagation();" id="{{::message.user.flag}}" class="flag flag-32 flag-{{message.user.flag}}"></div></div>
                                </div>
                                <div class="message-info-response-dt">{{::message.dt|date:"dd MMMM yyyy | HH:mm"}}</div>
                            </div>

                            <div ng-if="message.content_type!='IMAGE' || message.content_type!='VIDEO'" class="message-info-response-message" ng-bind-html="message.text|emoticonFix:trollboxCtrl:true"></div>

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
			<div class="dashboard-forum-message clearfix">
				<div ng-repeat="advertising in advertisings.forumbottom" class="advertising-cnt" ng-if="advertising.advertising_type == 'banner'">
					<div id="advertising-{{::advertising.id}}" a-data-id="{{::advertising.id}}" a-data-user-bonus="{{::advertising.user_bonus}}" class="advertising_script" ng-click="dashboardCtrl.setAdvertising({{::advertising.id}}, {{::advertising.user_bonus}}, {{::advertising.popup_interval}})">
						<a href="{{::advertising.link}}" target="_blank" title="{{::advertising.advertising_display_name}}"><img src ="{{::advertising.banner}}" alt="{{::advertising.advertising_display_name}}"></a>
					</div>
				</div>
				<div class="advertising-cnt">
					<advertising class="forum-bottom-advertisings">
						<div class="advertising-forumbottom">
						</div>
					</advertising>
				</div>
			</div>
        </div>
        <div class="bottom-corner"></div>
    </div>
</div>