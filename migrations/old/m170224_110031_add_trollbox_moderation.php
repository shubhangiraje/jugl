<?php

use yii\db\Migration;

class m170224_110031_add_trollbox_moderation extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `trollbox_message`
            ADD `status` enum('ACTIVE','AWAITING_ACTIVATION','REJECTED') NOT NULL,
            ADD `status_changed_dt` timestamp NULL AFTER `status`,
            ADD `status_changed_user_id` bigint(20) NULL AFTER `status_changed_dt`,
            ADD FOREIGN KEY (`status_changed_user_id`) REFERENCES `user` (`id`);
        ");

        $this->execute("
            CREATE TABLE `trollbox_message_status_history` (
              `id` bigint(20) NOT NULL AUTO_INCREMENT,
              `trollbox_message_id` bigint(20) NOT NULL,
              `status` enum('ACTIVE','AWAITING_ACTIVATION','REJECTED') NOT NULL,
              `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              `user_id` bigint(20) NOT NULL,
              PRIMARY KEY (`id`),
              KEY `trollbox_message_id` (`trollbox_message_id`),
              KEY `status_changed_user_id` (`user_id`),
              CONSTRAINT `trollbox_message_status_history_ibfk_1` FOREIGN KEY (`trollbox_message_id`) REFERENCES `trollbox_message` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

        $this->execute("
            ALTER TABLE `trollbox_message_status_history`
            ADD FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);
        ");

        $this->execute("
            INSERT INTO `setting` (`name`, `title`, `type`, `value`) VALUES
            ('VALIDATE_TROLLBOX_MESSAGE_STANDART',	'Jugl Forum vor Upload kontrollieren (Basis)',	'bool',	'1'),
            ('VALIDATE_TROLLBOX_MESSAGE_VIP',	'Jugl Forum vor Upload kontrollieren (Premium)',	'bool',	'1');
        ");
    }

    public function down()
    {
        echo "m170224_110031_add_trollbox_moderation cannot be reverted.\n";

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
