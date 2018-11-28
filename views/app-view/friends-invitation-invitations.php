<div class="friends-invitations-top clearfix">
    <div class="column invitations-top-text">
        <?=Yii::t('app','Hier hast Du immer den perfekten Überblick über alle Deine Einladungen. Du kannst sehen, wer Deine Einladung akzeptiert hat, wer sich bereits beim jugl.net angemeldet hat und wem Du eventuell noch einmal eine Einladung schicken solltest. <br/><br/>Du erhälst <b>{{settings.directEarn|priceFormat}}<jugl-currency></jugl-currency></b> Guthaben, wenn eine neue Registrierung getätigt wurde.')?>
    </div>

    <div class="column">
        <div class="invitations-top-box">
            <div class="invitations-stats-wrapper">
                <div class="invitations-stats-count sended clearfix">
                    <div class="title"><?=Yii::t('app','Einladungen versendet:')?></div>
                    <div class="amount">{{stats.sent}}</div>
                </div>
                <div class="invitations-stats-count clicked clearfix">
                    <div class="title"><?=Yii::t('app','Link geklickt:')?></div>
                    <div class="amount">{{stats.clicked}}</div>
                </div>
                <div class="invitations-stats-count registered clearfix">
                    <div class="title"><?=Yii::t('app','Registrierungen:')?></div>
                    <div class="amount">{{stats.registered}}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="column">
        <div class="invitations-top-box invitations-stats-summ-box">
            <div class="invitations-stats-summ">
                <div class="title">
                    <?=Yii::t('app','Verdienst aus Einladungen:')?>
                </div>
                <div class="summ">
                    {{stats.inRegRef|priceFormat}}<jugl-currency></jugl-currency>
                </div>
            </div>
            <div class="invitations-stats-summ">
                <div class="title">
                    <?=Yii::t('app','Verdienst durch Netzwerk:')?>
                </div>
                <div class="summ">
                    {{stats.inReg|priceFormat}}<jugl-currency></jugl-currency>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="invitations-invite-button"><a ui-sref="friendsInvitation.invite"><?=Yii::t('app','Weitere Einladungen versenden')?></a></div>

<div ng-if="invitations.invitations.length > 0" class="account-info-table">
    <table invitations-responsive responsive-table scroll-load="friendsInvitationInvitationsCtrl.moreInvitationsLoad" scroll-load-visible="0.7" scroll-load-has-more="invitations.hasMore">
        <thead>
            <tr>
                <th><?=Yii::t('app','Email-Adresse / Handynummer')?></th>
                <th><?=Yii::t('app','Versendet am')?></th>
                <th><?=Yii::t('app','Status')?></th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            <tr ng-repeat="invitation in invitations.invitations">
                <td ng-if="invitation.status==='Registriert'">{{::invitation.user | userName}}</td>
                <td ng-if="invitation.status!='Registriert'">{{::invitation.name}}</td>
                <td>{{::invitation.dt | date : 'dd.MM.yyyy HH:mm'}}</td>
                <td>{{::invitation.status}}</td>
                <td>
                    <button class="invitations-resend" ng-if="invitation.status!='Registriert' && invitation.type!='SMS'"
                            ng-click="friendsInvitationInvitationsCtrl.resendInvitation(invitation.id)"
                            ng-disabled="resendInvitation.saving"><?=Yii::t('app','Erneut einladen')?></button>

                    <button class="invitations-delete" ng-if="invitation.status!='Registriert' && invitation.type!='SMS'"
                            ng-click="friendsInvitationInvitationsCtrl.deleteInvitation(invitation.id)" type="button">&nbsp;</button>

                </td>
            </tr>
        </tbody>
    </table>
</div>
