<?php

use yii\db\Migration;

class m180328_125716_change_type_click_id_for_annecy_reward extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE `annecy_reward` CHANGE `click_id` `click_id` varchar(256) NULL AFTER `campaign_title`;');
    }

    public function down()
    {
        echo "m180328_125716_change_type_click_id_for_annecy_reward cannot be reverted.\n";

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
