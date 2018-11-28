<?php

use yii\db\Schema;
use yii\db\Migration;

class m150910_072132_add_offer_user_filter extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `offer`
            ADD `uf_enabled` tinyint(1) NOT NULL DEFAULT '0',
            ADD `uf_age_from` int NULL AFTER `uf_enabled`,
            ADD `uf_age_to` int NULL AFTER `uf_age_from`,
            ADD `uf_sex` enum('M','F') COLLATE 'utf8_general_ci' NULL AFTER `uf_age_to`,
            ADD `uf_offers_view_buy_ratio_from` float NULL AFTER `uf_sex`,
            ADD `uf_offers_view_buy_ratio_to` float NULL AFTER `uf_offers_view_buy_ratio_from`,
            ADD `uf_balance_from` decimal(14,2) NULL AFTER `uf_offers_view_buy_ratio_to`,
            ADD `uf_country_id` int(11) NULL AFTER `uf_balance_from`,
            ADD `uf_ort` varchar(32) COLLATE 'utf8_general_ci' NULL AFTER `uf_country_id`,
            ADD `uf_plz` varchar(8) COLLATE 'utf8_general_ci' NULL AFTER `uf_ort`,
            ADD `uf_distance_km` int NULL AFTER `uf_plz`,
            ADD `uf_offer_year_turnover_from` decimal(14,2) NULL AFTER `uf_distance_km`,
            ADD `uf_offer_year_turnover_to` decimal(14,2) NULL AFTER `uf_offer_year_turnover_from`,
            ADD `uf_active_search_requests_from` int NULL AFTER `uf_offer_year_turnover_to`,
            ADD `uf_messages_per_day_to` int NULL AFTER `uf_active_search_requests_from`,
            ADD FOREIGN KEY (`uf_country_id`) REFERENCES `country` (`id`),
            COMMENT='';

            ALTER TABLE `offer`
            CHANGE `uf_sex` `uf_sex` enum('M','F','A') COLLATE 'utf8_general_ci' NOT NULL DEFAULT 'A' AFTER `uf_age_to`,
            COMMENT='';

            ALTER TABLE `offer`
            ADD `uf_offer_request_completed_interest_id` int(11) NULL AFTER `uf_sex`,
            ADD FOREIGN KEY (`uf_offer_request_completed_interest_id`) REFERENCES `interest` (`id`),
            COMMENT='';

            ALTER TABLE `offer`
            ADD `uf_member_from` int(11) NULL AFTER `uf_offer_request_completed_interest_id`,
            ADD `uf_member_to` int(11) NULL AFTER `uf_member_from`,
            COMMENT='';

            ALTER TABLE `offer`
            CHANGE `uf_ort` `uf_city` varchar(32) COLLATE 'utf8_general_ci' NULL AFTER `uf_country_id`,
            CHANGE `uf_plz` `uf_zip` varchar(8) COLLATE 'utf8_general_ci' NULL AFTER `uf_city`,
            COMMENT='';

            ALTER TABLE `offer`
            CHANGE `uf_messages_per_day_to` `uf_messages_per_day_from` int(11) NULL AFTER `uf_active_search_requests_from`,
            ADD `uf_messages_per_day_to` int(11) NULL,
            COMMENT='';
        ");


    }

    public function down()
    {
        echo "m150910_072132_add_offer_user_filter cannot be reverted.\n";

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
