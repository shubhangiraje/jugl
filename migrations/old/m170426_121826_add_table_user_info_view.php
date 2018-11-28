<?php

use yii\db\Migration;

class m170426_121826_add_table_user_info_view extends Migration
{
    public function up()
    {
        $this->execute("CREATE TABLE `user_info_view` (
          `user_id` bigint(20) NOT NULL,
          `views` text,
          PRIMARY KEY (`user_id`),
          CONSTRAINT `user_info_view_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    }

    public function down()
    {
        echo "m170426_121826_add_table_user_info_view cannot be reverted.\n";

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
