<?php

use yii\db\Migration;

class m170118_100223_add_field_stat_buyed_jugl_user extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user`
                ADD `stat_buyed_jugl` decimal(14,2) NOT NULL DEFAULT '0';
        ");

        $this->execute("
            UPDATE user JOIN (SELECT user_id, SUM(jugl_sum) as sum FROM pay_in_request WHERE confirm_status='SUCCESS' AND type='PAY_IN' GROUP BY user_id) as payin_request 
                SET stat_buyed_jugl=payin_request.sum 
                    WHERE user.id=payin_request.user_id        
        ");

    }

    public function down()
    {
        echo "m170118_100223_add_field_stat_buyed_jugl_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
