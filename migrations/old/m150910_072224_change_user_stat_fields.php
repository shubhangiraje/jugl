<?php

use yii\db\Schema;
use yii\db\Migration;

class m150910_072224_change_user_stat_fields extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user`
            CHANGE `stat_active_offers` `stat_active_search_requests` int(11) NOT NULL DEFAULT '0' AFTER `stat_messages_per_day`,
            COMMENT='';
        ");
    }

    public function down()
    {
        echo "m150910_072224_change_user_stat_fields cannot be reverted.\n";

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
