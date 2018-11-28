<?php

use \app\models\SearchRequest;

?>

<div class="searches-add searches">
    <div class="container">
        <div ng-click="showInfoPopup('view-searches-add')" ng-class="{'blink':isOneShowInfoPopup('view-searches-add')}" class="info-popup-btn"></div>

        <div class="welcome-text">
            <h2><?= Yii::t('app', 'Die menschliche Suchmaschine bei Jugl.net<br />Suche oder Auftrag erstellen'); ?></h2>
            <p><?= Yii::t('app','Dein Vorteil gegenüber digitalen Suchmaschinen? Maschinen folgen Algorythmen, Menschen ihrem Verstand!') ?></p>
            <h2><?= Yii::t('app', 'Was können wir für Dich tun?'); ?></h2>
			<p><?= Yii::t('app','Was auch immer Du möchtest, ob Haus, Auto, Urlaub oder einfach Kleidung u.v.m., Dein Wunsch ist uns Befehl.') ?></p>
			<p><?= Yii::t('app','Der Sinn von Suchen oder Aufträgen besteht darin, dass sich die Jugler direkt über das Portal Jugl.net durch eine Recherche oder Dienstleistung Jugl-Punkte verdienen können. Recherche werden in Jugl-Punkten vergütet, zum Beispiel für das Vermitteln einer Baufirma, die Deine Wohnung renovieren soll. Die Zahlung der Dienstleistung wird aber dann direkt an die Baufirma geleistet. Ist der Vermittler auch der Dienstleister, stehen ihm sowohl der Vermittlungsbonus als auch die Entlohnung der Dienstleistung zu.') ?></p>
			<p><?= Yii::t('app','Der Vermittlungsbonus sollte je nach Schwierigkeitsgrad im Verhältnis stehen. Der Vermittlungsbonus an Deinen Helfer wird erst nach der Angebotsannahme gebucht.') ?></p>
	   </div>

        <div class="searches-title-box"><h2><?=Yii::t('app','Kategorie')?></h2></div>
        <div class="searches-box category clearfix">
			<div class="searches-users-related-interests">
				<?= Yii::t('app', 'Nutzer mit ähnlichen Interessen'); ?>: <span class="countRequests">{{countRequestInterests}}</span>
				<br>
				<?= Yii::t('app', 'Hier wird dir stets die aktuelle Anzahl der Nutzer mit gleichen Interessen angezeigt'); ?>
				</div>
            <div class="searches-category clearfix fields-full">
                <label><?=Yii::t('app', 'Kategorie');?><span>*</span>:</label>
				
                <ul class="list-category">
                    <li ng-if="searchRequest.searchRequestInterests[0].level1Interest.id">{{searchRequest.searchRequestInterests[0].level1Interest.title}}</li>
                    <li ng-if="searchRequest.searchRequestInterests[0].level2Interest.id">{{searchRequest.searchRequestInterests[0].level2Interest.title}}</li>
                    <li ng-if="searchRequest.searchRequestInterests[0].level3Interest.id"><span ng-repeat="interest in searchRequest.searchRequestInterests">{{interest.level3Interest.title}}{{!$last ? ', ':''}}</span></li>
                </ul>
			<div class="clearfix"></div>
            
			<p><?= Yii::t('app', 'Beispielkategorie - Du kannst aus diversen Kategorien die passende für Dich wählen.'); ?></p>
            <a ng-click="searchRequestAddCtrl.addInterests()" class="btn btn-submit add-category-btn" href=""><?=Yii::t('app', 'Kategorie w&auml;hlen');?></a>
			</div>
        </div>

        <div class="searches-title-box"><h2><?=Yii::t('app','Auftragsdetails')?></h2></div>
        <div class="searches-box category clearfix">
            <div class="searches-category-fields">
                <div class="clearfix">

                    <div class="field-box clearfix fields-full field-scheduled">
                        <div class="fieldin-item">
                            <p class="clearfix"><?=Yii::t('app', 'Datum der Veröffentlichung');?>:</p>
                        </div>
                        <div class="fieldin-item">
                            <div class="field-box-entry" bs-has-classes>
                                <input type="checkbox" ng-true-value="1" ng-false-value="0" i-check ng-model="searchRequest.is_active_immediately" server-error="searchRequest.$errors.is_active_immediately">
                                <label><?= Yii::t('app','Ab sofort') ?></label>
                            </div>
                        </div>

                        <div ng-if="!searchRequest.is_active_immediately" class="fields-scheduled-dt-box">
                            <label><?= Yii::t('app', 'Ab dem') ?>:</label>

                            <div class="scheduled-dt-box">
                                <div class="scheduled-date-box">
                                    <div class="field-box-select" dropdown-toggle select-click bs-has-classes>
                                        <select ng-model="searchRequest.scheduled_dt_parts.day" selectpicker="{title:''}" ng-options="item.key as item.val for item in birthDayList">
                                            <option value=""><?= Yii::t('app', 'Day'); ?></option>
                                        </select>
                                    </div>
                                    <div class="field-box-select" dropdown-toggle select-click bs-has-classes>
                                        <select ng-model="searchRequest.scheduled_dt_parts.month" selectpicker="{title:''}" ng-options="item.key as item.val for item in birthMonthList">
                                            <option value=""><?= Yii::t('app', 'Monat'); ?></option>
                                        </select>
                                    </div>
                                    <div class="field-box-select" dropdown-toggle select-click bs-has-classes>
                                        <select ng-model="searchRequest.scheduled_dt_parts.year" selectpicker="{title:''}" ng-options="item.key as item.val for item in birthYearList">
                                            <option value=""><?= Yii::t('app', 'Jahr'); ?></option>
                                        </select>
                                    </div>
                                </div>

                                <div class="scheduled-time-box">
                                    <div class="field-box-select field-box-select-hours" dropdown-toggle select-click bs-has-classes>
                                        <select ng-model="searchRequest.scheduled_dt_parts.hours" selectpicker="{title:''}" ng-options="item.key as item.val for item in hoursList">
                                            <option value=""><?= Yii::t('app', 'Stunde'); ?></option>
                                        </select>
                                    </div>
                                    <div class="field-box-select" dropdown-toggle select-click bs-has-classes>
                                        <select ng-model="searchRequest.scheduled_dt_parts.minutes" selectpicker="{title:''}" ng-options="item.key as item.val for item in minutesList">
                                            <option value=""><?= Yii::t('app', 'Minute'); ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="searches-fields-box fields-full">
					    <p><?=Yii::t('app','Verleihe Deinem Auftrag einen aussagekräftigen Titel. Er ist das Erste, was Deine Helfer lesen.')?></p>
                        <div class="field-box clearfix">
                            <div class="field-box-entry">
                                <div class="field-box-input" bs-has-classes>
                                    <input type="text" ng-model="searchRequest.title" server-error="searchRequest.$errors.title" placeholder="Auftragstitel eingeben*" />
                                </div>
                            </div>
                        </div>
                    </div>

					<div class="searches-fields-box fields-full">
						<p><?=Yii::t('app','Gib hier die Beschreibung Deiner Suche ein. Je detaillierter diese ist, umso genau wissen Deine Helfer wie und womit sie Dir zur Seite stehen können.')?></p>
						<div class="field-box clearfix">
							<div class="field-box-entry">
								<div class="field-box-textarea" bs-has-classes>
									<textarea ng-model="searchRequest.description" maxlength="4000" server-error="searchRequest.$errors.description" placeholder="Beschreibung eingeben*"></textarea>
								</div>
							</div>
						</div>
					</div>
					
					<div class="searches-fields-box fields-full">
						<p><?=Yii::t('app','Du hast eine genaue Vorstellung was Du suchst? Ein Bild sagt mehr als tausend Worte! Zudem machen Bilder Deinen Suchauftrag attraktiver.')?></p>
						<div class="field-box clearfix">
							
							<div class="fields-box-image offer-pictures-add-box">
								<div ng-repeat="file in searchRequest.files" class="preview-upload-image">
									<img ng-src="{{file.thumbs.imageBig}}" />
									<button ng-click="searchRequestAddCtrl.deleteFile(file.id)" class="btn-del-image"></button>
								</div>
								<div class="box-input-file" ng-if="uploader.queue.length != uploader.queueLimit">
									<div class="spinner" ng-if="uploader.isUploading"></div>
									<span class="icon-input-file" ng-if="!uploader.isUploading"></span>
									<input type="file" nv-file-select filters="imageFilter,queueLimit" uploader="uploader" options="fileUploadOptions" multiple />
								</div>
							</div>
						</div>
					</div>

					<div class="searches-fields-box fields-price fields-full">
						<p><?=Yii::t('app','Gib hier Deine preislichen Vorstellungen für das ein, was Du suchst, z.B. Was bist Du bereit für die Renovierung Deiner Wohnung zu zahlen? Dies dient Deinem Helfer als Orientierungshilfe. Suchst Du eine Dienstleistung, gibst du hier den Wert 0 ein.')?></p>
						<div class="fields-col2">
							<div class="field-box-label price">
								<label><?=Yii::t('app','Preisvorstellung:');?></label>
								<div></div>
							</div>
							<div class="field-box-entry">

								<div class="price-box">
									<div class="price-box-input" bs-has-classes>
										<input type="text" price-validator ng-model="searchRequest.price_from" server-error="searchRequest.$errors.price_from" placeholder="<?=Yii::t('app','von');?>" />
										<span>&euro;</span>
									</div>
								</div>
								<div class="price-box">
									<div class="price-box-input" bs-has-classes>
										<input type="text" price-validator ng-model="searchRequest.price_to" server-error="searchRequest.$errors.price_to" placeholder="<?=Yii::t('app','bis');?>" />
										<span>&euro;</span>
									</div>
								</div>
							</div>
						</div>
					</div>
					
					<div class="searches-fields-box fields-full">
						<p><?=Yii::t('app','Lege fest, wie viel Vermittlungsbonus Du Deinen Helfern für die Vermittlung eines passenden Angebots zahlst. Bitte beachte, dass Du bei der Annahme eines Angebots nicht den darin enthaltenen Artikel kaufst, sondern lediglich für die Recherche zahlst.<br />Der Vermittlungsbonus wird nur fällig, wenn Du das Angebot annimmst.')?></p>
                        <div class="field-box clearfix">
                            <div class="field-box-entry">
                                <div class="jugl-box-input" bs-has-classes>
                                    <input price-validator type="text" ng-model="searchRequest.bonus" server-error="searchRequest.$errors.bonus" placeholder="Vermittlungsbonus festlegen (<?= Yii::t('app', 'mind.') ?> {{searchRequest.view_bonus_interest}} Jugl)" />
									<span class="jugl-icon-light"></span>
                                </div>
                            </div>
                        </div>
                    </div>
				
				</div>
            </div>

        </div>

        <div class="searches-title-box"><h2><?=Yii::t('app','Ort')?></h2></div>
        <div class="searches-box">
			<p><?=Yii::t('app','Gib an, wo Du etwas suchst, z.B. Wo befindet sich die Wohnung, die renoviert werden soll?')?></p>
            <div class="places-fields-box clearfix">
			
                <div class="places-field-left-box clearfix">
                    <div class="field-box-label">
                        <label><?=SearchRequest::getEncodedAttributeLabel('country_id')?><span>*</span>:</label>
                    </div>
                    <div class="field-box-entry">
                        <div class="field-box-select" dropdown-toggle select-click bs-has-classes>
                            <select ng-model="searchRequest.country_id" selectpicker ng-options="item.id as item.country for item in countries" server-error="searchRequest.$errors.country_id">
                            </select>
                        </div>
                    </div>
                </div>
                <div class="places-field-right-box clearfix">
                    <div class="field-box-label">
                        <label><?=SearchRequest::getEncodedAttributeLabel('city')?>:</label>
                    </div>
                    <div class="field-box-entry">
                        <div class="field-box-input" bs-has-classes>
                            <input type="text" ng-model="searchRequest.city" server-error="searchRequest.$errors.city" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="places-fields-box clearfix">
                <div class="places-field-left-box clearfix">
                    <div class="field-box-label">
                        <label><?=SearchRequest::getEncodedAttributeLabel('zip')?><span>*</span>:</label>
                    </div>
                    <div class="field-box-entry">
                        <div class="field-box-input" bs-has-classes>
                            <input type="text" ng-model="searchRequest.zip" server-error="searchRequest.$errors.zip" />
                        </div>
                    </div>
                </div>
                <div class="places-field-right-box clearfix">
                    <div class="field-box-label">
                        <label><?=SearchRequest::getEncodedAttributeLabel('address')?>:</label>
                    </div>
                    <div class="field-box-entry">
                        <div class="field-box-input" bs-has-classes>
                            <input type="text" ng-model="searchRequest.address" server-error="searchRequest.$errors.address" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

		
		
		<div class="searches-title-box">
		    <h2><?=Yii::t('app','Dauer der Veröffentlichung')?></h2>
        </div>
        <div class="searches-box">
            <div class="duration clearfix">
                <p><?= Yii::t('app', 'Lege fest wie lange Dein Suchauftrag bei Jugl.net zu sehen sein soll. Bei fehlender Eingabe ist Dein Auftrag automatisch 6 Monate aktiv. Es sei denn Du deaktivierst sie vorher.'); ?></p>
                <div class="field-box">
                    <div class="field-box-label">
                        <label><?=Yii::t('app','Auftrag aktiv bis');?>:</label>
                    </div>
                    <div class="field-box-select" dropdown-toggle select-click bs-has-classes>
                        <select ng-model="searchRequest.active_till_parts.day" selectpicker="{title:''}" ng-options="item.key as item.val for item in birthDayList">
                            <option value=""><?= Yii::t('app', 'Day'); ?></option>
                        </select>
                    </div>
                    <div class="field-box-select" dropdown-toggle select-click bs-has-classes>
                        <select ng-model="searchRequest.active_till_parts.month" selectpicker="{title:''}" ng-options="item.key as item.val for item in birthMonthList">
                            <option value=""><?= Yii::t('app', 'Monat'); ?></option>
                        </select>
                    </div>
                    <div class="field-box-select" dropdown-toggle select-click bs-has-classes>
                        <select ng-model="searchRequest.active_till_parts.year" selectpicker="{title:''}" ng-options="item.key as item.val for item in birthYearList">
                            <option value=""><?= Yii::t('app', 'Jahr'); ?></option>
                        </select>
                    </div>
                </div>

            </div>
        </div>

        <ul class="errors-list" ng-if="searchRequest.$allErrors">
            <li ng-repeat="error in searchRequest.$allErrors" ng-bind="error"></li>
        </ul>

        <div class="searches-submit-box">
            <button class="btn btn-submit" ng-disabled="searchRequest.saving" ng-click="searchRequestAddCtrl.save()"><?=Yii::t('app','veröffentlichen');?></button>
			<br>
			<div class="welcome-text">
			
				<p>
				<?php/*=Yii::t('app','Die Vorschau ermöglicht es Dir, Deine Anzeige vor der Veröffentlichung noch einmal genau zu überprüfen und ggf. Korrekturen vorzunehmen.');*/?>
				</p>
			</div>
		
	   </div>


    </div>
</div>
