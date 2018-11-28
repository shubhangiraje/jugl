<?php

use yii\db\Schema;
use yii\db\Migration;

class m160506_095435_add_user_new_fields extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user`
            ADD `stat_new_offers` int NOT NULL DEFAULT '0',
            ADD `stat_new_offers_requests` int NOT NULL DEFAULT '0' AFTER `stat_new_offers`,
            ADD `stat_new_requests` int NOT NULL DEFAULT '0' AFTER `stat_new_offers_requests`,
            ADD `stat_new_requests_offers` int NOT NULL DEFAULT '0' AFTER `stat_new_requests`,
            COMMENT='';
        ");

        $this->execute("
            ALTER TABLE `user`
            CHANGE `stat_new_requests` `stat_new_search_requests` int(11) NOT NULL DEFAULT '0' AFTER `stat_new_offers_requests`,
            CHANGE `stat_new_requests_offers` `stat_new_search_requests_offers` int(11) NOT NULL DEFAULT '0' AFTER `stat_new_search_requests`,
            COMMENT='';
        ");

        $this->execute("
          insert into user_interest(user_id,level1_interest_id) (select id as user_id,685 as interest_id from user);
        ");
    }

    public function down()
    {
        echo "m160506_095435_add_user_new_fields cannot be reverted.\n";

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
