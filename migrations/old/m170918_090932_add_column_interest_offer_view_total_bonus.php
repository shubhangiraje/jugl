<?php

use yii\db\Migration;

class m170918_090932_add_column_interest_offer_view_total_bonus extends Migration
{
    public function up()
    {
		$this->execute("
            ALTER TABLE `interest`
             ADD `offer_view_total_bonus` decimal(14,2) NULL AFTER `offer_view_bonus`;   
        ");

    }

    public function down()
    {
        echo "m170918_090932_add_column_interest_offer_view_total_bonus cannot be reverted.\n";

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
