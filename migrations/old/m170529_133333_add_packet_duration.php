<?php

use yii\db\Migration;

class m170529_133333_add_packet_duration extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `pay_in_request`
            ADD `packet_duration_months` int NULL AFTER `type`;
        ");
    }

    public function down()
    {
        echo "m170529_133333_add_packet_duration cannot be reverted.\n";

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
