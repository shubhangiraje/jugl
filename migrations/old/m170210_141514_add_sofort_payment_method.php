<?php

use yii\db\Migration;

class m170210_141514_add_sofort_payment_method extends Migration
{
    public function up()
    {
        $this->execute("
ALTER TABLE `pay_in_request`
CHANGE `payment_method` `payment_method` enum('PAYONE_CC','PAYONE_ELV','PAYONE_PAYPAL','PAYONE_GIROPAY','PAYONE_SOFORT') COLLATE 'utf8_general_ci' NOT NULL AFTER `dt`;

        ");
    }

    public function down()
    {
        echo "m170210_141514_add_sofort_payment_method cannot be reverted.\n";

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
