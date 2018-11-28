<?php

use yii\db\Schema;
use yii\db\Migration;

class m151020_131336_add_user_packet extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user`
            ADD `packet` enum('STANDART','VIP') COLLATE 'utf8_general_ci' NULL DEFAULT 'STANDART',
            COMMENT='';
        ");
    }

    public function down()
    {
        echo "m151020_131336_add_user_packet cannot be reverted.\n";

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
