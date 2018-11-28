<?php

use yii\db\Migration;

class m170302_082048_teamchange extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user`
            ADD `team_rating` tinyint(4) NOT NULL DEFAULT '0' AFTER `rating`,
            ADD `team_feedback_count` int(11) NOT NULL DEFAULT '0' AFTER `feedback_count`;
        ");

        $this->execute("
            CREATE TABLE `user_team_feedback` (
              `id` bigint(20) NOT NULL AUTO_INCREMENT,
              `user_id` bigint(20) NOT NULL,
              `second_user_id` bigint(20) NOT NULL,
              `feedback` varchar(4096) NOT NULL,
              `rating` tinyint(4) NOT NULL,
              `create_dt` timestamp NOT NULL DEFAULT '2000-01-01 00:00:00',
              PRIMARY KEY (`id`),
              KEY `user_id` (`user_id`),
              KEY `second_user_id` (`second_user_id`),
              CONSTRAINT `user_team_feedback_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
              CONSTRAINT `user_team_feedback_ibfk_2` FOREIGN KEY (`second_user_id`) REFERENCES `user` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        $this->execute("
            CREATE TABLE `user_team_invitation` (
              `user_id` bigint(20) NOT NULL,
              `second_user_id` bigint(20) NOT NULL,
              `dt` timestamp NOT NULL,
              `user_event_id` bigint(20) NOT NULL,
              FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
              FOREIGN KEY (`second_user_id`) REFERENCES `user` (`id`),
              FOREIGN KEY (`user_event_id`) REFERENCES `user_event` (`id`)
            );
        ");

        $this->execute("
            CREATE TABLE `user_photo` (
              `user_id` bigint(20) NOT NULL,
              `file_id` bigint(20) NOT NULL,
              `sort_order` bigint(20) DEFAULT NULL,
              PRIMARY KEY (`user_id`,`file_id`),
              KEY `file_id` (`file_id`),
              CONSTRAINT `user_photo_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
              CONSTRAINT `user_photo_ibfk_2` FOREIGN KEY (`file_id`) REFERENCES `file` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

        $this->execute("
            INSERT INTO `setting` (`name`, `title`, `type`, `value`)
            VALUES ('TEAM_CHANGE_PERIOD_DAYS', 'Teamwechsel period (Tagen)', 1, '7');
        ");
    }

    public function down()
    {
        echo "m170302_082048_teamchange cannot be reverted.\n";

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
