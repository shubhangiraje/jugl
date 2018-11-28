<?php

use app\models\InviteByEmailForm;
use app\models\InviteBySMSForm;
?>

<div cloudsponge-insert="{domain_key: 'PA8ZZLXYYTJ55D6Y62VW', textarea_id: 'friends-invite-emails'}" class="friends-invite-blocks clearfix">

    <?php /* ?>
    <div class="friends-invite-block w25">
        <div class="friends-invite-block-content cloudsponge-box">
            <div class="title">
                <?=Yii::t('app','Möglichkeit')?> 1:
                <h3><?=Yii::t('app','Adressbuch einladen')?></h3>
            </div>
            <div class="friends-invite-cloudsponge clearfix">
                <div class="cloudsponge-servive-block">
                    <div class="cloudsponge-servive-box">
                        <div class="cloudsponge-servive gmail" cloudsponge-call="gmail">
                            <?=Yii::t('app','Gmail')?>
                        </div>
                    </div>
                    <div class="cloudsponge-servive-box">
                        <div class="cloudsponge-servive yahoo" cloudsponge-call="yahoo">
                            <?=Yii::t('app','Yahoo')?>
                        </div>
                    </div>
                </div>
                <div class="cloudsponge-servive-block">
                    <div class="cloudsponge-servive-box">
                        <div class="cloudsponge-servive outlook" cloudsponge-call="outlook">
                            <?=Yii::t('app','Outlook')?>
                        </div>
                    </div>
                    <div class="cloudsponge-servive-box">
                        <div class="cloudsponge-servive windowslive" cloudsponge-call="windowslive">
                            <?=Yii::t('app','Hotmail')?>
                        </div>
                    </div>
                </div>
                <div class="cloudsponge-servive-box">
                    <div class="cloudsponge-servive aol" cloudsponge-call="aol">
                        <?=Yii::t('app','AOL')?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php */ ?>

    <div class="friends-invite-block w100">
        <form novalidate class="friends-invite-block-content">
            <div class="title">
                <?=Yii::t('app','Möglichkeit')?> 1:
                <h3><?=Yii::t('app','Per E-Mail einladen')?></h3>
            </div>
            <div class="friends-invite-block-text">
                <?=Yii::t('app','<b>Bitte E-Mail-Adressen hier eingeben</b><br/>Kommagetrennt,<br/>z.B. mustermann@domain1.com,<br/>musterfrau@domain2.com usw.')?>
            </div>
            <div bs-has-classes>
                <textarea id="friends-invite-emails" ng-model="inviteByEmail.emails" placeholder="<?=InviteByEmailForm::getEncodedAttributeLabel('emails')?>" ng-bind="inviteEmailsList"></textarea>
            </div>
            <div class="textarea-label"><?=Yii::t('app','Nachricht')?></div>
            <div bs-has-classes>
                <textarea class="big" ng-model="inviteByEmail.text" placeholder="<?=InviteByEmailForm::getEncodedAttributeLabel('text')?>"></textarea>
            </div>
            <ul class="friends-invite-result" ng-if="inviteByEmail.result">
                <li ng-repeat="(address,result) in inviteByEmail.result" ng-class="{error: result!==true}">
                    <div ng-if="result===true">{{address}}: <span><?=Yii::t('app','Gesendet')?></span></div>
                    <div ng-if="result!==true">{{address}}: <span>{{result}}</span></div>
                </li>
            </ul>
            <ul class="friends-invite-errors" ng-if="inviteByEmail.$allErrors">
                <li ng-repeat="error in inviteByEmail.$allErrors" ng-bind="error"></li>
            </ul>
            <button ng-disabled="inviteByEmail.saving" ng-click="friendsInvitationInviteCtrl.inviteByEmail()"><?=Yii::t('app','Einladung verschicken')?></button>
        </form>
    </div>
    <?php /* ?>
    <div class="friends-invite-block w50">
        <form novalidate class="friends-invite-block-content">
            <div class="title">
                <?=Yii::t('app','Möglichkeit')?> 2:
                <h3><?=Yii::t('app','Per SMS einladen')?></h3>
            </div>
            <div class="friends-invite-block-text">
                <?=Yii::t('app','<b>Bitte Handynummern hier eingeben</b><br/>Kommagetrennt, z.B.<br/>+4917633442255,<br/>+491723456782 usw.')?>
            </div>
            <div bs-has-classes>
                <textarea ng-model="inviteBySMS.phones" placeholder="<?=InviteBySMSForm::getEncodedAttributeLabel('phones')?>" ></textarea>
            </div>
            <div class="textarea-label"><?=Yii::t('app','Nachricht')?></div>
            <div bs-has-classes>
                <textarea class="big" ng-model="inviteBySMS.text" maxlength="300" placeholder="<?=InviteBySMSForm::getEncodedAttributeLabel('text')?>"></textarea>
            </div>
            <ul class="friends-invite-result" ng-if="inviteBySMS.result">
                <li ng-repeat="(address,result) in inviteBySMS.result" ng-class="{error: result!==true}">
                    <div ng-if="result===true">{{address}}: <?=Yii::t('app','Sent')?></div>
                    <div ng-if="result!==true">{{address}}: <span>{{result}}</span></div>
                </li>
            </ul>
            <ul class="friends-invite-errors" ng-if="inviteBySMS.$allErrors">
                <li ng-repeat="error in inviteBySMS.$allErrors" ng-bind="error"></li>
            </ul>
            <button ng-disabled="inviteBySMS.saving" ng-click="friendsInvitationInviteCtrl.inviteBySMS()"><?=Yii::t('app','Einladung verschicken')?></button>
        </form>
    </div>
    <?php */ ?>
    <div class="friends-invite-block w625">
        <div class="friends-invite-block-content">
            <div class="title">
                <?=Yii::t('app','Möglichkeit')?> 3:
                <h3><?=Yii::t('app','Persönlicher Einladungslink')?></h3>
            </div>
            <div class="friends-invite-block-text">
                <?=Yii::t('app','Der folgende Link ist Dein persönlicher Einladungslink für jugl.net. Nutze diesen Link in e-mails, Foren, auf Facebook, Twitter etc. und Du wirst schneller als Du denkst ein beträchtliches J Guthaben besitzen.<br/><br/>Teile Deinen persönlichen Einladungslink!')?>
            </div>
            <div zero-clipboard="refLink" class="ref-link">
                <input type="text" class="referal-link" ng-value="refLink" />
                <div is-flash class="referal-link-copy-container"><span class="referal-link-copy"><?=Yii::t('app','Kopieren')?></span></div>
            </div>
        </div>
    </div>
    <div class="friends-invite-block w375">
        <div class="friends-invite-block-content">
            <div class="title">
                <?=Yii::t('app','Möglichkeit')?> 4:
                <h3><?=Yii::t('app','Einladungslink teilen')?></h3>
            </div>
            <div class="friends-invite-socials">
                <a href="https://www.facebook.com/sharer/sharer.php?u={{refLink|encodeURI}}" class="facebook" target="_blank">&#xe60b;</a>
                <a href="https://twitter.com/share?url={{refLink|encodeURI}}" class="twitter" target="_blank">&#xe61a;</a>
                <a href="https://plus.google.com/share?url={{refLink|encodeURI}}" class="googleplus" target="_blank">&#xe60d;</a>
            </div>
        </div>
    </div>
</div>


