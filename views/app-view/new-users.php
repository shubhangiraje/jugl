<div class="new-users container">
    
	<div class="welcome-text">
     <h2 class="trollbox-heading"><?=Yii::t('app','Neue Mitglieder heute')?></h2>
		 <div class="box">
			 <h2>
			 <span class="disNone"><?=Yii::t('app','Neue Mitglieder heute')?></span>
				<div class="searches-filter-list">
					<div class="field-box-select filter-select">
						<multiselect ng-model="newUsersCountry" labels="labels"
						options="countryArrayNewUser" id-prop="id" display-prop="name" show-select-all="true" show-unselect-all="true" show-search="true" >
						</multiselect>
					</div>
				</div>
			</h2>
		</div>
    </div>

    <div class="account-box">
        <div class="account-friends-list clearfix" ng-if="users.length==0">
            <div class="result-empty-text"><?=Yii::t('app','Keine Benutzer gefunden')?></div>
        </div>
        <div class="account-friends-list clearfix" scroll-load="newUsersCtrl.loadMore" scroll-load-visible="0.7" scroll-load-has-more="users.hasMore" ng-if="users.length>0">
            <div class="account-friends-element" ng-repeat="user in users">
                <?php include('user-box.php'); ?>
            </div>
        </div>
        <div class="bottom-corner"></div>
    </div>

</div>