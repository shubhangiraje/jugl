<?php

use yii\db\Migration;

class m171215_093336_add_user_blocked_in_trollbox_flag extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user`
            ADD `is_blocked_in_trollbox` tinyint(1) NOT NULL DEFAULT '0';
        ");

        $this->execute("
            ALTER TABLE `user`
            ADD `is_blocked_in_trollbox_moderator_user_id` bigint NULL;
        ");

        $this->execute("
            ALTER TABLE `user`
            ADD FOREIGN KEY (`is_blocked_in_trollbox_moderator_user_id`) REFERENCES `user` (`id`)
        ");
    }

    public function down()
    {
        echo "m171215_093336_add_user_blocked_in_trollbox_flag cannot be reverted.\n";

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
