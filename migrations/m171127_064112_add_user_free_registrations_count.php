<?php

use yii\db\Migration;

class m171127_064112_add_user_free_registrations_count extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user`
            ADD `free_registrations_used` int NOT NULL DEFAULT '0';
        ");
    }

    public function down()
    {
        echo "m171127_064112_add_user_free_registrations_count cannot be reverted.\n";

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
