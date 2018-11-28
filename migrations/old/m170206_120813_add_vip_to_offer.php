<?php

use yii\db\Migration;

class m170206_120813_add_vip_to_offer extends Migration
{
    public function up()
    {
        $this->execute("
ALTER TABLE `offer`
ADD `uf_only_for_vip` tinyint(1) NOT NULL DEFAULT '0' AFTER `uf_enabled`;        
        ");
    }

    public function down()
    {
        echo "m170206_120813_add_vip_to_offer cannot be reverted.\n";

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
