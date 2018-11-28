<?php

use yii\db\Migration;

class m170207_120714_add_fileds_offer extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `offer`
                CHANGE `status` `status` enum('ACTIVE','EXPIRED','CLOSED','DELETED','PAUSED','REJECTED') COLLATE 'utf8_general_ci' NOT NULL DEFAULT 'ACTIVE' AFTER `accepted_offer_request_id`,
                ADD `need_validation` tinyint(1) NOT NULL DEFAULT '0';
        ");
    }

    public function down()
    {
        echo "m170207_120714_add_fileds_offer cannot be reverted.\n";

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
