<?php

use yii\db\Schema;
use yii\db\Migration;

class m150831_065759_add_user_statistic_fields extends Migration
{
    public function up()
    {
        $this->execute("

            ALTER TABLE `user`
            ADD `feedback_count` int(11) NOT NULL DEFAULT '0',
            ADD `closed_deals` int(11) NOT NULL DEFAULT '0' AFTER `feedback_count`;

        ");
    }

    public function down()
    {
        echo "m150831_065759_add_user_statistic_fields cannot be reverted.\n";

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
