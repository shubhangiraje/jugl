<?php

use yii\db\Schema;
use yii\db\Migration;

class m150708_071523_add_user_feedback_id_links extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `search_request`
            ADD `user_feedback_id` bigint(20) NULL AFTER `accepted_search_request_offer_id`,
            ADD FOREIGN KEY (`user_feedback_id`) REFERENCES `user_feedback` (`id`),
            COMMENT='';

            ALTER TABLE `offer`
            ADD `user_feedback_id` bigint(20) NULL AFTER `accepted_offer_request_id`,
            ADD FOREIGN KEY (`user_feedback_id`) REFERENCES `user_feedback` (`id`),
            COMMENT='';
        ");
    }

    public function down()
    {
        echo "m150708_071523_add_user_feedback_id_links cannot be reverted.\n";

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
