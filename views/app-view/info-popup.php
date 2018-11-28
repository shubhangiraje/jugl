<div class="close" ng-click="modalService.hideInfo()"></div>
<div class="content" ng-controller="InfoPopupCtrl as infoPopupCtrl">
    <div scroll-pane scroll-config="{contentWidth: '0', autoReinitialise: true, showArrows: false}" auto-height-popup id="info-popup-scroll" class="info-popup-box">
        <div class="info-popup-data">
            <div class="info-popup-title">{{infoPopupData.title}}</div>
            <div ng-bind-html="infoPopupData.description|trustAsHtml" class="info-popup-text"></div>
        </div>
        <div class="info-wiki">
            <h2><?= Yii::t('app','Jugl-Wiki') ?></h2>

            <div class="dashboard-forum-form trollbox-form">

                <div class="forum-new-message-box">
                    <div class="forum-new-message-image">
                        <div class="preview-upload-image" ng-if="infoComment.newComment.image">
                            <img ng-src="{{infoComment.newComment.image}}"/>
                            <button ng-click="infoPopupCtrl.deleteInfoCommentImage()" class="btn-del-image"></button>
                        </div>
                        <div class="box-input-file" ng-if="!infoComment.newComment.image">
                            <div class="spinner" ng-if="uploader.isUploading"></div>
                            <input type="file" nv-file-select filters="imageVideoFilter,queueLimit" uploader="uploader" options="fileUploadOptions" />
                        </div>
                    </div>
                    <div class="forum-new-message-image-notification"><?=Yii::t('app','Bild / Video hochladen')?></div>
                    <div class="forum-new-message-send">
                        <div class="btn btn-submit" ng-click="infoPopupCtrl.add()"><?=Yii::t('app','Absenden')?></div>
                    </div>
                </div>

                <textarea placeholder="<?=Yii::t('app','Text eingeben')?>" ng-model="infoComment.newComment.comment"></textarea>

                <ul class="errors-list" ng-if="infoComment.$allErrors">
                    <li ng-repeat="error in infoComment.$allErrors">{{::error}}</li>
                </ul>
            </div>
			<div class="box wiki-multiselect">
				<ul class="searches-filter-list">
				   <li class="searches-filter-item">
						<div class="field-box-select filter-select multiselect-box">
							<multiselect ng-model="infoPopupData.currentCountry" labels="labels"
								selection-limit="1"	options="infoPopupData.countryList" id-prop="id" display-prop="name" show-select-all="false" show-unselect-all="false" show-search="true" >
							</multiselect>
						</div>
					</li>
				</ul>
            </div>
			
			<div ng-if="infoComments.items.length>0" class="info-comments-sort-box">
                <div ng-click="infoPopupCtrl.sort('dt')" ng-class="{'active': state.sort=='dt'}"><?= Yii::t('app','New') ?></div>
                <div ng-click="infoPopupCtrl.sort('votes_up')" ng-class="{'active': state.sort=='votes_up'}"><?= Yii::t('app','Best') ?></div>
            </div>

            <div class="trollbox-list" scroll-load="infoPopupCtrl.loadMore" scroll-load-visible="0.7" scroll-load-has-more="infoComments.hasMore" ng-if="infoComments.items.length>0">
            <div ng-repeat="itemComment in infoComments.items" class="dashboard-forum-message clearfix">
              
                <a ng-show="itemComment.file && itemComment.file.ext!='mp4'" href="{{::itemComment.file.image_big}}" fancybox class="dashboard-forum-image"><img ng-src="{{itemComment.file.image_medium}}" /></a>
                <a ng-show="itemComment.file && itemComment.file.ext=='mp4'" href="{{::itemComment.file.url}}" target="_blank" class="dashboard-forum-image play-video"><img ng-src="{{itemComment.file.image_medium}}" /></a>

                <div class="message-info">
                        <div class="message-info-body" ng-bind-html="itemComment.comment|linky:'_blank'"></div>
                        <div class="message-info-bottom clearfix">
                            <div class="offer-user-box">
                                <a href="" ng-click="infoPopupCtrl.goProfile(itemComment.user.id)">
                                    <div class="offer-user-avatar"><img ng-src="{{::itemComment.user.avatar}}" alt=""/></div>
                                </a>
                                <div class="offer-user-name">{{::itemComment.user|userName}} <div ng-click="updateCountry(itemComment.user.id,infoComments.items)" id="{{itemComment.user.flag}}" class="flag flag-32 flag-{{itemComment.user.flag}}"></div></div>
                                <div class="offer-user-rating">
                                    <div class="star-rating">
                                        <span once-style="{width:(+itemComment.user.rating)+'%'}"></span>
                                    </div>
                                    <div class="user-feedback-count">({{::itemComment.user.feedback_count}})</div>
                                    <div ng-if="itemComment.user.packet=='VIP'" class="user-packet">&nbsp;</div>
                                    <div ng-if="itemComment.user.packet=='VIP_PLUS'" class="user-packet-vip-plus">&nbsp;</div>
                                </div>
                            </div>
                            <div class="message-info-votes">
                                <div class="negative" ng-click="infoPopupCtrl.infoCommentVoteDown(itemComment.id)">{{::itemComment.votes_down}}</div>
                                <div class="positive" ng-click="infoPopupCtrl.infoCommentVoteUp(itemComment.id)">{{::itemComment.votes_up}}</div>
                                <div class="votes-view" ng-click="(itemComment.votes_down+itemComment.votes_up)>0 ? infoPopupCtrl.votesView(itemComment.id):null" ng-class="{'no-votes':(itemComment.votes_down+itemComment.votes_up)===0}">&nbsp;</div>
                            </div>
                        </div>

                        <div ng-if="status.is_moderator" class="message-info-action-box">
                            <div set-if="itemComment.status=='REJECTED' && itemComment.status_changed_dt" class="message-reject">
                                <div class="dt-message-reject">{{::itemComment.status_changed_dt|date : 'dd.MM.yyyy - HH:mm'}} <?= Yii::t('app','Uhr') ?> {{::itemComment.user.id==itemComment.status_changed_user_id ? '<?=Yii::t('app','Nutzer')?>':'<?= Yii::t('app','Moderator') ?>'}}</div>
                                <div class="user-message-reject">{{::itemComment.statusChangedUser}}</div>
                            </div>
                            <div set-if="itemComment.status=='ACTIVE' && itemComment.status_changed_dt" class="message-accept">
                                <div class="dt-message-accept">{{::itemComment.status_changed_dt|date : 'dd.MM.yyyy - HH:mm'}} <?= Yii::t('app','Uhr') ?> {{::itemComment.user.id==itemComment.status_changed_user_id ? '<?= Yii::t('app','Nutzer') ?>':'<?= Yii::t('app','Moderator') ?>'}}</div>
                                <div class="user-message-accept">{{::itemComment.statusChangedUser}}</div>
                            </div>

                            <button ng-if="itemComment.status=='ACTIVE'" ng-click="infoPopupCtrl.rejectComment(itemComment)" class="btn-reject-message"><?= Yii::t('app', 'Blockieren') ?></button>
                            <button ng-if="itemComment.status=='REJECTED'" ng-click="infoPopupCtrl.acceptComment(itemComment)" class="btn-accept-message"><?= Yii::t('app', 'Entblocken') ?></button>
                        </div>

                        <div class="forum-message-dt">
                            <span>{{itemComment.dt|date : 'dd.MM.yyyy HH:mm'}}</span>
                        </div>
                    </div>

                </div>
            </div>
			<div class="box-text text-center clearfix" ng-show="infoComments.items.length==0"><p><?= Yii::t('app','Für die ausgewählten Länder existieren aktuell keine Wiki Einträge.') ?></p></div>			
			
        </div>


    </div>
</div>