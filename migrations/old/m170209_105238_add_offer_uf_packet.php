<?php

use yii\db\Migration;

class m170209_105238_add_offer_uf_packet extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `offer`
            ADD `uf_packet` enum('ALL','STANDART','VIP') COLLATE 'utf8_general_ci' NOT NULL DEFAULT 'ALL' AFTER `uf_sex`,
            DROP `uf_only_for_vip`;
        ");
    }

    public function down()
    {
        echo "m170209_105238_add_offer_uf_packet cannot be reverted.\n";

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
