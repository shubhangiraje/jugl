<?php

use yii\db\Migration;

class m171201_104507_add_vip_plus_stick_users_to_parent_per_month extends Migration
{
    public function up()
    {
        $this->execute("
            INSERT INTO `setting` (`name`, `title`, `type`, `value`)
            VALUES ('STICK_TO_PARENT_REQUESTS_PER_MONTH', '\"Stick to parent\" requests per month', 1, '5');
        ");

        $this->execute("
            INSERT INTO `setting` (`name`, `title`, `type`, `value`)
            VALUES ('STICK_TO_PARENT_REQUEST_RESPONSE_TIME', '\"Stick to parent\" requests response time in hours', 1, '48');        
        ");

        $this->execute("
            ALTER TABLE `user`
            ADD `is_stick_to_parent` tinyint(1) NOT NULL DEFAULT '0';
        ");

        $this->execute("
            CREATE TABLE `user_stick_to_parent_request` (
              `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `user_id` bigint(20) NOT NULL,
              `referral_user_id` bigint(20) NOT NULL,
              `expires_at` timestamp NULL,
              FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
              FOREIGN KEY (`referral_user_id`) REFERENCES `user` (`id`)
            );
        ");

        $this->execute("
            ALTER TABLE `user_stick_to_parent_request`
            ADD `completed` tinyint NULL DEFAULT '0';
        ");

        $this->execute("
            ALTER TABLE `user_stick_to_parent_request`
            ADD INDEX `completed_expires_at` (`completed`, `expires_at`);
        ");
    }

    public function down()
    {
        echo "m171201_104507_add_vip_plus_stick_users_to_parent_per_month cannot be reverted.\n";

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
