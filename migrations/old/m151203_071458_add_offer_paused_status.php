<?php

use yii\db\Schema;
use yii\db\Migration;

class m151203_071458_add_offer_paused_status extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `offer`
            CHANGE `status` `status` enum('ACTIVE','EXPIRED','CLOSED','DELETED','PAUSED') COLLATE 'utf8_general_ci' NOT NULL DEFAULT 'ACTIVE' AFTER `user_feedback_id`,
            COMMENT='';
        ");
    }

    public function down()
    {
        echo "m151203_071458_add_offer_paused_status cannot be reverted.\n";

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
