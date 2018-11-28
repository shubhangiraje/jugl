<?php

use yii\db\Migration;

class m180614_113823_add_phone_sms_count_table extends Migration
{
    public function up()
    {
        $this->execute("
            CREATE TABLE `phone_sms_count` (
              `phone` text NOT NULL,
              `count` int NOT NULL
            );
        ");

        $this->execute("
            ALTER TABLE `phone_sms_count`
            CHANGE `phone` `phone` varchar(32) COLLATE 'utf8_general_ci' NOT NULL FIRST;
        ");

        $this->execute("
            ALTER TABLE `phone_sms_count`
            ADD PRIMARY KEY `phone` (`phone`);
        ");
    }

    public function down()
    {
        echo "m180614_113823_add_phone_sms_count_table cannot be reverted.\n";

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
