<?php

use yii\db\Schema;
use yii\db\Migration;

class m151029_120817_allow_multiple_offers_in_search_request extends Migration
{
    public function up()
    {
        $this->execute("
ALTER TABLE `search_request`
DROP FOREIGN KEY `search_request_ibfk_2`
        ");

        $this->execute("
ALTER TABLE `search_request`
DROP FOREIGN KEY `search_request_ibfk_3`
        ");

        $this->execute("
ALTER TABLE `search_request`
DROP `accepted_search_request_offer_id`,
DROP `user_feedback_id`,
COMMENT='';
        ");

        $this->execute("
ALTER TABLE `search_request_offer`
ADD `user_feedback_id` bigint(20) NULL,
ADD FOREIGN KEY (`user_feedback_id`) REFERENCES `user_feedback` (`id`),
COMMENT='';
        ");

        $this->execute("
ALTER TABLE `search_request`
ADD `closed_dt` timestamp NULL,
COMMENT='';
        ");

        $this->execute("
ALTER TABLE `search_request_offer`
ADD `closed_dt` timestamp NULL,
COMMENT='';
        ");

        $this->execute("
ALTER TABLE `offer`
ADD `closed_dt` timestamp NULL,
COMMENT='';
        ");

        $this->execute("
ALTER TABLE `offer_request`
ADD `closed_dt` timestamp NULL,
COMMENT='';
        ");
    }

    public function down()
    {
        echo "m151029_120817_allow_multiple_offers_in_search_request cannot be reverted.\n";

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
