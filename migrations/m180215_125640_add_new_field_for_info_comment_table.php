<?php

use yii\db\Migration;

class m180215_125640_add_new_field_for_info_comment_table extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE `info_comment`
            ADD `status` enum(\'ACTIVE\',\'REJECTED\',\'DELETED\') NOT NULL DEFAULT \'ACTIVE\',
            ADD `status_changed_dt` timestamp NULL AFTER `status`,
            ADD `status_changed_user_id` bigint(20) NULL AFTER `status_changed_dt`,
            ADD FOREIGN KEY (`status_changed_user_id`) REFERENCES `user` (`id`);
        ');
    }

    public function down()
    {
        echo "m180215_125640_add_new_field_for_info_comment_table cannot be reverted.\n";

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
