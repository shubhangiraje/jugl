<?php

use yii\db\Migration;

class m180626_111617_add_user_used_device_table extends Migration
{
    public function up()
    {
        $this->execute('CREATE TABLE `user_used_device` (
              `user_id` bigint(20) NOT NULL,
              `device_uuid` varchar(128) NOT NULL,
              FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
            ) ENGINE=\'InnoDB\' COLLATE \'utf8_general_ci\';
        ');

        $this->execute('ALTER TABLE `user_used_device` ADD PRIMARY KEY `user_id` (`user_id`), DROP INDEX `user_id`;');

        $this->execute('
            INSERT INTO user_used_device (user_id, device_uuid) 
            SELECT user_id, device_uuid 
            FROM user_device 
            WHERE (user_id, id) IN (SELECT user_id, MAX(id) FROM user_device GROUP BY user_id)
        ');

    }

    public function down()
    {
        echo "m180626_111617_add_user_used_device_table cannot be reverted.\n";

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
