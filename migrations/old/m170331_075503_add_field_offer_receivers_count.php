<?php

use yii\db\Migration;

class m170331_075503_add_field_offer_receivers_count extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `offer` ADD `receivers_count` int(11) NULL;");
    }

    public function down()
    {
        echo "m170331_075503_add_field_offer_receivers_count cannot be reverted.\n";

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
