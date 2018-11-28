<?php

use yii\db\Migration;

class m170208_080534_add_awaiting_validation extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `offer`
            CHANGE `status` `status` enum('ACTIVE','EXPIRED','CLOSED','DELETED','PAUSED','AWAITING_VALIDATION','REJECTED') COLLATE 'utf8_general_ci' NOT NULL DEFAULT 'ACTIVE' AFTER `accepted_offer_request_id`;
        ");

        $this->execute("
            ALTER TABLE `search_request`
            CHANGE `status` `status` enum('ACTIVE','EXPIRED','CLOSED','DELETED','AWAITING_VALIDATION','REJECTED') COLLATE 'utf8_general_ci' NOT NULL DEFAULT 'ACTIVE' AFTER `active_till`;
        ");

        $this->execute("
            ALTER TABLE `offer`
            ADD `validation_status` enum('NOT_REQUIRED','AWAITING','AWAITING_LATER','ACCEPTED','REJECTED') NOT NULL DEFAULT 'NOT_REQUIRED' AFTER `created_by_admin`,
            DROP `need_validation`;
        ");

        $this->execute("
            ALTER TABLE `search_request`
            ADD `validation_status` enum('NOT_REQUIRED','AWAITING','AWAITING_LATER','ACCEPTED','REJECTED') NOT NULL DEFAULT 'NOT_REQUIRED' AFTER `closed_dt`,
            DROP `need_validation`;
        ");
    }

    public function down()
    {
        echo "m170208_080534_add_awaiting_validation cannot be reverted.\n";

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
