<?php

use yii\helpers\Html;
use yii\helpers\Url;

?>
<div class="container">
<?php //if (hasAccess('admin-site/index')) { ?>
    <div class="row">
        <div class="col-sm-6">
            <table class="table">
                <thead>
                    <tr>
                        <th colspan="2"><?=Yii::t('app','Statistics')?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row"><?=Yii::t('app','Yesterday registrations')?></th>
                        <td><?=$data['registrations']?></td>
                    </tr>Hallo, _Alexander _Kreado
                    <tr>
                        <th scope="row"><?=Yii::t('app','Yesterday users active')?></th>
                        <td><?=$data['usersActive']?></td>
                    </tr>
                    <?php if (Yii::$app->admin->identity->type==\app\models\Admin::TYPE_SUPERVISOR) { ?>
                    <tr>
                        <th scope="row"><?=Yii::t('app','Yesterday incoming')?></th>
                        <td><?=$data['incoming']?> &euro;</td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <th scope="row"><?=Yii::t('app',' Gesamtausgaben gestern')?></th>
                        <td><?=$data['outcoming']?> &euro;</td>
                    </tr>
                    <tr>
                        <th scope="row"><?=Yii::t('app','Premiummitglieder gesamt')?></th>
                        <td><?=$data['count_vip_users']?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?=Yii::t('app','PremiumPlus Mitglieder gesamt')?></th>
                        <td><?=$data['count_vip_plus_users']?></td>
                    </tr>
                    <tr><th>&nbsp;</th><td>&nbsp;</td></tr>
					<tr>
					<th colspan="2"><?=Yii::t('app','Video-Statistik')?></th>
					</tr>
					<tr>
                        <th scope="row"><?=Yii::t('app','Videos geschaut gestern')?></th>
                        <td><?=$data['videos_watched_yesterday']?></td>
                    </tr>
					<tr>
                        <th scope="row"><?=Yii::t('app','Ausgaben gestern')?></th>
                        <td><?=$data['videos_outgoing_yesterday']?>&euro;</td>
                    </tr>
					<tr>
                        <th scope="row"><?=Yii::t('app','Videos geschaut gesamt')?></th>
                        <td><?=$data['videos_watched_complete']?></td>
                    </tr>
					<tr>
                        <th scope="row"><?=Yii::t('app','Ausgaben gesamt')?></th>
                        <td><?=$data['videos_outgoing_complete']?>&euro;</td>
                    </tr>
                    <tr><th>&nbsp;</th><td>&nbsp;</td></tr>
                    <tr>
                        <th colspan="2"><?=Yii::t('app','Annecy-Statistik')?></th>
                    </tr>
                    <tr>
                        <th scope="row"><?=Yii::t('app','Annecy Ausgaben gestern')?></th>
                        <td><?= $data['annecy_credits_yesterday'] ?> jugls</td>
                    </tr>


                </tbody>
            </table>
        </div>
        <div class="col-sm-6">
            <table class="table">
                <thead>
                <tr>
                    <th colspan="2"><?=Yii::t('app','Attention required')?></th>
                </tr>
                </thead>
                <tbody>
                <?php if (hasAccess('admin-user-validation/index')) { ?>
                    <tr>
                        <th scope="row"><?=Yii::t('app','Users awaiting validation')?></th>
                        <td><?=Html::a($data['usersAwaitingValidation'],['admin-user-validation/index'])?></td>
                    </tr>
                <?php } ?>
                <?php if (hasAccess('admin-pay-out-request/index')) { ?>
                    <tr>
                        <th scope="row"><?=Yii::t('app','Payout requests awaiting acceptance')?></th>
                        <td><?=Html::a($data['payoutRequestAwaitingDecision'],['admin-pay-out-request/index','PayOutRequestSearch[status]'=>\app\models\PayOutRequest::STATUS_NEW])?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?=Yii::t('app','Payout requests awaiting processing')?></th>
                        <td><?=Html::a($data['payoutRequestAwaitingProcessing'],['admin-pay-out-request/index','PayOutRequestSearch[status]'=>\app\models\PayOutRequest::STATUS_ACCEPTED])?></td>
                    </tr>
                <?php } ?>

                    <tr>
                        <th><?= Yii::t('app', 'Einladungskontingent aufgebraucht') ?></th>
                        <td><?= Html::a($data['registration_limit'], ['admin-registrations-limit/index']) ?></td>
                    </tr>

                    <tr>
                        <th><?= Yii::t('app', 'Spammer') ?></th>
                        <td><?= Html::a($data['spammers'], ['admin-spammer/index']) ?></td>
                    </tr>

                    <tr>
                        <th><?= Yii::t('app', 'freizugebende Anzeigen') ?></th>
                        <td><?= Html::a($data['offer_control_on_bonus'], ['admin-offer/control', 'OfferSearch[on_view_bonus]'=>true]) ?></td>
                    </tr>

                    <tr>
                        <th><?= Yii::t('app', 'freizugebende kostenfreie Anzeigen') ?></th>
                        <td><?= Html::a($data['offer_control_off_bonus'], ['admin-offer/control', 'OfferSearch[off_view_bonus]'=>true]) ?></td>
                    </tr>

                    <tr>
                        <th><?= Yii::t('app', 'freizugebende Suchaufträge') ?></th>
                        <td><?= Html::a($data['search_request_control'], ['admin-search-request/control', 'SearchRequestSearch[validation_status]'=>\app\models\SearchRequest::VALIDATION_STATUS_AWAITING]) ?></td>
                    </tr>
                    <tr>
                        <th><?= Yii::t('app', 'Hilfesuchende') ?></th>
                        <td><?= Html::a($data['registration_help'], ['admin-registration-help-request/index']) ?></td>
                    </tr>
                <tr>
                    <th>&nbsp;</th>
                    <td></td>
                </tr>
                <tr>
                    <th colspan="2"><?= Yii::t('app', 'Kontoübersicht aller User') ?></th>
                </tr>
                <tr>
                    <th><?= Yii::t('app', 'Jugls') ?></th>
                    <td><?= $data['total_jugls'] ?></td>
                </tr>
                <tr>
                    <th><?= Yii::t('app', 'Token') ?></th>
                    <td><?= $data['total_tokens'] ?></td>
                </tr>

                <?php if (Yii::$app->admin->identity->access_translator==1) { ?>
                <tr>
                    <th>&nbsp;</th>
                    <td></td>
                </tr>
                <tr>
                    <th colspan="2"><?= Yii::t('app', 'externe Anwendungen') ?></th>
                </tr>
                <tr>
                    <th><?= Yii::t('app', 'Übersetzungen') ?></th>
                    <td><a href="http://translationtool.jugl.net/" target="_blank">zur Webseite</a></td>
                </tr>
				<?php } ?>

                </tbody>
            </table>
        </div>
    </div>
	
    <div class="row" style="margin-top: 30px">
        <div class="col-sm-4">
            <table class="table">
                <thead>
                    <tr>
                        <th colspan="2"><?=Yii::t('app','Registrierungen gestern')?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row"><?=Yii::t('app','davon Basis')?></th>
                        <td><?= $data['reg_packet_standard'] ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?=Yii::t('app','davon Premium')?></th>
                        <td><?= $data['reg_packet_vip'] ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?=Yii::t('app','davon PremiumPlus')?></th>
                        <td><?= $data['reg_packet_vip_plus'] ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?=Yii::t('app','davon durch Gutscheincode')?></th>
                        <td><?= $data['users_reg_code'] ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?=Yii::t('app','davon durch Upgrade Premium')?></th>
                        <td><?= $data['packet_upgrade_vip'] ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?=Yii::t('app','davon durch Upgrade PremiumPlus')?></th>
                        <td><?= $data['packet_upgrade_vip_plus'] ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-sm-4">
            <table class="table">
                <thead>
                    <tr>
                        <th colspan="2"><?=Yii::t('app','Gestern aktiv')?></th>
                    </tr>
                </thead>
                <tbody>
                <tr>
                    <th scope="row"><?=Yii::t('app','davon Basis')?></th>
                    <td><?= $data['reg_packet_standard_online'] ?></td>
                </tr>
                <tr>
                    <th scope="row"><?=Yii::t('app','davon Premium')?></th>
                    <td><?= $data['reg_packet_vip_online'] ?></td>
                </tr>
                <tr>
                    <th scope="row"><?=Yii::t('app','davon durch Gutscheincode')?></th>
                    <td><?= $data['users_online_reg_code'] ?></td>
                </tr>
                <tr>
                    <th scope="row"><?=Yii::t('app','davon durch Upgrade')?></th>
                    <td><?= $data['packet_upgrade_online'] ?></td>
                </tr>
                </tbody>
            </table>
        </div>
		 <div class="col-sm-4">
            <table class="table">
                <thead>
                <tr>
                    <th colspan="2"><?=Yii::t('app','Likes gestern')?></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <th scope="row"><?=Yii::t('app','Wieviel Likes Basis')?></th>
                    <td><?= intval($data['votesData'][\app\models\User::PACKET_STANDART]['cnt']) ?></td>
                </tr>
                <tr>
                    <th scope="row"><?=Yii::t('app','Wieviel Likes Premium')?></th>
                    <td><?= intval($data['votesData'][\app\models\User::PACKET_VIP]['cnt']) ?></td>
                </tr>
                <tr>
                    <th scope="row"><?=Yii::t('app','Wieviel Likes Premium Plus')?></th>
                    <td><?= intval($data['votesData'][\app\models\User::PACKET_VIP_PLUS]['cnt']) ?></td>
                </tr>
                <tr>
                    <th scope="row"><?=Yii::t('app','Likes insgesamt')?></th>
                    <td><?= $data['votesData']['total'] ?></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <table class="table">
                <thead>
                <tr>
                    <th colspan="2"><?=Yii::t('app','Festgelegte Tokens')?></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <th scope="row"><?=Yii::t('app','Heute 1/2/3/Alle Jahr(e)')?></th>
                    <td>
                        <?php $row=$data['token_deposit']['today'];$row[]=array_sum($row);?>
                        <?=implode('<b>&nbsp;/&nbsp;</b>',array_map(function($a){return \app\components\Helper::formatPrice($a);},$row))?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?=Yii::t('app','Gestern 1/2/3/Alle Jahr(e)')?></th>
                    <td>
                        <?php $row=$data['token_deposit']['yesterday'];$row[]=array_sum($row);?>
                        <?=implode('<b>&nbsp;/&nbsp;</b>',array_map(function($a){return \app\components\Helper::formatPrice($a);},$row))?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?=Yii::t('app','Gesamt 1/2/3/Alle Jahr(e)')?></th>
                    <td>
                        <?php $row=$data['token_deposit']['total'];$row[]=array_sum($row);?>
                        <?=implode('<b>&nbsp;/&nbsp;</b>',array_map(function($a){return \app\components\Helper::formatPrice($a);},$row))?>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
<?php //} ?>
</div>
