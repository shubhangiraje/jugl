<?php

use yii\db\Schema;
use yii\db\Migration;

class m151027_130148_add_payout_request_id extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `pay_out_request`
            ADD `pay_out_method_num` bigint NOT NULL AFTER `status`,
            COMMENT='';
        ");
    }

    public function down()
    {
        echo "m151027_130148_add_payout_request_id cannot be reverted.\n";

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
