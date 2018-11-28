<?php

use yii\db\Migration;

class m170210_123205_add_field_setting_send_email_user extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `user` ADD `setting_off_send_email` tinyint(1) NOT NULL DEFAULT '0';");
    }

    public function down()
    {
        echo "m170210_123205_add_field_setting_send_email_user cannot be reverted.\n";

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
