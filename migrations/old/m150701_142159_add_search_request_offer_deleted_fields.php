<?php

use yii\db\Schema;
use yii\db\Migration;

class m150701_142159_add_search_request_offer_deleted_fields extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `search_request_offer`
            ADD `deleted` tinyint(1) NOT NULL DEFAULT '0',
            ADD `deleted_by_search_request_owner` tinyint(1) NOT NULL DEFAULT '0' AFTER `deleted`,
            COMMENT='';
        ");
    }

    public function down()
    {
        echo "m150701_142159_add_search_request_offer_deleted_fields cannot be reverted.\n";

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
