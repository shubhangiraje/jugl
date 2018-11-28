<div class="videos-details">
    <div class="container">
		<div class="videos-content">
			<div class="videos-column cnt-3">
				<h2 class="ng-binding">{{video.name}}</h2>
				<ul class="found-category">
				<li class="ng-binding"><?= Yii::t('app', 'Videos') ?></li>
					<li class="ng-binding">{{video.cat_name}}</li>
				</ul>
				<div class="videos-info mobile">
					<div class="videos-info-item">
						<p class="info-title"><?= Yii::t('app', 'Werbebonus') ?></p>
						<p class="info-content">{{video.bonus|priceFormat}} <jugl-currency></jugl-currency></p>
					</div>
					<div class="videos-info-item">
						<p class="info-title"><?= Yii::t('app', 'Videos heute') ?></p>
						<p class="info-content">{{video_state.video_total_view}}</p>
					</div>
					<div class="videos-info-item">
						<p class="info-title"><?= Yii::t('app', 'Heute Gesamt') ?></p>
						<p class="info-content">{{video_state.video_total_bonus}} <jugl-currency></jugl-currency></p>
					</div>
					
				</div>
				
				<div id="videosplayer" class="videos-player">
				</div>
				<p read-more content="{{video.description}}" class="videos-description">
				</p>

				<div class="videos-tags">
					<p ng-if="videoTags" ng-repeat="videoTags in video.video_tags">
						<a href="">#{{videoTags}}</a>
					</p>
				</div>
				
			</div>
			<div class="videos-column cnt-1">
				<div class="videos-info">
					<div class="videos-info-item">
						<p class="info-title"><?= Yii::t('app', 'Werbebonus') ?></p>
						<p class="info-content">{{video.bonus|priceFormat}} <jugl-currency></jugl-currency></p>
					</div>
					<div class="videos-info-item">
						<p class="info-title"><?= Yii::t('app', 'Videos heute') ?></p>
						<p class="info-content">{{video_state.video_total_view}}</p>
					</div>
					<div class="videos-info-item">
						<p class="info-title"><?= Yii::t('app', 'Heute Gesamt') ?></p>
						<p class="info-content">{{video_state.video_total_bonus}} <jugl-currency></jugl-currency></p>
					</div>
					
				</div>
				<div class="videos-playlist">
					<h3><?= Yii::t('app', 'Nächstes Video:') ?></h3>
					<div class="videos-playlist-container">
						<div ng-repeat="itemPlaylistVideos in video_list" class="videos-playlist-items">
							<a ui-sref="videos.details({id: itemPlaylistVideos.video_id})" class="dashboard-videos-image">
								<img ng-src="{{itemPlaylistVideos.image}}" />
							</a>
							<div class="videos-playlist-content">
								<h4>{{itemPlaylistVideos.name}}</h4>
								<p class="videos-categorie">{{itemPlaylistVideos.cat_name}}</p>
								<p class="videos-tag" ng-if="videoTags" ng-repeat="videoTags in itemPlaylistVideos.video_tags">
									<a href="#">#{{videoTags}}</a>
								</p>
							</div>
							<div class="clearfix"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
    </div>
</div>