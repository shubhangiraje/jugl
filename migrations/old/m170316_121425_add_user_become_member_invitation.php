<?php

use yii\db\Migration;

class m170316_121425_add_user_become_member_invitation extends Migration
{
    public function up()
    {
        $this->execute("
            CREATE TABLE `user_become_member_invitation` (
              `user_id` bigint(20) NOT NULL,
              `second_user_id` bigint(20) NOT NULL,
              `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              `ms` int(11) NOT NULL,
              PRIMARY KEY (`user_id`,`second_user_id`),
              KEY `second_user_id` (`second_user_id`),
              CONSTRAINT `user_become_member_invitation_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
              CONSTRAINT `user_become_member_invitation_ibfk_2` FOREIGN KEY (`second_user_id`) REFERENCES `user` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
    }

    public function down()
    {
        echo "m170316_121425_add_user_become_member_invitation cannot be reverted.\n";

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
