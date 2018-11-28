<?php

use yii\db\Schema;
use yii\db\Migration;

class m151028_110740_modify_interest_linking_to_deals extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `search_request_interest`
            CHANGE `level2_interest_id` `level2_interest_id` int(11) NULL AFTER `level1_interest_id`,
            CHANGE `level3_interest_id` `level3_interest_id` int(11) NULL AFTER `level2_interest_id`,
            COMMENT='';
        ");


        $this->execute("
        ALTER TABLE `search_request_interest`
ADD `id` bigint NOT NULL AUTO_INCREMENT UNIQUE FIRST,
COMMENT='';
        ");

        $this->execute("
ALTER TABLE `search_request_interest`
CHANGE `id` `id` bigint(20) NOT NULL FIRST,
COMMENT='';
        ");

        $this->execute("
ALTER TABLE `search_request_interest`
DROP INDEX `id`;
        ");

        $this->execute("
ALTER TABLE `search_request_interest`
ADD PRIMARY KEY `search_request_id_level1_interest_id_level2_interest_id_id` (`search_request_id`, `level1_interest_id`, `level2_interest_id`, `id`),
DROP INDEX `PRIMARY`;
        ");

        $this->execute("
ALTER TABLE `search_request_interest`
ADD PRIMARY KEY `search_request_id_level1_interest_id_id` (`search_request_id`, `level1_interest_id`, `id`),
DROP INDEX `PRIMARY`;
        ");

        $this->execute("
ALTER TABLE `search_request_interest`
ADD PRIMARY KEY `search_request_id_id` (`search_request_id`, `id`),
DROP INDEX `PRIMARY`;
        ");

        $this->execute("
ALTER TABLE `search_request_interest`
ADD PRIMARY KEY `id` (`id`),
ADD INDEX `search_request_id` (`search_request_id`),
DROP INDEX `PRIMARY`;
        ");

        $this->execute("
ALTER TABLE `search_request_interest`
CHANGE `id` `id` bigint(20) NOT NULL AUTO_INCREMENT FIRST,
CHANGE `level2_interest_id` `level2_interest_id` int(11) NULL AFTER `level1_interest_id`,
CHANGE `level3_interest_id` `level3_interest_id` int(11) NULL AFTER `level2_interest_id`,
COMMENT='';
        ");

        $this->execute("
            ALTER TABLE `offer_interest`
            CHANGE `level2_interest_id` `level2_interest_id` int(11) NULL AFTER `level1_interest_id`,
            CHANGE `level3_interest_id` `level3_interest_id` int(11) NULL AFTER `level2_interest_id`,
            COMMENT='';
        ");

        $this->execute("
ALTER TABLE `offer_interest`
ADD INDEX `offer_id` (`offer_id`),
DROP INDEX `PRIMARY`;
        ");

        $this->execute("
ALTER TABLE `offer_interest`
ADD `id` bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
CHANGE `level2_interest_id` `level2_interest_id` int(11) NULL DEFAULT '0' AFTER `level1_interest_id`,
CHANGE `level3_interest_id` `level3_interest_id` int(11) NULL DEFAULT '0' AFTER `level2_interest_id`,
COMMENT='';
        ");

        $this->execute("
ALTER TABLE `offer_interest`
CHANGE `level2_interest_id` `level2_interest_id` int(11) NULL AFTER `level1_interest_id`,
CHANGE `level3_interest_id` `level3_interest_id` int(11) NULL AFTER `level2_interest_id`,
COMMENT='';
        ");

    }

    public function down()
    {
        echo "m151028_110740_modify_interest_linking_to_deals cannot be reverted.\n";

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
