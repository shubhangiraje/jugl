<?php

use yii\db\Schema;
use yii\db\Migration;

class m151001_081359_modify_search_request_price_to extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `search_request`
              CHANGE `price_to` `price_to` decimal(14,2) NULL AFTER `price_from`;
        ");
    }

    public function down()
    {
        echo "m151001_081359_modify_search_request_price_to cannot be reverted.\n";

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
