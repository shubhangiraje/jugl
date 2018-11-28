<?php

use yii\db\Migration;

class m170619_113847_add_fields_counter_user_feedback_id extends Migration
{
    public function up()
    {
        $this->execute("
            update offer_request set bet_dt='2000-01-01 00:00:00' where cast(bet_dt as char(50))='0000-00-00 00:00:00'
        ");

        $this->execute("
            update offer_request set bet_active_till='2000-01-01 00:00:00' where cast(bet_active_till as char(50))='0000-00-00 00:00:00'
        ");

        $this->execute("ALTER TABLE `offer_request`
            CHANGE `bet_dt` `bet_dt` timestamp NOT NULL DEFAULT '2000-01-01 00:00:00' AFTER `bet_price`,
            CHANGE `bet_active_till` `bet_active_till` timestamp NOT NULL DEFAULT '2000-01-01 00:00:00' AFTER `bet_period`,
            ADD `counter_user_feedback_id` bigint(20) NULL AFTER `user_feedback_id`,
            ADD FOREIGN KEY (`counter_user_feedback_id`) REFERENCES `user_feedback` (`id`);
        ");

        $this->execute("ALTER TABLE `search_request_offer`
            CHANGE `create_dt` `create_dt` timestamp NOT NULL DEFAULT '2000-01-01 00:00:00' AFTER `id`,
            ADD `counter_user_feedback_id` bigint(20) NULL AFTER `user_feedback_id`,
            ADD FOREIGN KEY (`counter_user_feedback_id`) REFERENCES `user_feedback` (`id`);
        ");
    }

    public function down()
    {
        echo "m170619_113847_add_fields_counter_user_feedback_id cannot be reverted.\n";

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
