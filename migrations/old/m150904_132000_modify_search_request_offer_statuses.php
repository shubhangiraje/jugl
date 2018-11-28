<?php

use yii\db\Schema;
use yii\db\Migration;

class m150904_132000_modify_search_request_offer_statuses extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `search_request_offer`
            CHANGE `status` `status` enum('NEW','ACTIVE','ACCEPTED','REJECTED','DELETED') COLLATE 'utf8_general_ci' NOT NULL DEFAULT 'ACTIVE' AFTER `relevancy`,
            COMMENT='';

            update search_request_offer set status='NEW' where status='ACTIVE';

            ALTER TABLE `search_request_offer`
            CHANGE `status` `status` enum('NEW','CONTACTED','ACCEPTED','REJECTED','DELETED') COLLATE 'utf8_general_ci' NOT NULL DEFAULT 'NEW' AFTER `relevancy`,
            COMMENT='';
        ");
    }

    public function down()
    {
        echo "m150904_132000_modify_search_request_offer_statuses cannot be reverted.\n";

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
