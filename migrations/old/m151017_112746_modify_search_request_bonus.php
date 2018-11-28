<?php

use yii\db\Schema;
use yii\db\Migration;

class m151017_112746_modify_search_request_bonus extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `search_request`
              CHANGE `bonus` `bonus` decimal(14,2) NOT NULL AFTER `price_to`;
        ");
    }

    public function down()
    {
        echo "m151017_112746_modify_search_request_bonus cannot be reverted.\n";

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
