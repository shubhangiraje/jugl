<?php

use yii\db\Migration;

class m170422_063227_add_unlinked_status_to_offer_and_search_request extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `search_request`
            CHANGE `status` `status` enum('ACTIVE','EXPIRED','CLOSED','DELETED','AWAITING_VALIDATION','REJECTED','UNLINKED') COLLATE 'utf8_general_ci' NOT NULL DEFAULT 'ACTIVE' AFTER `active_till`,
            ADD `status_before_deleted` enum('ACTIVE','EXPIRED','CLOSED','DELETED','AWAITING_VALIDATION','REJECTED') COLLATE 'utf8_general_ci' NULL AFTER `status`;        
        ");

        $this->execute("
            update search_request set status='UNLINKED' where status='DELETED';        
        ");

        $this->execute("
            ALTER TABLE `offer`
            CHANGE `status` `status` enum('ACTIVE','EXPIRED','CLOSED','DELETED','PAUSED','AWAITING_VALIDATION','REJECTED','UNLINKED') COLLATE 'utf8_general_ci' NOT NULL DEFAULT 'ACTIVE' AFTER `accepted_offer_request_id`,
            ADD `status_before_deleted` enum('ACTIVE','EXPIRED','CLOSED','DELETED','PAUSED','AWAITING_VALIDATION','REJECTED') COLLATE 'utf8_general_ci' NULL AFTER `status`;        
        ");

        $this->execute("
            update offer set status='UNLINKED' where status='DELETED';                
        ");

        $this->execute("
            ALTER TABLE `offer`
            ADD `closed_dt_before_deleted` timestamp NULL AFTER `closed_dt`;
        ");

        $this->execute("
            ALTER TABLE `search_request`
            ADD `closed_dt_before_deleted` timestamp NULL AFTER `closed_dt`;        
        ");
    }

    public function down()
    {
        echo "m170422_063227_add_unlinked_status_to_offer_and_search_request cannot be reverted.\n";

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
