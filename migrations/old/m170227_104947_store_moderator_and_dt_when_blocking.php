<?php

use yii\db\Migration;

class m170227_104947_store_moderator_and_dt_when_blocking extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `chat_user_ignore`
            ADD `moderator_user_id` bigint(20) NULL,
            ADD `dt` timestamp NOT NULL AFTER `moderator_user_id`,
            ADD FOREIGN KEY (`moderator_user_id`) REFERENCES `user` (`id`);
        ");
    }

    public function down()
    {
        echo "m170227_104947_store_moderator_and_dt_when_blocking cannot be reverted.\n";

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
