<?php

use yii\db\Schema;
use yii\db\Migration;

class m150904_103851_add_balance_log_mod_table extends Migration
{
    public function up()
    {
        $this->execute("

            CREATE TABLE `balance_log_mod` (
              `id` bigint NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `admin_id` bigint(20) NOT NULL,
              `balance_log_id` bigint(20) NOT NULL,
              FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`),
              FOREIGN KEY (`balance_log_id`) REFERENCES `balance_log` (`id`)
            ) COMMENT='';

            ALTER TABLE `balance_log_mod`
            ADD `comment` mediumtext NOT NULL,
            COMMENT='';

            ALTER TABLE `balance_log_mod`
            CHANGE `comment` `comments` mediumtext COLLATE 'utf8_general_ci' NOT NULL AFTER `balance_log_id`,
            COMMENT='';

        ");
    }

    public function down()
    {
        echo "m150904_103851_add_balance_log_mod_table cannot be reverted.\n";

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
