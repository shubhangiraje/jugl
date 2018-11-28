<?php

namespace app\models;

use Yii;

class CfrDistribution extends \app\models\base\CfrDistribution
{
    const VOTE_PACKET_VIP_PLUS_MULTIPLIER=5;
    const VOTE_PACKET_VIP_MULTIPLIER=1;

    public static function Distribute() {
        $date=(new \app\components\EDateTime)->modify("-1 day");
        $sqlDate=$date->sqlDate();
        $model=static::findOne(['dt'=>$sqlDate]);

        if ($model) {
            return;
        }

        $trx=Yii::$app->db->beginTransaction();

        $model=new static;
        $model->dt=$sqlDate;
        $model->jugl_sum=\app\models\Setting::get('CASH_FOR_LIKES_'.$date->format('N').'_JUGLS');
        $model->jugl_sum_fact=0;
        $model->votes_count=0;
        $model->save();

        $votesData=Yii::$app->db->createCommand("
        select t.user_id,sum((vote-:min_like_count+1)*IF(user.packet=:packet_vip_plus,:packet_vip_plus_multiplier,IF(user.packet=:packet_vip,:packet_vip_multiplier,1))) as cnt
        from (
          select info_comment.user_id,info_comment.id,sum(vote) as vote
          from info_comment_vote
          join info_comment on (info_comment.id=info_comment_vote.info_comment_id)
          where info_comment_vote.dt>=:date_from and info_comment_vote.dt<:date_to and DATE_ADD(info_comment.dt, INTERVAL 1 DAY)>info_comment_vote.dt
          group by info_comment.user_id,info_comment.id
          having vote>=:min_like_count
          
          UNION ALL
          
          select trollbox_message.user_id,trollbox_message.id,sum(vote) as vote
          from trollbox_message_vote 
          join trollbox_message on (trollbox_message.id=trollbox_message_vote.trollbox_message_id and trollbox_message.status='ACTIVE' and trollbox_message.visible_for_all=1)
          where trollbox_message_vote.dt>=:date_from and trollbox_message_vote.dt<:date_to and DATE_ADD(trollbox_message.dt, INTERVAL 1 DAY)>trollbox_message_vote.dt
          group by trollbox_message.user_id,trollbox_message.id
          having vote>=:min_like_count
        ) as t
        join user on (user.id=t.user_id and user.status='ACTIVE' and user.is_blocked_in_trollbox=0 and user.video_identification_status='ACCEPTED')
        join (
          select user_id,count(*) as followers_count
          from user_follower
          group by user_id
        ) as t2 on (t2.user_id=t.user_id and followers_count>:min_follower_count)
        group by user_id
        having cnt>0
        ",[
            ':date_from'=>(new \app\components\EDateTime($sqlDate))->sqlDateTime(),
            ':date_to'=>(new \app\components\EDateTime($sqlDate))->modify('+1 day')->sqlDateTime(),
            ':packet_vip_plus'=>\app\models\User::PACKET_VIP_PLUS,
            ':packet_vip'=>\app\models\User::PACKET_VIP,
            ':packet_vip_plus_multiplier'=>static::VOTE_PACKET_VIP_PLUS_MULTIPLIER,
            ':packet_vip_multiplier'=>static::VOTE_PACKET_VIP_MULTIPLIER,
            ':min_like_count'=>\app\models\Setting::get('CASH_FOR_LIKES_MIN_POST_LIKES')<1 ? 1:\app\models\Setting::get('CASH_FOR_LIKES_MIN_POST_LIKES'),
            ':min_follower_count'=>\app\models\Setting::get('CASH_FOR_LIKES_MIN_FOLLOWERS'),
        ])->queryAll();

        //         join user on (user.id=t.user_id and user.status='ACTIVE' and user.is_blocked_in_trollbox=0 and user.packet=:packet_vip_plus)
        foreach($votesData as $voteData) {
            $model->votes_count = $model->votes_count + $voteData['cnt'];
        }

        $votePrice=0;
        if ($model->votes_count>0) {
            $votePrice=$model->jugl_sum/$model->votes_count;
        }

        $usersData=[];
        foreach($votesData as $voteData) {
            $sum=floor($votePrice*$voteData['cnt']*100000)/100000;
            $model->jugl_sum_fact=$model->jugl_sum_fact+$sum;
            $usersData[]=[
                'cfr_distribution_id'=>$model->id,
                'user_id'=>$voteData['user_id'],
                'votes_count'=>$voteData['cnt'],
                'jugl_sum'=>$sum
            ];
        }

        if (!empty($usersData)) {
            Yii::$app->db->createCommand()->batchInsert('cfr_distribution_user',array_keys($usersData[0]),$usersData)->execute();
        }

        $model->save();

        $trx->commit();
    }
    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'dt' => Yii::t('app','Date'),
            'votes_count' => Yii::t('app','Likes'),
            'jugl_sum' => Yii::t('app','Jugl Sum'),
            'jugl_sum_fact' => Yii::t('app','Jugl Sum Fact'),
        ];
    }
}
