<?php

use yii\db\Schema;
use yii\db\Migration;

class m150708_132812_modify_offer_request_tables extends Migration
{
    public function up()
    {
        $this->execute("
            SET foreign_key_checks = 0;
            DROP TABLE `offer_request_file`, `offer_request_param_value`;

            ALTER TABLE `offer_request`
            DROP `price_from`,
            DROP `price_to`,
            DROP `relevancy`,
            COMMENT='';

            ALTER TABLE `offer_request`
            CHANGE `description` `description` mediumtext COLLATE 'utf8_general_ci' NULL AFTER `user_id`,
            COMMENT='';
        ");
    }

    public function down()
    {
        echo "m150708_132812_modify_offer_request_tables cannot be reverted.\n";

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
