<div class="interests">
    <div class="container">
        <div class="welcome-text">
            <h2><?=Yii::t('app','Meine Interessen')?></h2>
            <?=Yii::t('app','Hier kannst Du Deine Interessen verwalten. Das Hinzuf&uuml;gen neuer Interessen ist ganz einfach: allgemeine Interessenkategorie w&auml;hlen -> Unterkategorie aussuchen -> das ganze durch zus&auml;tzliche Themenfilter pr&auml;zisieren. Durch Klicken auf ein Interesse siehst Du dann die entsprechende Interessenhierarchie.')?>
        </div>

        <div class="interests-content">
            <div class="interests-box">
                <ul class="interests-step-title">
                    <li>1.&nbsp;{{level_interests.level1_title}}</li>
                    <li>2.&nbsp;{{level_interests.level2_title}}</li>
                    <li>
                        <?=Yii::t('app', '3. W&auml;hle Themenfilter');?>
                        <div class="interests-btn-box">
                            <div ng-if="type=='addInterest'" ng-click="interestsAddStep3Ctrl.saveInterestLevel2()" class="btn btn-save"><?=Yii::t('app','Fertig');?></div>
                            <div ng-if="type=='addSearch' || type=='addOffer'" ng-click="interestsAddStep3Ctrl.saveSearchAndOffer()" class="btn btn-save"><?=Yii::t('app','Fertig');?></div>
                            <a href="" onclick="window.history.back(); return false;" class="btn btn-submit"><?=Yii::t('app','Zur&uuml;ck');?></a>
                        </div>
                    </li>
                </ul>

                <div class="interests-list-checkbox">
                    <ul>
                        <li ng-repeat="interest in interests|orderBy:'interest_title'" class="interests-box-checkbox">
                            <input type="checkbox" value="{{interest.interest_id}}" i-check ng-model="level3Interests[interest.interest_id]"><label>{{interest.interest_title}}</label>
                        </li>
                    </ul>
                </div>

                <div ng-if="type=='addInterest'" class="box-interests-submit">
                    <button ng-if="type=='addInterest'" class="btn btn-submit" ng-click="interestsAddStep3Ctrl.saveAndCreateSearchRequest()"><?=Yii::t('app','Suchanzeige aufgeben');?></button>
<!--                    <button ng-if="type=='addSearch'" class="btn btn-submit" ng-click="interestsAddStep3Ctrl.create()">--><?//=Yii::t('app','Suchanzeige erstellen');?><!--</button>-->
<!--                    <button ng-if="type=='addOffer'" class="btn btn-submit" ng-click="interestsAddStep3Ctrl.create()">--><?//=Yii::t('app','Allgemeine Angebote erstellen');?><!--</button>-->
                </div>

            </div>

            <div class="bottom-corner"></div>
        </div>

    </div>
</div>

