<?php

use yii\db\Migration;

class m170123_102052_user_table_mods extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user`
            ADD `parent_registration_bonus` decimal(14,2) NOT NULL DEFAULT '0';
        ");

        $this->execute("
            update user
            set parent_registration_bonus=500
            where packet in ('VIP','STANDART')
        ");

        $this->execute("
            ALTER TABLE `user`
            DROP `parent_got_registration_bonus`;
        ");
    }

    public function down()
    {
        echo "m170123_102052_user_table_mods cannot be reverted.\n";

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
