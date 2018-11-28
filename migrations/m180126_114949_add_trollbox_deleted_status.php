<?php

use yii\db\Migration;

class m180126_114949_add_trollbox_deleted_status extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `trollbox_message`
            CHANGE `status` `status` enum('ACTIVE','AWAITING_ACTIVATION','REJECTED','DELETED') COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `votes_down`;
        ");
    }

    public function down()
    {
        echo "m180126_114949_add_trollbox_deleted_status cannot be reverted.\n";

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
