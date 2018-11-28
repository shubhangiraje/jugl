<?php

use yii\db\Migration;

class m180125_095504_add_table_trollbox_category extends Migration
{
    public function up()
    {
        $this->execute("CREATE TABLE `trollbox_category` (
          `id` bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `title` varchar(200) NOT NULL,
          `sort_order` bigint(20) NULL
        ) ENGINE='InnoDB' COLLATE 'utf8_general_ci';");
    }

    public function down()
    {
        echo "m180125_095504_add_table_trollbox_category cannot be reverted.\n";

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
