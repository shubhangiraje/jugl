<?php

namespace app\controllers;

use app\models\DailyStats;
use app\models\Offer;
use app\models\SearchRequest;
use Yii;
use yii\filters\AccessControl;
use app\components\AdminController;
use app\components\EDateTime;
use app\models\AdminLoginForm;
use app\models\PayInRequest;
use app\models\User;
use app\models\PayOutRequest;


class AdminSiteController extends AdminController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'user' => 'admin',
                'rules' => [
                    [
                        'actions' => ['login'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex() {
        $yesterday=new EDateTime('-1 day',null);
        $today=new EDateTime();

        $data=[];

        if (Yii::$app->admin->identity->hasAccess('admin-site/index')) {
            // don't use read locks for long query
            $trx=Yii::$app->db->beginTransaction('READ UNCOMMITTED');

            Yii::$app->db->createCommand("SET SESSION query_cache_type = OFF")->execute();

            $key='DASHBOARD_STATS_'.__LINE__.'_'.date('Ymd');
            $data['usersActive']=Yii::$app->cache->get($key);
            if ($data['usersActive']===false) {
                $data['usersActive'] = intval(Yii::$app->db->createCommand("select count(*) from user_activity_log where dt=:yesterday", [
                    ':yesterday' => $yesterday->sqlDate()
                ])->queryScalar());

                Yii::$app->cache->set($key,$data['usersActive']);
            }

            $key='DASHBOARD_STATS_'.__LINE__.'_'.date('Ymd');
            $data['registrations']=Yii::$app->cache->get($key);
            if ($data['registrations']===false) {
                $data['registrations'] = intval(Yii::$app->db->createCommand("select count(*) from user where registration_dt between :yesterday and :today", [
                    ':yesterday' => $yesterday->sqlDate(),
                    ':today' => $today->sqlDate(),
                ])->queryScalar());

                Yii::$app->cache->set($key,$data['registrations']);
            }

            $data['incoming'] = floatval(Yii::$app->db->createCommand("select sum(currency_sum) from pay_in_request where confirm_status=:confirm_status and payment_method!=:payment_method and dt between :yesterday and :today", [
                ':yesterday' => $yesterday->sqlDate(),
                ':today' => $today->sqlDate(),
                ':confirm_status' => PayInRequest::CONFIRM_STATUS_SUCCESS,
                ':payment_method' => PayInRequest::PAYMENT_METHOD_JUGL
            ])->queryScalar());

            $data['usersAwaitingValidation'] = intval(Yii::$app->db->createCommand("select count(*) from user where validation_status=:validation_status", [
                ':validation_status' => User::VALIDATION_STATUS_AWAITING,
            ])->queryScalar());

            $data['payoutRequestAwaitingDecision'] = intval(Yii::$app->db->createCommand("select count(*) from pay_out_request where status=:status", [
                ':status' => PayOutRequest::STATUS_NEW
            ])->queryScalar());

            $data['payoutRequestAwaitingProcessing'] = intval(Yii::$app->db->createCommand("select count(*) from pay_out_request where status=:status", [
                ':status' => PayOutRequest::STATUS_ACCEPTED,
            ])->queryScalar());

            $data['outcoming'] = floatval(Yii::$app->db->createCommand("select sum(currency_sum) from pay_out_request where status=:statusProcessed and (dt_processed between :yesterday and :today)", [
                ':yesterday' => $yesterday->sqlDate(),
                ':today' => $today->sqlDate(),
                ':statusProcessed' => PayOutRequest::STATUS_PROCESSED
            ])->queryScalar());
			
			/*nviimedia*/
            $key='DASHBOARD_STATS_'.__LINE__.'_'.date('Ymd');
            $data['videos_watched_yesterday']=Yii::$app->cache->get($key);
            if ($data['videos_watched_yesterday']===false) {
                $data['videos_watched_yesterday'] = floatval(Yii::$app->db->createCommand("SELECT COUNT(video_id) AS gestern_klicks FROM user_video WHERE DATE(dt)=DATE(CURRENT_DATE-INTERVAL 1 DAY) ")->queryScalar());
                Yii::$app->cache->set($key,$data['videos_watched_yesterday']);
            }

            $key='DASHBOARD_STATS_'.__LINE__.'_'.date('Ymd');
            $data['videos_outgoing_yesterday']=Yii::$app->cache->get($key);
            if ($data['videos_outgoing_yesterday']===false) {
                $data['videos_outgoing_yesterday'] = floatval(Yii::$app->db->createCommand("SELECT (SUM(bonus)/100)AS gestern_bonus FROM user_video WHERE DATE(dt)=DATE(CURRENT_DATE-INTERVAL 1 DAY)")->queryScalar());
                Yii::$app->cache->set($key,$data['videos_outgoing_yesterday']);
            }

			$data['videos_watched_complete'] = floatval(Yii::$app->db->createCommand("SELECT COUNT(user_video.video_id) as gesamt_klicks FROM user_video")->queryScalar());
			$data['videos_outgoing_complete'] = floatval(Yii::$app->db->createCommand("SELECT (SUM(user_video.bonus)/100) as gesamt_bonus FROM user_video")->queryScalar());
			/*nviimedia*/

            $pData=\yii\helpers\ArrayHelper::index(
                Yii::$app->db->createCommand("select packet,count(*) as cnt from user where packet in (:packet_vip,:packet_vip_plus) and status=:status group by packet",[
                ':packet_vip' => User::PACKET_VIP,
                ':packet_vip_plus' => User::PACKET_VIP_PLUS,
                ':status' => User::STATUS_ACTIVE])->queryAll(),
            'packet');

            $data['count_vip_users'] = intval($pData[User::PACKET_VIP]['cnt']);
            $data['count_vip_plus_users'] = intval($pData[User::PACKET_VIP_PLUS]['cnt']);

            $key='DASHBOARD_STATS_'.__LINE__.'_'.date('Ymd');
            $dpart=Yii::$app->cache->get($key);
            if ($dpart===false) {
                $dpart['reg_packet_standard'] = intval(Yii::$app->db->createCommand("
                    SELECT count(*) FROM user WHERE packet=:packet 
                        AND (dt_packet_select BETWEEN :yesterday AND :today) 
                        AND (registration_dt BETWEEN :yesterday AND :today)
                        AND dt_packet_upgrade IS NULL", [
                    'packet' => User::PACKET_STANDART,
                    ':yesterday' => $yesterday->sqlDate(),
                    ':today' => $today->sqlDate(),
                ])->queryScalar());

                $dpart['reg_packet_vip'] = intval(Yii::$app->db->createCommand("
                    SELECT count(*) FROM user WHERE packet=:packet 
                        AND (dt_packet_select BETWEEN :yesterday AND :today) 
                        AND (registration_dt BETWEEN :yesterday AND :today)
                        AND dt_packet_upgrade IS NULL", [
                    ':packet' => User::PACKET_VIP,
                    ':yesterday' => $yesterday->sqlDate(),
                    ':today' => $today->sqlDate(),
                ])->queryScalar());

                $dpart['reg_packet_vip_plus'] = intval(Yii::$app->db->createCommand("
                    SELECT count(*) FROM user WHERE packet=:packet 
                        AND (dt_packet_select BETWEEN :yesterday AND :today) 
                        AND (registration_dt BETWEEN :yesterday AND :today)
                        AND dt_packet_upgrade IS NULL", [
                    ':packet' => User::PACKET_VIP_PLUS,
                    ':yesterday' => $yesterday->sqlDate(),
                    ':today' => $today->sqlDate(),
                ])->queryScalar());

                $dpart['packet_upgrade_vip'] = intval(Yii::$app->db->createCommand("
                    SELECT count(*) FROM user WHERE packet=:packet 
                        AND (dt_packet_upgrade BETWEEN :yesterday AND :today)
                        AND (registration_dt BETWEEN :yesterday AND :today)", [
                    ':packet' => User::PACKET_VIP,
                    ':yesterday' => $yesterday->sqlDate(),
                    ':today' => $today->sqlDate(),
                ])->queryScalar());

                $dpart['packet_upgrade_vip_plus'] = intval(Yii::$app->db->createCommand("
                    SELECT count(*) FROM user WHERE packet=:packet 
                        AND (dt_packet_upgrade BETWEEN :yesterday AND :today)
                        AND (registration_dt BETWEEN :yesterday AND :today)", [
                    ':packet' => User::PACKET_VIP_PLUS,
                    ':yesterday' => $yesterday->sqlDate(),
                    ':today' => $today->sqlDate(),
                ])->queryScalar());

                $dpart['reg_packet_standard_online'] = intval(Yii::$app->db->createCommand("
                    SELECT count(*) FROM user JOIN user_activity_log ON user_activity_log.user_id=user.id 
                        WHERE user.packet=:packet AND (user_activity_log.dt=:yesterday) AND (user.dt_packet_select IS NOT NULL)", [
                    ':packet' => User::PACKET_STANDART,
                    ':yesterday' => $yesterday->sqlDate(),
                ])->queryScalar());

                $dpart['reg_packet_vip_online'] = intval(Yii::$app->db->createCommand("
                    SELECT count(*) FROM user JOIN user_activity_log ON user_activity_log.user_id=user.id 
                        WHERE (user.packet=:packet or user.packet=:packet2) AND (user_activity_log.dt=:yesterday) AND (user.dt_packet_select IS NOT NULL)", [
                    ':packet' => User::PACKET_VIP,
                    ':packet2' => User::PACKET_VIP_PLUS,
                    ':yesterday' => $yesterday->sqlDate(),
                ])->queryScalar());

                $dpart['packet_upgrade_online'] = intval(Yii::$app->db->createCommand("
                    SELECT count(*) FROM user JOIN user_activity_log ON user_activity_log.user_id=user.id 
                        WHERE (user.packet=:packet or user.packet=:packet2) AND (user_activity_log.dt=:yesterday) AND (user.dt_packet_upgrade IS NOT NULL)", [
                    ':packet' => User::PACKET_VIP,
                    ':packet2' => User::PACKET_VIP_PLUS,
                    ':yesterday' => $yesterday->sqlDate(),
                ])->queryScalar());

                $dpart['users_reg_code'] = intval(Yii::$app->db->createCommand("
                    SELECT count(*) FROM user JOIN registration_code ON registration_code.referral_user_id=user.id 
                        WHERE user.registration_dt BETWEEN :yesterday AND :today", [
                    ':yesterday' => $yesterday->sqlDate(),
                    ':today' => $today->sqlDate(),
                ])->queryScalar());

                $dpart['users_online_reg_code'] = intval(Yii::$app->db->createCommand("
                    SELECT count(*) FROM user 
                        JOIN registration_code ON registration_code.referral_user_id=user.id 
                        JOIN user_activity_log ON user_activity_log.user_id=user.id
                            WHERE user_activity_log.dt=:yesterday", [
                    ':yesterday' => $yesterday->sqlDate(),
                ])->queryScalar());

                Yii::$app->cache->set($key,$dpart);
            }

            $data=array_merge($data,$dpart);

            $data['registration_limit'] = intval(Yii::$app->db->createCommand("
                select  SUM( IF(COALESCE(user.free_registrations_limit,IF(user.packet='VIP_PLUS',:limit_vip_plus,IF(user.packet='VIP',:limit_vip,:limit_std))) < t.cnt,1,0))
                from user 
                join (select parent_id,count(*) as cnt from user group by parent_id) as t on (t.parent_id=user.id)
                where user.status=:status_active 
            ",[
                ':status_active'=>\app\models\User::STATUS_ACTIVE,
                ':limit_vip_plus'=>\app\models\Setting::get('VIP_PLUS_FREE_REGISTRATIONS_LIMIT'),
                ':limit_vip'=>\app\models\Setting::get('VIP_FREE_REGISTRATIONS_LIMIT'),
                ':limit_std'=>\app\models\Setting::get('STANDART_FREE_REGISTRATIONS_LIMIT'),
            ]));

            $data['spammers'] = intval(Yii::$app->db->createCommand("SELECT COUNT(*) FROM user WHERE spam_reports>0")->queryScalar());

            $data['offer_control_on_bonus'] = intval(Yii::$app->db->createCommand("
                SELECT COUNT(*) FROM offer WHERE status!=:status_deleted and status!=:status_unlinked AND view_bonus>0 AND validation_status=:status_awaiting", [
                ':status_awaiting'=>Offer::VALIDATION_STATUS_AWAITING,
                ':status_deleted'=>Offer::STATUS_DELETED,
                ':status_unlinked'=>Offer::STATUS_UNLINKED,
            ])->queryScalar());

            $data['offer_control_off_bonus'] = intval(Yii::$app->db->createCommand("
                SELECT COUNT(*) FROM offer WHERE status!=:status_deleted and status!=:status_unlinked AND view_bonus is null AND validation_status=:status_awaiting", [
                ':status_awaiting'=>Offer::VALIDATION_STATUS_AWAITING,
                ':status_deleted'=>Offer::STATUS_DELETED,
                ':status_unlinked'=>Offer::STATUS_UNLINKED,
            ])->queryScalar());

            $data['search_request_control'] = intval(Yii::$app->db->createCommand("
                SELECT COUNT(*) FROM search_request WHERE status!=:status_deleted and status!=:status_unlinked AND validation_status=:status_awaiting", [
                ':status_awaiting'=>SearchRequest::VALIDATION_STATUS_AWAITING,
                ':status_deleted'=>SearchRequest::STATUS_DELETED,
                ':status_unlinked'=>SearchRequest::STATUS_UNLINKED,
            ])->queryScalar());

            $sqlDate=(new EDateTime())->modify('-1 day')->sqlDate();

            $key='DASHBOARD_STATS_'.__LINE__.'_'.date('Ymd');
            $votesData=Yii::$app->cache->get($key);
            if ($votesData===false) {
                $votesData = Yii::$app->db->createCommand("
                    select IF(user.packet=:packet_vip_plus,:packet_vip_plus,IF(user.packet=:packet_vip,:packet_vip,:packet_standart)) as packet ,count(*) as cnt
                    from (
                      select info_comment.user_id from info_comment_vote use index(dt)
                      join info_comment on (info_comment.id=info_comment_vote.info_comment_id)
                      where info_comment_vote.dt>=:date_from and info_comment_vote.dt<:date_to and vote>0
                      UNION ALL
                      select trollbox_message.user_id from trollbox_message_vote use index(dt) 
                      join trollbox_message on (trollbox_message.id=trollbox_message_vote.trollbox_message_id and trollbox_message.status='ACTIVE' and trollbox_message.visible_for_all=1)
                      where trollbox_message_vote.dt>=:date_from and trollbox_message_vote.dt<:date_to and vote>0
                    ) as t
                    join user on (user.id=t.user_id)
                    group by packet
                    ", [
                    ':date_from' => (new \app\components\EDateTime($sqlDate))->sqlDateTime(),
                    ':date_to' => (new \app\components\EDateTime($sqlDate))->modify('+1 day')->sqlDateTime(),
                    ':packet_vip_plus' => \app\models\User::PACKET_VIP_PLUS,
                    ':packet_vip' => \app\models\User::PACKET_VIP,
                    ':packet_standart' => \app\models\User::PACKET_STANDART,
                ])->queryAll();
                $votesData = \yii\helpers\ArrayHelper::index($votesData, 'packet');
                $votesData['total'] =
                    $votesData[\app\models\User::PACKET_VIP_PLUS]['cnt'] * \app\models\CfrDistribution::VOTE_PACKET_VIP_PLUS_MULTIPLIER +
                    $votesData[\app\models\User::PACKET_VIP]['cnt'] * \app\models\CfrDistribution::VOTE_PACKET_VIP_MULTIPLIER +
                    $votesData[\app\models\User::PACKET_STANDART]['cnt'];

                Yii::$app->cache->set($key,$votesData);
            }

            $data['votesData']=$votesData;

            $data['registration_help'] = intval(Yii::$app->db->createCommand("SELECT COUNT(*) FROM registration_help_request")->queryScalar());

            $data['annecy_credits_yesterday'] = floatval(Yii::$app->db->createCommand('SELECT SUM(credits) FROM annecy_reward WHERE dt BETWEEN :yesterday AND :today', [
                ':yesterday'=>$yesterday->sqlDate(),
                ':today' => $today->sqlDate(),
            ])->queryScalar());

            $balanceData=Yii::$app->db->createCommand("select sum(if(balance>0,balance,0)) as total_jugls,sum(if(balance_token>0,balance_token,0)) as total_tokens from user where status=:status_active",[
                ':status_active'=>\app\models\User::STATUS_ACTIVE
            ])->queryOne();

            $data['total_jugls']=floatval($balanceData['total_jugls']);
            $data['total_tokens']=floatval($balanceData['total_tokens']);

            $data['token_deposit']['today']=$this->getTokenStatsPart(
                (new EDateTime())->setTime(0,0,0),
                (new EDateTime())->setTime(23,59,59)
            );

            $data['token_deposit']['yesterday']=$this->getTokenStatsPart(
                (new EDateTime())->modify('-1 day')->setTime(0,0,0),
                (new EDateTime())->modify('-1 day')->setTime(23,59,59)
            );

            $data['token_deposit']['total']=$this->getTokenStatsPart(null,null);

            $trx->commit();
        }

        return $this->render('index',['data'=>$data]);
    }

    private function getTokenStatsPart($dtFrom,$dtTo) {
        $rawData=Yii::$app->db->createCommand("
                select period_months,sum(`sum`) as tokens 
                from token_deposit
                where status!=:status_awaiting_payment 
                  and (created_at>=:dt_from or :dt_from is null) 
                  and (created_at<=:dt_to or :dt_to is null)
                group by period_months",[
            ':status_awaiting_payment'=>\app\models\TokenDeposit::STATUS_AWAITING_PAYMENT,
            ':dt_from'=>$dtFrom ? $dtFrom->sqlDateTime():null,
            ':dt_to'=>$dtTo ? $dtTo->sqlDateTime():null
        ])->queryAll();

        $res=[12=>0,24=>0,36=>0];
        foreach($rawData as $row) {
            $res[$row['period_months']]=$row['tokens'];
        }

        return $res;
    }

    public function actionLogout()
    {
        Yii::$app->admin->logout();

        return $this->redirect('index');
    }

    public function actionLogin()
    {
        $this->layout='admin-simple';

        if (!\Yii::$app->admin->isGuest) {
            return $this->redirect('index');
        }

        $model = new AdminLoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect('index');
        } else {
            $model->password=null;
            $model->verifyCode=null;
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionTest() {

        $parentsTokenPercent=\app\models\Setting::get('TOKEN_DISTRIBUTION_PARENTS_PERCENT_TOKEN');
        $parentsPercent=\app\models\Setting::get('TOKEN_DISTRIBUTION_PARENTS_PERCENT_JUGL');
        $tokenToJuglExchangeRate=\app\models\Setting::get('TOKEN_TO_JUGL_EXCHANGE_RATE');
        $mePercent=100-$parentsTokenPercent-$parentsPercent;
        $sum = 0.7;
        $small = true;
        $parent = true;

        if (!$parent) {
            $sumTokens = $this->distributionSum($sum,100,$small ? $small : false);

            echo '<pre>';
            print_r([
                'sumTokens'=>$sumTokens
            ]);
            echo '</pre>';

        } else {

            $spisanie = $this->distributionSum($sum,100-$mePercent,$small ? $small : false);
            $sumTokens = $this->distributionSum($sum,$parentsTokenPercent,$small ? $small : false);
            $sumJugls = $this->distributionSum($sum,$parentsPercent,$small ? $small : false)*$tokenToJuglExchangeRate;

            echo '<pre>';
            print_r([
                'spisanie'=>$spisanie,
                'sumTokens'=>$sumTokens,
                'sumJugls'=>$sumJugls
            ]);
            echo '</pre>';
        }

    }


    public function distributionSum($sum,$percent,$small=false) {
        if($small){
            $sum =($sum*$percent)/100;
            $sum = number_format($sum,5);
        }
        else{
            $sum=floor($sum*$percent*1000)/100000;
        }
        return $sum;
    }

}
