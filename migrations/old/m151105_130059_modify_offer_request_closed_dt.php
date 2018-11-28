<?php

use yii\db\Schema;
use yii\db\Migration;

class m151105_130059_modify_offer_request_closed_dt extends Migration
{
    public function up()
    {
        $this->execute("
            UPDATE `offer_request` SET `closed_dt` = '2015-01-01 00:00:00';
        ");
    }

    public function down()
    {
        echo "m151105_130059_modify_offer_request_closed_dt cannot be reverted.\n";

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
