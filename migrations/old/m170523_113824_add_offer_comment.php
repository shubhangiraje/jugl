<?php

use yii\db\Migration;

class m170523_113824_add_offer_comment extends Migration
{
    public function up()
    {
        $this->execute("
        ALTER TABLE `offer`
        ADD `comment` mediumtext NULL;
        ");
    }

    public function down()
    {
        echo "m170523_113824_add_offer_comment cannot be reverted.\n";

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
