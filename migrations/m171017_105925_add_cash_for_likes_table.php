<?php

use yii\db\Migration;

class m171017_105925_add_cash_for_likes_table extends Migration
{
    public function up()
    {

        $this->execute("
            CREATE TABLE `cfr_distribution` (
              `id` bigint(20) NOT NULL AUTO_INCREMENT,
              `dt` date NOT NULL,
              `votes_count` int(11) NOT NULL,
              `jugl_sum` decimal(17,5) NOT NULL,
              `jugl_sum_fact` decimal(17,5) NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

        $this->execute("
            CREATE TABLE `cfr_distribution_user` (
              `id` bigint(20) NOT NULL AUTO_INCREMENT,
              `cfr_distribution_id` bigint(20) NOT NULL,
              `user_id` bigint(20) NOT NULL,
              `votes_count` int(11) NOT NULL,
              `jugl_sum` decimal(17,5) NOT NULL,
              `processed` tinyint(1) NOT NULL DEFAULT '0',
              PRIMARY KEY (`id`),
              KEY `cfr_distribution_id` (`cfr_distribution_id`),
              KEY `user_id` (`user_id`),
              CONSTRAINT `cfr_distribution_user_ibfk_1` FOREIGN KEY (`cfr_distribution_id`) REFERENCES `cfr_distribution` (`id`),
              CONSTRAINT `cfr_distribution_user_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

        $this->execute("
            ALTER TABLE `cfr_distribution_user`
            ADD INDEX `processed` (`processed`);
        ");
    }

    public function down()
    {
        echo "m171017_105925_add_cash_for_likes_table cannot be reverted.\n";

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
