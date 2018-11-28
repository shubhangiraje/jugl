<?php

use yii\db\Migration;

class m170629_104603_add_count_offer_view_for_offer extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE `offer` ADD `count_offer_view` int(11) NOT NULL DEFAULT '0';");
    }

    public function down()
    {
        echo "m170629_104603_add_count_offer_view_for_offer cannot be reverted.\n";

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
