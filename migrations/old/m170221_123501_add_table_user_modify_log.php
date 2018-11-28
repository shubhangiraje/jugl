<?php

use yii\db\Migration;

class m170221_123501_add_table_user_modify_log extends Migration
{
    public function up()
    {
        $this->execute("
            CREATE TABLE `user_modify_log` (
              `id` bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `user_id` bigint(20) NULL,
              `modify_dt` timestamp NULL,
              `description` text NULL,
              FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
            ) ENGINE='InnoDB' COLLATE 'utf8_general_ci';
        ");
    }

    public function down()
    {
        echo "m170221_123501_add_table_user_modify_log cannot be reverted.\n";

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
