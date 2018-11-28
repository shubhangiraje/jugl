<?php

use yii\db\Schema;
use yii\db\Migration;

class m150908_111358_add_user_stat_fields extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user`
            ADD `stat_offer_year_turnover` decimal(14,2) NOT NULL DEFAULT '0',
            ADD `stat_message_per_day` float NOT NULL DEFAULT '0' AFTER `stat_offer_year_turnover`,
            ADD `stat_active_offers` int(11) NOT NULL DEFAULT '0' AFTER `stat_message_per_day`,
            ADD `stat_offers_view_buy_ratio` float NOT NULL DEFAULT '0' AFTER `stat_active_offers`,
            COMMENT='';

            ALTER TABLE `user`
            CHANGE `stat_message_per_day` `stat_messages_per_day` float NOT NULL DEFAULT '0' AFTER `stat_offer_year_turnover`,
            COMMENT='';
        ");
    }

    public function down()
    {
        echo "m150908_111358_add_user_stat_fields cannot be reverted.\n";

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
