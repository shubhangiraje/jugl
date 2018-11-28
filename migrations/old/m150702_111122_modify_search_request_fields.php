<?php

use yii\db\Schema;
use yii\db\Migration;

class m150702_111122_modify_search_request_fields extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `search_request`
            CHANGE `deleted` `status` enum('ACTIVE','EXPIRED','CLOSED','DELETED') NOT NULL DEFAULT 'ACTIVE' AFTER `active_till`,
            COMMENT='';

            ALTER TABLE `search_request`
            ADD `closed_search_request_offer_id` bigint(20) NULL AFTER `active_till`,
            ADD FOREIGN KEY (`closed_search_request_offer_id`) REFERENCES `search_request_offer` (`id`),
            COMMENT='';

            ALTER TABLE `search_request_offer`
            CHANGE `deleted` `status` enum('ACTIVE','DELETED') NOT NULL DEFAULT 'ACTIVE' AFTER `relevancy`,
            COMMENT='';

            ALTER TABLE `search_request_offer`
            CHANGE `status` `status` enum('ACTIVE','ACCEPTED','REJECTED','DELETED') COLLATE 'utf8_general_ci' NOT NULL DEFAULT 'ACTIVE' AFTER `relevancy`,
            COMMENT='';

            ALTER TABLE `search_request`
            DROP FOREIGN KEY `search_request_ibfk_2`

            ALTER TABLE `search_request`
            CHANGE `closed_search_request_offer_id` `accepted_search_request_offer_id` bigint(20) NULL AFTER `active_till`,
            ADD FOREIGN KEY (`accepted_search_request_offer_id`) REFERENCES `search_request_offer` (`id`),
            COMMENT='';

            ALTER TABLE `search_request_offer`
            DROP `deleted_by_search_request_owner`,
            COMMENT='';
        ");
    }

    public function down()
    {
        echo "m150702_111122_modify_search_request_fields cannot be reverted.\n";

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
