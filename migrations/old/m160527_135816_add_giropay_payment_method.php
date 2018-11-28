<?php

use yii\db\Schema;
use yii\db\Migration;

class m160527_135816_add_giropay_payment_method extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `pay_in_request`
            CHANGE `payment_method` `payment_method` enum('PAYONE_CC','PAYONE_ELV','PAYONE_PAYPAL','PAYONE_GIROPAY') COLLATE 'utf8_general_ci' NOT NULL AFTER `dt`,
            COMMENT='';
        ");
    }

    public function down()
    {
        echo "m160527_135816_add_giropay_payment_method cannot be reverted.\n";

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
