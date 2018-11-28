<?php

use yii\db\Migration;

class m180327_134143_add_annecy_reward_table extends Migration
{
    public function up()
    {
        $this->execute('
            CREATE TABLE `annecy_reward` (
              `id` bigint(20) NOT NULL AUTO_INCREMENT,
              `user_id` bigint(20) DEFAULT NULL,
              `dt` timestamp NOT NULL DEFAULT \'2000-01-01 00:00:00\' ON UPDATE CURRENT_TIMESTAMP,
              `credits` decimal(14,2) DEFAULT NULL,
              `campaign_title` varchar(256) DEFAULT NULL,
              `click_id` bigint(20) DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `user_id` (`user_id`),
              CONSTRAINT `annecy_reward_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ');
    }

    public function down()
    {
        echo "m180327_134143_add_annecy_reward_table cannot be reverted.\n";

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
