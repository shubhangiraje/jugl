<?php

use yii\db\Migration;

class m180111_073007_create_table_user_validation_phone_notification extends Migration
{
    public function up()
    {
        $this->execute("CREATE TABLE `user_validation_phone_notification` (
          `id` bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `user_id` bigint(20) NULL,
          `dt` timestamp NULL,
          FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
        ) ENGINE='InnoDB' COLLATE 'utf8_general_ci';");
    }

    public function down()
    {
        echo "m180111_073007_create_table_user_validation_phone_notification cannot be reverted.\n";

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
